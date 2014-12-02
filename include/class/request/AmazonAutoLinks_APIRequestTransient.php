<?php
/**
 * Performs requests to the Product Advertising API.
 * 
 * @package         Amazon Auto Links
 * @copyright       Copyright (c) 2013, Michael Uno
 * @license         http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */


/**
 * Deals with Amazon Product Advertising API.
 * 
 * @action            aal_action_api_transient_renewal
 */ 
abstract class AmazonAutoLinks_APIRequestTransient {

    protected $arrParams = array();
    protected static $arrMandatoryParameters = array(
        'Service'            => 'AWSECommerceService',
        'AssociateTag'        => 'amazon-auto-links-20',        // the key must be provided; otherwise, API returns an error.
    );
    
    function __construct() {
        
        // Schedule the transient update task.
        add_action( 'shutdown', array( $this, '_replyToScheduleUpdatingCaches' ) );
        
        $this->oEncrypt = new AmazonAutoLinks_Encrypt;
        
        
    }
    
    /**
     * Checks if the request is cached or not from the given request array.
     * 
     * Note that this does not check if it's expired or not. Just checks the existence of the transient.
     * 
     */
    protected function isCached( $aParams=array() ) {
        
        $sTransientID = $this->generateIDFromRequestParameter( $aParams );
        $aCache = $this->getTransient( $sTransientID );
        return ( $aCache )
            ? true 
            : false;

    }
    
    /**
     * Performs the Twitter API request by the given URI.
     * 
     * This checks the existent caches and if it's not expired it uses the cache.
     * 
     * @since            2.0.0
     * @access            protected
     * @param            string            $strRequestURI                The GET request URI with the query.
     * @param            integer            $intCacheDuration            The cache duration in seconds. 0 will use the stored cache. null will use a freshly fetched data.
     * @return            array
     */ 
    protected function requestWithCache( $strRequestURI, $arrHTTPArgs=array(), $arrParams=array(), $intCacheDuration=3600, $strLocale='US' ) {
// AmazonAutoLinks_Debug::logArray( $strRequestURI );    
// var_dump( $strRequestURI );    
        // Create an ID from the URI - it's better not use the ID from an Amazon API request URI because it is built upon a timestamp.
        $strTransientID = $this->generateIDFromRequestParameter( $arrParams );

        // Retrieve the cache, and if there is, use it.
        $arrTransient = $this->getTransient( $strTransientID );
        if ( 
            ! is_null( $intCacheDuration )
            && $arrTransient !== false 
            && is_array( $arrTransient ) 
            && isset( $arrTransient['mod'], $arrTransient['data'] )
        ) {
            
            // Check the cache expiration.
            if ( ( $arrTransient['mod'] + ( ( int ) $intCacheDuration ) ) < time() ) {    // expired
                            
                $GLOBALS['arrAmazonAutoLinks_APIRequestURIs'][ $strTransientID ] = array( 
                    // these keys will be checked in the cache renewal events.
                    'parameters' => $arrParams,
                    'locale' => $strLocale,
                );
            }    

            return $this->oEncrypt->decode( $arrTransient['data'] );
            
        }

        return $this->setAPIRequestCache( $strRequestURI, $arrHTTPArgs, $strTransientID );
        
    }    
    
    /**
     * Performs the API request and sets the cache.
     * 
     * @return            string            The response string of xml.
     * @access            public
     * @remark            The scope is public since the cache renewal event also uses it.
     */
    public function setAPIRequestCache( $strRequestURI, $arrHTTPArgs, $strTransientID='' ) {
    
        // Perform the API request. - requestSigned() should be defined in the extended class.
        $asXMLResponse = $this->requestSigned( $strRequestURI, $arrHTTPArgs );

        // If it has a HTTP connection error, an array is returned.
        if ( is_array( $asXMLResponse ) )
            return $asXMLResponse;
        
        // If it's not a string, something went wrong.
        if ( ! is_string( $asXMLResponse ) )
            return $asXMLResponse;
            
        $osXML = AmazonAutoLinks_Utilities::getXMLObject( $asXMLResponse );
        
        // If it's not a valid XML, it returns a string.
        if ( ! is_object( $osXML ) )
            return array( 'Error' => array( 'Message' => $osXML, 'Code' => 'Invalid XML' ) );    // compose an error array.
            
        $arrResponse = AmazonAutoLinks_Utilities::convertXMLtoArray( $osXML );

        // If empty, return an empty array.
        if ( empty( $arrResponse ) ) return array();
        
        // If the result is not an array, something went wrong.
        if ( ! is_array( $arrResponse ) ) return ( array ) $arrResponse;
        
        // If an error occurs, do not set the cache.
        if ( isset( $arrResponse['Error'] ) ) return $arrResponse;
            
        // Save the cache
        $strTransientID = empty( $strTransientID ) 
            ? AmazonAutoLinks_Commons::TransientPrefix . "_" . md5( trim( $strRequestURI ) )
            : $strTransientID;

        $this->setTransient( $strTransientID, $asXMLResponse );

        return  $asXMLResponse;
        
    }
    
    /**
     * A wrapper method for the set_transient() function.
     * 
     */
    public function setTransient( $strTransientKey, $vData, $intTime=null ) {

        $sLockTransient = AmazonAutoLinks_Commons::TransientPrefix . '_' . md5( "Lock_{$strTransientKey}" );
         
        // Check if the transient is locked
        if ( AmazonAutoLinks_WPUtilities::getTransient( $sLockTransient ) !== false ) {
            return;    // it means the cache is being modified right now in a different process.
        }
        
        // Set a lock flag transient that indicates the transient is being renewed.
        AmazonAutoLinks_WPUtilities::setTransient(
            $sLockTransient, 
            time(), // the value can be anything that yields true
            AmazonAutoLinks_Utilities::getAllowedMaxExecutionTime( 30, 30 )    // max 30 seconds
        );
// AmazonAutoLinks_Debug::logArray( 'set transient: ' . $strTransientKey );
        // Save the cache
        AmazonAutoLinks_WPUtilities::setTransient(
            $strTransientKey, 
            array( 
                'mod' => $intTime ? $intTime : time(), 
                'data' => $this->oEncrypt->encode( $vData ) 
            )
        );    // no expiration by itself

// AmazonAutoLinks_Debug::logArray( 'the transient is saved: ' . $strTransientKey );

        // AmazonAutoLinks_WPUtilities::deleteTransient( $sLockTransient );
        
    }
    
    /**
     * A wrapper method for the get_transient() function.
     * 
     * This method does retrieves the transient with the given transient key. In addition, it checks if it is an array; otherwise, it makes it an array.
     * 
     * @access            public
     * @since            2.0.0
     * @remark            The scope is public as the event method uses it.
     */ 
    public function getTransient( $strTransientKey ) {
        
        $vData = AmazonAutoLinks_WPUtilities::getTransient( $strTransientKey );
        
        // if it's false, no transient is stored. Otherwise, some values are in there.
        if ( $vData === false ) return false;
                    
        // If it's array, okay.
        if ( is_array( $vData ) ) return $vData;

        // Maybe it's encoded
        if ( is_string( $vData ) && is_serialized( $vData ) ) 
            return unserialize( $vData );
                
        // Maybe it's an object. In that case, convert it to an associative array.
        if ( is_object( $vData ) )
            return get_object_vars( $vData );
            
        // It's an unknown type. So cast array and return it.
        return ( array ) $vData;
            
    }
    
    /**
     * Generates an ID from the passed parameter.
     * 
     * Signed request URI uses a timestamp so it is not suitable for transient ID.
     * 
     */
    public function generateIDFromRequestParameter( $arrParams ) {
        
        $arrParams = array_filter( $arrParams + $this->arrParams );        // Omits empty values.
        $arrParams = $arrParams + self::$arrMandatoryParameters;    // Append mandatory elements.
        ksort( $arrParams );        
        unset( $arrParams['AssociateTag'] );
        $arrParams['locale'] = $this->strLocale;    
        $strQuery = implode( '&', $arrParams );
        return AmazonAutoLinks_Commons::TransientPrefix . "_"  . md5( $strQuery );        
        
    }
    
    /*
     * Callbacks
     * */
    public function _replyToScheduleUpdatingCaches() {    // for the shutdown hook
        
        if ( empty( $GLOBALS['arrAmazonAutoLinks_APIRequestURIs'] ) ) return;
        
        $_iScheduled = 0;
        foreach( $GLOBALS['arrAmazonAutoLinks_APIRequestURIs'] as $arrExpiredCacheRequest ) {
            
            // Schedules the action to run in the background with WP Cron.
            $_iScheduled += $this->_scheduleCacheRenewal( $arrExpiredCacheRequest );
            
        }
        if ( $_iScheduled ) {            
            AmazonAutoLinks_Shadow::see();
        }
        
    }    
        /**
         * Schedules to renew the cache with WP Cron.
         * 
         * @since            2.0.4.1b
         * @param            array            $aExpiredCacheRequest            The cache request array. The structure should look like
         *    array(
         *        'parameters' => the request parameter values
         *        'locale' => the locale 
         *    )
         * @return            0 for not scheduling and 1 for scheduling.
         */
        protected function _scheduleCacheRenewal( $aExpiredCacheRequest ) {
            
            // If already scheduled, skip.
            if ( wp_next_scheduled( 'aal_action_api_transient_renewal', array( $aExpiredCacheRequest ) ) ) return 0; 
            
            wp_schedule_single_event( 
                time(), 
                'aal_action_api_transient_renewal',     // the AmazonAutoLinks_Event class will check this action hook and executes it with WP Cron.
                array( $aExpiredCacheRequest )    // must be enclosed in an array.
            );            
            return 1;
            
        }
}