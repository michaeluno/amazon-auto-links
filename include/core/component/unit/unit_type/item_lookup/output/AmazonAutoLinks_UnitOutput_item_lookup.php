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
 * Creates Amazon product links by Item Look-up.
 * 
 * @package         Amazon Auto Links
 */
class AmazonAutoLinks_UnitOutput_item_lookup extends AmazonAutoLinks_UnitOutput_search {
    
    /**
     * Stores the unit type.
     * @remark      Note that the base constructor will create a unit option object based on this value.
     */    
    public $sUnitType = 'item_lookup';
    
    /**
     * Stores the unit option key that is used for the search.
     * This is needed for the `search_per_keyword` option.
     * @since       3.2.0
     */
    public $sSearchTermKey = 'ItemId';
    
    /**
     * Represents the array structure of the API request arguments.
     * @since            2.0.2
     */
    public static $aStructure_APIParameters = array(    
        'Operation'             => 'ItemLookup',
        'Condition'             => 'New',
        'IdType'                => null,
        'IncludeReviewsSummary' => null,
        'ItemId'                => null,
        'MerchantId'            => null,
        'RelatedItemPage'       => null,
        'RelationshipType'      => null,
        'SearchIndex'           => null,
        'TruncateReviewsAt'     => null,
        'VariationPage'         => null,
        'ResponseGroup'         => null,
    );

    /**
     * Sorts items.
     * @remark      Overriding the method in the `AmazonutoLinks_Unit_search` class.
     * @since       3.2.1
     * @since       3.5.0       Moved from `AmazonAutoLinks_UnitOutput_url`.
     * @return      array
     */
    protected function getProducts( $aResponse ) {

        $_sSortType = $this->oUnitOption->get(
            array( '_sort' ),  // dimensional path
            'raw'   // default
        );

        /*
         * 'title'             => __( 'Title', 'amazon-auto-links' ),
         * 'title_descending'  => __( 'Title Descending', 'amazon-auto-links' ),
         * 'random'            => __( 'Random', 'amazon-auto-links' ),
         * 'raw'               => __( 'Raw', 'amazon-auto-links' ),
         */
        $_sMethodName = "_getItemsSorted_{$_sSortType}";
        return $this->{$_sMethodName}(
            parent::getProducts( $aResponse )
        );

    }
        /**
         * @since       3.2.1
         * @since       3.5.0       Moved from `AmazonAutoLinks_UnitOutput_url`.
         */
        private function _getItemsSorted_( $aProducts ) {
            return $this->_getItemsSorted_raw( $aProducts );
        }
        private function _getItemsSorted_title( $aProducts ) {
            uasort( $aProducts, array( $this, 'replyToSortProductsByTitle' ) );
            return $aProducts;
        }
        private function _getItemsSorted_title_descending( $aProducts ) {
            uasort( $aProducts, array( $this, 'replyToSortProductsByTitleDescending' ) );
            return $aProducts;
        }
        private function _getItemsSorted_random( $aProducts ) {
            shuffle( $aProducts );
            return $aProducts;
        }
        private function _getItemsSorted_raw( $aProducts ) {
            return $aProducts;
        }
            public function replyToSortProductsByTitle( $aProductA, $aProductB ) {
                $_sTitleA = $this->getElement( $aProductA, 'title' );
                $_sTitleB = $this->getElement( $aProductB, 'title' );
                return strnatcasecmp(
                    $_sTitleA,
                    $_sTitleB
                );
            }
            public function replyToSortProductsByTitleDescending( $aProductA, $aProductB ) {
                $_sTitleA = $this->getElement( $aProductA, 'title' );
                $_sTitleB = $this->getElement( $aProductB, 'title' );
                return strnatcasecmp(
                    $_sTitleB,
                    $_sTitleA
                );
            }

    /**
     * Performs an Amazon Product API request.
     * 
     * @since            2.0.2
     */
    protected function getRequest( $iCount ) {
        
        $_oAPI = new AmazonAutoLinks_ProductAdvertisingAPI( 
            $this->oUnitOption->get( 'country' ), 
            $this->oOption->get( 'authentication_keys', 'access_key' ),
            $this->oOption->get( 'authentication_keys', 'access_key_secret' ),
            $this->oUnitOption->get( 'associate_id' )
        );

        /**
         * Perform the search for the first page regardless the specified count (number of items).
         * Keys with an empty value will be filtered out when performing the request.
         */
        $_aResponse = $_oAPI->request(
            $this->getAPIParameterArray( 
                $this->oUnitOption->get( 'Operation' ) 
            ),
            $this->oUnitOption->get( 'cache_duration' ),
            $this->oUnitOption->get( '_force_cache_renewal' )
        );
        
        $_aResponse = $this->_getValidResponse( $_aResponse );
        return $_aResponse;
                 
    }
        /**
         * Sometimes Author data is found with an un-accessible url.
         * In that case drop those items.
         * @since       3.2.1
         * @return      array
         */
        private function _getValidResponse( $aResponse ) {
            
            $_aItems = $this->getElement(
                $aResponse,
                array( 'Items', 'Item' )
            );
            if ( ! isset( $_aItems[ 0 ] ) ) {
                $_aItems = array( $_aItems );
            }
            
            foreach( $_aItems as $_iIndex => $_aItem ) {
            
                $_sProductType = $this->getElement(
                    $_aItem,
                    array( 'ItemAttributes', 'ProductTypeName' )
                );            

                // These product links are broken
                // e.g. http://www.amazon.com/Amanda-Jaffe-Series/dp/B00CIZP3M0%3FSubscriptionId%3DAKIAIUOXXAXPYUKNVPVA%26tag%3Dmiunosoft-20%26linkCode%3Dxm2%26camp%3D2025%26creative%3D165953%26creativeASIN%3DB00CIZP3M0
                if ( in_array( $_sProductType, array( 'LITERARY_SERIES', 'CONTRIBUTOR_AUTHORITY_SET' ) ) ) {
                    unset( $_aItems[ $_iIndex ] );
                }
                
            }

            // Reindex - important as some sob-routines check with `isAssociative()`.
            $aResponse[ 'Items' ][ 'Item' ] = array_values( $_aItems );
            return $aResponse;
            
        }
    
    /**
     * 
     * 'Operation' => 'ItemSearch',    // ItemSearch, ItemLookup, SimilarityLookup
     * @see              http://docs.aws.amazon.com/AWSECommerceService/latest/DG/ItemLookup.html
     * @since            2.0.2
     */
    protected function getAPIParameterArray( $sOperation='ItemLookup', $iItemPage=null ) {

        // $this->arrArgs = $this->arrArgs + self::$aStructure_ItemLookup;
        $_aUnitOptions = $this->oUnitOption->get()
            + self::$aStructure_APIParameters;        
        $aParams = array(
            'Operation'             => $sOperation,
            'Condition'             => $_aUnitOptions['Condition'],    // (optional) Used | Collectible | Refurbished, All
            'IdType'                => $_aUnitOptions['IdType'],    // (optional) All IdTypes except ASINx require a SearchIndex to be specified.  SKU | UPC | EAN | ISBN (US only, when search index is Books). UPC is not valid in the CA locale.
            'IncludeReviewsSummary' => "True",        // (optional)
            'ItemId'                => $_aUnitOptions['ItemId'],    // (required)  If ItemIdis an ASIN, a SearchIndex cannot be specified in the request.
            // 'RelatedItemPage' => null,    // (optional) This optional parameter is only valid when the RelatedItems response group is used.
            // 'RelationshipType' => null,    // (conditional)    This parameter is required when the RelatedItems response group is used. 
            'SearchIndex'           => $_aUnitOptions['SearchIndex'],    // (conditional) see: http://docs.aws.amazon.com/AWSECommerceService/latest/DG/APPNDX_SearchIndexValues.html
            // 'TruncateReviewsAt' => 1000, // (optional)
            // 'VariationPage' => null, // (optional)
            'ResponseGroup'         => 'Large', // (optional)
        );

        if ( 'ASIN' === $_aUnitOptions['IdType'] ) {
            unset( $aParams['SearchIndex'] );
        }

        $_aAPIParameters = 'Amazon' === $_aUnitOptions['MerchantId']
            ? $aParams + array( 'MerchantId' => $_aUnitOptions['MerchantId'] )    // (optional) 'Amazon' restrict the returned items only to be soled by Amazon.
            : $aParams;
            
        return $_aAPIParameters;
        
    }
    
}