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

        // At this point, it is a cloaked URL.
        $this->___goToStore( $_sQueryValue, $_GET, $sQueryKey );
        
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
         * @since   3
         * @since   3.8.10  Made it respect additional URL query parameters.
         */
        private function ___goToStore( $sASIN, $aArgs, $sQueryKey ) {
            
            $aArgs = $aArgs + array(
                'locale' => null,
                'tag'    => null,
                'ref'    => null,
            );
            
            // http://www.amazon.[domain-suffix]/dp/ASIN/[asin]/ref=[...]?tag=[associate-id]
            $_sURL          = AmazonAutoLinks_PAAPI50___Locales::getMarketPlaceByLocale( $aArgs[ 'locale' ] );
            $_aURLElements  = parse_url( $_sURL );
            $_sStoreURL     = $_aURLElements[ 'scheme' ] . '://' . $_aURLElements[ 'host' ]
                . '/dp/ASIN/' . $sASIN . '/' 
                . ( empty( $aArgs[ 'ref' ] ) ? '' : 'ref=nosim' );

            unset( $aArgs[ 'ref' ], $aArgs[ 'ref' ], $aArgs[ 'locale' ], $aArgs[ $sQueryKey ] );
            $_sStoreURL     = add_query_arg( $aArgs, $_sStoreURL );
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