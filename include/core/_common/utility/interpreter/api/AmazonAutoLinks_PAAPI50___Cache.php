<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2018 Michael Uno
 */

/**
 * Provides methods to cache API requests.
 * 
 * @since       3.9.0
 */
class AmazonAutoLinks_PAAPI50___Cache extends AmazonAutoLinks_PluginUtility {

    private $___sRequestURI           = '';
    private $___aHTTPArguments        = array();
    private $___iCacheDuration        = 84000;
    private $___bForceCaching         = false;
    private $___sRequestType          = 'api';

    /**
     * Sets up properties.
     *
     * @param       array   $aConstructorArguments      Parameters passed to the constructor of the API class. This is used for background cache renewal events.
     */
    public function __construct( $sRequestURI, array $aHTTPArguments, $iCacheDuration, $bForceCaching, $sRequestType='api' ) {
        $this->___sRequestURI    = $sRequestURI;
        $this->___aHTTPArguments = $aHTTPArguments;
        $this->___iCacheDuration = $iCacheDuration;
        $this->___bForceCaching  = $bForceCaching;
        $this->___sRequestType   = $sRequestType;
    }

    /**
     * @since       3.9.0
     * @return      array|string
     */
    public function get() {
        return $this->___getResponseBySignedRequest(
            $this->___sRequestURI,
            $this->___aHTTPArguments,
            $this->___iCacheDuration,
            $this->___bForceCaching
        );
    }
        /**
         * Performs an API request.
         *
         * @since           unknown
         * @since           3.9.0
         * @return          string|array    Returns the retrieved HTML body string, and an error array on failure.
         */
        private function ___getResponseBySignedRequest( $sRequestURI, array $aHTTPArguments, $iDuration, $bForceCaching=false ) {

//            add_filter( 'aal_filter_http_request_cache_name', array( $this, 'replyToModifyCacheName' ), 10, 3 );
            add_action( 'aal_action_http_remote_get', array( $this, 'replyToHaveHTTPRequestInterval' ), 100, 3 );

            $_oHTTP = new AmazonAutoLinks_HTTPClient(
                $sRequestURI,
                $iDuration,
                $aHTTPArguments + array(
                    'timeout'       => 20,
                    'sslverify'     => false,
                    '_debug'        => __METHOD__,
                    'raw'           => true,    // return errors as WP Error, not string
                ),
                $this->___sRequestType // request type
            );
            if ( $bForceCaching ) {
                $_oHTTP->deleteCache();
            }
            $_asResponse =  $_oHTTP->get();

//            remove_filter( 'aal_filter_http_request_cache_name', array( $this, 'replyToModifyCacheName' ), 10 );
            remove_filter( 'aal_action_http_remote_get', array( $this, 'replyToHaveHTTPRequestInterval' ), 100 );
            return $_asResponse;

        }
            /**
             * @since           3.9.0
             * @param           string      $sRequestURL
             * @param           array       $aArguments
             * @param           string      $sRequestType
             * @callback        add_action  aal_action_http_remote_get
             */
            public function replyToHaveHTTPRequestInterval( $sRequestURL, $aArguments, $sRequestType ) {
                if ( $this->___sRequestType !== $sRequestType ) {
                    return;
                }
                $this->___sleep( $sRequestURL );
            }
                /**
                 * Gives an interval in API requests to avoid reaching the API rate limit.
                 *
                 * Check a lock transient that lasts only one second
                 * as Amazon Product Advertising API only allows one request per second.
                 *
                 * @since       3.9.0
                 * @return      void
                 */
                private function ___sleep( $sRequestURI ) {

                    $_sAPIRequestLock = AmazonAutoLinks_Registry::TRANSIENT_PREFIX . '_LOCK_APIREQUEST';
                    $_iIteration      = 0;
                    while( $this->getTransient( $_sAPIRequestLock ) && $_iIteration < 3 ) {
                        sleep( 1 );
                        $_iIteration++;
                    }
                    $this->setTransient(
                        $_sAPIRequestLock,
                        $sRequestURI, // any data will be sufficient
                        1  // one second
                    );
                }

    /**
     * Generates a cache name by removing request-specific keys from the query url.
     *
     * @callback    add_filter  aal_filter_http_request_cache_name
     * @param       string      $sCacheName
     * @param       string      $sURL
     * @param       string      $sRequestType
     * @since       3.9.0
     * @return      string
     * @deprecated  3.9.0
     */
    public function replyToModifyCacheName( $sCacheName, $sURL, $sRequestType ) {

        if ( $this->___sRequestType !== $sRequestType ) {
            return $sCacheName;
        }
        $_sPayload = ( string ) $this->getElement( $this->___aHTTPArguments, 'body' );
        return AmazonAutoLinks_Registry::TRANSIENT_PREFIX
               . '_'
               . md5( $sURL . $_sPayload );

    }

}