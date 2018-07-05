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
 * Redirects the visitor to an appropriate page depending on the request.
 * @package     Amazon Auto Links
 * @since       3
 * @since       3.5.0   Renamed from `AmazonAutoLinks_Event_Redirect`
 */
class AmazonAutoLinks_Event___Query_Redirect {
    
    /**
     * Routes url requests.
     * @since       3
     * @since       3.0.1       Added the `$sQueryKey` parameter.
     */
    public function __construct( $sQueryKey ) {

        $_sQueryValue = $_GET[ $sQueryKey ];

        if ( 'feed' === $_sQueryValue ) {
            return;
        }

        if ( 'vendor' === $_sQueryValue ) {
            $this->___goToVendor();
        }
        
        $this->___goToStore( $_GET[ $sQueryKey ], $_GET );    
        
    }
        
        /**
         * Redirects the user to the vendor site.
         */
        private function ___goToVendor() {
    
            $_sURL          = AmazonAutoLinks_Registry::PLUGIN_URI;
            $_oOption       = AmazonAutoLinks_Option::getInstance();
            $_isAffiliateID = $_oOption->get( 'miunosoft_affiliate', 'affiliate_id' );
            if ( strlen( $_isAffiliateID ) ) {
                $_sURL = add_query_arg(
                    array(
                        'ref'   => $_isAffiliateID,
                    ),
                    AmazonAutoLinks_Registry::STORE_URI_PRO
                );
            }
    
            exit( wp_redirect( $_sURL ) );
        }
        
        /**
         * 
         * For URL cloaking redirects.
         */
        private function ___goToStore( $sASIN, $aArgs ) {
            
            $aArgs = $aArgs + array(
                'locale' => null,
                'tag'    => null,
                'ref'    => null,
            );
            
            // http://www.amazon.[domain-suffix]/dp/ASIN/[asin]/ref=[...]?tag=[associate-id]
            $_sURL      = isset( AmazonAutoLinks_Property::$aCategoryRootURLs[ strtoupper( $aArgs[ 'locale' ] ) ] )
                ? AmazonAutoLinks_Property::$aCategoryRootURLs[ strtoupper( $aArgs[ 'locale' ] ) ]
                : AmazonAutoLinks_Property::$aCategoryRootURLs[ 'US' ];
            
            $_aURLElements  = parse_url( $_sURL );
            $_sStoreURL     = $_aURLElements[ 'scheme' ] . '://' . $_aURLElements[ 'host' ]
                . '/dp/ASIN/' . $sASIN . '/' 
                . ( empty( $aArgs[ 'ref' ] ) ? '' : 'ref=nosim' )
                . "?tag={$aArgs[ 'tag' ]}";
            
            exit( 
                wp_redirect( 
                    apply_filters( 
                        'aal_filter_store_redirect_url',
                        $_sStoreURL 
                    ) 
                ) 
            );
                    
        }
        
}