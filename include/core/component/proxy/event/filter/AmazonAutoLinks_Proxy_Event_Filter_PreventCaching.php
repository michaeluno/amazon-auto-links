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
 * Prevents caching HTTP requests with a proxy on failure.
 *
 * @since        4.2.0
 */
class AmazonAutoLinks_Proxy_Event_Filter_PreventCaching extends AmazonAutoLinks_PluginUtility {

    /**
     * @assmes  Already checked whether the proxy option is enabled or not.
     * @since   4.2.0
     */
    public function __construct() {
        add_filter( 'aal_filter_http_request_set_cache', array( $this, 'replyToCaptureCacheName' ), 10, 7 );
    }

    /**
     * @param mixed $mData
     * @param string $sCacheName
     * @param string $sCharSet
     * @param integer $iCacheDuration
     * @param string $sURL
     * @param array $aArguments
     * @param array $aOldCache
     * @since 4.2.0
     * @return mixed
     */
    public function replyToCaptureCacheName( $mData, $sCacheName, $sCharSet, $iCacheDuration, $sURL, array $aArguments, array $aOldCache ) {

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
        if ( $this->isBlockedByAmazonCaptcha( $_sHTTPBody, $sURL ) ) {
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