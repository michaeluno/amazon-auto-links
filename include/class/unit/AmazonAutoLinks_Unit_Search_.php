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
    function __construct( $arrArgs=array() ) {
            
        parent::__construct();
        $this->setArguments( $arrArgs );
        $this->strUnitType = 'search';
        
    }    
    
    public function setArguments( $arrArgs ) {
        
        $this->arrArgs = $arrArgs + self::$arrStructure_Args + self::getItemFormatArray();
        
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
         * @since            2.0.4.1b
         */
        protected function _getTotalPageNumber( $iCount, $aResponse, $sSearchIndex='All' ) {
            
            $iMaxAllowedPages = $sSearchIndex == 'All' ? 5 : 10;        // see the API documentation: http://docs.aws.amazon.com/AWSECommerceService/latest/DG/ItemSearch.html
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
                if ( ! isset( $_aItem['ASIN'] ) ) continue;
                $_aASINs[ $_aItem['ASIN'] ] = $_aItem['ASIN'];
            }
            
            // Add the items if not already there.
            foreach ( $aItems as $_aItem ) {
                if ( ! isset( $_aItem['ASIN'] ) ) continue;
                if ( in_array( $_aItem['ASIN'], $_aASINs ) ) continue;
                $aMain[] = $_aItem;    // finally add the item
            }
            
            return $aMain;
            
        }
    /**
     * 
     * 'Operation' => 'ItemSearch',    // ItemSearch, ItemLookup, SimilarityLookup
     * @since            2.0.2
     * @see                http://docs.aws.amazon.com/AWSECommerceService/latest/DG/ItemSearch.html
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
// var_dump( $this->arrArgs );
// var_dump( $_aParams );
        $_aParams = $iItemPage
            ? $_aParams + array( 'ItemPage' => $iItemPage )
            : $_aParams;
        return $_aParams;
    }
    
    protected function composeArray( $arrResponse ) {

        $arrItems = isset( $arrResponse['Items']['Item'] ) ? $arrResponse['Items']['Item'] : $arrResponse;

        // When only one item is found, the item elements are not contained in an array. So contain it.
        if ( isset( $arrItems['ASIN'] ) ) $arrItems = array( $arrItems );
        
        $arrProducts = array();
        foreach ( ( array ) $arrItems as $arrItem )    {

            if ( ! is_array( $arrItem ) ) continue;
            $arrItem = $arrItem + self::$aStructure_Item;
        
            if ( $this->isBlocked( $arrItem['ASIN'], 'asin' ) ) continue;
            if ( $this->arrArgs['is_preview'] || ! $this->fNoDuplicate )
                $this->arrBlackListASINs[] = $arrItem['ASIN'];    // this search unit type does not have the preview mode so it won't be triggered
            else 
                $GLOBALS['arrBlackASINs'][] = $arrItem['ASIN'];    
                
            $strTitle = $this->sanitizeTitle( $arrItem['ItemAttributes']['Title'] );
            if ( $this->isBlocked( $strTitle, 'title' ) ) continue;
            
            $strProductURL = $this->formatProductLinkURL( rawurldecode( $arrItem['DetailPageURL'] ), $arrItem['ASIN'] );

            $strContent = isset( $arrItem['EditorialReviews']['EditorialReview'] ) 
                ? $this->joinIfArray( $arrItem['EditorialReviews']['EditorialReview'], 'Content' )
                : '';
            $strDescription = $this->sanitizeDescription( $strContent, $this->arrArgs['description_length'], $strProductURL );
            if ( $this->isBlocked( $strDescription, 'description' ) ) continue;

        // unset( $arrItem['ItemLinks'], $arrItem['ImageSets'], $arrItem['BrowseNodes'], $arrItem['SimilarProducts'] );
            $arrProduct = array(
                'ASIN'                =>    $arrItem['ASIN'],
                'product_url'        =>    $strProductURL,
                'title'                =>    $strTitle,
                'text_description'    =>    $this->sanitizeDescription( $strContent, 250 ),
                'description'        =>    $strDescription,
                'meta'                =>    '',
                'content'             =>    $strContent,
                'image_size'        =>    $this->arrArgs['image_size'],
                'thumbnail_url'        =>    $this->formatImage( isset( $arrItem['MediumImage'] ) ? $arrItem['MediumImage']['URL'] : null, $this->arrArgs['image_size'] ),
                'author'            =>    isset( $arrItem['ItemAttributes']['Author'] ) ? implode( ', ', ( array ) $arrItem['ItemAttributes']['Author'] ) : '',
                // 'manufacturer' => $arrItem['ItemAttributes']['Manufacturer'], 
                'category'            =>    isset( $arrItem['ItemAttributes']['ProductGroup'] ) ? $arrItem['ItemAttributes']['ProductGroup'] : '',
                'date'                =>    isset( $arrItem['ItemAttributes']['PublicationDate'] ) ? $arrItem['ItemAttributes']['PublicationDate'] : '',    // ReleaseDate
                // 'is_adult_product' => $arrItem['ItemAttributes']['IsAdultProduct'],
                'price'                =>    isset( $arrItem['ItemAttributes']['ListPrice']['FormattedPrice'] ) ? "<span class='amazon-product-price-value'>"  . $arrItem['ItemAttributes']['ListPrice']['FormattedPrice'] . "</span>" : '',
                'lowest_new_price'    =>    isset( $arrItem['OfferSummary']['LowestNewPrice']['FormattedPrice'] ) ? "<span class='amazon-product-lowest-new-price-value'>" . $arrItem['OfferSummary']['LowestNewPrice']['FormattedPrice'] . "</span>" : '',
                'lowest_used_price'    =>    isset( $arrItem['OfferSummary']['LowestUsedPrice']['FormattedPrice'] ) ? "<span class='amazon-product-lowest-used-price-value'>" . $arrItem['OfferSummary']['LowestUsedPrice']['FormattedPrice'] . "</span>" : '',
            ) + $arrItem;
            
            // Add meta data to the description
            $arrProduct['meta'] .= $arrProduct['author'] ? "<span class='amazon-product-author'>" . sprintf( __( 'by %1$s', 'amazon-auto-links' ) . "</span>", $arrProduct['author'] ) . ' ' : '';
            $arrProduct['meta'] .= $arrProduct['price']    ? "<span class='amazon-product-price'>" . sprintf( __( 'at %1$s', 'amazon-auto-links' ), $arrProduct['price'] ) . "</span> " : '';
            $arrProduct['meta'] .= $arrProduct['lowest_new_price'] ? "<span class='amazon-product-lowest-new-price'>" . sprintf( __( 'New from %1$s', 'amazon-auto-links' ) . "</span> ", $arrProduct['lowest_new_price'] ) . ' ' : '';
            $arrProduct['meta'] .= $arrProduct['lowest_used_price'] ? "<span class='amazon-product-lowest-used-price'>" . sprintf( __( 'Used from %1$s', 'amazon-auto-links' ) . "</span> ", $arrProduct['lowest_used_price'] ) . ' ' : '';
            $arrProduct['meta'] = empty( $arrProduct['meta'] ) ? '' : "<div class='amazon-product-meta'>{$arrProduct['meta']}</div>";
            $arrProduct['description'] = $arrProduct['meta'] . "<div class='amazon-product-description'>" . $arrProduct['description'] . "</div>";

            /* Format the item */
            // Thumbnail
            $arrProduct['formed_thumbnail'] = isset( $arrProduct['thumbnail_url'] )
                ? str_replace( 
                    array( "%href%", "%title_text%", "%src%", "%max_width%", "%description_text%" ),
                    array( $arrProduct['product_url'], $arrProduct['title'], $arrProduct['thumbnail_url'], $this->arrArgs['image_size'], $arrProduct['text_description'] ),
                    $this->arrArgs['image_format'] 
                ) 
                : '';
                
            // Title
            $arrProduct['formed_title'] = str_replace( 
                array( "%href%", "%title_text%", "%description_text%" ),
                array( $arrProduct['product_url'], $arrProduct['title'], $arrProduct['text_description'] ),
                $this->arrArgs['title_format'] 
            );
            // Item        
            $arrProduct['formed_item'] = str_replace( 
                array( 
                    "%href%", 
                    "%title_text%",
                    "%description_text%",
                    "%title%",
                    "%image%",
                    "%description%",
                    "%price%"
                ),
                array( 
                    $arrProduct['product_url'],
                    $arrProduct['title'],
                    $arrProduct['text_description'],
                    $arrProduct['formed_title'],
                    $arrProduct['formed_thumbnail'],
                    $arrProduct['description'],
                    $arrProduct['price']
                ),
                $this->arrArgs['item_format'] 
            );
            
            $arrProducts[] = $arrProduct;
            
            // Max Number of Items 
            if ( count( $arrProducts ) >= $this->arrArgs['count'] ) break;            
            
        }
            
        return $arrProducts;
        
    }
        /**
         * Joins the given value if it is an array with the provided key.
         * 
         */
        protected function joinIfArray( $arrParentArray, $strKey ) {
            
            if ( isset( $arrParentArray[ $strKey ] ) ) return ( string ) $arrParentArray[ $strKey ];
            
            $arrElems = array();
            foreach( $arrParentArray as $vElem ) 
                if ( isset( $vElem[ $strKey ] ) )
                    $arrElems[] = $vElem[ $strKey ];
                    
            return implode( '', $arrElems );        
            
        }
        
    
    protected function formatImage( $sImageURL, $numImageSize ) {
        
        // If no product image is found
        if ( ! $sImageURL ) {
            $sImageURL = isset( AmazonAutoLinks_Properties::$aNoImageAvailable[ $this->arrArgs['country'] ] )
                ? AmazonAutoLinks_Properties::$aNoImageAvailable[ $this->arrArgs['country'] ]
                : AmazonAutoLinks_Properties::$aNoImageAvailable['US'];            
        }
        
        if ( $this->fIsSSL ) {
            $sImageURL = $this->respectSSLImage( $sImageURL );
        }
        
        return $this->setImageSize( $sImageURL, $numImageSize );
        
    }
    
}