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
 * Provides methods to cache API requests.
 * 
 * @since 3.9.0
 */
class AmazonAutoLinks_PAAPI50___Cache extends AmazonAutoLinks_PluginUtility {

    private $___sRequestURI    = '';
    private $___aHTTPArguments = array();
    private $___iCacheDuration = 84000;
    private $___bForceRenew    = false;
    private $___sRequestType   = 'api';

    /**
     * @since 5.2.2
     * @var   integer   Stores an error lock interval in seconds.
     */
    private $___iLockInterval  = 1800;  // 60 * 30 = 30 minutes

    /**
     * @var   string
     * @since 4.3.5
     */
    private $___sLocale        = '';

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
        // @todo  Let the user set a lock interval through UI.
        $this->___iLockInterval  = ( integer ) apply_filters( 'aal_filter_paapi_request_error_lock_interval', $this->___iLockInterval );
    }

    /**
     * @since  3.9.0
     * @return array|string
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
         * @since         ?
         * @since         3.9.0
         * @param string  $sRequestURI
         * @param array   $aHTTPArguments
         * @param integer $iDuration
         * @param boolean $bForceRenew
         * @return        array|WP_Error    Returns a wp_remote_request() response array or a WP_Error object.
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
             * @callback add_action() aal_action_detected_paapi_errors
             */
            public function replyToSetLockOnError( $aErrors, $sURL, $sCacheName, $sCharSet, $iCacheDuration, $aArguments ) {

                $_sErrors = implode( ' ', $aErrors );

                // For a list of PA-API 5 errors, see https://webservices.amazon.com/paapi5/documentation/troubleshooting/error-messages.html
                $_sErrorTypes = 'AccessDenied|AssociateValidation|IncompleteSignature|'
                    . 'InvalidPartnerTag|InvalidSignature|TooManyRequests|UnrecognizedClient';
                if ( ! preg_match( '/' . $_sErrorTypes . '/', $_sErrors ) ) {    // if one of those errors is not found,
                    return;
                }
                $this->___setAPIRequestLock( $this->getAsArray( $aArguments ), $_sErrors );

            }
                /**
                 * Extend the unlock time
                 * @param array  $aArguments
                 * @param string $sErrors
                 * @since 4.3.5
                 * @since 5.2.2  Added the `$sErrors` parameter.
                 */
                private function ___setAPIRequestLock( array $aArguments, $sErrors ) {
                    $_aAPIParams = $this->getElementAsArray( $aArguments, array( 'constructor_parameters' ) );
                    $_oLock      = new AmazonAutoLinks_VersatileFileManager_PAAPILock(
                        $this->___sLocale,
                        $this->getElement( $_aAPIParams, array( 1 ) ), // public access key
                        $this->getElement( $_aAPIParams, array( 2 ) )  // secret access key
                    );
                    $_oLock->set( $sErrors );
                    $_oLock->lock( time() + $this->___iLockInterval );
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

                $_aAPIParams = $this->getElementAsArray( $aArguments, array( 'constructor_parameters' ) );
                $_oLock      = new AmazonAutoLinks_VersatileFileManager_PAAPILock(
                    $this->___sLocale,
                    $this->getElement( $_aAPIParams, array( 1 ) ), // public access key
                    $this->getElement( $_aAPIParams, array( 2 ) )  // secret access key
                );

                $_iIteration = 0;
                while( $_oLock->isLocked() ) {
                    sleep( 1 );
                    $_iIteration++;
                    if ( $_iIteration <= 10 ) {
                        continue;
                    }
                    $_iFrom = time();
                    $_iTo   = $_oLock->getModificationTime() + 1;
                    $_sMessage = sprintf(
                        __( 'The API request is locked for %1$s. Error: %2$s', 'amazon-auto-links' ),
                        human_time_diff( $_iFrom, $_iTo ),
                        $_oLock->get()
                    );
                    throw new Exception( $_sMessage, 1 );
                    break;
                }

            }

}