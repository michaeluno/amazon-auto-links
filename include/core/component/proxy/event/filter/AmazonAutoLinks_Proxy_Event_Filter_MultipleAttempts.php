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
 * Loads the component, HTTP Proxy.
 *
 * @since        4.2.0
 */
class AmazonAutoLinks_Proxy_Event_Filter_MultipleAttempts extends AmazonAutoLinks_PluginUtility {

    /**
     * @assmes  Already checked whether the proxy option is enabled or not.
     * @since   4.2.0
     */
    public function __construct() {

        add_filter( 'aal_filter_http_request_response', array( $this, 'replyToAttemptHTTPRequestsMultipleTimes' ), 10, 5 );

    }

    /**
     * @param WP_Error|array $aoResponse
     * @param string $sURL
     * @param array $aArguments
     * @param string $sRequestType
     * @param integer $iCacheDuration
     * @callback add_filter() aal_filter_http_request_response
     * @return  array|WP_Error
     * @since   4.2.0
     */
    public function replyToAttemptHTTPRequestsMultipleTimes( $aoResponse, $sURL, array $aArguments, $sRequestType, $iCacheDuration ) {
        
        if ( empty( $aArguments[ 'proxy' ] ) ) {
            return $aoResponse;
        }

        // At this point, the request is with a proxy
        
        if ( ! $this->___hasError( $aoResponse, $sURL ) ) {
            return $aoResponse;
        }
        /* @deprecated 4.3.4
        $aoResponse = $this->___getError( $aoResponse, $sURL );        
        if ( ! is_wp_error( $aoResponse ) ) {
            return $aoResponse;
        }
        */
        
        // At this point, the response has an error
        /// Store it as the previous response
        $_aoPrevious = $aoResponse;

        // Let the unusable one to be saved.
        do_action( 'aal_action_detected_unusable_proxy', $aArguments );

        // if there is a previous attempt, check the count and if it exceeds a certain number, bail
        $_iAttempts = ( integer ) $aArguments[ 'attempts' ];
        if ( 1 <= $_iAttempts ) {
            new AmazonAutoLinks_Error( 'PROXY_FAILURE', 'HTTP requests failed with proxies.', array( 'arguments' => $aArguments ), true );
            return $aoResponse;
        }
        $aArguments[ 'attempts' ] = $_iAttempts + 1;
        $aArguments[ 'interval' ] = 0;

        $_oHTTP = new AmazonAutoLinks_HTTPClient(
            $sURL,
            $iCacheDuration,
            $aArguments,
            $sRequestType
        );
        $_aoResponse = $_oHTTP->getResponse();  // not retrieving the HTTP body because the response format must be an array or an instance of WP_Error
        return $this->___hasError( $_aoResponse, $sURL )
            ? $_aoPrevious  // the response of the previous attempt
            : $_aoResponse;

    }
        /**
         * If a response contains an error, the result will be replaced with a WP_Error object.
         * At the moment, only captcha errors are checked.
         * @param $aoResponse
         * @param $sURL
         * @return WP_Error
         * @deprecated 4.3.4    The timing of BLOCKED_BY_CAPTCHA WP_Error object has changed and caching the raw response instead of a WP_Error object.
         */
/*        private function ___getError( $aoResponse, $sURL ) {
            if ( is_wp_error( $aoResponse ) ) {
                return $aoResponse;
            }
            $_sBody = wp_remote_retrieve_body( $aoResponse );
            if ( $this->isBlockedByAmazonCaptcha( $_sBody, $sURL ) ) {
                return new WP_Error( 'BLOCKED_BY_CAPTCHA', 'Blocked by captcha.' );
            }
            return $aoResponse;
        }*/
        /**
         * @param $aoResponse
         * @param $sURL
         * @return bool
         * @since 4.3.4
         */
        private function ___hasError( $aoResponse, $sURL ) {
            if ( is_wp_error( $aoResponse ) ) {
                return true;
            }
            return $this->isBlockedByAmazonCaptcha(
                wp_remote_retrieve_body( $aoResponse ),
                $sURL
            );
        }

}