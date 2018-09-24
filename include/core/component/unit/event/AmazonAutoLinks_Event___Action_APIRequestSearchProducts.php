<<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2018 Michael Uno
 */

/**
 * Searches products by the given ASIN and locale.
 *
 * This is a plural version of `AmazonAutoLinks_Event___Action_APIRequestSearchProducts` which queries multiple products at a time.
 *
 * @since       3.7.1
 */
class AmazonAutoLinks_Event___Action_APIRequestSearchProducts extends AmazonAutoLinks_Event___Action_APIRequestSearchProduct {

    protected $_sActionHookName = 'aal_action_api_get_products_info';

    /**
     * Searches passed products and saves their data.
     */
    protected function _doAction( /* $aArguments */ ) {

        $_aParams        = func_get_args() + array( array() );
        $_aList          = $_aParams[ 0 ];
        $_sASINs         = $this->___getASINs( $_aList );
        $_aResponse      = $this->___getAPIResponse( $_sASINs, $_aList );

        if ( $this->___getErrorType( $_aResponse ) ) {
           // @todo for the throttling error, schedule the same request with an interval.

        }
        $this->___setData( $_aResponse, $_aList );

    }
        /**
         * @return  integer     The found error type
         */
        private function ___getErrorType( array $aResponse ) {
            return 0;
        }
        /**
         * Constructs the ASIN parameter.
         * @remark  It is assumed that the passed list contains only up to 10 products
         * as the `ItemLookup` operation API parameter only accepts up to 10 items.
         *
         * @param $aList
         */
        private function ___getASINs( $aList ) {
            // Extract the ASINs
            $_aASINs = array();
            foreach( $aList as $_iIndex => $_aArguments ) {

                // For the argument structure, see AmazonAutoLinks_Event_Scheduler::scheduleProductInformation()
                // Extract ASIN and the loaded scheduled action may be of an old format.
                if ( ! isset( $_aArguments[ 1 ] ) && is_string( $_aArguments[ 1 ] ) ) {
                    continue;
                }

                $_aASINs[] = $_aArguments[ 1 ];

                // This should not happen but third-parties may use this action.
                if ( $_iIndex > 10 ) {
                    break;
                }
            }
            return implode( ',', $_aASINs );
        }

        private function ___getAPIResponse( $aASINs, $aList ) {
            $_oOption     = AmazonAutoLinks_Option::getInstance();
            if ( ! $_oOption->isAPIConnected() ) {
                return array();
            }
            $_sPublicKey  = $_oOption->get( array( 'authentication_keys', 'access_key' ), '' );
            $_sPrivateKey = $_oOption->get( array( 'authentication_keys', 'access_key_secret' ), '' );
            if ( empty( $_sPublicKey ) || empty( $_sPrivateKey ) ) {
                return array();
            }

return array();
        }

        /**
         * @param $aResponse
         * @param $aList
         */
        private function ___setData( $aResponse, $aList ) {
        }

}