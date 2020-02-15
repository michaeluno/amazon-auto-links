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
 * @since       3
 * @deprecated  Not used anymore and has some critical errors.
 */
class AmazonAutoLinks_HTTPClient_FileGetContents extends AmazonAutoLinks_HTTPClient_Base {
       
    /**
     * Sets up properties.
     */
    public function __construct( $sURL, $iCacheDuration=86400, $aArguments=array() ) {
        
        parent::__construct( $sURL, $iCacheDuration, $aArguments );
        $this->sCacheName .= '_1';
        
    }

    /**
     * Fetches HTML body with the specified URL with caching functionality.
     * 
     * @remark      Handles character encoding conversion.
     * @return      string
     */
    public function get() {
        
        // file_get_contents() will return the response body by default.
        $_sHTTPBody    = $this->getResponses();
        $_sCharSetFrom = $this->sLastCharSet;
        $_sCharSetTo   = $this->sSiteCharSet;
        if ( $_sCharSetFrom && ( strtoupper( $_sCharSetTo ) <> strtoupper( $_sCharSetFrom ) ) ) {
            $_sHTTPBody = $this->convertCharacterEncoding(
                $_sHTTPBody,
                $_sCharSetTo,  // to
                $_sCharSetFrom, // from
                false // no html-entities conversion
            );
        }
        return $_sHTTPBody;
        
    }
        /**
         * 
         * @return      object|array        Response array or WP Error object.
         * @deprecated  3.5.4
         */
        protected function ____getHTTPResponseWithCache( $sURL, $aArguments=array(), $iCacheDuration=86400 ) {
            
            $_oCacheTable = new AmazonAutoLinks_DatabaseTable_aal_request_cache;
            
            // If a cache exists, use it.
            $_aData        = 0 === $iCacheDuration
                ? array()
                : $_oCacheTable->getCache(  
                    $this->sCacheName // name
                );
            $_aData = $_aData + array( // structure
                'remained_time' => 0,
                'charset'       => null,
                'data'          => null,
            );
            if ( $_aData[ 'remained_time' ] && $_aData[ 'data' ] ) {
                $this->sLastCharSet = $_aData[ 'charset' ];
                return $_aData[ 'data' ];
            }
            
            // @todo maybe implement a mechanism that fetches data in the background 
            // and return the stored data anyway.
            
            // Otherwise, retrieve a data from a remote server and set a cache.            
            $_asResponse        = file_get_contents( $sURL );
            $this->sLastCharSet = $this->getCharacterSetFromResponseHeader( 
                $http_response_header 
            );              
            
            $_oCacheTable->setCache( 
                $_sCacheName, // name
                $_asResponse,
                ( integer ) $iCacheDuration, // cache life span
                array( // extra column items
                    'request_uri' => $sURL,
                    'type'        => 'file_get_contents',
                    'charset'     => $this->sLastCharSet,
                )
            );               
            return $_asResponse;
            
        }
            
    /**
     *
     * @remark      this does not set cache
     */
    protected function _getHTTPResponse( $sURL, array $aArguments ) {
        return file_get_contents( $sURL );
    }
           
    
}