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
 * Prevents caching HTTP requests with a proxy on failure.
 *
 * @package      Amazon Auto Links
 * @since        4.2.0
 */
class AmazonAutoLinks_Proxy_Event_Filter_PreventCaching extends AmazonAutoLinks_PluginUtility {

    /**
     * @assmes  Already checked whether the proxy option is enabled or not.
     * @since   4.2.0
     */
    public function __construct() {
        add_action( 'aal_filter_http_request_set_cache', array( $this, 'replyToCaptureCacheName' ), 10, 6 );
    }

    /**
     * @param mixed $mData
     * @param string $sCacheName
     * @param string $sCharSet
     * @param integer $iCacheDuration
     * @param string $sURL
     * @param array $aArguments
     * @since 4.2.0
     * @return mixed
     */
    public function replyToCaptureCacheName( $mData, $sCacheName, $sCharSet, $iCacheDuration, $sURL, array $aArguments ) {

        // If a proxy is not set, do nothing.
        if ( empty( $aArguments[ 'proxy' ] ) ) {
            return $mData;
        }

        if ( is_wp_error( $mData ) ) {
            $this->___setFilterToSetCacheDuration( $sCacheName );
            return $mData;
        }

        // If the body contains a value, do nothing.
        $_sHTTPBody = ( string ) $this->getElement( $mData, array( 'body' ) );
        if ( empty( $_sHTTPBody ) ) {
            $this->___setFilterToSetCacheDuration( $sCacheName );
            return $mData;
        }

        // If the body contains a Captcha result
        if ( $this->___isBlockedByCaptcha( $_sHTTPBody, $sURL ) ) {
            $this->___setFilterToSetCacheDuration( $sCacheName );
            return $mData;
        }

        return $mData;

    }
        private function ___setFilterToSetCacheDuration( $sCacheName ) {
            add_filter(
                'aal_filter_http_request_set_cache_duration_' . $sCacheName,
                array( $this, 'replyToGiveShortCacheDuration' ),
                10,
                4
            );
        }

        /**
         * @param   string $sHTML
         * @param   string $sURL
         * @since   4.0.1
         * @return  boolean
         * @remark  duplicated code: copied from AmazonAutoLinks_Unit_Log_PAAPIErrors
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

    /**
     * @param integer $iCacheDuration
     * @param string $sCacheName
     * @param string $sURL
     * @param string $sRequestType
     *
     * @return      integer
     * @callback    aal_filter_http_request_set_cache_duration_{cache name}
     */
    public function replyToGiveShortCacheDuration( $iCacheDuration, $sCacheName, $sURL, $sRequestType ) {
        remove_filter( 'aal_filter_http_request_set_cache_duration_' . $sCacheName, array( $this, 'replyToGiveShortCacheDuration' ), 10 );
        return 0;
    }
}