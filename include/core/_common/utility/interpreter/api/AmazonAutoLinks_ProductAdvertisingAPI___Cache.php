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
 * @since       3.5.0
 * @deprecated  3.9.0   PA-API 4.0 was deprecated as of Oct 31st, 2019.
 */
class AmazonAutoLinks_ProductAdvertisingAPI___Cache extends AmazonAutoLinks_PluginUtility {

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
     * @since       3.5.0
     * @return      array|string
     */
    public function get() {
        return $this->___getAPIResponseBySettingCache(
            $this->___sRequestURI,
            $this->___aHTTPArguments,
            $this->___iCacheDuration,
            $this->___bForceCaching
        );
    }
    
        /**
         * Performs the API request and sets the cache.
         *
         * @since       3
         * @since       3.5.0           Moved from `AmazonAutoLinks_ProductAdvertisingAPI_Base`.
         * @return      array|string    The response string of xml or an array of error.
         */
        private function ___getAPIResponseBySettingCache( $sRequestURI, array $aHTTPArguments, $iDuration, $bForceCaching ) {

            $_asXMLResponse = $this->___getResponseBySignedRequest( $sRequestURI, $aHTTPArguments, $iDuration, $bForceCaching );
            if ( ! $this->___isValidAPIResponse( $_asXMLResponse ) ) {
                return $_asXMLResponse;
            }

            $_boXML = $this->getXMLObject( $_asXMLResponse );

            // If it's not a valid XML, it returns false.
            if ( ! is_object( $_boXML ) ) {
                return array(
                    'Error' => array(
                        'Message'   => strip_tags( $_asXMLResponse ),
                        'Code'      => 'Invalid XML'
                    )
                );
            }

            $aResponse = $this->convertXMLtoArray( $_boXML );

            // If empty, return an empty array.
            if ( empty( $aResponse ) ) {
                return array();
            }

            // If the result is not an array, something went wrong.
            if ( ! is_array( $aResponse ) ) {
                return ( array ) $aResponse;
            }

            // If an error occurs, do not set the cache.
            if ( isset( $aResponse[ 'Error' ] ) ) {
                return $aResponse;
            }

            return $_asXMLResponse;


        }
            /**
             * @param   array|string    $_asXMLResponse
             *
             * @since   3.5.0
             * @return  boolean
             */
            private function ___isValidAPIResponse( $_asXMLResponse ) {
                // If it has a HTTP connection error, an array is returned.
                if ( is_array( $_asXMLResponse ) ) {
                    return false;
                }
                // If it's not a string, something went wrong.
                if ( ! is_string( $_asXMLResponse ) ) {
                    return false;
                }
                return true;
            }

            /**
             * Performs an API request.
             *
             * @since           unknown
             * @since           3               Moved to the base class.
             * @since           3.5.0           Changed the scope to private.
             * @return          string|array    Returns the retrieved HTML body string, and an error array on failure.
             */
            private function ___getResponseBySignedRequest( $sRequestURI, array $aHTTPArguments, $iDuration, $bForceCaching=false ) {

                add_filter( 'aal_filter_http_request_cache_name', array( $this, 'replyToModifyCacheName' ), 10, 3 );
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
                if ( $bForceCaching ) {
                    $_oHTTP->deleteCache();
                }
                $_asResponse =  $_oHTTP->get();

                remove_filter( 'aal_filter_http_request_cache_name', array( $this, 'replyToModifyCacheName' ), 10 );
                remove_filter( 'aal_action_http_remote_get', array( $this, 'replyToHaveHTTPRequestInterval' ), 100 );
                return $_asResponse;

            }
                /**
                 * @since           3.5.0
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
                     * @since       3.5.0
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
                 * @since       3.5.0
                 * @return      string
                 */
                public function replyToModifyCacheName( $sCacheName, $sURL, $sRequestType ) {

                    if ( $this->___sRequestType !== $sRequestType ) {
                        return $sCacheName;
                    }
                    // e.g. http://webservices.amazon.com/onca/xml?AWSAccessKeyId=AKIAIUOXXAXPYUKNVPVA&AssociateTag=miunosoft-20&Condition=New&IdType=ASIN&IncludeReviewsSummary=True&ItemId=B00DBYBNEE%2CB00ZV9PXP2%2CB01DYC10PE%2CB016ZNRC0Q%2CB00K2EOONI%2CB00TEFTA80%2CB01JZHCBM8%2CB00E9JIP5A%2CB01BLUCAY6%2CB009KAARUE&Operation=ItemLookup&ResponseGroup=Large&Service=AWSECommerceService&Timestamp=2017-01-08T12%3A37%3A13Z&Version=2013-08-01&Signature=yBq0JAVSG4%2BB9JthTyvfunsyuAKyhr3u3s%2BQTPMq%2Fq0%3D
                    $sURL = remove_query_arg(
                        array( 'AWSAccessKeyId', 'AssociateTag', 'Timestamp', 'Signature', ),
                        $sURL
                    );
                    return AmazonAutoLinks_Registry::TRANSIENT_PREFIX . '_' . md5( $sURL );

                }

}