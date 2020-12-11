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
 * Logs errors of HTTP requests.
 *
 * @since        4.2.0
 */
class AmazonAutoLinks_Main_Event_Filter_HTTPRequestError extends AmazonAutoLinks_PluginUtility {

    /**
     * Sets up hooks.
     */
    public function __construct() {

        add_filter( 'aal_filter_http_request_set_cache', array( $this, 'replyToCheckAPIHTTPCacheResponse' ), 10, 7 );

    }

    /**
     * Called when an API HTTP response is about to be cached.
     *
     * @param    mixed     $aoResponse
     * @param    string    $sCacheName
     * @param    string    $sCharSet
     * @param    integer   $iCacheDuration
     * @param    string    $sURL
     * @param    array     $aArguments
     * @param    array     $aOldCache
     * @callback add_filter() aal_filter_http_request_set_cache
     * @return   mixed
     * @since    4.2.0
     */
    public function replyToCheckAPIHTTPCacheResponse( $aoResponse, $sCacheName, $sCharSet, $iCacheDuration, $sURL, $aArguments, $aOldCache ) {

        $_aErrors = $this->___getErrors( $aoResponse, $sURL );
        if ( empty( $_aErrors ) ) {
            return $aoResponse;
        }

        // Add it to the log.
        foreach( $_aErrors as $_sCode => $_sError ) {
            $_sError .= ' ' . $sCacheName . ' ' . $sURL;
            new AmazonAutoLinks_Error(
                'HTTP_REQUEST ' . $_sCode,
                $_sError,
                array(
                    'has_cache'      => ! empty( $aOldCache ),
                    'cache_duration' => $iCacheDuration,
                    'character_set'  => $sCharSet,
                    'arguments'      => $aArguments,
                ),
                true
            );
        }

        // Use the old cache if available
        $_mOldData = $this->getElement( $aOldCache, array( 'data' ) );
        if ( ! empty( $_mOldData ) ) {
            do_action( 'aal_action_debug_log', 'HTTP_REQUEST_CACHE', "Using the old cache for {$sURL}.", AmazonAutoLinks_PluginUtility::getAsArray( $aOldCache ), current_filter(), true );
            return $_mOldData;
        }

        do_action( 'aal_action_debug_log', 'HTTP_REQUEST_CACHE', "Not using an old cache for {$sURL}.", AmazonAutoLinks_PluginUtility::getAsArray( $aOldCache ), current_filter(), true );
        return $aoResponse;

    }
        /**
         * @param  WP_Error|array $aoResponse
         * @param  string         $sURL
         * @return string[]
         */
        private function ___getErrors( $aoResponse, $sURL ) {

            $_aErrors = $this->___getWPError( $aoResponse );
            if ( ! empty( $_aErrors ) ) {
                return $_aErrors;
            }
            $_aErrors = $this->___getHTTPStatusError( $aoResponse );
            if ( ! empty( $_aErrors ) ) {
                return $_aErrors;
            }
            return $this->___getCaptchaError( $aoResponse, $sURL );

        }
            /**
             * @param  WP_Error|array $aoResponse
             * @return array
             * @since  4.3.5
             */
            private function ___getWPError( $aoResponse ) {
                if ( is_wp_error( $aoResponse ) ) {
                    return array(
                        '(WP_ERROR) ' . $aoResponse->get_error_code() => $aoResponse->get_error_message(),
                    );
                }
                return array();
            }
            /**
             * @param  array $aResponse
             * @return array
             */
            private function ___getHTTPStatusError( array $aResponse ) {
                $_sCode    = $this->getElement( $aResponse, array( 'response', 'code' ) );
                $_s1stChar = substr( $_sCode, 0, 1 );
                if ( in_array( $_s1stChar, array( 2, 3 ) ) ) {
                    return array();
                }
                return array(
                    '(HTTP_STATUS_ERROR) ' . $_sCode => $this->getElement( $aResponse, array( 'response', 'message' ) )
                );
            }

            /**
             *
             * Since v4.3.4, the timing of creating captcha error WP_Error object has changed
             * and therefore, the error needs to be captured here.
             * @param  array  $aResponse
             * @param  string $sURL
             * @return array
             * @since  4.3.4
             */
            private function ___getCaptchaError( array $aResponse, $sURL ) {
                if ( $this->isBlockedByAmazonCaptcha( wp_remote_retrieve_body( $aResponse ), $sURL ) ) {
                    return array(
                        'CAPTCHA' => 'Blocked by Captcha',
                    );
                }
                return array();
            }


}