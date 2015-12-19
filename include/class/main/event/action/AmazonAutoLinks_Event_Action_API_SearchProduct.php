<?php
/**
 * Amazon Auto Links
 * 
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2015 Michael Uno
 * 
 */

/**
 * Searches a product by the given ASIN and locale.
 
 * @package      Amazon Auto Links
 * @since        3
 * @action       aal_action_api_get_product_info
 */
class AmazonAutoLinks_Event_Action_API_SearchProduct extends AmazonAutoLinks_Event_Action_Base {
    
    /**
     * Searches the product and saves the data.
     * @callback        action        aal_action_api_get_product_info
     */
    public function doAction( /* $aArguments */ ) {

        $_aParams        = func_get_args() + array( null );

        $aArguments      = $_aParams[ 0 ] + array( null, null, null, null );
        $_sAssociateID   = $aArguments[ 0 ];
        $_sASIN          = $aArguments[ 1 ];
        $_sLocale        = strtoupper( $aArguments[ 2 ] );
        $_iCacheDuration = $aArguments[ 3 ];
 
        // Extract the product data from the entire API response.
        $_aProductData     = $this->_getProductData( 
            $_sASIN, 
            $_sLocale, 
            $_sAssociateID 
        );
        if ( empty( $_aProductData ) ) {
            return;
        }

        $this->_setProductData( 
            $_aProductData, 
            $_sASIN,
            $_sLocale,
            $_iCacheDuration
        );
        
    }
        /**
         * Checks whether a customer review exists or not.
         * @return      boolean
         */
        private function _hasCustomerReview( array $aProductData ) {
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
        private function _setProductData( array $aAPIResponseProductData, $sASIN, $sLocale, $iCacheDuration ) {
             
            // Check if a customer review exists.
            $_bCustomerReviewExists = $this->_hasCustomerReview( $aAPIResponseProductData );
       
            if ( $_bCustomerReviewExists ) {            
                AmazonAutoLinks_Event_Scheduler::getCustomerReviews(
                    $this->getElement(
                        $aAPIResponseProductData,
                        array( 'CustomerReviews', 'IFrameURL' ),
                        ''  // default
                    ),
                    $sASIN,
                    $sLocale,
                    $iCacheDuration
                );
            }            
            
            $_aRow             = $this->_formatRow( 
                $aAPIResponseProductData, 
                $sASIN, 
                $sLocale,
                $iCacheDuration,
                $_bCustomerReviewExists
            );

            $_oProductTable    = new AmazonAutoLinks_DatabaseTable_product(
                AmazonAutoLinks_Registry::$aDatabaseTables[ 'product' ]
            );             
            $_iObjectID = $_oProductTable->setRowByASINLocale(
                $sASIN . '_' . strtoupper( $sLocale ),  // asin _ locale
                $_aRow // row data to set
            );           

        }
            /**
             * Formats the given API Item (which contains product information) array 
             * to insert then into the database table.
             * 
             * @return      array
             */
            private function _formatRow( array $aAPIResponseProductData, $sASIN, $sLocale, $iCacheDuration, $bCustomerReviewExists ) {
                
                $_aRow = array(
                    'asin_locale'        => $sASIN . '_' . $sLocale,  
                    'locale'             => $sLocale,
                    'modified_time'      => date( 'Y-m-d H:i:s' ), 
                    'expiration_time'    => date( 'Y-m-d H:i:s', time() + $iCacheDuration ),
                    'links'              => $this->getElement(
                        $aAPIResponseProductData,
                        array( 'ItemLinks', 'ItemLink', )
                    ),
                    'price'              => $this->_getPriceByKey(
                        $aAPIResponseProductData,
                        'Amount', // key
                        0  // default - when not found
                    ),
                    'price_formatted'    => $this->_getPriceByKey( 
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
                        array( 'SalesRank' )
                    ),
                    'lowest_new_price'   => $this->getElement(
                        $aAPIResponseProductData,
                        array( 'OfferSummary', 'LowestNewPrice', 'Amount' )
                    ),
                    'lowest_new_price_formatted'  => $this->getElement(
                        $aAPIResponseProductData,
                        array( 'OfferSummary', 'LowestNewPrice', 'FormattedPrice' )
                    ),                    
                    'lowest_used_price'           => $this->getElement(
                        $aAPIResponseProductData,
                        array( 'OfferSummary', 'LowestUsedPrice', 'Amount' )
                    ),
                    'lowest_used_price_formatted' => $this->getElement(
                        $aAPIResponseProductData,
                        array( 'OfferSummary', 'LowestUsedPrice', 'FormattedPrice' )
                    ),   
                    'count_new'          => $this->getElement(
                        $aAPIResponseProductData,
                        array( 'OfferSummary', 'TotalNew' )
                    ),   
                    'count_used'         => $this->getElement(
                        $aAPIResponseProductData,
                        array( 'OfferSummary', 'TotalUsed' )
                    ),                       
                    'title'              => $this->getElement(
                        $aAPIResponseProductData,
                        array( 'ItemAttributes', 'Title' )
                    ),    
                    'images'             => $this->_getImages( 
                        $aAPIResponseProductData 
                    ),
                    'similar_products'   => $this->getElement(
                        $aAPIResponseProductData,
                        array( 'SimilarProducts', 'SimilarProduct' )
                    ),        

                    'editorial_reviews'  => $this->getElement(
                        $aAPIResponseProductData,
                        array( 'EditorialReviews', 'EditorialReview' )
                    ),
                                        
                    // 'description'        => null,   // (string) product details

                    // @todo Add the browse_nodex column
                    // 'browse_nodes'    => $this->getElement(
                        // $aAPIResponseProductData,
                        // array( 'BrowseNodes', 'BrowseNode' )                        
                    // ),
                    
                ); 
                
                // Retrieve or calculate a discounted price.
                $_aOffer           = $this->_getOfferArray( $aAPIResponseProductData, $_aRow[ 'price' ] );
                $_nDiscountedPrice = $this->_getDiscountedPrice( 
                    $_aOffer,
                    $_aRow[ 'price' ]
                );
                $_aRow = $_aRow + array(
                    'discounted_price'            => $_nDiscountedPrice,
                    'discounted_price_formatted'  => $this->_getFormattedDiscountPrice(
                        $_aOffer,
                        $_aRow[ 'price' ],
                        $_aRow[ 'price_formatted' ],
                        $_nDiscountedPrice
                    ),
                );
                
                // If a customer review does not exist, fill these elements an empty value.
                // When a element value is null when retrieved from the front-end, 
                // It scheduels the background task. So in order to avoid it, set a value.
                if ( ! $bCustomerReviewExists ) {
                    
                    $_aRow[ 'rating' ]                  = 0;
                    $_aRow[ 'rating_image_url' ]        = '';
                    $_aRow[ 'rating_html' ]             = '';
                    $_aRow[ 'number_of_reviews' ]       = 0;
                    $_aRow[ 'customer_review_url' ]     = '';
                    $_aRow[ 'customer_review_charset' ] = '';
                    $_aRow[ 'customer_reviews' ]        = '';
                    
                }
                
                return $_aRow;
            }
                /**
                 * 
                 * @return      string
                 */
                private function _getPriceByKey( $aAPIResponseProductData, $sKey, $mDefault ) {
                    
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
                private function _getImages( $aProduct ) {
                    
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
                private function _getFormattedDiscountPrice( $aOffer, $nPrice, $sPriceFormatted, $nDiscountedPrice ) {
                    
                    // If the formatted price is set in the Offer element, use it.
                    $_sDiscountedPriceFormatted = $this->getElement(
                        $aOffer,
                        array( 'Price', 'FormattedPrice' ),
                        null
                    );
                    if ( null !== $_sDiscountedPriceFormatted ) {
                        return $_sDiscountedPriceFormatted;
                    }
                    
                    // Otherwise, replace the price part of the listed price with the discounted one.
                    return $this->_getFormattedPriceFromModel( 
                        $nDiscountedPrice / 100,   // decimal
                        $sPriceFormatted 
                    );
                    
                }
                    private function _getFormattedPriceFromModel( $nPrice, $sModel ) {
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
                private function _getDiscountedPrice( $aOffer, $nPrice ) {
                    
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
                    return null;
                }
                /**
                 * @return      array
                 */
                private function _getOfferArray( $aProduct, $nPrice=null ) {

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
                        
                        $_aDiscountedPrices[ $_iIndex ] = $this->_getDiscountedPrice(
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
        private function _getProductData( $sASIN, $sLocale, $sAssociateID ) {
            
            $_oOption     = AmazonAutoLinks_Option::getInstance();
            if ( ! $_oOption->isAPIConnected() ) {
                return array();
            }            
            
            $_sPublicKey  = $_oOption->get( array( 'authentication_keys', 'access_key' ), '' );
            $_sPrivateKey = $_oOption->get( array( 'authentication_keys', 'access_key_secret' ), '' );
            
            if ( empty( $_sPublicKey ) || empty( $_sPrivateKey ) ) {
                return array();
            }
            
            // Construct API arguments
            $_aAPIArguments = array(
            
                'Operation'             => 'ItemLookup',
                
                // (optional) Used | Collectible | Refurbished, All
                'Condition'             => 'All',    
                
                // (optional) All IdTypes except ASINx require a SearchIndex to be specified.  SKU | UPC | EAN | ISBN (US only, when search index is Books). UPC is not valid in the CA locale.
                'IdType'                => 'ASIN',    
                
                // (optional)
                'IncludeReviewsSummary' => "True", 
                
                // (required)  If ItemIdis an ASIN, a SearchIndex cannot be specified in the request.
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
                $sAssociateID
            );
            $_aRawData = $_oAmazonAPI->request(
                $_aAPIArguments,
                $sLocale,
// @todo Examine whether a unit cache duration can be set.                
                60 // cache duration - null to not to use cache.
            );
            return $this->getElement(
                $_aRawData, // subject
                array( 'Items', 'Item' ), // dimensional keys
                array() // default
            );
            
        }
}