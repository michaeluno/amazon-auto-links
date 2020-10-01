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
 * Provides methods to cache API requests.
 * 
 * @since       3.9.0
 */
class AmazonAutoLinks_PAAPI50___Cache extends AmazonAutoLinks_PluginUtility {

    private $___sRequestURI           = '';
    private $___aHTTPArguments        = array();
    private $___iCacheDuration        = 84000;
    private $___bForceRenew           = false;
    private $___sRequestType          = 'api';

    /**
     * Sets up properties.
     *
     * @param string  $sRequestURI
     * @param array   $aHTTPArguments
     * @param integer $iCacheDuration
     * @param boolean $bForceRenew
     * @param string  $sRequestType
     */
    public function __construct( $sRequestURI, array $aHTTPArguments, $iCacheDuration, $bForceRenew, $sRequestType='api' ) {
        $this->___sRequestURI    = $sRequestURI;
        $this->___aHTTPArguments = $aHTTPArguments;
        $this->___iCacheDuration = $iCacheDuration;
        $this->___bForceRenew    = $bForceRenew;
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
            $this->___bForceRenew
        );
    }
        /**
         * Performs an API request.
         *
         * @since           unknown
         * @since           3.9.0
         * @return          string|array    Returns the retrieved HTML body string, and an error array on failure.
         */
        private function ___getResponseBySignedRequest( $sRequestURI, array $aHTTPArguments, $iDuration, $bForceRenew=false ) {

            add_action( 'aal_action_http_remote_get', array( $this, 'replyToHaveHTTPRequestInterval' ), 100, 3 );

            $_oHTTP = new AmazonAutoLinks_HTTPClient(
                $sRequestURI,
                $iDuration,
                $aHTTPArguments + array(
                    'timeout'       => 20,
                    'sslverify'     => false,
                    '_debug'        => __METHOD__,
                ),
                $this->___sRequestType // request type
            );
            if ( $bForceRenew ) {
                $_oHTTP->deleteCache();
            }
            $_asResponse =  $_oHTTP->getRaw(); // return errors as WP Error, not string

            remove_action( 'aal_action_http_remote_get', array( $this, 'replyToHaveHTTPRequestInterval' ), 100 );
            return $_asResponse;

        }
            /**
             * Gives an interval in API requests to avoid reaching the API rate limit.
             *
             * Check a lock transient that lasts only one second
             * as Amazon Product Advertising API only allows one request per second.
             *
             * @since           3.9.0
             * @param           string      $sRequestURL
             * @param           array       $aArguments
             * @param           string      $sRequestType
             * @callback        add_action  aal_action_http_remote_get
             */
            public function replyToHaveHTTPRequestInterval( $sRequestURL, $aArguments, $sRequestType ) {

                $_sAPIRequestLock = AmazonAutoLinks_Registry::TRANSIENT_PREFIX . '_LOCK_APIREQUEST';
                $_iIteration      = 0;
                $_oLock           = new AmazonAutoLinks_VersatileFileManager( __METHOD__, 1, $_sAPIRequestLock . '_' );
                while( $_oLock->isLocked() || $this->getTransientWithoutCache( $_sAPIRequestLock ) ) {
                    sleep( 1 );
                    $_iIteration++;
                    if ( $_iIteration > 10 ) {
                        break;
                    }
                }
                // Storing any data will be sufficient. One second lifespan.
                $this->setTransient( $_sAPIRequestLock, $sRequestURL, 1 );

            }

}