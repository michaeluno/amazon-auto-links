<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2019 Michael Uno
 */

/**
 * Logs errors of Product Advertising API responses.
 *
 * @since        3.9.0
 */
class AmazonAutoLinks_Unit_Log_PAAPIErrors {

    public function __construct() {

        add_filter( 'aal_filter_http_request_set_cache_api', array( $this, 'replyToCheckAPIHTTPCacheResponse' ), 10, 4 );

    }

    /**
     * Called when an API HTTP response is about to be cached.
     * @param $mData
     * @param $sCacheName
     * @param $sCharSet
     * @param $iCacheDuration
     *
     * @return mixed
     * @since   3.9.0
     */
    public function replyToCheckAPIHTTPCacheResponse( $mData, $sCacheName, $sCharSet, $iCacheDuration ) {

        $this->___setAPIErrorLog( $mData );
        return $mData;

    }
        private function ___setAPIErrorLog( $mData ) {
AmazonAutoLinks_Debug::log( 'setting api cache' );
AmazonAutoLinks_Debug::log( $mData );
        }

}