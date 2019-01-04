<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2018 Michael Uno
 */

/**
 * Searches a product by the given ASIN and locale.
 
 * @package     Amazon Auto Links
 * @since       3
 * @since       3.5.0       Renamed from `AmazonAutoLinks_Event_Action_API_SearchProduct`.
 * @since       3.7.7       Deprecated but serves as a base class for a new class that handles multiple item look-ups.
 */
class AmazonAutoLinks_Event___Action_APIRequestSearchProduct extends AmazonAutoLinks_Event___Action_Base {

    protected $_sActionHookName = 'aal_action_api_get_product_info';

    private $___sAPIRequestType = 'api_product_info'; // up to 20 chars

    protected function _construct() {
        add_filter( 'aal_filter_disallowed_http_request_types_for_background_cache_renewal', array( $this, 'replyToAddExceptedRequestType' ) );
    }
        /**
         * Adds the request type for excepted types.
         *
         * This way, cache renewal events of HTTP requests of the type do not get processed in the background.
         * If the caches are expired, they will be fetched at the time the request is made.
         *
         * @return array
         */
        public function replyToAddExceptedRequestType( $aExceptedRequestTypes ) {
            $aExceptedRequestTypes[] = $this->___sAPIRequestType;
            return $aExceptedRequestTypes;
        }

    /**
     * Searches the product and saves the data.
     */
    protected function _doAction( /* $aArguments */ ) {

        $_aParams        = func_get_args() + array( null );

        $aArguments      = $_aParams[ 0 ] + array( null, null, null, null, null );
        $_sAssociateID   = $aArguments[ 0 ];
        $_sASIN          = $aArguments[ 1 ];
        $_sLocale        = strtoupper( $aArguments[ 2 ] );
        $_iCacheDuration = $aArguments[ 3 ];
        $_bForceRenew    = ( boolean ) $aArguments[ 4 ];
        $_sItemFormat    = $aArguments[ 5 ];

        // Extract the product data from the entire API response.
        $_aProductData     = $this->___getProductData(
            $_sASIN,
            $_sLocale,
            $_sAssociateID,
            $_iCacheDuration,
            $_bForceRenew
        );

        if ( empty( $_aProductData ) ) {
            return;
        }

        // Retrieve similar products in a separate routine
        // Do this only `%similar%` is present in the Item Format option.
        if ( false !== strpos( $_sItemFormat, '%similar%' ) ) {
            $this->_scheduleFetchingSimilarProducts(
                $_aProductData,
                $_sASIN,
                $_sLocale,
                $_sAssociateID,
                $_iCacheDuration,
                $_bForceRenew
            );
        }
        $this->_setProductData(
            $_aProductData,
            $_sASIN,
            $_sLocale,
            $_iCacheDuration,
            $_bForceRenew,
            $_sItemFormat
        );
        
    }

        /**
         * @return      void
         * @since       unknown
         * @since       3.7.7       Changed the scope to `protected` to allow extended class to access it.
         */
        protected function _scheduleFetchingSimilarProducts( $aAPIResponseProductData, $sASIN, $sLocale, $sAssociateID, $iCacheDuration, $bForceRenew ) {
            
            $_aSimilarProducts = $this->getElementAsArray(
                $aAPIResponseProductData,
                array( 'SimilarProducts', 'SimilarProduct' )
            );
            $_aSimilarProductASINs = array();
            foreach( $_aSimilarProducts as $_aItem ) {
                if ( ! isset( $_aItem[ 'ASIN' ] ) ) {
                    continue;
                }
                $_aSimilarProductASINs[] = $_aItem[ 'ASIN' ];
            }

            AmazonAutoLinks_Event_Scheduler::scheduleSimilarProducts(
                $_aSimilarProductASINs,
                $sASIN,
                $sLocale,
                $sAssociateID,
                $iCacheDuration,
                $bForceRenew
            );            
            
        }

        /**
         * @param       array       $aAPIResponseProductData
         * @param       string      $sASIN
         * @param       string      $sLocale
         * @param       integer     $iCacheDuration
         * @return      void
         * @since       unknown
         * @since       3.7.7       Changed the visibility to `protected` to allow extended classes to access it.
         */
        protected function _setProductData( array $aAPIResponseProductData, $sASIN, $sLocale, $iCacheDuration, $bForceRenew, $sItemFormat ) {
             
            // Check if a customer review exists in the API response.
            $_bCustomerReviewExists = $this->___hasCustomerReview( $aAPIResponseProductData );

            if ( $_bCustomerReviewExists ) {
                AmazonAutoLinks_Event_Scheduler::scheduleCustomerReviews(
                    $this->getElement(
                        $aAPIResponseProductData,
                        array( 'CustomerReviews', 'IFrameURL' ),
                        ''  // default
                    ),
                    $sASIN,
                    $sLocale,
                    $iCacheDuration,
                    $bForceRenew
                );
            }   
                                   
            $_aRow             = $this->___getRowFormatted( 
                $aAPIResponseProductData, 
                $sASIN, 
                $sLocale,
                $iCacheDuration,
                $_bCustomerReviewExists
            );

            $_oProductTable = new AmazonAutoLinks_DatabaseTable_aal_products;
            $_oProductTable->setRowByASINLocale(
                $sASIN . '_' . strtoupper( $sLocale ),  // asin _ locale
                $_aRow // row data to set
            );           

        }
            /**
             * Checks whether a customer review exists or not.
             * @return      boolean
             */
            private function ___hasCustomerReview( array $aProductData ) {
                return in_array(
                    $this->getElement(
                        $aProductData,
                        array( 'CustomerReviews', 'HasReviews' ),
                        false
                    ),
                    array( true, 'true', 'TRUE', 'True', 1, '1',  ),
                    true    // type-sensitive
                );
            }            
            /**
             * Formats the given API Item (which contains product information) array 
             * to insert them into the database table.
             * 
             * @return      array
             * @since       unknown
             * @since       3.8.5   Changed some of the default values from `null` to an empty string to avoid automatic scheduling of product details retrieval tasks.
             */
            private function ___getRowFormatted( array $aAPIResponseProductData, $sASIN, $sLocale, $iCacheDuration, $bCustomerReviewExists ) {
                
                $_aRow = array(
                    'asin_locale'        => $sASIN . '_' . $sLocale,  
                    'locale'             => $sLocale,
                    'modified_time'      => date( 'Y-m-d H:i:s' ),
                    'links'              => $this->getElement(
                        $aAPIResponseProductData,
                        array( 'ItemLinks', 'ItemLink', ),
                        ''
                    ),
                    'price'              => $this->___getPriceByKey(
                        $aAPIResponseProductData,
                        'Amount', // key
                        0  // default - when not found
                    ),
                    'price_formatted'    => $this->___getPriceByKey( 
                        $aAPIResponseProductData,
                        'FormattedPrice',
                        ''  // default 
                    ),
                    'currency'           => $this->getElement(
                        $aAPIResponseProductData,
                        array( 'ItemAttributes', 'ListPrice', 'CurrencyCode' )
                    ),
                    'sales_rank'         => ( integer ) $this->getElement(
                        $aAPIResponseProductData,
                        array( 'SalesRank' ),
                        0
                    ),
                    'lowest_new_price'   => $this->getElement(
                        $aAPIResponseProductData,
                        array( 'OfferSummary', 'LowestNewPrice', 'Amount' ),
                        0   // 3.8.5 Set a value to avoid setting `null` for automatic scheduling background task to fetch product details
                    ),
                    'lowest_new_price_formatted'  => $this->getElement(
                        $aAPIResponseProductData,
                        array( 'OfferSummary', 'LowestNewPrice', 'FormattedPrice' ),
                        '' // 3.8.5 Set a value to avoid setting `null` for automatic scheduling background task to fetch product details
                    ),                    
                    'lowest_used_price'           => $this->getElement(
                        $aAPIResponseProductData,
                        array( 'OfferSummary', 'LowestUsedPrice', 'Amount' ),
                        0 // 3.8.5 Set a value to avoid setting `null` for automatic scheduling background task to fetch product details
                    ),
                    'lowest_used_price_formatted' => $this->getElement(
                        $aAPIResponseProductData,
                        array( 'OfferSummary', 'LowestUsedPrice', 'FormattedPrice' ),
                        ''  // 3.8.5 Set a value to avoid setting `null` for automatic scheduling background task to fetch product details
                    ),   
                    'count_new'          => $this->getElement(
                        $aAPIResponseProductData,
                        array( 'OfferSummary', 'TotalNew' ),
                        0
                    ),   
                    'count_used'         => $this->getElement(
                        $aAPIResponseProductData,
                        array( 'OfferSummary', 'TotalUsed' ),
                        0
                    ),                       
                    'title'              => $this->getElement(
                        $aAPIResponseProductData,
                        array( 'ItemAttributes', 'Title' ),
                        ''
                    ),    
                    'images'             => $this->___getImages( 
                        $aAPIResponseProductData 
                    ),     
                    
                    // Similar products will be set with a separate routine.
                    // Once an empty value is set, it will no longer trigger the value retrieval background routine
                    // 'similar_products'   => '',
                    
                    'editorial_reviews'  => $this->getElement(
                        $aAPIResponseProductData,
                        array( 'EditorialReviews', 'EditorialReview' ),
                        ''
                    ),
                                        
                    // 'description'        => null,   // (string) product details

                    // @todo Add the browse_nodes column
                    // 'browse_nodes'    => $this->getElement(
                        // $aAPIResponseProductData,
                        // array( 'BrowseNodes', 'BrowseNode' )                        
                    // ),
                    
                );

                // if `0` is passed for the cache duration, it just renews the cache and do not update the expiration time.
                if ( $iCacheDuration ) {
                    $_aRow[ 'expiration_time' ] = date( 'Y-m-d H:i:s', time() + $iCacheDuration );
                }

                // Retrieve or calculate a discounted price.
                $_aOffer           = $this->___getOfferArray( $aAPIResponseProductData, $_aRow[ 'price' ] );
                $_nDiscountedPrice = $this->___getDiscountedPrice( 
                    $_aOffer,
                    $_aRow[ 'price' ]
                );
                $_aRow = $_aRow + array(
                    'discounted_price'            => $_nDiscountedPrice,
                    'discounted_price_formatted'  => $this->___getFormattedDiscountPrice(
                        $_aOffer,
                        $_aRow[ 'price' ],
                        $_aRow[ 'price_formatted' ],
                        $_nDiscountedPrice
                    ),
                );
                
                // If a customer review does not exist, fill these elements with an empty value.
                // When an element value is null when retrieved from the front-end, 
                // It schedules the background task. So in order to avoid it, set a value.
                if ( ! $bCustomerReviewExists ) {
                    
                    $_aRow[ 'rating' ]                  = 0;
                    $_aRow[ 'rating_image_url' ]        = '';
                    $_aRow[ 'rating_html' ]             = '';
                    $_aRow[ 'number_of_reviews' ]       = 0;
                    $_aRow[ 'customer_review_url' ]     = '';
                    $_aRow[ 'customer_review_charset' ] = '';
                    $_aRow[ 'customer_reviews' ]        = '';
                    
                }

                // 3.8.0 If the table version is above 1.1.0b01 or above,
                $_sCurrentVersion = get_option( "aal_products_version", '0' );
                if ( version_compare( $_sCurrentVersion, '1.1.0b01', '>=')) {
                    $_aRow[ 'features' ]   = $this->getElementAsArray(
                        $aAPIResponseProductData,
                        array( 'ItemAttributes', 'Feature', )
                    );
                    $_aRow[ 'categories' ] = $this->getElementAsArray(
                        $aAPIResponseProductData,
                        array( 'BrowseNodes', )
                    );
                }

                return $_aRow;
            }
                /**
                 * 
                 * @return      string
                 */
                private function ___getPriceByKey( $aAPIResponseProductData, $sKey, $mDefault ) {
                    
                    // There are cases the listed price is not set.
                    $_sFormattedPrice = $this->getElement(
                        $aAPIResponseProductData,
                        array( 'ItemAttributes', 'ListPrice', $sKey ),
                        $mDefault // avoid null as in the fron-end, when null is returend, it triggers a background task
                    );

                    if ( ! empty( $_sFormattedPrice ) ) {
                        return $_sFormattedPrice;
                    }
                    // Try to use a lowest new one.
                    $_sFormattedPrice = $this->getElement(
                        $aAPIResponseProductData,
                        array( 'OfferSummary', 'LowestNewPrice', $sKey ),
                        $mDefault  // avoid null as in the fron-end, when null is returend, it triggers a background task
                    ); 
                    return $_sFormattedPrice;
                }            
                /**
                 * Extracs image info from the product array.
                 * @return      array
                 */
                private function ___getImages( $aProduct ) {
                    
                    $_aMainImage = array(
                        'SwatchImage' => $this->getElement(
                            $aProduct,
                            array( 'SwatchImage' )
                        ),                    
                        'SmallImage' => $this->getElement(
                            $aProduct,
                            array( 'SmallImage' )
                        ),
                        'MediumImage' => $this->getElement(
                            $aProduct,
                            array( 'MediumImage' )
                        ), 
                        'LargeImage' => $this->getElement(
                            $aProduct,
                            array( 'LargeImage' )
                        ),         
                        'HiResImage' => $this->getElement(
                            $aProduct,
                            array( 'HiResImage' )
                        ),                                 
                    );
                    // Will be numerically indexed array holding sub-image each.
                    $_aSubImages = $this->getElementAsArray(
                        $aProduct,
                        array( 'ImageSets', 'ImageSet' ),
                        array()
                    );  
                    
                    // Sub-images can be only single. In that case, put it in a numeric element.
                    if ( ! isset( $_aSubImages[ 0 ] ) ) {
                        $_aSubImages = array( $_aSubImages );
                    }
                    
                    return array(
                        'main' => $_aMainImage,
                    ) + $_aSubImages;
                }
                /**
                 * 
                 * @return      string
                 */
                private function ___getFormattedDiscountPrice( $aOffer, $nPrice, $sPriceFormatted, $nDiscountedPrice ) {
                    
                    // If the formatted price is set in the Offer element, use it.
                    $_sDiscountedPriceFormatted = $this->getElement(
                        $aOffer,
                        array( 'Price', 'FormattedPrice' ),
                        ''  // 3.8.5 Changed the value from `null` to an empty string to avoid automatic background product detail retrieval tasks
                    );
                    if ( '' !== $_sDiscountedPriceFormatted ) {
                        return $_sDiscountedPriceFormatted;
                    }
                    
                    // Otherwise, replace the price part of the listed price with the discounted one.
                    return $this->___getFormattedPriceFromModel(
                        $nDiscountedPrice / 100,   // decimal
                        $sPriceFormatted 
                    );
                    
                }
                    /**
                     * @param $nPrice
                     * @param $sModel
                     *
                     * @return string
                     */
                    private function ___getFormattedPriceFromModel( $nPrice, $sModel ) {
                        return preg_replace(
                            '/[\d\.,]+/',   // needle
                            $nPrice,
                            $sModel
                        );                        
                    }
                /**
                 * Calculates the discounted price.
                 * @param       array       $aOffer     ListedOffer element in the Offer element array in the response product data.
                 * @param       mixed       $nPrice     The listed price.
                 * @return      integer
                 */
                private function ___getDiscountedPrice( $aOffer, $nPrice ) {
                    
                    $_nDiscountedPrice = $this->getElement(
                        $aOffer,
                        array( 'Price', 'Amount' ),
                        null
                    );
                    if ( null !== $_nDiscountedPrice ) {
                        return $_nDiscountedPrice;
                    }
                    
                    // If saving amount is set
                    $_nSavingAmount = $this->getElement(
                        $aOffer,
                        array( 'AmountSaved', 'Amount' ),
                        null
                    );
                    if ( null !== $_nSavingAmount ) {
                        return $nPrice - $_nSavingAmount;
                    }
                    
                    // If discount percentage is set,
                    $_nDiscountPercentage = $this->getElement(
                        $aOffer,
                        array( 'PercentageSaved' ),
                        null
                    );
                    if ( null !== $_nDiscountPercentage ) {
                        return $nPrice * ( ( 100 - $_nDiscountPercentage ) / 100 );
                    }                    
                    return 0;   // 3.8.5 changed the default value from null to 0 to avoid automatic background task of retrieving product details
                }
                /**
                 * @return      array
                 */
                private function ___getOfferArray( $aProduct, $nPrice=null ) {

                    $_iTotalOffers = $this->getElement(
                        $aProduct,
                        array( 'Offers', 'TotalOffers' ),
                        0
                    );  
                    if ( 2 > $_iTotalOffers  ) {
                        return $this->getElementAsArray(
                            $aProduct,
                            array( 'Offers', 'Offer', 'OfferListing' ),
                            array()
                        );
                    }
                    $_aOffers = $this->getElementAsArray(
                        $aProduct,
                        array( 'Offers', 'Offer' ),
                        array()
                    );
                    
                    $_aDiscountedPrices = array();
                    foreach( $_aOffers as $_iIndex => $_aOffer ) {
                        
                        if ( ! isset( $_aOffer[ 'OfferListing' ] ) ) {
                            continue;
                        }
                        
                        $_aDiscountedPrices[ $_iIndex ] = $this->___getDiscountedPrice(
                            $_aOffer[ 'OfferListing' ],
                            $nPrice
                        );
                        
                    }
                    $_iIndex = $this->getKeyOfLowestElement( $_aDiscountedPrices );
                    return $_aOffers[ $_iIndex ][ 'OfferListing' ];                    

                }
        /**
         * Extracts product data from the response data fetched from amazon server with the Product Advertising API.
         * @return      array
         * @see         http://docs.aws.amazon.com/AWSECommerceService/latest/DG/ItemLookup.html
         */
        private function ___getProductData( $sASIN, $sLocale, $sAssociateID, $iCacheDuration, $bForceRenew ) {
            
            $_oOption     = AmazonAutoLinks_Option::getInstance();
            if ( ! $_oOption->isAPIConnected() ) {
                return array();
            }
            $_sPublicKey  = $_oOption->get( array( 'authentication_keys', 'access_key' ), '' );
            $_sPrivateKey = $_oOption->get( array( 'authentication_keys', 'access_key_secret' ), '' );
            if ( empty( $_sPublicKey ) || empty( $_sPrivateKey ) ) {
                return array();
            }

            return $this->_getAPIResponse(
                $_sPublicKey,
                $_sPrivateKey,
                $sASIN,
                $sLocale,
                $sAssociateID,
                $iCacheDuration,
                $bForceRenew
            );

        }
            /**
             * @remark  Also accessed by an extended class.
             * @since   3.7.7
             * @return  array
             */
            protected function _getAPIResponse( $_sPublicKey, $_sPrivateKey, $sASIN, $sLocale, $sAssociateID, $iCacheDuration, $bForceRenew, $aResponseElement=array( 'Items', 'Item' ) ) {
                $_aAPIArguments = array(
                    'Operation'             => 'ItemLookup',

                    // (optional) Used | Collectible | Refurbished, All
                    'Condition'             => 'All',

                    // (optional) All IdTypes except ASINx require a SearchIndex to be specified.  SKU | UPC | EAN | ISBN (US only, when search index is Books). UPC is not valid in the CA locale.
                    'IdType'                => 'ASIN',

                    // (optional)
                    'IncludeReviewsSummary' => 'True',

                    // (required)  If ItemId is an ASIN, a SearchIndex cannot be specified in the request.
                    'ItemId'                => $sASIN,

                    // 'RelatedItemPage' => null,    // (optional) This optional parameter is only valid when the RelatedItems response group is used.
                    // 'RelationshipType' => null,    // (conditional)    This parameter is required when the RelatedItems response group is used.

                    // (conditional) see: http://docs.aws.amazon.com/AWSECommerceService/latest/DG/APPNDX_SearchIndexValues.html
                    // 'SearchIndex'           => $this->arrArgs['SearchIndex'],

                    // 'TruncateReviewsAt' => 1000, // (optional)
                    // 'VariationPage' => null, // (optional)

                    // (optional)
                    'ResponseGroup'         => 'Large',

                );

                $_oAmazonAPI = new AmazonAutoLinks_ProductAdvertisingAPI(
                    $sLocale,   // locale
                    $_sPublicKey,
                    $_sPrivateKey,
                    $sAssociateID,
                    array(),    // HTTP arguments
                    $this->___sAPIRequestType  // type
                );
                $_aRawData = $_oAmazonAPI->request(
                    $_aAPIArguments,
                    $iCacheDuration,    // @note before v3.5.0, the cache duration was 60 for some reasons.
                    $bForceRenew
                );
                if ( empty( $aResponseElement ) ) {
                    return $_aRawData;
                }
                return $this->getElementAsArray(
                    $_aRawData, // subject
                    $aResponseElement, // dimensional keys
                    array() // default
                );

            }

}