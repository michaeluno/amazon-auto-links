<?php
/**
 * Amazon Auto Links
 * 
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2015 Michael Uno
 * 
 */

/**
 * Creates Amazon product links by ItemSearch.
 * 
 * @package         Amazon Auto Links
 */
class AmazonAutoLinks_Unit_search extends AmazonAutoLinks_Unit_Base_ElementFormat {
    
    /**
     * Stores the unit type.
     * @remark      Note that the base constructor will create a unit option object based on this value.
     */    
    public $sUnitType = 'search';
    
    /**
     * Represents the array structure of the item array element of API response data.
     * @since            unknown
     */    
    public static $aStructure_Item = array(
        'ASIN'              => null,
        'ItemAttributes'    => null,
        'DetailPageURL'     => null,
        'EditorialReviews'  => null,
        'ItemLinks'         => null,
        'ImageSets'         => null,
        'BrowseNodes'       => null,
        'SimilarProducts'   => null,
        'MediumImage'       => null,
        'OfferSummary'      => null,
    );
    
    /**
     * 
     * @return    array    The response array.
     */
    public function fetch( $aURLs=array() ) {
        
        // The search unit type does not use directly passed urls.
        // Maybe later at some point, custom request URIs can get implemented and they can be directly passed to this method.
        unset( $aURLs );
        
        $_aResponse = $this->getRequest( $this->oUnitOption->get( 'count' ) );

        // Check errors
        if ( isset( $_aResponse[ 'Error' ][ 'Code' ] ) ) {
            return $this->oUnitOption->get( 'show_errors' )
                ? $_aResponse
                : array();
        }            
        // Error in the Request element.
        if ( isset( $_aResponse[ 'Items' ][ 'Request' ][ 'Errors' ] ) ) {
            return $this->oUnitOption->get( 'show_errors' )
                ? $_aResponse[ 'Items' ][ 'Request' ][ 'Errors' ]
                : array();
        }
            
        $_aProducts = $this->getProducts( $_aResponse );

        // echo "<pre>" . htmlspecialchars( print_r( $_aResponse[ 'Items' ][ 'Item' ], true ) ) . "</pre>"
        return $_aProducts;
        
    }
    
        /**
         * Checks whether response has an error.
         * @return      boolean
         * @since       3
         */
        protected function _isError( $aProducts ) {
            if ( isset( $aProducts[ 'Error' ][ 'Code' ] ) ) {
                return true;
            }
            if ( isset( $aProducts[ 'Items' ][ 'Request' ][ 'Errors' ] ) ) {
                return true;
            }
            return parent::_isError( $aProducts );
            
        }    
    
    /**
     * Performs paged API requests.
     * 
     * This enables to retrieve more than 10 items. However, for it, it performs multiple requests, thus, it will be slow.
     * 
     * @since            2.0.1
     */
    protected function getRequest( $iCount ) {
        
        $_oAPI = new AmazonAutoLinks_ProductAdvertisingAPI( 
            $this->oUnitOption->get( 'country' ), 
            $this->oOption->get( 'authentication_keys', 'access_key' ),
            $this->oOption->get( 'authentication_keys', 'access_key_secret' ),
            $this->oUnitOption->get( 'associate_id' )
        );

        // First, perform the search for the first page regardless the specified count (number of items).
        // Keys with an empty value will be filtered out when performing the request.            
        $_aResponse = $_oAPI->request(
            $this->getAPIParameterArray( $this->oUnitOption->get( 'Operation' ) ), 
            $this->oUnitOption->get( 'country' ),   // locale
            $this->oUnitOption->get( 'cache_duration' )
        );    
        if ( $iCount <= 10 ) {
            return $_aResponse;
        }
        
        // Check necessary key is set
        if ( ! isset( $_aResponse[ 'Items' ][ 'Item' ] ) || ! is_array( $_aResponse[ 'Items' ][ 'Item' ] ) ) {
            return $_aResponse;
        }
        
        // Calculate the required number of pages.
        $_iPage = $this->_getTotalPageNumber( 
            $iCount, 
            $_aResponse, 
            $this->oUnitOption->get( 'SearchIndex' ) 
        );
        
        $_aResponseTrunk = $_aResponse;
                
        // First perform fetching data in the background if caches are not available. Parse backwards 
        $_bScheduled = null;
        for ( $_i = $_iPage; $_i >= 2 ; $_i-- ) {
            $_fResult = $_oAPI->scheduleInBackground( 
                $this->getAPIParameterArray( $this->oUnitOption->get( 'Operation' ), $_i ) 
            );
            $_bScheduled = $_bScheduled 
                ? $_bScheduled 
                : $_fResult;
        }
        if ( $_bScheduled ) {
            // there are items scheduled to fetch in the background, do it right now.
            AmazonAutoLinks_Shadow::gaze();
        }
        
        // Start from the second page since the first page has been already done. 
        for ( $_i = 2; $_i <= $_iPage; $_i++ ) {
            
            $_aResponse = $_oAPI->request( 
                $this->getAPIParameterArray(
                    $this->oUnitOption->get( 'Operation' ), 
                    $_i 
                ), 
                '', 
                $this->oUnitOption->get( 'cache_duration' )
            );
            if ( isset( $_aResponse[ 'Items' ][ 'Item' ] ) && is_array( $_aResponse[ 'Items' ][ 'Item' ] ) ) {
                $_aResponseTrunk[ 'Items' ][ 'Item' ] = $this->_addItems( $_aResponseTrunk[ 'Items' ][ 'Item' ], $_aResponse[ 'Items' ][ 'Item' ] );    
            }
                            
        }    
        
        return $_aResponseTrunk;
        
    }
        /**
         * Returns the total page number
         * 
         * @since   2.0.4.1b
         * @see     http://docs.aws.amazon.com/AWSECommerceService/latest/DG/ItemSearch.html
         */
        protected function _getTotalPageNumber( $iCount, $aResponse, $sSearchIndex='All' ) {
            
            $iMaxAllowedPages = $sSearchIndex == 'All' ? 5 : 10;        
            $iPage = ceil( $iCount / 10 );
            $iPage = $iPage > $iMaxAllowedPages ? $iMaxAllowedPages : $iPage;
            $iFoundTotalPages = isset( $aResponse[ 'Items' ][ 'TotalPages' ] ) ? $aResponse[ 'Items' ][ 'TotalPages' ] : 1;
            return $iFoundTotalPages <= $iPage ? 
                $iFoundTotalPages 
                : $iPage;
            
        }    
        /**
         * Adds product item elements in a response array if the same ASIN is not already in there
         * 
         * @since            2.0.4.1
         */
        protected function _addItems( $aMain, $aItems ) {
            
            // Extract all ASINs from the main array.
            $_aASINs = array();
            foreach( $aMain as $_aItem ) {
                if ( ! isset( $_aItem[ 'ASIN' ] ) ) { continue; }
                $_aASINs[ $_aItem[ 'ASIN' ] ] = $_aItem[ 'ASIN' ];
            }
            
            // Add the items if not already there.
            foreach ( $aItems as $_aItem ) {
                if ( ! isset( $_aItem[ 'ASIN' ] ) ) { continue; }
                if ( in_array( $_aItem[ 'ASIN' ], $_aASINs ) ) { continue; }
                $aMain[] = $_aItem;    // finally add the item
            }
            
            return $aMain;
            
        }
    /**
     * 
     * 'Operation' => 'ItemSearch',    // ItemSearch, ItemLookup, SimilarityLookup
     * @since   2.0.2
     * @see     http://docs.aws.amazon.com/AWSECommerceService/latest/DG/ItemSearch.html
     * @see     http://docs.aws.amazon.com/AWSECommerceService/latest/DG/PowerSearchSyntax.html
     */
    protected function getAPIParameterArray( $sOperation='ItemSearch', $iItemPage=null ) {

        $_bIsIndexAllOrBlended  = ( 'All' === $this->oUnitOption->get( 'SearchIndex' ) || 'Blended' === $this->oUnitOption->get( 'SearchIndex' ) );
        $_sTitle                = $this->trimDelimitedElements( 
            $this->oUnitOption->get( 'Title' ), 
            ',', 
            false 
        ); 
        $_aParams               = array(
            'Keywords'              => $this->trimDelimitedElements( 
                $this->oUnitOption->get( 'Keywords' ), 
                ',', 
                false 
            ),
            // 3+
            'Power'                 => $this->oUnitOption->get( 'Power' ), 
            
            'Title'                 => $_bIsIndexAllOrBlended 
                ? null 
                : ( $_sTitle ? $_sTitle : null ),

            // 'Operation'             => $sOperation,
            'Operation'             => $this->oUnitOption->get( 'Operation' ),
            'SearchIndex'           => $this->oUnitOption->get( 'SearchIndex' ),
            $this->oUnitOption->get( 'search_by' ) => $this->oUnitOption->get( 'additional_attribute' )
                ? $this->oUnitOption->get( 'additional_attribute' )
                : null,

            // when the search index is All, sort cannot be specified
            'Sort'                  => $_bIsIndexAllOrBlended 
                ? null 
                : $this->oUnitOption->get( 'Sort' ),    
                
            'ResponseGroup'         => "Large",
            'BrowseNode'            => ! $_bIsIndexAllOrBlended && $this->oUnitOption->get( 'BrowseNode' ) 
                ? $this->oUnitOption->get( 'BrowseNode' )
                : null,
            'Availability'          => $this->oUnitOption->get( 'Availability' ) 
                ? 'Available' 
                : null,
            'Condition'             => $_bIsIndexAllOrBlended 
                ? null 
                : $this->oUnitOption->get( 'Condition' ),
            'IncludeReviewsSummary' => "True",
            'MaximumPrice'          => ! $_bIsIndexAllOrBlended && $this->oUnitOption->get( 'MaximumPrice' )
                ? $this->oUnitOption->get( 'MaximumPrice' )
                : null,
            'MinimumPrice'          => ! $_bIsIndexAllOrBlended && $this->oUnitOption->get( 'MinimumPrice' )
                ? $this->oUnitOption->get( 'MinimumPrice' )
                : null,
            'MinPercentageOff'      => $this->oUnitOption->get( 'MinPercentageOff' )
                ? $this->oUnitOption->get( 'MinPercentageOff' )
                : null,
            
            // 2.0.7+
            'MerchantId'            => 'Amazon' === $this->oUnitOption->get( 'MerchantId' )
                ? 'Amazon'
                : null, 
                
            // 2.1.0+
            'MarketplaceDomain'     => 'Marketplace' === $this->oUnitOption->get( 'SearchIndex' )
                ? AmazonAutoLinks_Property::getMarketplaceDomainByLocale( $this->oUnitOption->get( 'country' ) )
                : null,                
                
        );        
        $_aParams = $iItemPage
            ? $_aParams + array( 'ItemPage' => $iItemPage )
            : $_aParams;

        // 3+ When the Power argument is set, the SearchIndex must not be set. 
        // and when the SearchIndex is not set, Sort cannot be set.
        if ( $_aParams[ 'Power' ] ) {
            unset( 
                $_aParams[ 'Sort' ]
            );
        }
        // if ( $_aParams[ 'Title' ] ) {
            // unset(
                // $_aParams[ 'SearchIndex' ]
            // );
            // $_aParams[ 'SearchIndex' ] = 'Books';
        // }
            
        return $_aParams;
    }
    
    /**
     * Constructs products array to be parsed in the template.
     * 
     * @return      array
     */
    protected function getProducts( $aResponse ) {
        
        $_aASINLocales = array();  // stores added product ASINs for performing a custom database query.
        $_sLocale      = strtoupper( $this->oUnitOption->get( 'country' ) );
        $_sAssociateID = $this->oUnitOption->get( 'associate_id' );        
        
        // First Iteration - Extract displaying ASINs.
        $_aProducts    = array();
        foreach ( $this->_getItems( $aResponse ) as $_aItem ) {

            if ( ! is_array( $_aItem ) ) { 
                continue; 
            }
            $_aItem = $_aItem + self::$aStructure_Item;
        
            if ( $this->isASINBlocked( $_aItem[ 'ASIN' ] ) ) {
                continue; 
            }
                
            $_sTitle = $this->sanitizeTitle( $_aItem[ 'ItemAttributes' ][ 'Title' ] );
            if ( $this->isTitleBlocked( $_sTitle ) ) { 
                continue; 
            }
            
            $_sProductURL = $this->formatProductLinkURL( 
                rawurldecode( $_aItem[ 'DetailPageURL' ] ),
                $_aItem[ 'ASIN' ] 
            );

            $_sContent = isset( $_aItem[ 'EditorialReviews' ][ 'EditorialReview' ] ) 
                ? $this->joinIfArray( 
                    $_aItem[ 'EditorialReviews' ][ 'EditorialReview' ], 
                    'Content' 
                )
                : '';
                
            $_sDescription = $this->sanitizeDescription( 
                $_sContent, 
                $this->oUnitOption->get( 'description_length' ), 
                $_sProductURL 
            );
            if ( $this->isDescriptionBlocked( $_sDescription ) ) { 
                continue; 
            }
            
            // At this point, update the black&white lists as this item is parsed.
            $this->setParsedASIN( $_aItem['ASIN'] );            
            
            // Construct a product array. This will be passed to a template.
            $_aProduct = array(
                'ASIN'               => $_aItem[ 'ASIN' ],
                'product_url'        => $_sProductURL,
                'title'              => $_sTitle,
                'text_description'   => $this->sanitizeDescription( $_sContent, 250 ),
                'description'        => $_sDescription,
                'meta'               => '',
                'content'            => $_sContent,
                'image_size'         => $this->oUnitOption->get( 'image_size' ),
                'thumbnail_url'      => $this->_formatProductImageURL( 
                    isset( $_aItem[ 'MediumImage' ] ) 
                        ? $_aItem[ 'MediumImage' ][ 'URL' ] 
                        : null, 
                    $this->oUnitOption->get( 'image_size' )
                ),
                'author'             => isset( $_aItem[ 'ItemAttributes' ][ 'Author' ] ) 
                    ? implode( ', ', ( array ) $_aItem[ 'ItemAttributes' ][ 'Author' ] ) 
                    : '',
                // 'manufacturer' => $_aItem[ 'ItemAttributes' ][ 'Manufacturer' ], 
                'category'           => $this->getElement(
                    $_aItem,
                    array( 'ItemAttributes', 'ProductGroup' ),
                    ''
                ),
                'date'               => $this->getElement(
                    $_aItem,
                    array( 'ItemAttributes', 'PublicationDate' ),
                    ''
                ),
                'is_adult_product'   => $this->getElement(
                    $_aItem,
                    array( 'ItemAttributes', 'IsAdultProduct' ),
                    false
                ),

                'review'              => '',  // customer reviews
                'rating'              => '',  // 3+
                'button'              => '',  // 3+
                'image_set'           => '',  // 3+
// @todo add a formt method for editorial reviews.
                'editorial_review'    => '',  // 3+
            ) 
            + $this->_getPrices( $_aItem )
            + $_aItem;
            
            // Add meta data to the description
            $_aProduct[ 'meta' ]        = $this->_formatProductMeta( $_aProduct );
            $_aProduct[ 'description' ] = $this->_formatProductDescription( $_aProduct );

            // Thumbnail
            $_aProduct[ 'formatted_thumbnail' ] = $this->_formatProductThumbnail( $_aProduct );
            $_aProduct[ 'formed_thumbnail' ]    = $_aProduct[ 'formatted_thumbnail' ]; // backward compatibility
        
            // Title
            $_aProduct[ 'formatted_title' ] = $this->_formatProductTitle( $_aProduct );
            $_aProduct[ 'formed_title' ]    = $_aProduct[ 'formatted_title' ]; // backward compatibility
           
            
            // Button - check if the %button% variable exists in the item format definition.
            // It accesses the database, so if not found, the method should not be called.
            if ( 
                $this->_hasCustomVariable( 
                    $this->oUnitOption->get( 'item_format' ),
                    array( '%button%', )
                ) 
            ) {                

                $_aProduct[ 'button' ] = $this->_getButton( 
                    $this->oUnitOption->get( 'button_type' ), 
                    $this->_getButtonID(), 
                    $_aProduct[ 'product_url' ], 
                    $_aProduct[ 'ASIN' ], 
                    $_sLocale, 
                    $_sAssociateID, 
                    $this->_getButtonID(), 
                    $this->oOption->get( 'authentication_keys', 'access_key' ) // public access key
                );                
            
            }            
            
            $_aASINLocales[] = $_aProduct[ 'ASIN' ] . '_' . strtoupper( $_sLocale );
            $_aProducts[]    = $_aProduct;
            
            // Max Number of Items 
            if ( count( $_aProducts ) >= $this->oUnitOption->get( 'count' ) ) {
                break;            
            }
            
        }
        
        return $this->_formatProducts( 
            $_aProducts,
            $_aASINLocales,
            $_sLocale,
            $_sAssociateID
        );
        
    }
  
        /**
         * Extracts items array from the API response array.
         * @since       3
         * @return      array
         */
        private function _getItems( $aResponse ) {

            $_aItems = $this->getElement(
                $aResponse, // subject array
                array( 'Items', 'Item' ), // dimensional keys
                $aResponse  // default
            );
            
            // When only one item is found, the item elements are not contained in an array. So contain it.
            if ( isset( $_aItems[ 'ASIN' ] ) ) {
                $_aItems = array( $_aItems ); 
            }
            return $_aItems;
            
        }
        
        /**
         * Returns prices of the product as an array.
         * @since       2.1.2
         * @return      array
         */
        private function _getPrices( array $aItem ) {
            
            $_sProperPirce      = $this->getElement(
                $aItem,
                array( 'ItemAttributes', 'ListPrice', 'FormattedPrice' ),
                ''
            );
            $_sDiscountedPrice  = $this->getElement(
                $aItem,
                array( 'Offers', 'Offer', 'OfferListing', 'Price', 'FormattedPrice' ),
                ''
            );
            $_sDiscountedPrice  = $_sProperPirce && $_sDiscountedPrice === $_sProperPirce
                ? ''
                : $_sDiscountedPrice;
            $_sProperPirce      = $_sDiscountedPrice
                ? "<s>" . $_sProperPirce . "</s>"
                : $_sProperPirce;
                
            $_aPrices = array(
                'price'              => $_sProperPirce
                    ? "<span class='amazon-product-price-value'>"  
                           . $_sProperPirce
                        . "</span>"
                    : "",
                'discounted_price'   => $_sDiscountedPrice
                    ? "<span class='amazon-product-discounted-price-value'>" 
                            . $aItem[ 'Offers' ][ 'Offer' ][ 'OfferListing' ][ 'Price' ][ 'FormattedPrice' ]
                        . "</span>"
                    : '',
                'lowest_new_price'   => isset( $aItem[ 'OfferSummary' ][ 'LowestNewPrice' ][ 'FormattedPrice' ] )
                    ? "<span class='amazon-product-lowest-new-price-value'>"
                            . $aItem[ 'OfferSummary' ][ 'LowestNewPrice' ][ 'FormattedPrice' ]
                        . "</span>"
                    : '',
                'lowest_used_price'  => isset( $aItem[ 'OfferSummary' ][ 'LowestUsedPrice' ][ 'FormattedPrice' ] )
                    ? "<span class='amazon-product-lowest-used-price-value'>"
                            . $aItem[ 'OfferSummary' ][ 'LowestUsedPrice' ][ 'FormattedPrice' ]
                        . "</span>"
                    : '',
            );
            
            return $_aPrices;
        }

        /**
         * Returns the formatted product meta HTML block.
         * 
         * @since       2.1.1
         */
        private function _formatProductMeta( array $aProduct ) {
            
            $_aOutput = array();
            if ( $aProduct[ 'author' ] ) {
                $_aOutput[] = "<span class='amazon-product-author'>" 
                        . sprintf( __( 'by %1$s', 'amazon-auto-links' ) , $aProduct[ 'author' ] ) 
                    . "</span>";
            }
            if ( $aProduct[ 'price' ] ) {
                $_aOutput[] = "<span class='amazon-product-price'>" 
                        . sprintf( __( 'for %1$s', 'amazon-auto-links' ), $aProduct[ 'price' ] )
                    . "</span>";
            }
            if ( $aProduct[ 'discounted_price' ] ) {
                $_aOutput[] = "<span class='amazon-product-discounted-price'>" 
                        . $aProduct[ 'discounted_price' ]
                    . "</span>";
            }
            if ( $aProduct[ 'lowest_new_price' ] ) {
                $_aOutput[] = "<span class='amazon-product-lowest-new-price'>" 
                        . sprintf( __( 'New from %1$s', 'amazon-auto-links' ), $aProduct[ 'lowest_new_price' ] )
                    . "</span>";
            }
            if ( $aProduct[ 'lowest_used_price' ] ) {
                $_aOutput[] = "<span class='amazon-product-lowest-used-price'>" 
                        . sprintf( __( 'Used from %1$s', 'amazon-auto-links' ), $aProduct[ 'lowest_used_price' ] ) 
                    . "</span>";
            }
            return empty( $_aOutput )
                ? ''
                : "<div class='amazon-product-meta'>"
                    . implode( ' ', $_aOutput )
                    . "</div>";          
                    
        }
        /**
         * Returns the formatted product description HTML block.
         * 
         * @since       2.1.1
         */        
        private function _formatProductDescription( array $aProduct ) {
               
            return $aProduct[ 'meta' ] 
                . "<div class='amazon-product-description'>" 
                    . $aProduct[ 'description' ] 
                . "</div>";

        }
              
        /**
         * Joins the given value if it is an array with the provided key.
         * 
         */
        protected function joinIfArray( $aParentArray, $sKey ) {
            
            if ( isset( $aParentArray[ $sKey ] ) ) { 
                return ( string ) $aParentArray[ $sKey ]; 
            }
            
            $_aElems = array();
            foreach( $aParentArray as $_vElem ) {
                if ( ! isset( $_vElem[ $sKey ] ) ) {
                    continue;
                }
                $_aElems[] = $_vElem[ $sKey ];
            }
                    
            return implode( '', $_aElems );        
            
        }
        
        /**
         * 
         * @since       unknown
         * @since       2.1.1       Changed the name from `formatImage()`. Changed the scope from protected to private.
         */
        private function _formatProductImageURL( $sImageURL, $isImageSize ) {
            
            // If no product image is found
            if ( ! $sImageURL ) {
                $_sLocale  = strtoupper( $this->oUnitOption->get( 'country' ) );
                $sImageURL = isset( AmazonAutoLinks_Property::$aNoImageAvailable[ $_sLocale ] )
                    ? AmazonAutoLinks_Property::$aNoImageAvailable[ $_sLocale ]
                    : AmazonAutoLinks_Property::$aNoImageAvailable[ 'US' ];
            }
            
            if ( $this->bIsSSL ) {
                $sImageURL = $this->getAmazonSSLImageURL( $sImageURL );
            }
            
            return $this->setImageSize( $sImageURL, $isImageSize );
            
        }
    
}