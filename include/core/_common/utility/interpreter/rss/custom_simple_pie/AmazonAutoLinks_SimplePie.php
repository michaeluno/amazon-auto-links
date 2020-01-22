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
 * 
 */ 
class AmazonAutoLinks_SimplePie extends AmazonAutoLinks_SimplePie_Base {
        
    // This class specific properties
    /**
     * Stores the feed url(s) set by the user.
     */
    var $aSetURLs = array();
    /**
     * Indicates whether it is for a background routine.
     */
    var $bIsBackgroundProcess = false;      
    
    var $iCacheLifetimeExpand = 100;
    
    protected $sPluginKey = '';
    
    public function __construct() {

        $this->sPluginKey = AmazonAutoLinks_Registry::TRANSIENT_PREFIX . '_FeedMs';
    
        // Set up the global arrays. Consider the cases that multiple instances of this object are created so the arrays may have been already created.
        // - This stores real mod timestamps.
        $GLOBALS['aSimplePieCacheModTimestamps'] = isset( $GLOBALS['aSimplePieCacheModTimestamps'] ) && is_array( $GLOBALS['aSimplePieCacheModTimestamps'] ) 
            ? $GLOBALS['aSimplePieCacheModTimestamps'] 
            : array();
        $GLOBALS['aSimplePieCacheModTimestamps'][ $this->sPluginKey ] = isset( $GLOBALS['aSimplePieCacheModTimestamps'][ $this->sPluginKey ] ) && is_array( $GLOBALS['aSimplePieCacheModTimestamps'][ $this->sPluginKey ] ) 
            ? $GLOBALS['aSimplePieCacheModTimestamps'][ $this->sPluginKey ]
            : ( array ) AmazonAutoLinks_WPUtility::getTransient( $this->sPluginKey );
            
        // - this stores expired cache items.
        $GLOBALS['aSimplePieCacheExpiredItems'] = isset( $GLOBALS['aSimplePieCacheExpiredItems'] ) && is_array( $GLOBALS['aSimplePieCacheExpiredItems'] )
            ? $GLOBALS['aSimplePieCacheExpiredItems'] 
            : array();
        $GLOBALS['aSimplePieCacheExpiredItems'][ $this->sPluginKey ] = isset( $GLOBALS['aSimplePieCacheExpiredItems'][ $this->sPluginKey ] ) && is_array( $GLOBALS['aSimplePieCacheExpiredItems'][ $this->sPluginKey ] ) 
            ? $GLOBALS['aSimplePieCacheExpiredItems'][ $this->sPluginKey ] 
            : array();
        
        // Schedule the transient update task.
        add_action( 
            'shutdown', 
            array( $this, 'replyToUpdateCacheItems' ) 
        );
        
        parent::__construct();
            
    }
        /**
         * @callback        action      shutdown
         */
        public function replyToUpdateCacheItems() {    
        
            // Saves the global array, $aSimplePieCacheModTimestamps, into the transient of the option table.
            // This is used to avoid multiple calls of set_transient() by the cache class.
            if ( ! ( isset( $GLOBALS['aSimplePieCacheModTimestamps'][ $this->sPluginKey ]['bIsCacheTransientSet'] ) && $GLOBALS['aSimplePieCacheModTimestamps'][ $this->sPluginKey ]['bIsCacheTransientSet'] ) ) {
                unset( $GLOBALS['aSimplePieCacheModTimestamps'][ $this->sPluginKey ]['bIsCacheTransientSet'] ); // remove the unnecessary data.
                AmazonAutoLinks_WPUtility::setTransient( 
                    $this->sPluginKey, 
                    $GLOBALS['aSimplePieCacheModTimestamps'][ $this->sPluginKey ], 
                    $this->cache_duration * $this->iCacheLifetimeExpand 
                );
                $GLOBALS['aSimplePieCacheModTimestamps'][ $this->sPluginKey ]['bIsCacheTransientSet'] = true;
            }
            
            $GLOBALS['aSimplePieCacheExpiredItems'][ $this->sPluginKey ] = array_unique( $GLOBALS['aSimplePieCacheExpiredItems'][ $this->sPluginKey ] );
            if ( count( $GLOBALS['aSimplePieCacheExpiredItems'][ $this->sPluginKey ] ) > 0 ) {
                $this->_scheduleCacheRenewal( 
                    $GLOBALS['aSimplePieCacheExpiredItems'][ $this->sPluginKey ] 
                );
            }
            
        }
            protected function _scheduleCacheRenewal( $aURLs ) {
                
                // Schedules the action to run in the background with WP Cron.
                if ( wp_next_scheduled( 'aal_action_simplepie_renew_cache', array( $aURLs ) ) ) {
                    return;        
                }
                wp_schedule_single_event( 
                    time(), // now
                    'aal_action_simplepie_renew_cache', // action hook name
                    array( $aURLs )  // arguments
                );
                        
            }
    
    
    /*
     * For background cache renewal task.
     * */        
    public function set_feed_url( $asURL ) {
        
        $this->aSetURLs = is_array( $asURL )
            ? $asURL
            : array( $asURL );
        
        // Hook the cache lifetime filter
        foreach( $this->aSetURLs as $_sURL ) {
            add_filter( 
                'SimplePie_filter_cache_transient_lifetime_' . md5( $_sURL ), 
                array( $this, 'replyToSetCacheTransientLifetime' ) 
            );
        }
        add_filter( 
            'SimplePie_filter_cache_transient_lifetime_' . $this->sPluginKey, 
            array( $this, 'replyToSetCacheTransientLifetimeByGlobalKey' ) 
        );
        
        return parent::set_feed_url( $asURL );
        
    }
        /**
         * A callback for the filter that sets cache duration for the SimplePie cache object.
         * @callback        filter      SimplePie_filter_cache_transient_lifetime_{ md5( url ) }
         */
        public function replyToSetCacheTransientLifetime( $iLifespan, $sFileID=null ) {
            return isset( $this->cache_duration ) 
                ? $this->cache_duration 
                : 0;
        }    
        /**
         * A callback for the filter that sets cache duration for the SimplePie cache object.
         * @callback        filter      SimplePie_filter_cache_transient_lifetime_{plujgin key}
         */
        public function replyToSetCacheTransientLifetimeByGlobalKey( $iLifespan, $sKey=null ) {
            
            // If the key is not the one set by this class, it could be some other script's ( plugin's ) filtering item.
            if ( $sKey != $this->sPluginKey ) {
                return $iLifespan;    
            }
            return isset( $this->cache_duration ) 
                ? $this->cache_duration 
                : 0;
            
        }
    
    /**
     * Initializer.
     */
    public function init() {

        // Setup Caches
        $this->enable_cache( true );
        
        // force the cache class to the custom plugin cache class
        $this->set_cache_class( 
            'AmazonAutoLinks_SimplePie_Cache' 
        );
        $this->set_file_class( 
            'AmazonAutoLinks_SimplePie_File' 
        );
                        
        if ( ! $this->bIsBackgroundProcess ) {
            
            foreach ( $this->aSetURLs as $_sURL ) {
                
                $_sFileID       = md5( $_sURL );
                $_iModTimestamp = isset( $GLOBALS['aSimplePieCacheModTimestamps'][ $this->sPluginKey ][ $_sFileID ] ) 
                    ? $GLOBALS['aSimplePieCacheModTimestamps'][ $this->sPluginKey ][ $_sFileID ] 
                    : 0;
                if ( $_iModTimestamp + $this->cache_duration < time() ) {
                    $GLOBALS['aSimplePieCacheExpiredItems'][ $this->sPluginKey ][] = $_sURL;
                }

            }
                                            
        }    

        return parent::init();
        
    }
    public function setBackground( $bIsBackgroundProcess=false ) {
        $this->bIsBackgroundProcess = $bIsBackgroundProcess;
    }    

    function set_force_cache_class( $sClassName='AmazonAutoLinks_SimplePie_Cache' ) {
        $this->cache_class = $sClassName;
    }
    function set_force_file_class( $sClassName='AmazonAutoLinks_SimplePie_File' ) {
        $this->file_class = $sClassName;
    }    
}

class AmazonAutoLinks_SimplePie_Cache extends SimplePie_Cache {
    
    /**
     * Create a new SimplePie_Cache object
     *
     * @static
     * @access public
     */
    function create( $location, $sFileName, $extension ) {
        return new AmazonAutoLinks_SimplePie_Cache_Transient(
            $location, 
            $sFileName,
            $extension 
        );
    }
    
}
class AmazonAutoLinks_SimplePie_Cache_Transient {
    
    var $sTransientName;
    /**
     * Default cache lifetime of 12 hours. This should be overridden by the filter callback function. 
     */
    var $iLifetime   = 43200; 
    var $iExpand     = 100;
    var $sPluginKey  = '';
    
    /**
     * Stores the file name given to the constructor.
     */
    protected $sFileID;
    
    /**
     * @param                   $location       Not used in this class ) : './cache'
     * @param       string      $sFileID        md5( $url ) e.g. b22d9dad80577a8e66a230777d91cc6e // <-- the hash type may be changed by the user.
     * @param                   $extension      Not used in this class: spc
     */    
    public function __construct( $location, $sFileID, $extension ) {
                
        $this->sPluginKey     = AmazonAutoLinks_Registry::TRANSIENT_PREFIX . '_FeedMs';

        // $sFileID should not be empty but I've seen a case that happened with v3.4.x or below.
        $this->sFileID        = empty( $sFileID ) 
            ? $this->sPluginKey . '_a_file' 
            : $sFileID;    
        
        $this->sTransientName = $this->sPluginKey . '_' . $this->sFileID;
        $this->iLifetime = apply_filters( 
            'SimplePie_filter_cache_transient_lifetime_' . $this->sFileID, 
            $this->iLifetime, // it barely expires by itself
            $this->sFileID
        );
        
    }

    public function save( $data ) {
        
        if ( is_a( $data, 'SimplePie' ) ) {
            $data = $data->data;
        }

        $GLOBALS['aSimplePieCacheModTimestamps'][ $this->sPluginKey ][ $this->sFileID ] = time();    

        // make it 100 times longer so that it barely gets expires by itself
        AmazonAutoLinks_WPUtility::setTransient( 
            $this->sTransientName, 
            $data, 
            $this->iLifetime * $this->iExpand 
        );
        return true;
        
    }
    /**
     * Loads the data.
     */
    public function load() {       
    
        // If this returns an empty value, SimplePie will fetch the feed.
        if ( 0 == $this->iLifetime ) { 
            return null;  
        }
        
        // the stored cache data
        return AmazonAutoLinks_WPUtility::getTransient( 
            $this->sTransientName 
        );
        
    }
    /**
     * Trick SimplePie in order to force it to use the remaining cache 
     * and renew the cache in the background, not doing it right away.
     */
    public function mtime() {        
// return 0;    
        return isset( $GLOBALS['aSimplePieCacheModTimestamps'][ $this->sPluginKey ][ $this->sFileID ] ) 
            ? time() // return the current time so that SimplePie believes it's not expired yet.
            : 0;     // if the array key is not set, So pass 0 to tell that the cache needs to be created.
    }
    
    public function touch() {
        $GLOBALS[ 'aSimplePieCacheModTimestamps' ][ $this->sPluginKey ][ $this->sFileID ] = time();
        return true;
    }
    public function unlink() {
        unset( 
            $GLOBALS[ 'aSimplePieCacheModTimestamps' ][ $this->sPluginKey ][ $this->sFileID ]
        );
        AmazonAutoLinks_WPUtility::deleteTransient( 
            $this->sTransientName 
        );
        return true;
    }
}