<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2017 Michael Uno
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
     */
    public $aArguments =  array(
        'timeout'     => 5,
        'redirection' => 5,
        'httpversion' => '1.0',
        'user-agent'  => 'Amazon Auto Links',
        'blocking'    => true,
        'headers'     => array(),
        'cookies'     => array(),
        'body'        => null,
        'compress'    => false,
        'decompress'  => true,
        'sslverify'   => true,
        'stream'      => false,
        'filename'    => null
    ); 

    /**
     * Sets up properties.
     */
    public function __construct( $asURLs, $iCacheDuration=86400, $aArguments=null, $sRequestType='wp_remote_get' ) {
                        
        $this->bIsSingle      = is_string( $asURLs );
        $this->aURLs          = $this->_getFormattedURLContainer( 
            $this->getAsArray( $asURLs ) 
        );
        $this->iCacheDuration = $iCacheDuration;
        $this->aArguments     = $this->_getFormattedArguments( $aArguments );
        $this->sSiteCharSet   = get_bloginfo( 'charset' );
        $this->sRequestType   = $sRequestType;
        
        
    }      
        /**
         * @return      array
         */
        private function _getFormattedArguments( $aArguments ) {
            $aArguments     = null === $aArguments
                ? $this->aArguments
                : $this->getAsArray( $aArguments ) + $this->aArguments;
                
            $aArguments[ 'user-agent' ] = 'Amazon Auto Links/' . AmazonAutoLinks_Registry::VERSION . '; ' . get_bloginfo( 'url' );
            
            //  3.7  or later, it should be true.
            $aArguments[ 'sslverify' ] = version_compare( $GLOBALS[ 'wp_version' ], '3.7', '>=' );
            
            return apply_filters(
                'aal_filter_http_request_arguments',
                $aArguments
            );
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
                $_sCacheName = AmazonAutoLinks_Registry::TRANSIENT_PREFIX
                    . '_' 
                    . md5( $sURL );
                return apply_filters( 'aal_filter_http_request_cache_name', $_sCacheName, $sURL, $this->sRequestType );
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
         
            $_sHTTPBody         = is_wp_error( $_aoResponse )
                ? $_aoResponse->get_error_message()
                : wp_remote_retrieve_body( $_aoResponse );         
            // $_sHTTPBody         = wp_remote_retrieve_body( $_aResponse );
            $_sCharSetFrom      = $this->getCharacterSet( $_sURL );
            $_sCharSetTo        = $this->sSiteCharSet;

            // Encode the document from the source character set to the site character set.
            if ( $_sCharSetFrom && ( strtoupper( $_sCharSetTo ) <> strtoupper( $_sCharSetFrom ) ) ) {
                $_sHTTPBody = $this->convertCharacterEncoding(
                    $_sHTTPBody,
                    $_sCharSetTo,  // to
                    $_sCharSetFrom, // from
                    false // no html-entities conversion
                );
            }
            $_aData[ $_sURL ] = $_sHTTPBody;
            
            if ( $this->bIsSingle ) {
                return $_aData[ $_sURL ];
            }
        }
        return $_aData;
        
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
        return $this->_getCharacterSetFromCache( $_sCacheName );
    }
        /**
         * 
         * @return      string
         */
        private function _getCharacterSetFromCache( $sCacheName ) {
            
            $_oCacheTable = new AmazonAutoLinks_DatabaseTable_request_cache(
                AmazonAutoLinks_Registry::$aDatabaseTables[ 'request_cache' ]
            );             
            $_aRow = $_oCacheTable->getCache( 
                $sCacheName // single item returns a single row
            ); 
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
         * @return      object|array        Response array or WP Error object.
         */
        protected function _getHTTPResponseWithCache( array $aURLs, $aArguments=array(), $iCacheDuration=86400 ) {

            $_aData        = array();
            $_aValidCaches = array();
            
            // First retrieve the cache
            $_oCacheTable = new AmazonAutoLinks_DatabaseTable_request_cache(
                AmazonAutoLinks_Registry::$aDatabaseTables[ 'request_cache' ]
            ); 
            
            // If a cache exists, use it.
            $_aCaches = 0 === $iCacheDuration
                ? array()
                : $_oCacheTable->getCache(  
                    array_keys( $aURLs ), // multiple names - the url array is indexed with cache names
                    $iCacheDuration
                );     

            foreach( $_aCaches as $_aCache ) {
                
                // Format
                $_aCache = $_aCache + array( // structure
                    'remained_time' => 0,
                    'charset'       => null,
                    'data'          => null,
                    'request_uri'   => null,
                    'name'          => null,
                );
                
                if ( ! isset( $_aCache[ 'data' ] ) ) {
                    continue;
                }
                
                // Filters - this allows external components to modify the remained time, 
                // which can be used to trick the below check and return the stored data anyway.
                // So the cache renewal event can be scheduled in the background.
                $_aCache = apply_filters(
                    'aal_filter_http_response_cache',
                    $_aCache,
                    $iCacheDuration,
                    $aArguments,
                    $this->sRequestType
                );
                
                // Set a valid item.
                if ( $_aCache[ 'remained_time' ] && $_aCache[ 'data' ] ) {
                    $this->sLastCharSet = $_aCache[ 'charset' ];
                    $_aValidCaches[ $_aCache[ 'request_uri' ] ] = $_aCache[ 'data' ];
                }
                
            }
            
            // Check if caches exist one by one and if not, get the response and set a cache.
            foreach( $aURLs as $_sURL ) {
                
                if ( isset( $_aValidCaches[ $_sURL ] ) ) {
                    $_aData[ $_sURL ] = $_aValidCaches[ $_sURL ];
                    continue;
                }
                                
                // Perform an HTTP request.
                $_aData[ $_sURL ] = $this->_getHTTPResponse( 
                    $_sURL, 
                    $aArguments 
                );
                $this->_setCache( 
                    $_sURL,
                    $_aData[ $_sURL ], 
                    $iCacheDuration 
                );
            }
            return $_aData;
            
        }

        /**
         * 
         * @remark      this does not set cache
         */
        protected function _getHTTPResponse( $sURL, array $aArguments ) {
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
         * @return      boolean     
         * @todo        Examine the return value as it is not tested.
         */
        private function _setCache( $sURL, $mData, $iCacheDuration=86400 ) {

            $_sCharSet    = $this->_getCharacterSet( $mData );            
            $_oCacheTable = new AmazonAutoLinks_DatabaseTable_request_cache(
                AmazonAutoLinks_Registry::$aDatabaseTables[ 'request_cache' ]
            );
            // when 0 is passed, use a default value of 86400 (one day). So pass 0 to renew the cache.
            $iCacheDuration = $iCacheDuration ? ( integer ) $iCacheDuration : 86400;
            $_bResult = $_oCacheTable->setCache( 
                $this->_getCacheName( $sURL ), // name
                $mData,
                $iCacheDuration,
                array( // extra column items
                    'request_uri' => $sURL,
                    'type'        => $this->sRequestType,
                    'charset'     => $_sCharSet,
                )
            );        
            $this->sLastCharSet = $_sCharSet;
            return $_bResult;
            
        }
            
            
            
    /**
     * Deletes the cache of the provided URL.
     */
    public function deleteCache() {
        
        $_oCacheTable = new AmazonAutoLinks_DatabaseTable_request_cache(
            AmazonAutoLinks_Registry::$aDatabaseTables[ 'request_cache' ]
        );
        foreach( $this->aURLs as $_sCacheName => $_sURL ) {            
// @todo implement deleteCaches() method
            $_oCacheTable->deleteCache(
                $_sCacheName
            );    
        }
        
    }
    
}