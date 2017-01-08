<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2017 Michael Uno
 */

/**
 * 
 * @package      Amazon Auto Links
 * @since        3.3.0
 * @action       aal_action_api_get_similar_products
 */
class AmazonAutoLinks_Event_Action_SimilarProducts extends AmazonAutoLinks_Event_Action_Base {
        
    /**
     * 
     * @callback        action        aal_action_api_get_similar_products
     */
    public function doAction( /* $aArguments=array( 0 => asins, 1 => ASIN, 2 => locale, 3 => associate id, 4 => cache_duration  ) */ ) {
        
        $_aParams        = func_get_args() + array( null );
        $_aArguments     = $_aParams[ 0 ] + array( null, null, null, null );
        $_aASINs         = $_aArguments[ 0 ];
        $_sASIN          = $_aArguments[ 1 ];
        $_sLocale        = $_aArguments[ 2 ];
        $_sAssociateID   = $_aArguments[ 3 ];
        $_iCacheDuration = $_aArguments[ 4 ];

        $_aASINs         = array_diff( 
            $_aASINs,   // the similar items to fetch 
            array( $_sASIN ) // the subject product
        );             
        $_aProducts      = $this->_getProducts( $_aASINs, $_sASIN, $_sLocale, $_sAssociateID, $_iCacheDuration );

        // This caused infinite recursion.
/*         $_oUnit          = new AmazonAutoLinks_UnitOutput_item_lookup(
            array(
                'Operation'                 => 'ItemLookup',
                'ItemId'                    => implode( ',', $_aASINs ),
                'search_per_keyword'        => true,
                'title_length'              => -1,
                '_search_similar_products'  => false,
                'cache_duration'            => $_iCacheDuration,
            )
        );
        $_aProducts      = $_oUnit->fetch(); */

        $_aRow           = $this->_formatColumns( 
            $_aProducts,
            $_iCacheDuration
        );

        $_oProductTable = new AmazonAutoLinks_DatabaseTable_product;
        $_iSetObjectID  = $_oProductTable->setRowByASINLocale(
            $_sASIN . '_' . strtoupper( $_sLocale ),
            $_aRow
        );  

    }   
    
        /**
         * Performs API request for the similar products.
         * 
         * Do not use the AmazonAutoLinks_UnitOutput_item_lookup class to fetch items because it will recursively fetch similar items of simialr items.
         * 
         * @return      array
         * @since       3.3.0
         * @see         http://docs.aws.amazon.com/AWSECommerceService/latest/DG/ItemLookup.html
         */
        private function _getProducts( $aASINs, $sASIN, $sLocale, $sAssociateID, $iCacheDuration ) {
            
            $_oOption     = AmazonAutoLinks_Option::getInstance();
            if ( ! $_oOption->isAPIConnected() ) {
                return array();
            }            
            
            $_sPublicKey  = $_oOption->get( array( 'authentication_keys', 'access_key' ), '' );
            $_sPrivateKey = $_oOption->get( array( 'authentication_keys', 'access_key_secret' ), '' );
            
            if ( empty( $_sPublicKey ) || empty( $_sPrivateKey ) ) {
                return array();
            }
            
            // THe API does not allow more than 10 items.
            array_splice( $aASINs, 10 );
            $_sASINs = implode( ',', $aASINs );
            
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
                'ItemId'                => $_sASINs,    
                
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
                $iCacheDuration // cache duration - null to not to use cache.
            );

            return $this->getElement(
                $_aRawData, // subject
                array( 'Items', 'Item' ), // dimensional keys
                array() // default
            );
            
        }            
        /**
         * @since       3.3.0
         * @return      array
         */
        private function _formatColumns( $aProducts, $iCacheDuration ) {
            $_aColumns = array(
                'similar_products'        => $this->getAsArray( $aProducts ),
                'modified_time'           => date( 'Y-m-d H:i:s' ),
            );
            // if `0` is passed for the cache duration, it just renews the cache and do not update the expiration time.
            if ( $iCacheDuration ) {
                $_aColumns[ 'expiration_time' ] = date( 'Y-m-d H:i:s', time() + $iCacheDuration );
            }
            return $_aColumns;
        }

}