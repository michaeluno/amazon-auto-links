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
 * Logs errors of Product Advertising API responses.
 *
 * @since        3.9.0
 */
class AmazonAutoLinks_Unit_Log_PAAPIErrors extends AmazonAutoLinks_PluginUtility {

    public function __construct() {

        // JSON
        add_filter( 'aal_filter_http_request_set_cache_api_product_info', array( $this, 'replyToCheckAPIHTTPCacheResponse' ), 10, 5 );
        add_filter( 'aal_filter_http_request_set_cache_api50_test', array( $this, 'replyToCheckAPIHTTPCacheResponse' ), 10, 5 );
        add_filter( 'aal_filter_http_request_set_cache_api', array( $this, 'replyToCheckAPIHTTPCacheResponse' ), 10, 5 );

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
     * @since   3.9.0
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

        // If it is the TooManyRequests error, give a cache short lifespan
        if( false !== strpos( $_sError, 'TooManyRequests' ) ){
            add_filter(
                'aal_filter_http_request_set_cache_duration_' . $sCacheName,
                array( $this, 'replyToGiveShortCacheDuration' ),
                10,
                4
            );
        }

        return $mData;

    }
        public function replyToGiveShortCacheDuration( $iCacheDuration, $sCacheName, $sURL, $sRequestType ) {
            remove_filter( 'aal_filter_http_request_set_cache_duration_' . $sCacheName, array( $this, 'replyToGiveShortCacheDuration' ), 10 );
            return 'api50_test' === $sRequestType
                ? 0
                : 60 * 10; // 10 minutes
        }

        /**
         * @param mixed $mData
         * @param string $sURL
         * @return  string
         */
        private function ___getError( $mData, $sURL ) {

            // WP_Error
            if ( is_wp_error( $mData ) ) {
                return $mData ->get_error_code() . ': ' . $mData ->get_error_message();
            }

            // HTTP Status Error
            $_sError  = $this->___getHTTPStatusError( $mData ) . ' ';

            $_sBody   = $this->getElement( $mData, array( 'body' ) );

            if ( $this->isJSON( $_sBody ) ) {

                $_aResponse = json_decode( $_sBody, true );

                // PA-API Error
                $_sError .= $this->___getAPIResponseFailure( $_aResponse );
                $_sError .= $this->___getAPIResponseError( $_aResponse );
                return trim( $_sError );

            }

            // At this point, it is not JSON.

            // If there is an HTTP Status error, return it.
            $_sError = trim( $_sError );
            if ( $_sError ) {
                return $_sError;
            }

            // At this point, it seems to be fine.

            // At last, check if it is blocked by Captcha.
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
                if ( ! preg_match( '/https?:\/\/(www\.)?amazon\.[^"\' >]+/', $sURL ) ) {
                    return false;
                }
                // At this point, it is an access to an Amazon site.
                $_oDOM      = new AmazonAutoLinks_DOM;
                $_oDoc      = $_oDOM->loadDOMFromHTML( $sHTML );
                $_oXPath    = new DOMXPath( $_oDoc );
                $_noNode    = $_oXPath->query( './/form[@action="/errors/validateCaptcha"]' )->item( 0 );
                return null !== $_noNode;
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

            private function ___getHTTPStatusError( array $mData ) {
                $_sCode    = $this->getElement( $mData, array( 'response', 'code' ) );
                $_s1stChar = substr( $_sCode, 0, 1 );
                if ( in_array( $_s1stChar, array( 2, 3 ) ) ) {
                    return '';
                }
                return $_sCode . ': ' . $this->getElement( $mData, array( 'response', 'message' ) );
            }
        /**
         * @param $mData
         * @deprecated 4.0.0
         */
        /*private function ___setAPIErrorLog( $sErrorItem, $sCacheName, $sURL ) {
            $_sOptionKey = AmazonAutoLinks_Registry::$aOptionKeys[ 'error_log' ];
            $_aErrorLog  = $this->getAsArray( get_option( $_sOptionKey, array() ) );
            $_aErrorLog[ microtime( true ) ] = array(
                'time'           => time(),
                'cache_name'     => $sCacheName,
                'url'            => $sURL,
                'current_url'    => $this->getCurrentURL(),
                // 'note'       => '',
                'message'    => $sErrorItem,
            );
            // Keep up to latest 300 items
            $_aErrorLog = array_slice( $_aErrorLog, -300, 300, true );
            update_option( $_sOptionKey, $_aErrorLog );

        }*/

}