<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2020 Michael Uno
 */

/**
 * 
 * @package      Amazon Auto Links
 * @since        3.3.0
 * @since        3.5.0      Renamed from `AmazonAutoLinks_Event_Action_SimilarProducts`.
 * @deprecated   3.9.0      Similar products are gone in PA-API5.
 */
class AmazonAutoLinks_Event___Action_APIRequestSimilarProducts extends AmazonAutoLinks_Event___Action_Base {

    protected $_sActionHookName = 'aal_action_api_get_similar_products';

    private $___sAPIRequestType = 'similar_products'; // up to 20 chars

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
     *
     */
    protected function _doAction( /* $aArguments=array( 0 => asins, 1 => ASIN, 2 => locale, 3 => associate id, 4 => cache_duration, 5=> force renew  ) */ ) {
        
        $_aParams               = func_get_args() + array( null );
        $_aArguments            = $_aParams[ 0 ] + array( null, null, null, null );
        $_aSimilarProductASINs  = $_aArguments[ 0 ];
        $_sSubjectASIN          = $_aArguments[ 1 ];
        $_sLocale               = $_aArguments[ 2 ];
        $_sAssociateID          = $_aArguments[ 3 ];
        $_iCacheDuration        = $_aArguments[ 4 ];
        $_bForceRenew           = $_aArguments[ 5 ];

        $_aSimilarProductASINs  = array_diff(
            $_aSimilarProductASINs,     // the similar items to fetch
            array( $_sSubjectASIN )     // the subject product
        );             
        $_aProducts             = $this->___getProducts( $_aSimilarProductASINs, $_sLocale, $_sAssociateID, $_iCacheDuration, $_bForceRenew );
        $_aRow                  = $this->___getRowFormatted( $_aProducts, $_iCacheDuration );
        $_oProductTable         = new AmazonAutoLinks_DatabaseTable_aal_products;
        $_iSetObjectID          = $_oProductTable->setRowByASINLocale(
            $_sSubjectASIN . '_' . strtoupper( $_sLocale ),
            $_aRow
            // @todo add currency when similar products are supported agaon
            // language
        );  

    }   
    
        /**
         * Performs API request for the similar products.
         * 
         * Do not use the AmazonAutoLinks_UnitOutput_item_lookup class to fetch items because it will recursively fetch similar items of similar items.
         * 
         * @return      array
         * @since       3.3.0
         * @since       3.5.0       Renamed from `_getProducts()`. Removed the `$sASIN` parameter.
         * @see         http://docs.aws.amazon.com/AWSECommerceService/latest/DG/ItemLookup.html
         */
        private function ___getProducts( $aASINs, $sLocale, $sAssociateID, $iCacheDuration, $bForceRenew ) {

            if ( empty( $aASINs ) ) {
                return array();
            }

            $_oOption     = AmazonAutoLinks_Option::getInstance();
            if ( ! $_oOption->isAPIConnected() ) {
                return array();
            }            
            
            $_sPublicKey  = $_oOption->get( array( 'authentication_keys', 'access_key' ), '' );
            $_sPrivateKey = $_oOption->get( array( 'authentication_keys', 'access_key_secret' ), '' );
            
            if ( empty( $_sPublicKey ) || empty( $_sPrivateKey ) ) {
                return array();
            }
            
            // The API does not allow more than 10 items.
            array_splice( $aASINs, 10 );
            $_sASINs = implode( ',', $aASINs );
            
            // Construct API arguments
            $_aAPIArguments = array(
            
                'Operation'             => 'ItemLookup',
                
                // (optional) Used | Collectible | Refurbished | Newn| Any
                'Condition'             => 'Any',
                
                // (optional) All IdTypes except ASINx require a SearchIndex to be specified.  SKU | UPC | EAN | ISBN (US only, when search index is Books). UPC is not valid in the CA locale.
                'IdType'                => 'ASIN',    
                
                // (optional)
                'IncludeReviewsSummary' => "True", 
                
                // (required)  If ItemId is an ASIN, a SearchIndex cannot be specified in the request.
                'ItemId'                => $_sASINs,    

                'ResponseGroup'         => 'Images,ItemAttributes', // 3.7.0+ Changed from `Large` for a smaller data set

            );

            $_oAmazonAPI = new AmazonAutoLinks_ProductAdvertisingAPI( 
                $sLocale,   // locale
                $_sPublicKey, 
                $_sPrivateKey,
                $sAssociateID,
                array(),    // HTTP Arguments
                $this->___sAPIRequestType
            );
            $_aRawData = $_oAmazonAPI->request( $_aAPIArguments, $iCacheDuration, $bForceRenew );

            return $this->getElement(
                $_aRawData, // subject
                array( 'Items', 'Item' ), // dimensional keys
                array() // default
            );
            
        }            
        /**
         * @since       3.3.0
         * @since       3.5.0       Renamed from `_formatColumns()`.
         * @return      array
         */
        private function ___getRowFormatted( $aProducts, $iCacheDuration ) {
            $_aRow = array(
                'similar_products'        => $this->getAsArray( $aProducts ),
                'modified_time'           => date( 'Y-m-d H:i:s' ),
            );
            // if `0` is passed for the cache duration, it just renews the cache and do not update the expiration time.
            if ( $iCacheDuration ) {
                $_aRow[ 'expiration_time' ] = date( 'Y-m-d H:i:s', time() + $iCacheDuration );
            }
            return $_aRow;
        }

}