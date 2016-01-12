<?php
/**
 * Amazon Auto Links
 * 
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2016 Michael Uno
 * 
 */

/**
 * Deals with Amazon Product Advertising API.
 * 
 * @action      schedule    aal_action_api_transient_renewal
 * @since       3           Changed the name from `AmazonAutoLinks_APIRequestTransient`.
 */ 
abstract class AmazonAutoLinks_ProductAdvertisingAPI_Base {

    protected $aParams = array();
    protected static $aMandatoryParameters = array(
        'Service'      => 'AWSECommerceService',
        // a dummy Amazon Associate tag - required as the API returns an error without it.
        'AssociateTag' => 'amazon-auto-links-20',  
    );
    
    /**
     * Sets up hooks and properties.
     */
    function __construct() {
        
        // Schedule the transient update task.
        add_action( 'shutdown', array( $this, '_replyToScheduleUpdatingCaches' ) );
        
        $this->oEncrypt       = new AmazonAutoLinks_Encrypt;
        
        $this->bUseCacheTable = $this->_checkCacheTableExists();
        
        $GLOBALS[ 'aAmazonAutoLinks_APIRequestURIs' ] = isset( $GLOBALS[ 'aAmazonAutoLinks_APIRequestURIs' ] )
            ? $GLOBALS[ 'aAmazonAutoLinks_APIRequestURIs' ]
            : array();
        
    }
        /**
         * @return      boolean
         * @since       3
         */
        private function _checkCacheTableExists() {
// @todo complete this method.            
return true;            
        }
    /**
     * Checks if the request is cached or not from the given request array.
     * 
     * Note that this does not check if it's expired or not. Just checks the existence of the transient.
     * 
     */
    protected function isCached( $aParams=array() ) {
        return ( boolean ) $this->getCache( 
            $this->generateIDFromRequestParameter( $aParams ) 
        );
    }
    
    /**
     * Performs the Twitter API request by the given URI.
     * 
     * This checks the existent caches and if it's not expired it uses the cache.
     * 
     * @since            2.0.0
     * @access           protected
     * @param            string            $sRequestURI                The GET request URI with the query.
     * @param            integer            $iCacheDuration            The cache duration in seconds. 0 will use the stored cache. null will use a freshly fetched data.
     * @return           array
     */ 
    protected function requestWithCache( $sRequestURI, $aHTTPArgs=array(), $aParams=array(), $iCacheDuration=3600, $sLocale='US' ) {

        // Create an ID from the URI - it's better not use the ID from an Amazon API request URI because it is built upon a timestamp.
        $sTransientID = $this->generateIDFromRequestParameter( $aParams );

        // Retrieve the cache, and if there is, use it.
        $aTransient = $this->getCache( $sTransientID );     
        if ( 
            null !== $iCacheDuration
            && $aTransient !== false 
            && is_array( $aTransient ) 
            && isset( $aTransient[ 'mod' ], $aTransient[ 'data' ] )
        ) {
            
            // Check the cache expiration.
            if ( ( $aTransient[ 'mod' ] + ( ( int ) $iCacheDuration ) ) < time() ) { // expired
                $GLOBALS[ 'aAmazonAutoLinks_APIRequestURIs' ][ $sTransientID ] = array( 
                    // these keys will be checked in the cache renewal events.
                    'parameters' => $aParams,
                    'locale'     => $sLocale,
                );
            }    
            
            return $this->bUseCacheTable
                ? $aTransient[ 'data' ]
                : $this->oEncrypt->decode( $aTransient[ 'data' ] );
            
        }

        return $this->setAPIRequestCache( 
            $sRequestURI, 
            $aHTTPArgs, 
            $sTransientID,
            $iCacheDuration            
        );
        
    }    
    
    /**
     * Performs the API request and sets the cache.
     * 
     * @return            string            The response string of xml.
     * @access            public
     * @remark            The scope is public since the cache renewal event also uses it.
     */
    public function setAPIRequestCache( $sRequestURI, $aHTTPArgs, $sTransientID='', $iDuration=0 ) {
    
        // Perform the API request. 
        $_asXMLResponse = $this->requestSigned( $sRequestURI, $aHTTPArgs );

        // If it has a HTTP connection error, an array is returned.
        if ( is_array( $_asXMLResponse ) ) {
            return $_asXMLResponse;
        }
        
        // If it's not a string, something went wrong.
        if ( ! is_string( $_asXMLResponse ) ) {
            return $_asXMLResponse;
        }
            
        $_boXML = AmazonAutoLinks_WPUtility::getXMLObject( 
            $_asXMLResponse 
        );
        
        // If it's not a valid XML, it returns false.
        if ( ! is_object( $_boXML ) ) {
            return array( 
                'Error' => array( 
                    'Message'   => strip_tags( $_asXMLResponse ), 
                    'Code'      => 'Invalid XML' 
                ) 
            );    
        }
            
        $aResponse = AmazonAutoLinks_WPUtility::convertXMLtoArray( $_boXML );

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
            
        // Save the cache
        $sTransientID = empty( $sTransientID ) 
            ? AmazonAutoLinks_Registry::TRANSIENT_PREFIX . "_" . md5( trim( $sRequestURI ) )
            : $sTransientID;

        $this->setCache( 
            $sTransientID, // name (transient key)
            $_asXMLResponse, // data
            $iDuration, // cache life span
            $sRequestURI // request uri (optional)
        );

        return $_asXMLResponse;
        
    }

    /**
     * 
     * @since           unknown
     * @since           3       Moved to the base class.
     * @remark          Used by the parent class.
     * @return          string|array            Returns the retrieved HTML body string, and an error array on failure.
     */
    public function requestSigned( $sRequestURI, $aHTTPArgs=array() ) {
        
        // [3+] Check a lock transient that lasts only one second 
        // as Amazon Product Advertising API only allows one request per second.
        $_sAPIRequestLock = AmazonAutoLinks_Registry::TRANSIENT_PREFIX . '_LOCK_APIREQUEST';
        $_iIteration      = 0;
        while( AmazonAutoLinks_WPUtility::getTransient( $_sAPIRequestLock ) && $_iIteration < 3 ) {
            sleep( 1 );
            $_iIteration++;
        }
        AmazonAutoLinks_WPUtility::setTransient(
            $_sAPIRequestLock,
            $sRequestURI, // any data will be sufficient
            1  // one second
        );
        
        // Arguments
        $aHTTPArgs = $aHTTPArgs + array(
            'timeout'       => 20,
            'redirection'   => 5,
            'sslverify'     => false,
            'headers'       => null,
            'user-agent'    => $this->sUserAgent,
        );
        $aHTTPArgs = array_filter( $aHTTPArgs );    // drop non value elements.        
        
        $vResponse = wp_remote_get( $sRequestURI, $aHTTPArgs );
        // $vResponse = wp_safe_remote_request( $sRequestURI, $aHTTPArgs );    // not supported in WP version 3.5.x or below.

        if ( is_wp_error( $vResponse ) ) {
            return array( 'Error' => array(
                    'Code'    => $vResponse->get_error_code(),
                    'Message' => 'WP HTTP Error: ' . $vResponse->get_error_message(),
                ) 
            );
        }

        return wp_remote_retrieve_body( $vResponse );    // returns the xml document.
                
    }    
    
    /**
     * A wrapper method for the set_transient() function.
     * 
     * @since       unknown
     * @since       3           Changed the name from `setTransient()`.
     */
    public function setCache( $sTransientKey, $vData, $iDuration=0, $sRequestURI='' ) {

        $sLockTransient = AmazonAutoLinks_Registry::TRANSIENT_PREFIX . '_' . md5( "Lock_{$sTransientKey}" );
         
        // Check if the transient is locked
        if ( AmazonAutoLinks_WPUtility::getTransient( $sLockTransient ) !== false ) {
            return;    // it means the cache is being modified right now in a different process.
        }
        
        // Set a lock flag transient that indicates the transient is being renewed.
        AmazonAutoLinks_WPUtility::setTransient(
            $sLockTransient, 
            time(), // the value can be anything that yields true
            AmazonAutoLinks_WPUtility::getAllowedMaxExecutionTime( 30, 30 )    // max 30 seconds
        );

        // Save the cache
        if ( $this->bUseCacheTable ) {   
            $_oCacheTable = new AmazonAutoLinks_DatabaseTable_request_cache(
                AmazonAutoLinks_Registry::$aDatabaseTables[ 'request_cache' ]
            );
// @todo set the cache duration                
            $_aData        = $_oCacheTable->setCache( 
                $sTransientKey, // name
                array(
                    'mod'  => time(),
                    'data' => $vData,  // data
                ), // data
                ( integer ) $iDuration, // cache life span
                array(
                    'request_uri' => $sRequestURI,
                    'type'        => 'api',
                )
            );            
        }
        else {            
            AmazonAutoLinks_WPUtility::setTransient(
                $sTransientKey, 
                array( 
                    'mod'  => time(), 
                    'data' => $this->oEncrypt->encode( $vData ) 
                ),
                9999999     // set a value to avoid auto-loaded
            ); 
        }

    }
    
    /**
     * A wrapper method for the get_transient() function.
     * 
     * This method does retrieves the transient with the given transient key. In addition, it checks if it is an array; otherwise, it makes it an array.
     * 
     * @access          public
     * @since           2.0.0
     * @since           3       Changed to use a custom database.
     * @remark          The scope is public as the event method uses it.
     */ 
    public function getCache( $sTransientKey ) {
        
        if ( $this->bUseCacheTable ) {            
            $_oCacheTable = new AmazonAutoLinks_DatabaseTable_request_cache(
                AmazonAutoLinks_Registry::$aDatabaseTables[ 'request_cache' ]
            );
            $_aData        = $_oCacheTable->getCache( 
                $sTransientKey 
            );
            $vData         = $_aData[ 'data' ];
        } else {            
            $vData = AmazonAutoLinks_WPUtility::getTransient( $sTransientKey );
        }
        
        // if it's false, no transient is stored. Otherwise, some values are in there.
        if ( in_array( $vData, array( false, '' ), true ) ) {
            return false;
        }
                    
        // If it's array, okay.
        if ( is_array( $vData ) ) { 
            return $vData;
        }

        // Maybe it's encoded
        if ( is_string( $vData ) && is_serialized( $vData ) ) {
            return unserialize( $vData );
        }
                
        // Maybe it's an object. In that case, convert it to an associative array.
        if ( is_object( $vData ) ) {
            return get_object_vars( $vData );
        }
            
        // It's an unknown type, then cast array.
        return ( array ) $vData;
            
    }
    
    /**
     * Generates an ID from the passed parameter.
     * 
     * Signed request URI uses a timestamp so it is not suitable for transient ID.
     * 
     */
    public function generateIDFromRequestParameter( $aParams ) {
        
        $aParams = array_filter( $aParams + $this->aParams );        // Omits empty values.
        $aParams = $aParams + self::$aMandatoryParameters;    // Append mandatory elements.
        ksort( $aParams );        
        unset( $aParams[ 'AssociateTag' ] );
        $aParams[ 'locale' ] = $this->sLocale;    
        $sQuery = implode( '&', $aParams );
        return AmazonAutoLinks_Registry::TRANSIENT_PREFIX . "_"  . md5( $sQuery );
        
    }
    
    /*
     * Callbacks
     * */
    /**
     * 
     * @callback        action      shutdown
     */
    public function _replyToScheduleUpdatingCaches() {    
        
        if ( empty( $GLOBALS[ 'aAmazonAutoLinks_APIRequestURIs' ] ) ) { 
            return;
        }
        
        $_iScheduled = 0;
        foreach( $GLOBALS[ 'aAmazonAutoLinks_APIRequestURIs' ] as $aExpiredCacheRequest ) {
            
            // Schedules the action to run in the background with WP Cron.
            $_iScheduled += $this->_scheduleCacheRenewal( $aExpiredCacheRequest );
            
        }
        if ( $_iScheduled ) {            
            AmazonAutoLinks_Shadow::see();
        }
        
    }    
        /**
         * Schedules to renew the cache with WP Cron.
         * 
         * @since            2.0.4.1b
         * @param            array            $aExpiredCacheRequest            The cache API request array. The structure should look like
         *    array(
         *        'parameters' => the request parameter values
         *        'locale' => the locale 
         *    )
         * @return           0 for not scheduling and 1 for scheduling.
         */
        protected function _scheduleCacheRenewal( $aExpiredCacheRequest ) {
            
            // If already scheduled, skip.
            if ( wp_next_scheduled( 'aal_action_api_transient_renewal', array( $aExpiredCacheRequest ) ) ) { 
                return 0; 
            }
            
            wp_schedule_single_event( 
                time(), 
                'aal_action_api_transient_renewal', // the AmazonAutoLinks_Event class will check this action hook and executes it with WP Cron.
                array( $aExpiredCacheRequest ) // must be enclosed in an array.
            );            
            return 1;
            
        }
        
}