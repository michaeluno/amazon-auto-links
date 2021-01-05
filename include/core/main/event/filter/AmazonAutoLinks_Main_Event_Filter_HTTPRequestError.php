<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
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

        $_aError = $this->getHTTPResponseError( $aoResponse, $sURL );
        if ( empty( $_aError ) ) {
            return $aoResponse;
        }

        // Add it to the log.
        foreach( $_aError as $_sCode => $_sError ) {
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
        if ( empty( $aOldCache ) ) {
            $_oCacheTable  = new AmazonAutoLinks_DatabaseTable_aal_request_cache;
            $aOldCache = $_oCacheTable->getCache( $sCacheName, $iCacheDuration );
        }
        $_mOldData = $this->getElement( $aOldCache, array( 'data' ) );
        if ( ! empty( $_mOldData ) ) {
            do_action( 'aal_action_debug_log', 'HTTP_REQUEST_CACHE', "Using the old cache for {$sURL}.", AmazonAutoLinks_PluginUtility::getAsArray( $aOldCache ), current_filter(), true );
            return $_mOldData;
        }

        do_action( 'aal_action_debug_log', 'HTTP_REQUEST_CACHE', "Not using an old cache for {$sURL}.", AmazonAutoLinks_PluginUtility::getAsArray( $aOldCache ), current_filter(), true );
        return $aoResponse;

    }

}