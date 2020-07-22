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
class AmazonAutoLinks_Event_Event_Log_HTTPRequestErrors extends AmazonAutoLinks_PluginUtility {

    public function __construct() {

        // HTML documents
        add_filter( 'aal_filter_http_request_set_cache_customer_review', array( $this, 'replyToCheckAPIHTTPCacheResponse' ), 10, 5 );
        add_filter( 'aal_filter_http_request_set_cache_customer_review2', array( $this, 'replyToCheckAPIHTTPCacheResponse' ), 10, 5 );
        add_filter( 'aal_filter_http_request_set_cache_url_unit_type', array( $this, 'replyToCheckAPIHTTPCacheResponse' ), 10, 5 );
        add_filter( 'aal_filter_http_request_set_cache_wp_remote_get', array( $this, 'replyToCheckAPIHTTPCacheResponse' ), 10, 5 );

    }

    /**
     * Called when an API HTTP response is about to be cached.
     *
     * @param mixed     $mData
     * @param string    $sCacheName
     * @param string    $sCharSet
     * @param integer   $iCacheDuration
     * @param string    $sURL
     *
     * @return mixed
     * @since   4.2.0
     */
    public function replyToCheckAPIHTTPCacheResponse( $mData, $sCacheName, $sCharSet, $iCacheDuration, $sURL ) {

        $_sError = $this->___getError( $mData, $sURL );
        if ( $_sError ) {
            AmazonAutoLinks_Event_ErrorLog::setErrorLogItem(
                $_sError,
                array(
                    'url'        => $sURL,
                    'cache_name' => $sCacheName,
                )
            );
        }
        return $mData;

    }

        /**
         * @param mixed $mData
         * @param string $sURL
         * @return  string
         */
        private function ___getError( $mData, $sURL ) {

            // WP_Error
            if ( is_wp_error( $mData ) ) {
                return '(' . get_class( $mData ) . ') ' . $mData ->get_error_code() . ': ' . $mData ->get_error_message();
            }

            // HTTP Status Error
            $_sError  = $this->___getHTTPStatusError( $mData ) . ' ';

            // If there is an HTTP Status error, return it.
            $_sError = trim( $_sError );
            if ( $_sError ) {
                return $_sError;
            }

            // At this point, it seems to be fine.

            // At last, check if it is blocked by Captcha.
            if ( ! preg_match( '/https?:\/\/(www\.)?amazon\.[^"\' >]+/', $sURL ) ) {
                return false;
            }

            /// At this point. the url is of Amazon
            $_sBody             = $this->getElement( $mData, array( 'body' ) );
            $_bBlockedByCaptcha = $this->___isBlockedByCaptcha( $_sBody, $sURL );
            return $_bBlockedByCaptcha
                ? 'Blocked by captcha'
                : '';

        }
            /**
             * @param   string $sHTML
             * @param   string $sURL
             * @since   4.0.1
             * @return  boolean
             */
            private function ___isBlockedByCaptcha( $sHTML, $sURL ) {
                $_oDOM      = new AmazonAutoLinks_DOM;
                $_oDoc      = $_oDOM->loadDOMFromHTML( $sHTML );
                $_oXPath    = new DOMXPath( $_oDoc );
                $_noNode    = $_oXPath->query( './/form[@action="/errors/validateCaptcha"]' )->item( 0 );
                return null !== $_noNode;
            }

            private function ___getHTTPStatusError( array $mData ) {
                $_sCode    = $this->getElement( $mData, array( 'response', 'code' ) );
                $_s1stChar = substr( $_sCode, 0, 1 );
                if ( in_array( $_s1stChar, array( 2, 3 ) ) ) {
                    return '';
                }
                return $_sCode . ': ' . $this->getElement( $mData, array( 'response', 'message' ) );
            }

}