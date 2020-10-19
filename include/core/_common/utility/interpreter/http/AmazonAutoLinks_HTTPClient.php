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
 * Serves as an HTTP client.
 *
 * @remark Has a caching system built-in.
 * @remark Handles auto-encoding of the document from the source character set to the site character set.
 * @since  3
 * @sicne  4.3.4 Deprecated the multiple URLs.  For multiple URLs to process at once, use `AmazonAutoLinks_HTTPClient_Multiple`.
 */
class AmazonAutoLinks_HTTPClient extends AmazonAutoLinks_PluginUtility {

    /**
     * Stores the request type.
     *
     * Change this property to mark a cache item in the database.
     * @var string
     */
    public $sRequestType = 'wp_remote_get';

    /**
     * @var int
     */
    public $iCacheDuration = 86400;

    /**
     * Stores the request result.
     *
     * Used to retrieve response elements after performing get() or getRaw().
     * @var WP_Error|array
     */
    public $aoResponse;

    /**
     * Stores the site character set.
     * @scope static This is static to cache the value throughout the page load.
     * @var   string
     */
    static public $sSiteCharSet;

    /**
     * Stores the processing URL.
     * @var string
     * @sicne 4.3.4
     */
    public $sURL;

    /**
     * Stores the processing response cache name.
     * @var string
     * @since 4.3.4
     */
    public $sCacheName;

    /**
     * HTTP request, wp_remote_request() arguments.
     * @var array
     */
    public $aArguments =  array();

    /**
     * HTTP request, wp_remote_request() argument structure.
     * @see WP_Http::request()
     */
    public $aArgumentStructure =  array(
        'timeout'            => 5,
        'redirection'        => 5,
        'httpversion'        => '1.0',
        'user-agent'         => null,
        'blocking'           => true,
        'headers'            => array(),
        'cookies'            => array(),
        'body'               => null,
        'compress'           => false,      // does not seem to take effect
        'decompress'         => true,
        'sslverify'          => true,
        'stream'             => false,
        'filename'           => null,
        'method'             => null,       // [3.9.0]
        'reject_unsafe_urls' => true,       // [4.3.4]
    );

    /**
     * Specific arguments to this class.
     */
    public $aCustomArguments = array(
        'constructor_parameters' => array(),
        'api_parameters'         => array(),
        'compress_cache'         => false,    // [4.0.0] (boolean) whether to compress cache data
        'proxy'                  => null,     // [4.2.0]
        'attempts'               => 0,        // [4.2.0] (integer) for multiple attempts for requests, especially for cases with proxies.
        'skip_argument_format'   => false,    // [4.3.4] (boolean) whether to skip argument formatting. Used in the multiple mode.
        'interval'               => 0,        // [4.3.4] (integer) An interval in seconds between request with the same request type.
        'renew_cache'            => false,    // [4.3.4] (boolean) Whether to renew the cache.
        'amazon_language'        => '',       // [4.3.4] (string)  preferred language code for Amazon sites. Used to generate cookies.
    );

    /**
     * A response cache.
     * @var array
     */
    private $___aCache;

    /**
     * Sets up properties.
     * @param string  $sURL
     * @param integer $iCacheDuration
     * @param array   $aArguments
     * @param string  $sRequestType
     * @param array   $aCache           A cache array to suppress the database cache. This is used for multiple URLs to query them for caches at once.
     * @sicne 3
     * @sicne 4.3.4   Added the $aCache array.
     */
    public function __construct( $sURL, $iCacheDuration=86400, array $aArguments=array(), $sRequestType='wp_remote_get', array $aCache=array() ) {

        self::$sSiteCharSet   = isset( self::$sSiteCharSet ) ? self::$sSiteCharSet : get_bloginfo( 'charset' );
        $this->iCacheDuration = $iCacheDuration;
        $this->sRequestType   = $sRequestType;
        $this->sURL           = trim( $sURL );
        $this->aArguments     = $this->_getArgumentsFormatted( $aArguments, $this->sURL );
        $this->sCacheName     = $this->_getCacheName( $this->sURL, $this->aArguments, $this->sRequestType );
        $this->___aCache      = $aCache;

    }

    /**
     * @return string
     */
    public function getCacheName() {
        return $this->sCacheName;
    }

    /**
     * Deletes the cache of the provided URL.
     *
     * @remark  [3.7.6] If multiple URLs are set, they will be deleted at once
     */
    public function deleteCache() {
        $this->___aCache  = array();
        $this->aoResponse = null;
        $_oCacheTable     = new AmazonAutoLinks_DatabaseTable_aal_request_cache;
        $_oCacheTable->deleteCache( $this->sCacheName );
    }

    /**
     * Returns a raw response.
     *
     * Here _raw_ refers to un-formatted wp_remote_request() return value, which is WP_Error or an response array.
     * Even though it says _raw_, filters are applied to those responses. To get real raw responses, use `getRawResponse()`
     * @param string If a URL is given, it returns ,
     * @return array|WP_Error
     * @since 4.3.4
     * @deprecated Due to a confusing name.
     */
//    public function getRaw() {
//        return $this->getResponse();
//    }

    /**
     * Returns the HTTP body.
     *
     * An alias of getBody().
     * @remark      Handles character encoding conversion.
     * @return      string
     */
    public function get() {
        return $this->getBody();
    }

    /**
     * Retrieves the HTTP response body.
     * This is public because for a case that the user calls `getRawResponse()` first to check inside the response then wants to convert the document encoding.
     * @param  WP_Error|array|null
     * @return string
     * @since  4.3.4
     */
    public function getBody() {
        $aoResponse    = $this->getResponse();
        $_sHTTPBody    = $this->___getResponseBody( $aoResponse );
        $_sCharSetFrom = $this->___getCharacterSetFromResponse( $aoResponse );
        return $this->___getResponseBodySanitized( $_sHTTPBody, $_sCharSetFrom );
    }

        /**
         * Returns the sanitized response body as string.
         *
         * @remark Handles character encoding conversion. Encodes the document from the source character set to the site character set.
         * @param  string  $sHTTPBody    An HTTP body.
         * @param  string  $sCharSetFrom A character set of the HTTP body.
         * @sicne  4.3.4
         * @return string
         */
        private function ___getResponseBodySanitized( $sHTTPBody, $sCharSetFrom ) {

            $_sCharSetTo   = self::$sSiteCharSet;
            if ( ! $sCharSetFrom ) {
                return $sHTTPBody;
            }
            if ( strtoupper( $_sCharSetTo ) === strtoupper( $sCharSetFrom ) ) {
                return $sHTTPBody;
            }
            return $this->convertCharacterEncoding( $sHTTPBody, $_sCharSetTo, $sCharSetFrom, false );

        }
            /**
             * @param       array|WP_Error $aoResponse
             * @return      string
             * @since       unknown
             * @since       4.2.0   Changed not to return raw.
             */
            private function ___getResponseBody( $aoResponse ) {
                if ( is_wp_error( $aoResponse ) ) {
                    return $aoResponse->get_error_message();
                }
                return wp_remote_retrieve_body( $aoResponse );
            }

    /**
     * Returns the response's character set by the url.
     *
     * @since       3
     * @return      string
     */
    public function getCharacterSet() {
        return $this->___getCharacterSetFromResponse( $this->getRawResponse() );
    }

    /**
     * Returns an unfiltered HTTP response.
     * @remark Stores the response into the property.
     * @remark Still `aal_filter_http_request_response` is applied.
     * @return WP_Error|array
     * @since  4.3.4
     */
    public function getRawResponse() {
        $_aoResponse = ! empty( $this->aoResponse )
            ? $this->aoResponse
            : $this->___getHTTPResponseWithCache( $this->sURL, $this->aArguments, $this->iCacheDuration );
        $this->aoResponse = $_aoResponse;   // property cache.
        return $_aoResponse;
    }

    /**
     * @return WP_Error|array
     */
    public function getResponse() {

        $_aoResponse = $this->getRawResponse();

        /**
         * This filter passes the value of the both cached one and the newly fetched one.
         * This is useful to check errors.
         * @since   4.2.2
         */
        return apply_filters( 'aal_filter_http_request_result', $_aoResponse, $this->sURL, $this->aArguments, $this->iCacheDuration );

    }

        /**
         * Get an HTTP response.
         * If its cache exists, it will be used.
         * @param       string      $sURL      The URL to request.
         * @param       array       $aArguments
         * @param       integer     $iCacheDuration
         * @return      array       Response array
         */
        private function ___getHTTPResponseWithCache( $sURL, $aArguments, $iCacheDuration ) {

            $_aoResponse = $this->getResponseFromCache();
            if ( ! empty( $_aoResponse ) ) {
                return $_aoResponse;
            }
            $_aoResponse = $this->___getHTTPResponse( $sURL, $aArguments );
            $this->___setCacheInDatabase( $sURL, $this->sCacheName, $_aoResponse, $iCacheDuration, $aArguments, $this->___aCache, $this->sRequestType );
            return $_aoResponse;

        }
            /**
             * Extracts the HTTP response from the given cache data structured as the database table columns.
             * @param  array   $aCache
             * @param  string  $sCacheName
             * @param  integer $iCacheDuration
             * @param  array   $aArguments
             * @param  string  $sRequestType
             * @return array   The cache array. If the data is corrupt or uncached, an empty array will be returned.
             * @since 4.3.4
             */
            private function ___getHTTPResponseFromCache( array $aCache, $sCacheName, $iCacheDuration, $aArguments, $sRequestType ) {

                if ( ! isset( $aCache[ 'data' ] ) ) {
                    return array();
                }

                $aCache = $aCache + array(
                    'remained_time' => 0,
                    'charset'       => null,
                    'data'          => null,
                    'request_uri'   => null,
                    'name'          => $sCacheName,
                );

                if ( ! is_wp_error( $aCache[ 'data' ] ) ) {

                    // 4.0.0+ There is a reported case that the `data` element is neither an array nor null.
                    // @todo the reason of corrupt data is unknown. It could be that the compressed data can be treated as a string when the WordPress core sanitizes the data when retrieving from the database table.
                    if ( ! is_array( $aCache[ 'data' ] ) ) {
                        do_action( 'aal_action_error_http_request_cache_data', 'The cache data is corrupted.', $aCache );
                        return array();
                    }

                    /**
                     * @since      3.7.6b01  Deprecated as this seemed to cause unexpected errors when the cache is not properly set for some reasons like exceeding max_allowed_packet or max_execution_time
                     * @since      3.12.0    Re-added
                     */
                    $_bsUncompressed = function_exists( 'gzuncompress' )
                        ? @gzuncompress( $this->getElement( $aCache, array( 'data', 'body' ), '' ) )    // returns string|false
                        : false;
                    if ( $_bsUncompressed ) {
                        $aCache[ 'data' ][ 'body' ] = $_bsUncompressed;
                    }
                    unset( $aCache[ 'data' ][ 'http_response' ] );

                }

                /**
                 * This filter allows other components to modify the remained time,
                 * which can be used to trick the below check and return the stored data anyway.
                 * So the cache renewal event can be scheduled in the background.
                 */
                $aCache = apply_filters( 'aal_filter_http_response_cache', $aCache, $iCacheDuration, $aArguments, $sRequestType );
                return $this->___isCacheExpired( $aCache )
                    ? array()
                    : $aCache[ 'data' ];

            }
            /**
             * Retrieves a cache from the database table.
             * @param  string  $sCacheName
             * @param  integer $iCacheDuration
             * @param  array   $aSetCache       The cache data passed to the constructor, used for the multiple mode.
             * @param  array   $aArguments      The cache data passed to the constructor, used for the multiple mode.
             * @return array
             * @since  4.3.4
             */
            private function ___getCacheFromDatabase( $sCacheName, $iCacheDuration, array $aSetCache, array $aArguments ) {
                if ( 0 === ( integer ) $iCacheDuration ) {
                    return array();
                }
                if ( $aArguments[ 'renew_cache' ] ) {
                    return array();
                }
                if ( ! empty( $aSetCache ) ) {
                    return $aSetCache;
                }
                $_oCacheTable  = new AmazonAutoLinks_DatabaseTable_aal_request_cache;
                return $_oCacheTable->getCache( $sCacheName, $iCacheDuration );
            }

            /**
             * @since       3.5.0
             * @return      boolean
             * @param       array   $aCache
             */
            private function ___isCacheExpired( array $aCache ) {

                if ( empty( $aCache[ 'data' ] ) && 0 !== $aCache[ 'data' ] ) {
                    return true;
                }
                return $aCache[ 'remained_time' ] <= 0;

            }

        /**
         * Performs an HTTP request.
         * @param   string  $sURL
         * @param   array   $aArguments
         * @return  array|WP_Error The response or WP_Error on failure.
         */
        private function ___getHTTPResponse( $sURL, array $aArguments ) {

            do_action( 'aal_action_http_remote_get', $sURL, $aArguments, $this->sRequestType ); // 3.5.0
            $_aoResponse = $this->___getHTTPRequested( $sURL, $aArguments );

            /**
             * Allows the response to be modified, mainly for multiple attempts with different proxies.
             * If a cache is set, this won't reach.
             * @since   4.2.0
             */
            return apply_filters( 'aal_filter_http_request_response', $_aoResponse, $sURL, $aArguments, $this->sRequestType, $this->iCacheDuration );

        }
            /**
             * @param $sURL
             * @param array $aArguments
             *
             * @return array|WP_Error
             * @since   4.2.0
             */
            private function ___getHTTPRequested( $sURL, array $aArguments ) {

                // Give intervals.
                $this->___haveInterval( $aArguments[ 'interval' ] );

                // If the proxy option is set, do with Curl.
                if ( isset( $aArguments[ 'proxy' ] ) && $aArguments[ 'proxy' ] ) {
                    return $this->___getHTTPResponseByCurl( $sURL, $aArguments );
                }

                // Drop unsupported arguments
                $aArguments = array_intersect_key(
                    $aArguments,    // subject that its elements get extracted
                    $this->aArgumentStructure  // model to be compared with
                );

                if ( $this->getElement( $aArguments, 'body' ) || 'POST' === strtoupper( $aArguments[ 'method' ] ) ) {
                    return wp_remote_post( $sURL, $aArguments );
                }
                if ( 'HEAD' === strtoupper( $aArguments[ 'method' ] ) ) {
                    unset( $aArguments[ 'method' ] ); // not sure but if this is present, it causes 400 Bad Request
                    return wp_remote_head( $sURL, $aArguments );
                }
                return wp_remote_get( $sURL, $aArguments );

            }
                static private $___aRequestTimes = array();
                /**
                 * @param integer $iInterval In seconds.
                 * @since 4.3.4
                 */
                private function ___haveInterval( $iInterval ) {
                    $iInterval = ( integer ) $iInterval;
                    if ( ! $iInterval ) {
                        return;
                    }
                    self::$___aRequestTimes[ $this->sRequestType ] = isset( self::$___aRequestTimes[ $this->sRequestType ] ) ? self::$___aRequestTimes[ $this->sRequestType ] : time();
                    $_iElapsed = time() - self::$___aRequestTimes[ $this->sRequestType ];
                    if ( $_iElapsed > $iInterval  ) {
                        return;
                    }
                    $_iSleep = (integer) floor( $iInterval - $_iElapsed );
                    sleep( $_iSleep );
                    self::$___aRequestTimes[ $this->sRequestType ] = time();
                }

            /**
             * @param $sURL
             * @param array $aArguments
             * @param string $sMethod
             * @param null $sPostFields
             * @return array|WP_Error The response or WP_Error on failure.
             */
            private function ___getHTTPResponseByCurl( $sURL, array $aArguments, $sMethod='GET', $sPostFields=null  ) {

                $this->___aCurlHeader = array();
                $_oCurl               = curl_init();

                // Is proxy set?
                $_aProxy       = $this->getElementAsArray( $aArguments, array( 'proxy' ) );
                $_bProxySet    = ! empty( $_aProxy ) && isset( $_aProxy[ 'host' ] );

                // Curl settings
                $_sUserAgent   = $this->getElement( $aArguments, 'user-agent' );
                if ( ! empty( $_sUserAgent ) ) {
                    curl_setopt( $_oCurl, CURLOPT_USERAGENT, $_sUserAgent );
                }

                $_iTimeout     = $this->getElement( $aArguments, 'timeout' );
                if ( ! empty( $_iTimeout ) ) {
                    curl_setopt( $_oCurl, CURLOPT_TIMEOUT, $_iTimeout );
                }
                curl_setopt( $_oCurl, CURLOPT_CONNECTTIMEOUT, 30 );

                $_bSSLVerify   = ( boolean ) $this->getElement( $aArguments, 'sslverify' );
                curl_setopt( $_oCurl, CURLOPT_SSL_VERIFYPEER, $_bSSLVerify );

                curl_setopt( $_oCurl, CURLOPT_RETURNTRANSFER, true );

                /// Headers
                $_aHeaders  = $this->getElementAsArray( $aArguments, array( 'headers' ) );
                $_aHeaders[] = 'Content-Type: text/html; charset=' . get_bloginfo( 'charset' );
                $_aHeaders   = array_unique( $_aHeaders );
                curl_setopt( $_oCurl, CURLOPT_HTTPHEADER, $_aHeaders );
                curl_setopt( $_oCurl, CURLOPT_HEADERFUNCTION, array( $this, 'replyToGetCurlHeader' ) );
                curl_setopt( $_oCurl, CURLOPT_HEADER, false ); // whether to output the header contents in the response body

                // Encoding
                curl_setopt( $_oCurl, CURLOPT_ENCODING, 'identity' );

                if ( $_bProxySet ) {

                    $_aProxy = $_aProxy + array(
                        'host'      => null,
                        'port'      => null,
                        'username'  => null,
                        'password'  => null,
                    );
                    curl_setopt( $_oCurl, CURLOPT_PROXYTYPE, CURLPROXY_HTTP );
                    curl_setopt( $_oCurl, CURLOPT_PROXY, $_aProxy[ 'host' ] );
                    if ( $_aProxy[ 'port' ] ) {
                        curl_setopt( $_oCurl, CURLOPT_PROXYPORT, $_aProxy[ 'port' ] );
                    }

                    if ( $_aProxy[ 'username' ] && $_aProxy[ 'password' ] ) {
                        curl_setopt( $_oCurl, CURLOPT_PROXYAUTH, CURLAUTH_ANY );
                        curl_setopt( $_oCurl, CURLOPT_PROXYUSERPWD, $_aProxy[ 'username' ] . ':' . $_aProxy[ 'password' ] );
                    }

                }

                switch( $sMethod ) {
                    case 'POST':
                        curl_setopt( $_oCurl, CURLOPT_POST, true );
                        if ( ! empty( $sPostFields ) ) {
                            curl_setopt( $_oCurl, CURLOPT_POSTFIELDS, $sPostFields );
                        }
                    break;
                    case 'DELETE':
                        curl_setopt( $_oCurl, CURLOPT_CUSTOMREQUEST, 'DELETE' );
                        if ( ! empty( $sPostFields )) {
                            $sURL = "{$sURL}?{$sPostFields}";
                        }
                    break;
                }

                curl_setopt( $_oCurl, CURLOPT_URL, $sURL );
                $_bsResponse      = curl_exec( $_oCurl );
                $_sHTTPCode       = curl_getinfo( $_oCurl, CURLINFO_HTTP_CODE );
                $_aHTTPHeaders    = ( array ) curl_getinfo( $_oCurl ) + $this->___aCurlHeader;
                curl_close( $_oCurl );

                // Error handling
                if ( ! $_bsResponse ) {
                    $_aErrorData = array( 'url' => $sURL ) + $_aProxy;
                    $_sHTTPCode  = $_sHTTPCode ? $_sHTTPCode : 'CURL_CONNECTION_FAILURE';
                    return new WP_Error( $_sHTTPCode, sprintf( 'The cURL connection failed with a proxy: %1$s.', $_aProxy[ 'raw' ] ), $_aErrorData );
                }
                $_iResponseCode   = ( integer ) $_sHTTPCode;
                if ( $_iResponseCode >= 400 ) {
                    $_aErrorData = array( 'url' => $sURL ) + $_aProxy;
                    return new WP_Error( $_iResponseCode, sprintf( 'The cURL response returned an error with a proxy: %1$s.', $_aProxy[ 'raw' ] ), $_aErrorData );
                }

                // Format the response to be compatible with wp_remote_get().
                return array(
                    'headers'       => $_aHTTPHeaders,
                    'body'          => $_bsResponse,
                    'response'      => array(
                        'code'    => $_sHTTPCode,
                        'message' => false,
                    ),
                    'cookies'       => array(),
                    'http_response' => null,
                );

            }

            private $___aCurlHeader = array();
            /**
             * Get the header info to store.
             * @param   resource    $ch
             * @param   string      $sHeader
             * @return  integer
             */
            public function replyToGetCurlHeader( $ch, $sHeader ) {
                $_iPos = strpos( $sHeader, ':' );
                if ( ! empty( $_iPos ) ) {
                    $_sKey   = str_replace('-', '_', strtolower( substr( $sHeader, 0, $_iPos ) ) );
                    $_sValue = trim( substr( $sHeader, $_iPos + 2 ) );
                    $this->___aCurlHeader[ $_sKey ] = $_sValue;
                }
                return strlen( $sHeader );
            }

        /**
         *
         * @param       array|WP_Error  $aoHTTPResponse
         * @return      string
         */
        protected function ___getCharacterSetFromResponse( $aoHTTPResponse ) {
            return $this->___getCharacterSetFromHeader( wp_remote_retrieve_headers( $aoHTTPResponse )) ;
        }
            /**
             * Extracts character set from a given response header.
             *
             * @remark  The value set to the header charset should be case-insensitive.
             * @see     http://www.iana.org/assignments/character-sets/character-sets.xhtml
             * @param   array|string|Requests_Utility_CaseInsensitiveDictionary $asHeaderResponse
             * @return  string      The found character set. e.g. ISO-8859-1, utf-8, Shift_JIS
             * @since 4.3.4 Moved from AmazonAutoLinks_PluginUtility
             */
            private function ___getCharacterSetFromHeader( $asHeaderResponse ) {

                $_sContentType = '';
                if ( is_string( $asHeaderResponse ) ) {
                    $_sContentType = $asHeaderResponse;
                }
                // It should be an array then.
                else if ( isset( $asHeaderResponse[ 'content-type' ] ) ) {
                    $_sContentType = $asHeaderResponse[ 'content-type' ];
                }
                else {
                    foreach( $asHeaderResponse as $_iIndex => $_sHeaderElement ) {
                        if ( ! is_scalar( $_sHeaderElement ) ) {    // [4.2.0] with a proxy, there is a case that this element is an array
                            continue;
                        }
                        if ( false !== stripos( $_sHeaderElement, 'charset=' ) ) {
                            $_sContentType = $asHeaderResponse[ $_iIndex ];
                        }
                    }
                }

                preg_match('/charset=(.+?)($|[;\s])/i', $_sContentType, $_aMatches );
                return isset( $_aMatches[ 1 ] )
                    ? ( string ) $_aMatches[ 1 ]
                    : '';

            }

        /**
         * Sets a cache.
         * It internally sets a cache name.
         * @remark      To renew its cache, use the `deleteCache()` method prior to call the `get()` method.
         * @remark      The cache duration here is for the value set to the database `expiration_time` column value.
         * However, the default behavior of this plugin suppresses the set value in the database with the passed cache duration argument value.
         * @param string $sURL
         * @param string $sCacheName
         * @param array|WP_Error $aoResponse
         * @param int $iCacheDuration
         * @param array $aArguments
         * @param array $aOldCache
         * @param string $sRequestType
         * @todo        Examine the return value as it is not tested.
         * @since       3.7.5   Added the `aal_filter_http_request_set_cache` filter so that third parties can modify set cache contents.
         * @since       3.7.7   deprecated the `aal_filter_http_request_set_cache` filter and introduced `aal_filter_http_request_set_cache_{request type}`.
         * @since       3.9.0   Added the `aal_filter_http_request_set_cache_duration_{cache name}` filter to give a chance to change the cache duration, depending on the cached data content, checked in the above filter hook callbacks. This is useful when an error is returned which should not be kept so long.
         * @since       3.9.2   Added $sRequestType to be passed to `aal_filter_http_request_set_cache_duration_{cache name}`.
         * @since       4.2.0   Removed the return value as it is not used anywhere
         * @since       4.2.0   Revived the `aal_filter_http_request_set_cache` filter for requests with proxies and they are failed.
         * @since       4.3.0   Added the `$aOldCache` parameter and made it passed to the `aal_filter_http_request_set_cache` filter.
         * @since       4.3.4   Added the `$sCacheName` and `$sRequestType` parameters.
         */
        private function ___setCacheInDatabase( $sURL, $sCacheName, $aoResponse, $iCacheDuration, $aArguments, array $aOldCache, $sRequestType ) {

            $_sCharSet       = $this->___getCharacterSetFromResponse( $aoResponse );
            $aoResponse      = apply_filters( "aal_filter_http_request_set_cache_{$sRequestType}", $aoResponse, $sCacheName, $_sCharSet, $iCacheDuration, $sURL );
            $aoResponse      = apply_filters( 'aal_filter_http_request_set_cache', $aoResponse, $sCacheName, $_sCharSet, $iCacheDuration, $sURL, $aArguments, $aOldCache );
            $iCacheDuration  = apply_filters( 'aal_filter_http_request_set_cache_duration_' . $sCacheName, $iCacheDuration, $sCacheName, $sURL, $sRequestType );

            // [4.2.0] If the cache duration is 0, do not set a cache. This is important when a request is made with a proxy and it has an errors.
            if ( ! $iCacheDuration ) {
                return;
            }

            $aoResponse      = $this->___getCacheCompressed( $aoResponse );  // [3.7.6]
            $_oCacheTable    = new AmazonAutoLinks_DatabaseTable_aal_request_cache;
            $_bResult        = $_oCacheTable->setCache(
                $sCacheName, // name
                $aoResponse,
                $iCacheDuration,
                array( // extra column items
                    'request_uri' => $sURL,
                    'type'        => $sRequestType,
                    'charset'     => $_sCharSet,
                )
            );
            if ( $_bResult ) {
                do_action( 'aal_action_set_http_request_cache', $sCacheName, $sURL, $aoResponse, $iCacheDuration, $_sCharSet );
            }

        }
            /**
             * @param  array|WP_Error $aoResponse
             * @return mixed
             * @since  3.7.6
             */
            private function ___getCacheCompressed( $aoResponse ) {

                if ( is_wp_error( $aoResponse ) ) {
                    return $aoResponse;
                }

                // this cause the data to be excessive large
                unset( $aoResponse[ 'http_response' ] );

                /**
                 * gz compress
                 * @deprecated 3.7.6b01  Causes unexpected errors when the cache is not properly set for some reasons like exceeding max_allowed_packet or max_execution_time
                 * @since   3.12.0  Re-added
                 */
                if ( $this->aArguments[ 'compress_cache' ] ) {
                    $_bsCompressed = gzcompress( $aoResponse[ 'body' ] );
                    if ( $_bsCompressed ) {
                        $aoResponse[ 'body' ] = $_bsCompressed ;
                    }
                }

                return $aoResponse;

            }

    /**
     * @param  array   $aArguments
     * @param  array|string  $asURLs    The reason that accepts an array is that an extended class accepts it.
     * @return array
     */
    protected function _getArgumentsFormatted( array $aArguments, $asURLs ) {

        $aArguments     = $this->getAsArray( $aArguments )
            + $this->aArguments
            + $this->aArgumentStructure
            + $this->aCustomArguments;

        // [4.3.4] Skips formatting. This is used in the multiple mode.
        if ( $aArguments[ 'skip_argument_format' ] ) {
            return $aArguments;
        }

        // WordPress v3.7 or later, it should be true.
        $aArguments[ 'sslverify' ] = version_compare( $GLOBALS[ 'wp_version' ], '3.7', '>=' );

        // Drop unsupported arguments
        // @deprecated 4.3.4 Support custom arguments. Can be used to pass data to the background routine.
//        $aArguments = array_intersect_key(
//            $aArguments,    // subject that its elements get extracted
//            $this->aArguments + $this->aArgumentStructure + $this->aCustomArguments    // model to be compared with
//        );

        // [4.2.0]
        $aArguments[ 'user-agent' ] = $aArguments[ 'user-agent' ]
            ? $aArguments[ 'user-agent' ]
            : 'WordPress/' . $GLOBALS[ 'wp_version' ];

        // [4.3.4]
        if ( ! empty( $aArguments[ 'body' ] ) ) {
            $aArguments[ 'method' ] = 'POST';
        }

        $aArguments = apply_filters(
            'aal_filter_http_request_arguments',
            $aArguments,
            $this->sRequestType,
            $asURLs     // 4.2.0
        );

        // [4.0.0] If the gzcompress function is not available, disable the argument
        if ( ! function_exists( 'gzcompress' )  ) {
            $aArguments[ 'compress_cache' ] = false;
        }

        // [4.3.4] If the body is an array sort by key. This is important to generate identical cache names
        if ( is_array( $aArguments[ 'body' ] ) ) {
            ksort( $aArguments[ 'body' ] );
        }

        // [4.3.4]
        if ( isset( $aArguments[ 'raw' ] ) ) {
            trigger_error( "The 'raw' argument has been deprecated. Use 'getRaw()' instead.", E_USER_WARNING );
        }
        return $aArguments;
    }

    /**
     * Generates a cache name from the given url.
     * @param  string  $sURL
     * @param  array   $aArguments
     * @param  string  $sRequestType
     * @return string
     * @since  3
     * @since  4.3.4   Added the `$aArguments` parameter.
     */
    protected function _getCacheName( $sURL, array $aArguments, $sRequestType ) {

        // [3.9.0] For the POST method and the `body` argument is present, use it to identify the request.
        $_sBody = '';
        if ( $aArguments[ 'body' ] ) {
            $_sBody = maybe_serialize( $aArguments[ 'body' ] );
        }
        $_sMethod = '';
        if ( 'HEAD' === strtoupper( $aArguments[ 'method' ] ) ) {
            $_sMethod = 'HEAD';
        }

        $_sCacheName = AmazonAutoLinks_Registry::TRANSIENT_PREFIX . '_' . md5( $sURL . $_sBody . $_sMethod );
        return apply_filters(
            'aal_filter_http_request_cache_name',
            $_sCacheName,
            $sURL,
            $sRequestType,
            $aArguments   // 3.9.0
        );

    }

    /**
     * Checks whether the response is a cache.
     * @return boolean
     * @since  4.3.4
     */
    public function isCacheUsed() {
        return ! empty( $this->___aCache );
    }

    /**
     * @return bool
     * @since  4.3.4
     */
    public function hasCache() {
        $this->___aCache = $this->___getCacheFromDatabase( $this->sCacheName, $this->iCacheDuration, $this->___aCache, $this->aArguments );
        if ( empty( $this->___aCache ) ) {
            return false;
        }
        return ! $this->___isCacheExpired( $this->___aCache );
    }

    /**
     * @return array
     * @since  4.3.4
     */
    public function getResponseFromCache() {
        $this->___aCache = $this->___getCacheFromDatabase( $this->sCacheName, $this->iCacheDuration, $this->___aCache, $this->aArguments );
        return $this->___getHTTPResponseFromCache( $this->___aCache, $this->sCacheName, $this->iCacheDuration, $this->aArguments, $this->sRequestType );
    }

    /**
     * Returns a response status code such as 404, 200 etc.
     * @remark Assumes to be called after performing get(), getBody(), getRawResponse(), or getResponse().
     * @return integer HTTP status code such as 404, 200. 0 for WP_Error.
     * @since  4.3.4
     */
    public function getStatusCode() {
        $_aoResponse = $this->getRawResponse();
        return is_wp_error( $_aoResponse )
            ? 0
            : ( integer ) $this->getElement( ( array ) $_aoResponse, array( 'response', 'code' ) );
    }

    /**
     * @return string
     * @since  4.3.4
     */
    public function getStatusMessage() {
        return $this->getElement( $this->getRawResponse(), array( 'response', 'message' ) );
    }

    /**
     * @return WP_Http_Cookie[]
     * @since  4.3.4
     */
    public function getCookies() {
        return $this->getRequestCookiesFromResponse( $this->getRawResponse() );
    }

    /**
     * Returns cookies to parse.
     * Meant be used for parsing or displaying.
     * @remark The structure is different from the request/response cookies.
     * @return array
     * @since 4.3.4
     */
    public function getCookiesParsable() {
        return $this->getCookiesToParseFromResponse( $this->getRawResponse() );
    }

}