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
class AmazonAutoLinks_Event_Filter_HTTPRequestCache extends AmazonAutoLinks_PluginUtility {

    public function __construct() {

        // @deprecated 4.3.0
//        add_filter( 'aal_filter_http_request_set_cache_customer_review', array( $this, 'replyToCheckAPIHTTPCacheResponse' ), 10, 5 );
//        add_filter( 'aal_filter_http_request_set_cache_customer_review2', array( $this, 'replyToCheckAPIHTTPCacheResponse' ), 10, 5 );
//        add_filter( 'aal_filter_http_request_set_cache_url_unit_type', array( $this, 'replyToCheckAPIHTTPCacheResponse' ), 10, 5 );
//        add_filter( 'aal_filter_http_request_set_cache_wp_remote_get', array( $this, 'replyToCheckAPIHTTPCacheResponse' ), 10, 5 );
//        add_filter( 'aal_filter_http_request_set_cache_category_unit_type', array( $this, 'replyToCheckAPIHTTPCacheResponse' ), 10, 5 );

        add_filter( 'aal_filter_http_request_set_cache', array( $this, 'replyToCheckAPIHTTPCacheResponse' ), 10, 7 );

    }

    /**
     * Called when an API HTTP response is about to be cached.
     *
     * @param    mixed     $mData
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
    public function replyToCheckAPIHTTPCacheResponse( $mData, $sCacheName, $sCharSet, $iCacheDuration, $sURL, $aArguments, $aOldCache ) {

        $_sError = $this->___getError( $mData, $sURL );
        if ( ! $_sError ) {
            return $mData;
        }

        // Add it to the log.
        $_sError .= ' ' . $sCacheName . ' ' . $sURL;
        new AmazonAutoLinks_Error(
            'HTTP_REQUEST',
            $_sError,
            array(
                'has_cache'      => ! empty( $aOldCache ),
                'cache_duration' => $iCacheDuration,
                'character_set'  => $sCharSet,
                'arguments'      => $aArguments,
            ),
            true
        );

        // Use the old cache if available
        $_mOldData = $this->getElement( $aOldCache, array( 'data' ) );
        if ( ! empty( $_mOldData ) ) {
            return $_mOldData;
        }

        return $mData;

    }

        /**
         * @param mixed $mData
         * @param string $sURL
         * @return  string
         */
        private function ___getError( $mData, $sURL ) {

            if ( is_wp_error( $mData ) ) {
                return $mData ->get_error_code() . ': ' . $mData ->get_error_message();
            }

            $_sStatusError = $this->___getHTTPStatusError( $mData );
            if ( $_sStatusError ) {
                return $_sStatusError;
            }
            return $this->___getCaptchaError( $mData, $sURL );

        }

            /**
             *
             * Since v4.3.4, the timing of creating captcha error WP_Error object has changed
             * and therefore, the error needs to be captured here.
             * @param $mData
             * @param string $sURL
             * @return string
             * @since 4.3.4
             */
            private function ___getCaptchaError( $mData, $sURL ) {
                if ( $this->isBlockedByAmazonCaptcha( wp_remote_retrieve_body( $mData ), $sURL ) ) {
                    return 'CAPTCHA: Blocked by Captcha';
                }
                return '';
            }
            /**
             * @param array $mData
             * @return string
             */
            private function ___getHTTPStatusError( array $mData ) {
                $_sCode    = $this->getElement( $mData, array( 'response', 'code' ) );
                $_s1stChar = substr( $_sCode, 0, 1 );
                if ( in_array( $_s1stChar, array( 2, 3 ) ) ) {
                    return '';
                }
                return $_sCode . ': ' . $this->getElement( $mData, array( 'response', 'message' ) );
            }

}