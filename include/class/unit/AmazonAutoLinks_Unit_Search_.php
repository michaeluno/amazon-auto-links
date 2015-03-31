<?php
/**
 * Creates Amazon product links by ItemSearch.
 * 
 * @package         Amazon Auto Links
 * @copyright       Copyright (c) 2013, Michael Uno
 * @license         http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

abstract class AmazonAutoLinks_Unit_Search_ extends AmazonAutoLinks_Unit {

    public static $arrStructure_Args = array(
        'count'                 => 10,
        'column'                => 4,
        'country'               => 'US',
        'associate_id'          => null,
        'image_size'            => 160,
        'Keywords'              => '',      // the keyword to search
        'Operation'             => 'ItemSearch',    // ItemSearch, ItemLookup, SimilarityLookup
        'Title'                 => '',      // for the advanced Title option
        'Sort'                  => 'salesrank',        // pricerank, inversepricerank, sales_rank, relevancerank, reviewrank
        'SearchIndex'           => 'All',        
        'BrowseNode'            => '',    // ( optional )
        'Availability'          => 'Available',    // ( optional ) 
        'Condition'             => 'New',    
        'MaximumPrice'          => null,
        'MinimumPrice'          => null,
        'MinPercentageOff'      => null,
        'MerchantId'            => null,    // 2.0.7+
        'MarketplaceDomain'     => null,    // 2.1.0+
        'ItemPage'              => null,
        'additional_attribute'  => null,
        'search_by'             => 'Author',
        // 'nodes' => 0,    // 0 is for all nodes.    Comma delimited strings will be passed. e.g. 12345,12425,5353
        'ref_nosim'             => false,
        'title_length'          => -1,
        'description_length'    => 250,
        'link_style'            => 1,
        'credit_link'           => 1,
        'title'                 => '',      // won't be used to fetch links. Used to create a unit.
        'template'              => '',      // the template name - if multiple templates with a same name are registered, the first found item will be used.
        'template_id'           => null,    // the template ID: md5( dir path )
        'template_path'         => '',      // the template can be specified by the template path. If this is set, the 'template' key won't take effect.
        'cache_duration'        => '',
        
        'image_format'          => '',
        'title_format'          => '',
        'item_format'           => '',
        
        /* used outside the class */
        'is_preview'            => false,   // for the search unit, true won't be used but just for the code consistency. 
        'operator'              => 'AND',   // this is for fetching by label. AND, IN, NOT IN can be used
        
        'id'                    => null,    // the unit ID
        '_labels'               => array(), // stores labels (plugin custom taxonomy)
    );
    

    /**
     * Represents the array structure of the API request arguments.
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
     * Sets up properties.
     */
    function __construct( $aArgs=array() ) {
            
        parent::__construct();
        $this->setArguments( $aArgs );
        $this->strUnitType = 'search';
        
    }    
    
    public function setArguments( $aArgs ) {
        $this->arrArgs = $aArgs + self::$arrStructure_Args + self::getItemFormatArray();
    }
    
    /**
     * 
     * @return    array    The response array.
     */
    public function fetch( $aURLs=array() ) {
        
        // The search unit type does not use directly passed urls.
        // Maybe later at some point, custom request URIs can get implemented and they can be directly passed to this method.
        unset( $aURLs );
        
        $_aResponse = $this->getRequest( $this->arrArgs['count'] );        
        // Check errors
        if ( isset( $_aResponse['Error']['Code']['Message'] ) ) {
            return $_aResponse;
        }            
        // Error in the Request element.
        if ( isset( $_aResponse['Items']['Request']['Errors'] ) ) {
            return $_aResponse['Items']['Request']['Errors'];
        }
            
        $_aProducts = $this->composeArray( $_aResponse );

        // echo "<pre>" . htmlspecialchars( print_r( $_aResponse['Items']['Item'], true ) ) . "</pre>"
        return $_aProducts;
        
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
            $this->arrArgs['country'], 
            $this->oOption->getAccessPublicKey(),
            $this->oOption->getAccessPrivateKey(),
            $this->arrArgs['associate_id']
        );

        // First, perform the search for the first page regardless the specified count (number of items).
        // Keys with an empty value will be filtered out when performing the request.            
        $_aResponse = $_oAPI->request( $this->getAPIParameterArray( $this->arrArgs['Operation'] ), '', $this->arrArgs['cache_duration'] );    
        if ( $iCount <= 10 ) {
            return $_aResponse;
        }
        
        // Check necessary key is set
        if ( ! isset( $_aResponse['Items']['Item'] ) || ! is_array( $_aResponse['Items']['Item'] ) ) {
            return $_aResponse;
        }
        
        // Calculate the required number of pages.
        $_iPage = $this->_getTotalPageNumber( $iCount, $_aResponse, $this->arrArgs['SearchIndex'] );
        
        $_aResponseTrunk = $_aResponse;
                
        // First perform fetching data in the background if caches are not available. Parse backwards 
        $_fScheduled = null;
        for ( $_i = $_iPage; $_i >= 2 ; $_i-- ) {
            $_fResult = $_oAPI->scheduleInBackground( $this->getAPIParameterArray( $this->arrArgs['Operation'], $_i ) );
            $_fScheduled = $_fScheduled ? $_fScheduled : $_fResult;
        }
        if ( $_fScheduled ) {
            // there are items scheduled to fetch in the background, do it right now.
            AmazonAutoLinks_Shadow::gaze();
        }
        
        // Start from the second page since the first page has been already done. 
        for ( $_i = 2; $_i <= $_iPage; $_i++ ) {
            
            $_aResponse = $_oAPI->request(     $this->getAPIParameterArray( $this->arrArgs['Operation'], $_i ), '', $this->arrArgs['cache_duration'] );
            if ( isset( $_aResponse['Items']['Item'] ) && is_array( $_aResponse['Items']['Item'] ) ) {
                $_aResponseTrunk['Items']['Item'] = $this->_addItems( $_aResponseTrunk['Items']['Item'], $_aResponse['Items']['Item'] );    
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
            $iFoundTotalPages = isset( $aResponse['Items']['TotalPages'] ) ? $aResponse['Items']['TotalPages'] : 1;
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
                if ( ! isset( $_aItem['ASIN'] ) ) { continue; }
                $_aASINs[ $_aItem['ASIN'] ] = $_aItem['ASIN'];
            }
            
            // Add the items if not already there.
            foreach ( $aItems as $_aItem ) {
                if ( ! isset( $_aItem['ASIN'] ) ) { continue; }
                if ( in_array( $_aItem['ASIN'], $_aASINs ) ) { continue; }
                $aMain[] = $_aItem;    // finally add the item
            }
            
            return $aMain;
            
        }
    /**
     * 
     * 'Operation' => 'ItemSearch',    // ItemSearch, ItemLookup, SimilarityLookup
     * @since   2.0.2
     * @see     http://docs.aws.amazon.com/AWSECommerceService/latest/DG/ItemSearch.html
     */
    protected function getAPIParameterArray( $sOperation='ItemSearch', $iItemPage=null ) {

        $_bIsIndexAllOrBlended  = ( 'All' === $this->arrArgs['SearchIndex'] || 'Blended' === $this->arrArgs['SearchIndex'] );
        $_aParams               = array(
            'Keywords'              => AmazonAutoLinks_Utilities::trimDelimitedElements( $this->arrArgs['Keywords'], ',', false ),
            'Title'                 => $_bIsIndexAllOrBlended 
                ? null 
                : AmazonAutoLinks_Utilities::trimDelimitedElements( $this->arrArgs['Title'], ',', false ),
            'Operation'             => $this->arrArgs['Operation'],
            'SearchIndex'           => $this->arrArgs['SearchIndex'],
            $this->arrArgs['search_by'] => $this->arrArgs['additional_attribute'] 
                ? $this->arrArgs['additional_attribute'] 
                : null,
            'Sort'                  => $_bIsIndexAllOrBlended ? null : $this->arrArgs['Sort'],    // when the search index is All, sort cannot be specified
            'ResponseGroup'         => "Large",
            'BrowseNode'            => ! $_bIsIndexAllOrBlended && isset( $this->arrArgs['BrowseNode'] ) && $this->arrArgs['BrowseNode'] ? $this->arrArgs['BrowseNode'] : null,
            'Availability'          => isset( $this->arrArgs['Availability'] ) && $this->arrArgs['Availability'] ? 'Available' : null,
            'Condition'             => $_bIsIndexAllOrBlended ? null : $this->arrArgs['Condition'],
            'IncludeReviewsSummary' => "True",
            'MaximumPrice'          => ! $_bIsIndexAllOrBlended && $this->arrArgs['MaximumPrice'] 
                ? $this->arrArgs['MaximumPrice'] 
                : null,
            'MinimumPrice'          => ! $_bIsIndexAllOrBlended && $this->arrArgs['MinimumPrice'] 
                ? $this->arrArgs['MinimumPrice'] 
                : null,
            'MinPercentageOff'      => $this->arrArgs['MinPercentageOff']
                ? $this->arrArgs['MinPercentageOff'] 
                : null,
            // 2.0.7+
            'MerchantId'            => 'Amazon' === $this->arrArgs['MerchantId']
                ? $this->arrArgs['MerchantId'] 
                : null, 
            // 2.1.0+
            'MarketplaceDomain'     => 'Marketplace' === $this->arrArgs['SearchIndex'] 
                ? AmazonAutoLinks_Properties::getMarketplaceDomainByLocale( $this->arrArgs['country'] )
                : null,                
        );        
        $_aParams = $iItemPage
            ? $_aParams + array( 'ItemPage' => $iItemPage )
            : $_aParams;
        return $_aParams;
    }
    
    protected function composeArray( $aResponse ) {

        $_aItems = isset( $aResponse['Items']['Item'] )
            ? $aResponse['Items']['Item'] 
            : $aResponse;

        // When only one item is found, the item elements are not contained in an array. So contain it.
        if ( isset( $_aItems['ASIN'] ) ) {
            $_aItems = array( $_aItems ); 
        }
        
        $_aProducts = array();
        foreach ( ( array ) $_aItems as $_aItem )    {

            if ( ! is_array( $_aItem ) ) { continue; }
            $_aItem = $_aItem + self::$aStructure_Item;
        
            if ( $this->isBlocked( $_aItem['ASIN'], 'asin' ) ) { continue; }
            if ( $this->arrArgs['is_preview'] || ! $this->fNoDuplicate ) {
                $this->arrBlackListASINs[] = $_aItem['ASIN'];    // this search unit type does not have the preview mode so it won't be triggered
            } else {
                $GLOBALS['arrBlackASINs'][] = $_aItem['ASIN'];    
            }
                
            $_sTitle = $this->sanitizeTitle( $_aItem['ItemAttributes']['Title'] );
            if ( $this->isBlocked( $_sTitle, 'title' ) ) { continue; }
            
            $_sProductURL = $this->formatProductLinkURL( rawurldecode( $_aItem['DetailPageURL'] ), $_aItem['ASIN'] );

            $_sContent = isset( $_aItem['EditorialReviews']['EditorialReview'] ) 
                ? $this->joinIfArray( $_aItem['EditorialReviews']['EditorialReview'], 'Content' )
                : '';
            $_sDescription = $this->sanitizeDescription( $_sContent, $this->arrArgs['description_length'], $_sProductURL );
            if ( $this->isBlocked( $_sDescription, 'description' ) ) { continue; }
                        
            $_aProduct = array(
                'ASIN'               => $_aItem['ASIN'],
                'product_url'        => $_sProductURL,
                'title'              => $_sTitle,
                'text_description'   => $this->sanitizeDescription( $_sContent, 250 ),
                'description'        => $_sDescription,
                'meta'               => '',
                'content'            => $_sContent,
                'image_size'         => $this->arrArgs['image_size'],
                'thumbnail_url'      => $this->_formatProductImageURL( isset( $_aItem['MediumImage'] ) ? $_aItem['MediumImage']['URL'] : null, $this->arrArgs['image_size'] ),
                'author'             => isset( $_aItem['ItemAttributes']['Author'] ) ? implode( ', ', ( array ) $_aItem['ItemAttributes']['Author'] ) : '',
                // 'manufacturer' => $_aItem['ItemAttributes']['Manufacturer'], 
                'category'           => isset( $_aItem['ItemAttributes']['ProductGroup'] ) ? $_aItem['ItemAttributes']['ProductGroup'] : '',
                'date'               => isset( $_aItem['ItemAttributes']['PublicationDate'] ) ? $_aItem['ItemAttributes']['PublicationDate'] : '',    // ReleaseDate
                // 'is_adult_product' => $_aItem['ItemAttributes']['IsAdultProduct'],

                // The below review items are not implemented yet
                'editorial_review'   => '',
                'user_review'        => '', 
            ) 
            + $this->_getPrices( $_aItem )
            + $_aItem;
            
            // Add meta data to the description
            $_aProduct['meta']        = $this->_formatProductMeta( $_aProduct );
            $_aProduct['description'] = $this->_formatProductDescription( $_aProduct );

            // Thumbnail
            $_aProduct['formatted_thumbnail'] = $this->_formatProductThumbnail( $_aProduct );
            $_aProduct['formed_thumbnail'] = $_aProduct['formatted_thumbnail']; // backward compatibility
        
            // Title
            $_aProduct['formatted_title'] = $this->_formatProductTitle( $_aProduct );
            $_aProduct['formed_title'] = $_aProduct['formatted_title']; // backward compatibility

            // Item        
            $_aProduct['formatted_item']  = $this->_formatProductOutput( $_aProduct );
            $_aProduct['formed_item'] = $_aProduct['formatted_item'];   // backward compatibility
            
            $_aProducts[] = $_aProduct;
            
            // Max Number of Items 
            if ( count( $_aProducts ) >= $this->arrArgs['count'] ) {
                break;            
            }
            
        }
            
        return $_aProducts;
        
    }
        /**
         * Returns prices of the product as an array.
         * @since       2.1.2
         */
        private function _getPrices( array $aItem ) {
            
            $_sProperPirce      = isset( $aItem['ItemAttributes']['ListPrice']['FormattedPrice'] )
                ? $aItem['ItemAttributes']['ListPrice']['FormattedPrice']
                : '';     
            $_sDiscountedPrice  = isset( $aItem['Offers']['Offer']['OfferListing']['Price']['FormattedPrice'] )
                ? $aItem['Offers']['Offer']['OfferListing']['Price']['FormattedPrice']
                : '';
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
                            . $aItem['Offers']['Offer']['OfferListing']['Price']['FormattedPrice']
                        . "</span>"
                    : '',
                'lowest_new_price'   => isset( $aItem['OfferSummary']['LowestNewPrice']['FormattedPrice'] )
                    ? "<span class='amazon-product-lowest-new-price-value'>"
                            . $aItem['OfferSummary']['LowestNewPrice']['FormattedPrice']
                        . "</span>"
                    : '',
                'lowest_used_price'  => isset( $aItem['OfferSummary']['LowestUsedPrice']['FormattedPrice'] )
                    ? "<span class='amazon-product-lowest-used-price-value'>"
                            . $aItem['OfferSummary']['LowestUsedPrice']['FormattedPrice']
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
            if ( $aProduct['author'] ) {
                $_aOutput[] = "<span class='amazon-product-author'>" 
                        . sprintf( __( 'by %1$s', 'amazon-auto-links' ) , $aProduct['author'] ) 
                    . "</span>";
            }
            if ( $aProduct['price'] ) {
                $_aOutput[] = "<span class='amazon-product-price'>" 
                        . sprintf( __( 'for %1$s', 'amazon-auto-links' ), $aProduct['price'] )
                    . "</span>";
            }
            if ( $aProduct['discounted_price'] ) {
                $_aOutput[] = "<span class='amazon-product-discounted-price'>" 
                        . $aProduct['discounted_price']
                    . "</span>";
            }
            if ( $aProduct['lowest_new_price'] ) {
                $_aOutput[] = "<span class='amazon-product-lowest-new-price'>" 
                        . sprintf( __( 'New from %1$s', 'amazon-auto-links' ), $aProduct['lowest_new_price'] )
                    . "</span>";
            }
            if ( $aProduct['lowest_used_price'] ) {
                $_aOutput[] = "<span class='amazon-product-lowest-used-price'>" 
                        . sprintf( __( 'Used from %1$s', 'amazon-auto-links' ), $aProduct['lowest_used_price'] ) 
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
               
            return $aProduct['meta'] 
                . "<div class='amazon-product-description'>" 
                    . $aProduct['description'] 
                . "</div>";

        }
        
        /**
         * Returns the formatted product thumbnail HTML block.
         * 
         * @since       2.1.1
         */
        private function _formatProductThumbnail( array $aProduct ) {
            
            return isset( $aProduct['thumbnail_url'] )
                ? str_replace( 
                    array( "%href%", "%title_text%", "%src%", "%max_width%", "%description_text%" ),
                    array( $aProduct['product_url'], $aProduct['title'], $aProduct['thumbnail_url'], $this->arrArgs['image_size'], $aProduct['text_description'] ),
                    $this->arrArgs['image_format'] 
                ) 
                : '';            
            
        }
        /**
         * Returns the formatted product title HTML Block.
         * @since       2.1.1
         */
        private function _formatProductTitle( array $aProduct ) {
            return str_replace( 
                array( "%href%", "%title_text%", "%description_text%" ),
                array( $aProduct['product_url'], $aProduct['title'], $aProduct['text_description'] ),
                $this->arrArgs['title_format'] 
            );        
        }        
        /**
         * Returns the formatted product HTML Block.
         * @since       2.1.1
         */
        private function _formatProductOutput( array $aProduct ) {
         
            return str_replace( 
                array( 
                    "%href%", 
                    "%title_text%",
                    "%description_text%",
                    "%title%",
                    "%image%",
                    "%description%",
                    "%price%",
                    "%editorial_review%",
                    "%user_review%",
                ),
                array( 
                    $aProduct['product_url'],
                    $aProduct['title'],
                    $aProduct['text_description'],
                    $aProduct['formatted_title'],
                    $aProduct['formatted_thumbnail'],
                    $aProduct['description'],
                    $aProduct['price'],
                    $aProduct['editorial_review'],
                    $aProduct['user_review'],
                ),
                $this->arrArgs['item_format'] 
            );
         
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
         * @since       2.1.1       Chagned the name from `formatImage()`. Changed the scope from protected to private.
         */
        private function _formatProductImageURL( $sImageURL, $isImageSize ) {
            
            // If no product image is found
            if ( ! $sImageURL ) {
                $sImageURL = isset( AmazonAutoLinks_Properties::$aNoImageAvailable[ $this->arrArgs['country'] ] )
                    ? AmazonAutoLinks_Properties::$aNoImageAvailable[ $this->arrArgs['country'] ]
                    : AmazonAutoLinks_Properties::$aNoImageAvailable['US'];            
            }
            
            if ( $this->fIsSSL ) {
                $sImageURL = $this->respectSSLImage( $sImageURL );
            }
            
            return $this->setImageSize( $sImageURL, $isImageSize );
            
        }
    
}