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
 * Triggers an action when there is a PA-API response error.
 * @since 4.3.5
 */
class AmazonAutoLinks_Unit_Event_Filter_PAAPIErrors extends AmazonAutoLinks_PluginUtility {

    /**
     * Sets up hooks.
     */
    public function __construct() {

        add_filter( 'aal_filter_http_request_set_cache_api_product_info', array( $this, 'replyToCheckAPIHTTPCacheResponse' ), 10, 6 );
        add_filter( 'aal_filter_http_request_set_cache_api50_test', array( $this, 'replyToCheckAPIHTTPCacheResponse' ), 10, 6 );
        add_filter( 'aal_filter_http_request_set_cache_api', array( $this, 'replyToCheckAPIHTTPCacheResponse' ), 10, 6 );

    }

    /**
     * Called when an API HTTP response is about to be cached.
     *
     * @param    mixed        $mData
     * @param    string       $sCacheName
     * @param    string       $sCharSet
     * @param    integer      $iCacheDuration
     * @param    string       $sURL
     * @param    array        $aArguments
     * @return   mixed
     * @since    3.9.0
     * @since    4.3.5        Moved from `AmazonAutoLinks_Unit_Log_PAAPIErrors`.
     * @callback add_filter() aal_filter_http_request_set_cache_{request type}
     */
    public function replyToCheckAPIHTTPCacheResponse( $mData, $sCacheName, $sCharSet, $iCacheDuration, $sURL, $aArguments ) {
        $_aErrors = $this->___getErrors( $mData, $sURL );
        if ( empty( $_aErrors ) ) {
            return $mData;
        }
        do_action( 'aal_action_detected_paapi_errors', $_aErrors, $sURL, $sCacheName, $sCharSet, $iCacheDuration, $aArguments );
        return $mData;
    }
        /**
         * @param  mixed $mData
         * @param  string $sURL
         * @return array
         */
        private function ___getErrors( $mData, $sURL ) {
            $_aErrors = array();
            /**
             * WP_Error and HTTP Status Error will be checked in
             * @see AmazonAutoLinks_Event_Error_Log_HTTPRequestErrors
             */
            if ( is_wp_error( $mData ) ) {
                return $_aErrors;
            }

            $_sBody   = $this->getElement( $this->getAsArray( $mData ), array( 'body' ) );
            if ( ! $this->isJSON( $_sBody ) ) {
                return $_aErrors;
            }

            // At this point, the body is JSON.

            $_aResponse = json_decode( $_sBody, true );

            // PA-API Errors
            $_aErrors[] = $this->___getAPIResponseFailure( $_aResponse );
            $_aErrors[] = $this->___getAPIResponseError( $_aResponse );

            return array_filter( $_aErrors ); // drop non-true values.

        }

            private function ___getAPIResponseFailure( $_aResponse ) {
                if ( ! isset( $_aResponse[ '__type' ], $_aResponse[ 'message' ] ) ) {
                    return '';
                }
                $_sError = $_aResponse[ '__type' ] . ': ' . $_aResponse[ 'message' ];
                if ( isset( $_aResponse[ 'response' ] ) ) {
                    $_sError .= '; '
                        . $this->getElement( $_aResponse, array( 'response', 'code' ) )
                        . ': ' . $this->getElement( $_aResponse, array( 'response', 'message' ) );
                }
                return $_sError;
            }
            private function ___getAPIResponseError( $_aResponse ) {
                if ( ! isset( $_aResponse[ 'Errors' ] ) ) {
                    return '';
                }
                $_sError = '';
                foreach( $_aResponse[ 'Errors' ] as $_aError ) {
                    $_sError .= $_aError[ 'Code' ] . ': ' . $_aError[ 'Message' ] . ' ';
                }
                return trim( $_sError );
            }

}