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
     * @var   string
     * @since 4.3.5
     */
    private $___sLocale               = '';

    /**
     * Sets up properties.
     *
     * @param string  $sRequestURI
     * @param array   $aHTTPArguments
     * @param integer $iCacheDuration
     * @param boolean $bForceRenew
     * @param string  $sRequestType
     * @param string  $sLocale          Used to identify a lock file.
     * @since 3.9.0
     * @since 4.3.5   Added the `$sLocale` parameter.
     */
    public function __construct( $sRequestURI, array $aHTTPArguments, $iCacheDuration, $bForceRenew, $sRequestType, $sLocale ) {
        $this->___sRequestURI    = $sRequestURI;
        $this->___aHTTPArguments = $aHTTPArguments;
        $this->___iCacheDuration = $iCacheDuration;
        $this->___bForceRenew    = $bForceRenew;
        $this->___sRequestType   = $sRequestType;
        $this->___sLocale        = $sLocale;
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
         * @param string  $sRequestURI
         * @param array   $aHTTPArguments
         * @param integer $iDuration
         * @param bool    $bForceRenew
         * @return        array|WP_Error    Returns a wp_remote_request() response array or a WP_Error object.
         * @since         3.9.0
         * @since         unknown
         */
        private function ___getResponseBySignedRequest( $sRequestURI, array $aHTTPArguments, $iDuration, $bForceRenew=false ) {

            add_action( 'aal_action_http_remote_get', array( $this, 'replyToHaveHTTPRequestInterval' ), 100, 3 );
            add_action( 'aal_action_detected_paapi_errors', array( $this, 'replyToSetLockOnError' ), 10, 6 );

            try {
                
                $_oHTTP = new AmazonAutoLinks_HTTPClient(
                    $sRequestURI,
                    $iDuration,
                    $aHTTPArguments + array(
                        'timeout'       => 20,
                        'sslverify'     => false,
                        'renew_cache'   => ( boolean ) $bForceRenew,
                        'interval'      => 1,
                        '_debug'        => __METHOD__,
                    ),
                    $this->___sRequestType // request type
                );
                $_aoResponse = $_oHTTP->getResponse(); // return errors as WP Error, not string

            } catch ( Exception $_oException ) {

                $_aCodeMap   = array(
                    1 => 'PAAPI_REQUEST_LOCK',
                );
                $_iExceptionCode = $_oException->getCode();
                $_sCode          = $this->getElement( $_aCodeMap, array( $_iExceptionCode ), $_iExceptionCode );
                $_aoResponse     = new WP_Error( $_sCode, $_oException->getMessage() );

            }

            remove_action( 'aal_action_http_remote_get', array( $this, 'replyToHaveHTTPRequestInterval' ), 100 );
            remove_action( 'aal_action_detected_paapi_errors', array( $this, 'replyToSetLockOnError' ), 10 );
            return $_aoResponse;

        }

            /**
             * @since    4.3.5
             * @param    array   $aErrors
             * @param    string  $sURL
             * @param    string  $sCacheName
             * @param    string  $sCharSet
             * @param    integer $iCacheDuration
             * @param    array   $aArguments
             * @return   void
             * @callback add_action() aal_action_detected_paapi_errors
             */
            public function replyToSetLockOnError( $aErrors, $sURL, $sCacheName, $sCharSet, $iCacheDuration, $aArguments ) {
                if ( ! $this->___hasRateLimitError( $this->getAsArray( $aErrors ) ) ) {
                    return;
                }
                $this->___setAPIRequestLock( $this->getAsArray( $aArguments ) );
            }
                /**
                 * Extend the unlock time
                 * @param array  $aArguments
                 * @since 4.3.5
                 * @todo  Let the user decide the lock interval.
                 */
                private function ___setAPIRequestLock( array $aArguments ) {

                    $_oFile = new AmazonAutoLinks_VersatileFileManager_PAAPILock( $this->___sLocale );
                    $_oFile->lock( time() + ( 60 * 30 ) ); // 30 minites

                }            
                /**
                 * @param  array $aErrors
                 * @return boolean
                 * @since  4.3.5
                 */
                private function ___hasRateLimitError( $aErrors ) {
                    foreach( $aErrors as $_sError ) {
                        if ( false !== strpos( $_sError, 'TooManyRequests' ) ) {
                            return true;
                        }
                    }
                    return false;
                }
            /**
             * Gives an interval in API requests to avoid reaching the API rate limit.
             *
             * Check a lock transient that lasts only one second
             * as Amazon Product Advertising API only allows one request per second.
             *
             * @param    string       $sRequestURL
             * @param    array        $aArguments
             * @param    string       $sRequestType
             * @callback add_action() aal_action_http_remote_get
             * @throws   Exception
             * @since    3.9.0
             * @since    4.3.5        Made it throw an exception.
             */
            public function replyToHaveHTTPRequestInterval( $sRequestURL, $aArguments, $sRequestType ) {

                $_iIteration = 0;
                $_oLock      = new AmazonAutoLinks_VersatileFileManager_PAAPILock( $this->___sLocale );
                while( $_oLock->isLocked() ) {
                    sleep( 1 );
                    $_iIteration++;
                    if ( $_iIteration > 10 ) {
                        $_sMessage = sprintf(
                            'The API request is locked. It will be unlocked at %1$s. Now: %2$s.',
                            $this->getSiteReadableDate( $_oLock->getModificationTime() + 1, 'Y/m/d/ H:i:s' ), // modification + 1
                            $this->getSiteReadableDate( time(), 'Y/m/d/ H:i:s' )
                        );
                        throw new Exception( $_sMessage, 1 );
                        break;
                    }
                }

            }

}