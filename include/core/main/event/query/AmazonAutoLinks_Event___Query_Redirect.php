<?php
/**
 * Auto Amazon Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2022 Michael Uno
 */

/**
 * Redirects the visitor to an appropriate page depending on the request.
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

        $_aGet = AmazonAutoLinks_Utility::getHTTPQueryGET();

        if ( 'feed' === $_aGet[ $sQueryKey ] ) {
            return;
        }

        if ( 'vendor' === $_aGet[ $sQueryKey ] ) {
            $this->___goToVendor();
        }

        // At this point, it is a cloaked URL.
        $this->___goToStore( $_aGet[ $sQueryKey ], $_aGet, $sQueryKey );
        
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
         *
         * @param string $sASIN
         * @param array  $aGET
         * @param string $sQueryKey
         * @since 3.8.10  Made it respect additional URL query parameters.
         * @since 3
         */
        private function ___goToStore( $sASIN, $aGET, $sQueryKey ) {
            
            $_aGET = $aGET + array(
                'locale' => null,
                'tag'    => null,
                'ref'    => null,
            );
            
            // http://www.amazon.[domain-suffix]/dp/ASIN/[asin]/ref=[...]?tag=[associate-id]
            $_oLocale       = new AmazonAutoLinks_Locale( $_aGET[ 'locale' ] );
            $_sURL          = $_oLocale->getMarketPlaceURL();
            $_aURLElements  = parse_url( $_sURL );
            $_sStoreURL     = $_aURLElements[ 'scheme' ] . '://' . $_aURLElements[ 'host' ]
                . '/dp/ASIN/' . $sASIN . '/' 
                . ( empty( $_aGET[ 'ref' ] ) ? '' : 'ref=nosim' );

            unset( $_aGET[ 'ref' ], $_aGET[ 'ref' ], $_aGET[ 'locale' ], $_aGET[ $sQueryKey ] );
            $_sStoreURL     = add_query_arg( $_aGET, $_sStoreURL );
            exit( wp_redirect( apply_filters( 'aal_filter_store_redirect_url', $_sStoreURL, $sASIN, $aGET ) ) );
                    
        }
        
}