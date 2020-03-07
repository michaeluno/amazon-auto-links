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
 * Reads, loads and saves HTML documents.
 * 
 * It has a caching system built-in.
 * 
 * It handles auto-encoding of the document from the source character set to the site character set.
 * 
 * @since       3       
 * @filter      apply       aal_filter_http_response_cache
 */
abstract class AmazonAutoLinks_HTTPClient_Base extends AmazonAutoLinks_PluginUtility {
    
    /**
     * Stores the request type.
     * 
     * Change this property to mark a cache item in the database.
     */
    public $sRequestType = 'wp_remote_get';
    
    /**
     * Indicates whether processing a single url or not.
     */
    public $bIsSingle;
     
    
    public $iCacheDuration = 86400;
    
    /**
     * Stores the charset for a last performed HTTP request.
     */
    public $sLastCharSet = '';
    
    /**
     * Stores the site character set.
     */
    public $sSiteCharSet = '';
    
    /**
     * Stores processing urls.
     */ 
    public $aURLs = array();
    
    /**
     * HTTP request, wp_remote_get() arguments.
     * @see WP_Http::request()
     */
    public $aArguments =  array(
        'timeout'     => 5,
        'redirection' => 5,
        'httpversion' => '1.0',
        'user-agent'  => null,
        'blocking'    => true,
        'headers'     => array(),
        'cookies'     => array(),
        'body'        => null,
        'compress'    => false, // does not seem to take effect
        'decompress'  => true,
        'sslverify'   => true,
        'stream'      => false,
        'filename'    => null,
        'method'      => null,  // 3.9.0
    ); 

    /**
     * Specific arguments to this class.
     */
    public $aCustomArguments = array(
        'raw'                    => false,   // (boolean) return the raw HTTP response
        'constructor_parameters' => array(),
        'api_parameters'         => array(),
        'compress_cache'         => false,    // 4.0.0+ (boolean) whether to compress cache data
    );

    /**
     * Sets up properties.
     */
    public function __construct( $asURLs, $iCacheDuration=86400, $aArguments=null, $sRequestType='wp_remote_get' ) {
                        
        $this->bIsSingle      = is_string( $asURLs );
        $this->iCacheDuration = $iCacheDuration;
        $this->sSiteCharSet   = get_bloginfo( 'charset' );
        $this->sRequestType   = $sRequestType;
        $this->aArguments     = $this->_getFormattedArguments( $aArguments );
        $this->aURLs          = $this->_getFormattedURLContainer( 
            $this->getAsArray( $asURLs ) 
        );

    }      
        /**
         * @return      array
         */
        private function _getFormattedArguments( $aArguments ) {
            $aArguments     = null === $aArguments
                ? $this->aArguments
                : $this->getAsArray( $aArguments ) + $this->aArguments;
            $aArguments     = $aArguments + $this->aCustomArguments;

            // @deprecated 3.7.4 Use the WordPress default user agent.
            // $aArguments[ 'user-agent' ] = 'Amazon Auto Links/' . AmazonAutoLinks_Registry::VERSION . '; ' . get_bloginfo( 'url' );

            // WordPress v3.7 or later, it should be true.
            $aArguments[ 'sslverify' ] = version_compare( $GLOBALS[ 'wp_version' ], '3.7', '>=' );

            // Drop unsupported arguments
            $aArguments = array_intersect_key(
                $aArguments,    // subject that its elements get extracted
                $this->aArguments + $this->aCustomArguments    // model to be compared with
            );

            $aArguments = apply_filters( 'aal_filter_http_request_arguments', $aArguments, $this->sRequestType );

            // 4.0.0+ If the gzcompress function is not available, disable the argument
            if ( ! function_exists( 'gzcompress' )  ) {
                $aArguments[ 'compress_cache' ] = false;
            }
            return $aArguments;
        }
        /**
         * 
         * @return      array       The formatted array.
         */
        private function _getFormattedURLContainer( $aURLs ) {
            $_aFormatted = array();
            foreach( $aURLs as $_sURL ) {
                $_sURL = trim( $_sURL );
                // Set the key to the cache name
                $_aFormatted[ $this->_getCacheName( $_sURL ) ] = $_sURL;
            }
            return $_aFormatted;            
        }
            /**
             * Generates a cache name from the given url.
             * @return      string
             */
            protected function _getCacheName( $sURL ) {

                // @since   3.9.0   For the POST method and the `body` argument is present, use it to identify the request.
                $_sBody = '';
                if ( isset( $this->aArguments[ 'body' ] ) && $this->aArguments[ 'body' ] ) {
                    $_sBody = maybe_serialize( $this->aArguments[ 'body' ] );
                }

                $_sCacheName = AmazonAutoLinks_Registry::TRANSIENT_PREFIX
                    . '_' 
                    . md5( $sURL . $_sBody );

                return apply_filters(
                    'aal_filter_http_request_cache_name',
                    $_sCacheName,
                    $sURL,
                    $this->sRequestType,
                    $this->aArguments   // 3.9.0
                );
            }
            
    /**
     * Returns HTTP body(s).
     * 
     * @remark      Handles character encoding conversion.
     * @return      string|array
     */
    public function get() {
        
        $_aData      = array();
        foreach( $this->getResponses() as $_sURL => $_aoResponse ) {

            $_asHTTPResponse    = $this->_getResponseItem( $_aoResponse );
            $_sCharSetFrom      = $this->_getCharacterSet( $_aoResponse );
            $_sCharSetTo        = $this->sSiteCharSet;

            // Encode the document from the source character set to the site character set.
            if ( $_sCharSetFrom && ( strtoupper( $_sCharSetTo ) <> strtoupper( $_sCharSetFrom ) ) ) {
                $_asHTTPResponse = $this->convertCharacterEncoding(
                    $_asHTTPResponse,
                    $_sCharSetTo,  // to
                    $_sCharSetFrom, // from
                    false // no html-entities conversion
                );
            }
            $_aData[ $_sURL ] = $_asHTTPResponse;
            
            if ( $this->bIsSingle ) {
                return $_aData[ $_sURL ];
            }
        }
        return $_aData;
        
    }
        /**
         * @return      string|array
         */
        private function _getResponseItem( $aoResponse ) {

            if ( $this->aArguments[ 'raw' ] ) {
                return $aoResponse;
            }

            if ( is_wp_error( $aoResponse ) ) {
                return $aoResponse->get_error_message();
            }

            return wp_remote_retrieve_body( $aoResponse );

        }
    /**
     * Returns the response's character set by the url.
     * 
     * @remark      This should be used after performing getResponses().
     * @since       3
     * @return      string
     * @param       string      $sURLOrCacheName    If specified, it checks the character set from the cache.
     */
    public function getCharacterSet( $sURLOrCacheName='' ) {
        if ( ! $sURLOrCacheName ) {
            return $this->sLastCharSet;
        }
        $_sCacheName = filter_var( $sURLOrCacheName, FILTER_VALIDATE_URL )
            ? $this->_getCacheName( $sURLOrCacheName )
            : $sURLOrCacheName;
        return $this->___getCharacterSetFromCache( $_sCacheName );
    }
        /**
         * 
         * @return      string
         */
        private function ___getCharacterSetFromCache( $sCacheName ) {
            $_oCacheTable = new AmazonAutoLinks_DatabaseTable_aal_request_cache;
            $_aRow        = $_oCacheTable->getCache( $sCacheName ); // single item returns a single row
            return isset( $_aRow[ 'charset' ] ) 
                ? $_aRow[ 'charset' ]
                : null;
        }

    /**
     * Returns raw HTTP response.
     * 
     * @return      array|object        An array holding response arrays or WP Error object.
     */
    public function getResponses() {
        return $this->_getHTTPResponseWithCache( 
            $this->aURLs, 
            $this->aArguments, 
            $this->iCacheDuration
        );        
    }    
        /**
         *
         * @param       array       $aURLs      The URLs to be requested. The array keys must be indexed with cache names.
         * @return      object|array        Response array or WP Error object.
         */
        protected function _getHTTPResponseWithCache( array $aURLs, $aArguments=array(), $iCacheDuration=86400 ) {

            $_aData        = array();
            $_aValidCaches = array();

            // If a cache exists, use it.
            $_oCacheTable  = new AmazonAutoLinks_DatabaseTable_aal_request_cache;
            $_aCaches      = 0 === $iCacheDuration
                ? array()
                : $_oCacheTable->getCache(  
                    array_keys( $aURLs ), // multiple names - the url array is indexed with cache names
                    $iCacheDuration
                );     

            foreach( $_aCaches as $_sCacheName => $_aCache ) {
                
                // Format
                $_aCache = $_aCache + array( // structure
                    'remained_time' => 0,
                    'charset'       => null,
                    'data'          => null,
                    'request_uri'   => null,
                    'name'          => $_sCacheName,
                );
                
                if ( ! isset( $_aCache[ 'data' ] ) ) {
                    continue;
                }

                if ( ! is_wp_error( $_aCache[ 'data' ] ) ) {

                    // 4.0.0+ There is a reported case that the `data` element is neither an array nor null.
                    // @todo the reason of corrupt data is unknown. It could be that the compressed data can be treated as a string when the WordPress core sanitizes the data when retrieving from the database table.
                    if ( ! is_array( $_aCache[ 'data' ] ) ) {
                        do_action( 'aal_action_error_http_request_cache_data', 'The cache data is corrupted.', $_aCache );
                        continue;
                    }

                    /**
                     * @since      3.7.6b01  Deprecated as this seemed to cause unexpected errors when the cache is not properly set for some reasons like exceeding max_allowed_packet or max_execution_time
                     * @since      3.12.0    Re-added
                     */
                    $_bsUncompressed = function_exists( 'gzuncompress' )
                        ? @gzuncompress( $this->getElement( $_aCache, array( 'data', 'body' ), '' ) )    // returns string|false
                        : false;
                    if ( $_bsUncompressed ) {
                        $_aCache[ 'data' ][ 'body' ] = $_bsUncompressed;
                    }
                    unset( $_aCache[ 'data' ][ 'http_response' ] );

                }

                /**
                 * Filters - this allows external components to modify the remained time,
                 * which can be used to trick the below check and return the stored data anyway.
                 * So the cache renewal event can be scheduled in the background.
                 */
                $_aCache = apply_filters(
                    'aal_filter_http_response_cache',
                    $_aCache,
                    $iCacheDuration,
                    $aArguments,
                    $this->sRequestType
                );

                // Set a valid item.
                if ( ! $this->___isCacheExpired( $_aCache ) ) {
                    $this->sLastCharSet = $_aCache[ 'charset' ];
                    $_aValidCaches[ $_sCacheName ] = $_aCache[ 'data' ];
                }
                
            }
            
            // Check if caches exist one by one and if not, get the response and set a cache.
            foreach( $aURLs as $_sCacheName => $_sURL ) {
                
                if ( isset( $_aValidCaches[ $_sCacheName ] ) ) {
                    $_aData[ $_sURL ] = $_aValidCaches[ $_sCacheName ];
                    continue;
                }
                                
                // Perform an HTTP request.
                $_aData[ $_sURL ] = $this->_getHTTPResponse( $_sURL, $aArguments );
                $this->___setCache( $_sURL, $_aData[ $_sURL ], $iCacheDuration );
            }
            return $_aData;
            
        }
            /**
             * @since       3.5.0
             * @return      boolean
             */
            private function ___isCacheExpired( array $aCache ) {

                if ( empty( $aCache[ 'data' ] ) && 0 !== $aCache[ 'data' ] ) {
                    return true;
                }
                return $aCache[ 'remained_time' ] <= 0;

            }

        /**
         * 
         * @remark      this does not set cache
         */
        protected function _getHTTPResponse( $sURL, array $aArguments ) {
            do_action( 'aal_action_http_remote_get', $sURL, $aArguments, $this->sRequestType ); // 3.5.0
            return function_exists( 'wp_safe_remote_get' )
                ? wp_safe_remote_get( $sURL, $aArguments )
                : wp_remote_get( $sURL, $aArguments );
        }
        /**
         * 
         * @return      string
         */
        protected function _getCharacterSet( $aHTTPResponseBody ) {
            return $this->getCharacterSetFromResponseHeader(
                wp_remote_retrieve_headers( $aHTTPResponseBody ) 
            );
        }
        /**
         * Sets a cache by url.
         * It internally sets a cache name.
         * @remark      To renew its cache, use the `deleteCache()` method prior to call the `get()` method.
         * @return      boolean     
         * @todo        Examine the return value as it is not tested.
         * @since       3.7.5   Added the `aal_filter_http_request_set_cache` filter so that third parties can modify set cache contents.
         * @since       3.7.7   deprecated the `aal_filter_http_request_set_cache` filter and introduced `aal_filter_http_request_set_cache_{request type}`.
         */
        private function ___setCache( $sURL, $mData, $iCacheDuration=86400 ) {
            
            $_sCharSet       = $this->_getCharacterSet( $mData );
            $_sCacheName     = $this->_getCacheName( $sURL );
            $_oCacheTable    = new AmazonAutoLinks_DatabaseTable_aal_request_cache;
            $mData           = apply_filters(
                'aal_filter_http_request_set_cache_' . $this->sRequestType,
                $mData,
                $_sCacheName,
                $_sCharSet,
                $iCacheDuration,
                $sURL   // 3.9.0
            );
            // 3.9.0 - gives a chance to change the cache duration, depending on the cached data content, checked in the above filter hook callbacks
            // this is useful when an error is returned which should not be kept so long
            $iCacheDuration  = apply_filters(
                'aal_filter_http_request_set_cache_duration_' . $_sCacheName,
                $iCacheDuration,
                $_sCacheName,
                $sURL,
                $this->sRequestType // 3.9.2
            );
            $mData           = $this->___getCacheCompressed( $mData );  // 3.7.6+
            $_bResult        = $_oCacheTable->setCache(
                $_sCacheName, // name
                $mData,
                $iCacheDuration,
                array( // extra column items
                    'request_uri' => $sURL,
                    'type'        => $this->sRequestType,
                    'charset'     => $_sCharSet,
                )
            );        
            $this->sLastCharSet = $_sCharSet;
            if ( $_bResult ) {
                do_action( 'aal_action_set_http_request_cache', $_sCacheName, $sURL, $mData, $iCacheDuration, $_sCharSet );
            }
            return $_bResult;
            
        }
            /**
             * @param $mData
             *
             * @return mixed
             * @since   3.7.6
             */
            private function ___getCacheCompressed( $mData ) {

                if ( is_wp_error( $mData ) ) {
                    return $mData;
                }

                // this cause the data to be excessive large
                unset( $mData[ 'http_response' ] );

                /**
                 * gz compress
                 * @deprecated 3.7.6b01  Causes unexpected errors when the cache is not properly set for some reasons like exceeding max_allowed_packet or max_execution_time
                 * @since   3.12.0  Re-added
                 */
                if ( $this->aArguments[ 'compress_cache' ] ) {
                    $_bsCompressed = gzcompress( $mData[ 'body' ] );
                    if ( $_bsCompressed ) {
                        $mData[ 'body' ] = $_bsCompressed ;
                    }
                }

                return $mData;

            }

    /**
     * Deletes the cache of the provided URL.
     *
     */
    public function deleteCache() {
        
        $_oCacheTable = new AmazonAutoLinks_DatabaseTable_aal_request_cache;

        // 3.7.6+ If multiple URLs are set, they will be deleted at once
        $_oCacheTable->deleteCache( array_keys( $this->aURLs ) );

    }
    
}