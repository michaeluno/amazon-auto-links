<?php
/**
    Extends the SimplePie library. 
 * 
 * @package     Amazon Auto Links
 * @copyright   Copyright (c) 2013, Michael Uno
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since        2.0.0
*/

/*
 * Custom Hooks
 * - aal_action_simplepie_renew_cache : the event action that renew caches in the background.
 * - SimplePie_filter_cache_transient_lifetime_{FileID} : applies to cache transients. FileID is md5( $url ).
 * 
 * Global Variables
 * - $arrSimplePieCacheModTimestamps : stores mod timestamps of cached data. This will be stored in a transient when WordPress exits, 
 *   which prevents multiple calls of get_transiet() that performs a database query ( slows down the page load ).
 * - $arrSimplePieCacheExpiredItems : stores expired cache items' file IDs ( md5( $url ) ). This will be saved in the transient at the WordPress shutdown action event.
 *   the separate cache renewal event with WP Cron will read it and renew the expired caches.
 * 
 * */

// Make sure that SimplePie has been already loaded. This is very important. Without this line, the cache setting breaks. 
// Do not include class-simplepie.php, which causes an unknown class warning.
// if ( ! class_exists( 'SimplePie' ) ) include_once( ABSPATH . WPINC . '/class-feed.php' );        
if ( ! class_exists( 'WP_SimplePie_File' ) ) include_once( ABSPATH . WPINC . '/class-feed.php' );


abstract class AmazonAutoLinks_SimplePie___ extends SimplePie {
    
    /*
     * For sort
     * */
    public static $sortorder = 'random';
    public static $bKeepRawTitle = false;
    public static $strCharEncoding = 'UTF-8';
    
    public function set_sortorder( $sortorder ) {
        self::$sortorder = $sortorder;
    }
    public function set_keeprawtitle( $bKeepRawTitle ) {
        self::$bKeepRawTitle = $bKeepRawTitle;        
    }
    public function set_charset_for_sort( $strCharEncoding ) {
        self::$strCharEncoding = $strCharEncoding;        
    }

    public static function sort_items_by_title( $a, $b ) {
        $a_title = ( self::$bKeepRawTitle ) ? $a->get_title() : preg_replace('/#\d+?:\s?/i', '', $a->get_title());
        $b_title = ( self::$bKeepRawTitle ) ? $b->get_title() : preg_replace('/#\d+?:\s?/i', '', $b->get_title());
        $a_title = html_entity_decode( trim( strip_tags( $a_title ) ), ENT_COMPAT, self::$strCharEncoding );
        $b_title = html_entity_decode( trim( strip_tags( $b_title ) ), ENT_COMPAT, self::$strCharEncoding );
        return strnatcasecmp( $a_title, $b_title );    
    }
    public static function sort_items_by_title_descending( $a, $b ) {
        $a_title = ( self::$bKeepRawTitle ) ? $a->get_title() : preg_replace('/#\d+?:\s?/i', '', $a->get_title());
        $b_title = ( self::$bKeepRawTitle ) ? $b->get_title() : preg_replace('/#\d+?:\s?/i', '', $b->get_title());
        $a_title = html_entity_decode( trim( strip_tags( $a_title ) ), ENT_COMPAT, self::$strCharEncoding );
        $b_title = html_entity_decode( trim( strip_tags( $b_title ) ), ENT_COMPAT, self::$strCharEncoding );
        return strnatcasecmp( $b_title, $a_title );
    }
    
}

// If the WordPress version is below 3.5, which uses SimplePie below 1.3,
if ( version_compare( get_bloginfo( 'version' ) , '3.5', "<" ) ) {    

    abstract class AmazonAutoLinks_SimplePie__ extends AmazonAutoLinks_SimplePie___ {
        
        public function sort_items( $a, $b ) {

            // Sort 
            // by date
            if ( self::$sortorder == 'date' ) 
                return $a->get_date( 'U' ) <= $b->get_date( 'U' );
            // by title ascending
            if ( self::$sortorder == 'title' ) 
                return self::sort_items_by_title( $a, $b );
            // by title decending
            if ( self::$sortorder == 'title_descending' ) 
                return self::sort_items_by_title_descending( $a, $b );
            // by random 
            return rand( -1, 1 );    
            
        }        
    }
    
} else {
    
    abstract class AmazonAutoLinks_SimplePie__ extends AmazonAutoLinks_SimplePie___ {
        
        public static function sort_items( $a, $b ) {
            
            // Sort 
            // by date
            if ( self::$sortorder == 'date' ) 
                return $a->get_date( 'U' ) <= $b->get_date( 'U' );
            // by title ascending
            if ( self::$sortorder == 'title' ) 
                return self::sort_items_by_title( $a, $b );
            // by title decending
            if ( self::$sortorder == 'title_descending' ) 
                return self::sort_items_by_title_descending( $a, $b );
            // by random 
            return rand( -1, 1 );    
            
        }        
    }    

}
    
class AmazonAutoLinks_SimplePie extends AmazonAutoLinks_SimplePie__ {
        
    // This class specific properties
    var $vSetURL;    // stores the feed url(s) set by the user.
    var $fIsBackgroundProcess = false;        // indicates whether it is from the event action ( background call )
    var $numCacheLifetimeExpand = 100;
    protected $strPluginKey = '';
    
    public function __construct() {
    
        $this->useragent = AmazonAutoLinks_Commons::$strPluginName;
        $this->strPluginKey = AmazonAutoLinks_Commons::TransientPrefix . 'FeedMs';
    
        // Set up the global arrays. Consider the cases that multiple instances of this object are created so the arrays may have been already created.
        // - This stores real mod timestamps.
        $GLOBALS['arrSimplePieCacheModTimestamps'] = isset( $GLOBALS['arrSimplePieCacheModTimestamps'] ) && is_array( $GLOBALS['arrSimplePieCacheModTimestamps'] ) ? $GLOBALS['arrSimplePieCacheModTimestamps'] : array();
        $GLOBALS['arrSimplePieCacheModTimestamps'][ $this->strPluginKey ] = isset( $GLOBALS['arrSimplePieCacheModTimestamps'][ $this->strPluginKey ] ) && is_array( $GLOBALS['arrSimplePieCacheModTimestamps'][ $this->strPluginKey ] ) 
            ? $GLOBALS['arrSimplePieCacheModTimestamps'][ $this->strPluginKey ]
            : ( array ) AmazonAutoLinks_WPUtilities::getTransient( $this->strPluginKey ) ;
// AmazonAutoLinks_Debug::DumpArray( $GLOBALS['arrSimplePieCacheModTimestamps'], dirname( __FILE__ ) . '/mods.txt'  );
            
        // - this stores expired cache items.
        $GLOBALS['arrSimplePieCacheExpiredItems'] = isset( $GLOBALS['arrSimplePieCacheExpiredItems'] ) && is_array( $GLOBALS['arrSimplePieCacheExpiredItems'] ) ? $GLOBALS['arrSimplePieCacheExpiredItems'] : array();
        $GLOBALS['arrSimplePieCacheExpiredItems'][ $this->strPluginKey ] = isset( $GLOBALS['arrSimplePieCacheExpiredItems'][ $this->strPluginKey ] ) && is_array( $GLOBALS['arrSimplePieCacheExpiredItems'][ $this->strPluginKey ] ) ? $GLOBALS['arrSimplePieCacheExpiredItems'][ $this->strPluginKey ] : array();
        
        // Schedule the transient update task.
        add_action( 'shutdown', array( $this, 'updateCacheItems' ) );
        
        parent::__construct();
            
    }
    public function updateCacheItems() {    
    
        // Saves the global array, $arrSimplePieCacheModTimestamps, into the transient of the option table.
        // This is used to avoid multiple calls of set_transient() by the cache class.
        if ( ! ( isset( $GLOBALS['arrSimplePieCacheModTimestamps'][ $this->strPluginKey ]['bIsCacheTransientSet'] ) && $GLOBALS['arrSimplePieCacheModTimestamps'][ $this->strPluginKey ]['bIsCacheTransientSet'] ) ) {
            unset( $GLOBALS['arrSimplePieCacheModTimestamps'][ $this->strPluginKey ]['bIsCacheTransientSet'] ); // remove the unnecessary data.
            AmazonAutoLinks_WPUtilities::setTransient( $this->strPluginKey, $GLOBALS['arrSimplePieCacheModTimestamps'][ $this->strPluginKey ], $this->cache_duration * $this->numCacheLifetimeExpand );
            $GLOBALS['arrSimplePieCacheModTimestamps'][ $this->strPluginKey ]['bIsCacheTransientSet'] = true;
        }
        
        $GLOBALS['arrSimplePieCacheExpiredItems'][ $this->strPluginKey ] = array_unique( $GLOBALS['arrSimplePieCacheExpiredItems'][ $this->strPluginKey ] );
        if ( count( $GLOBALS['arrSimplePieCacheExpiredItems'][ $this->strPluginKey ] ) > 0 )
            $this->scheduleCacheRenewal( $GLOBALS['arrSimplePieCacheExpiredItems'][ $this->strPluginKey ] );
        

    }
    protected function scheduleCacheRenewal( $arrURLs ) {
        
        // Schedules the action to run in the background with WP Cron.
        if ( wp_next_scheduled( 'aal_action_simplepie_renew_cache', array( $arrURLs ) ) ) return;        
        wp_schedule_single_event( time() , 'aal_action_simplepie_renew_cache', array( $arrURLs ) );
                
    }
    
    public function setCacheTransientLifetime( $intLifespan, $strFileID=null ) {
        
        // This is a callback for the filter that sets cache duration for the SimplePie cache object.
        return isset( $this->cache_duration ) ? $this->cache_duration : 0;
        
    }
    public function setCacheTransientLifetimeByGlobalKey( $intLifespan, $strKey=null ) {
        
        // This is a callback for the filter that sets cache duration for the SimplePie cache object.
        
        // If the key is not the one set by this class, it could be some other script's ( plugin's ) filtering item.
        if ( $strKey != $this->strPluginKey ) return $intLifespan;    
        
        return isset( $this->cache_duration ) ? $this->cache_duration : 0;
        
    }
    
    /*
     * For background cache renewal task.
     * */        
    public function set_feed_url( $vURL ) {
        
        $this->vSetURL = $vURL;    // array or string
        
        // Hook the cache lifetime filter
        foreach( ( array ) $vURL as $strURL ) 
            add_filter( 'SimplePie_filter_cache_transient_lifetime_' . md5( $strURL ), array( $this, 'setCacheTransientLifetime' ) );
        add_filter( 'SimplePie_filter_cache_transient_lifetime_' . $this->strPluginKey, array( $this, 'setCacheTransientLifetimeByGlobalKey' ) );
        
        return parent::set_feed_url( $vURL );
        
    }
    public function init() {

        // Setup Caches
        $this->enable_cache( true );
        
        // force the cache class to the custom plugin cache class
        $this->set_cache_class( 'AmazonAutoLinks_SimplePie_Cache' );
        $this->set_file_class( 'AmazonAutoLinks_SimplePie_File' );
                        
        if ( isset( $this->vSetURL ) && ! $this->fIsBackgroundProcess ) {
            
            foreach ( ( array) $this->vSetURL as $strURL ) {
                
                $strFileID = md5( $strURL );
                $intModTimestamp = isset( $GLOBALS['arrSimplePieCacheModTimestamps'][ $this->strPluginKey ][ $strFileID ] ) ? $GLOBALS['arrSimplePieCacheModTimestamps'][ $this->strPluginKey ][ $strFileID ] : 0;
                if ( $intModTimestamp + $this->cache_duration < time() )     
                    $GLOBALS['arrSimplePieCacheExpiredItems'][ $this->strPluginKey ][] = $strURL;

            }
                                            
        }    
        
// if ( ! $this->fIsBackgroundProcess ) {
    
    // if ( ! empty( $GLOBALS['arrSimplePieCacheExpiredItems'] ) ) 
        // AmazonAutoLinks_Debug::DumpArray( $GLOBALS['arrSimplePieCacheExpiredItems'], dirname( __FILE__ ) . '/expired_urls.txt'  );
    // AmazonAutoLinks_Debug::DumpArray( $GLOBALS['arrSimplePieCacheModTimestamps'], dirname( __FILE__ ) . '/cache_mods.txt'  );
    
// } else {
    // AmazonAutoLinks_Debug::DumpArray( $this->vSetURL, dirname( __FILE__ ) . '/background_process.txt'  );
// }
        return parent::init();
        
    }
    public function setBackground( $fIsBackgroundProcess=false ) {
        $this->fIsBackgroundProcess = $fIsBackgroundProcess;
    }    

    function set_force_cache_class( $class = 'AmazonAutoLinks_SimplePie_Cache' ) {
        $this->cache_class = $class;
    }
    function set_force_file_class( $class = 'AmazonAutoLinks_SimplePie_File' ) {
        $this->file_class = $class;
    }    
}

class AmazonAutoLinks_SimplePie_Cache extends SimplePie_Cache {
    
    /**
     * Create a new SimplePie_Cache object
     *
     * @static
     * @access public
     */
    function create( $location, $filename, $extension ) {
        return new AmazonAutoLinks_SimplePie_Cache_Transient( $location, $filename, $extension );
    }
    
}
class AmazonAutoLinks_SimplePie_Cache_Transient {
    
    var $strTransientName;
    var $lifetime = 43200; // Default cache lifetime of 12 hours. This should be overridden by the filter callback function. 
    var $numExpand = 100;
    var $strPluginKey = '';
    
    protected $strFileID;    // stores the file name given to the constructor.
    
    public function __construct( $location, $strFileID, $extension ) {
        
        /* 
         * Parameters:
         * - $location ( not used in this class ) : './cache'
         * - $strFileID : md5( $url )    e.g. b22d9dad80577a8e66a230777d91cc6e // <-- the hash type may be changed by the user.
         * - $extension ( not used in this class ) : spc
         */
        
        $this->strPluginKey = AmazonAutoLinks_Commons::TransientPrefix . 'FeedMs';

        // $strFileID should not be empty but I've seen a case that happened with v3.4.x or below.
        $this->strFileID = empty( $strFileID ) ? $this->strPluginKey . '_a_file' : $strFileID;    
        
        $this->strTransientName = $this->strPluginKey . '_' . $this->strFileID;
        $this->lifetime = apply_filters( 
            'SimplePie_filter_cache_transient_lifetime_' . $this->strFileID, 
            $this->lifetime,     // it barely expires by itself
            $this->strFileID
        );
        
    }

    public function save( $data ) {
        
        if ( is_a( $data, 'SimplePie' ) )
            $data = $data->data;

        // $GLOBALS['arrSimplePieCacheModTimestamps'] should be already created by the caller (parent) custom SimplePie class.
        $GLOBALS['arrSimplePieCacheModTimestamps'][ $this->strPluginKey ][ $this->strFileID ] = time();    

// AmazonAutoLinks_Debug::DumpArray( $GLOBALS['arrSimplePieCacheModTimestamps'][ $this->strFileID ] , dirname( __FILE__ ) . '/saved_cache.txt'  );        

        // make it 100 times longer so that it barely gets expires by itself
        AmazonAutoLinks_WPUtilities::setTransient( $this->strTransientName, $data, $this->lifetime * $this->numExpand );
        return true;
    }
    public function load() {        
// AmazonAutoLinks_Debug::DumpArray( $this->lifetime , dirname( __FILE__ ) . '/lifetime.txt'  );
        // If this returns an empty value, SimplePie will fetch the feed.
        if ( $this->lifetime == 0 ) return null;  
        
        return AmazonAutoLinks_WPUtilities::getTransient( $this->strTransientName );    // the stored cache data
        
    }
    public function mtime() {        
    
        // Here we are going to deceive SimplePie in order to force it to use the remaining cache and renew the cache in the background, not doing it right away.
        return isset( $GLOBALS['arrSimplePieCacheModTimestamps'][ $this->strPluginKey ][ $this->strFileID ] ) 
            ? time()    // return the current time so that SimplePie believes it's not expired yet.
            : 0;    // if the array key is not set, So pass 0 to tell that the cache needs to be created. 
            
    }
    public function touch() {
        $GLOBALS['arrSimplePieCacheModTimestamps'][ $this->strPluginKey ][ $this->strFileID ] = time(); 
        return true;
    }
    public function unlink() {
        unset( $GLOBALS['arrSimplePieCacheModTimestamps'][ $this->strPluginKey ][ $this->strFileID ] );
        AmazonAutoLinks_WPUtilities::deleteTransient( $this->strTransientName );
        return true;
    }
}

// class AmazonAutoLinks_SimplePie_File extends SimplePie_File {
class AmazonAutoLinks_SimplePie_File extends WP_SimplePie_File {

    var $url;
    var $useragent = 'Amazon Auto Links';
    var $success = true;
    var $headers = array();
    var $body;
    var $status_code;
    var $redirects = 0;
    var $error;
    var $method = SIMPLEPIE_FILE_SOURCE_REMOTE;
    
    protected $arrArgs = array(
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
    
    public function __construct( $strURL, $intTimeout = 10, $intRedirects = 5, $arrHeaders = null, $strUserAgent = null, $fForceFsockOpen = false ) {

        $this->timeout = $intTimeout;
        $this->redirects = $intRedirects;
        $this->headers = $strUserAgent;
        $this->useragent = $strUserAgent;        
        $this->url = $strURL;

        if ( preg_match( '/^http(s)?:\/\//i', $strURL ) ) {
            
            // Arguments
            $arrURLElems = parse_url( $strURL );
            $arrArgs = array(
                'timeout' => $this->timeout,
                'redirection' => $this->redirects,
                'sslverify' => $arrURLElems['scheme'] == 'https' ? false : true,    // this is missing in WP_SimplePie_File
            );

            if ( ! empty( $this->headers ) )
                $arrArgs['headers'] = $this->headers;

            if ( SIMPLEPIE_USERAGENT != $this->useragent ) 
                $arrArgs['user-agent'] = $this->useragent;
            
            // Request
            $res = wp_remote_get( $strURL, $arrArgs );
            // $res = wp_safe_remote_request( $strURL, $arrArgs ); // not compatible with WordPress 3.5.x or below

            if ( is_wp_error( $res ) ) {
                
                $this->error = 'WP HTTP Error: ' . $res->get_error_message();
                $this->success = false;
                
            } else {
                
                $this->headers = wp_remote_retrieve_headers( $res );
                $this->body = wp_remote_retrieve_body( $res );
                $this->status_code = wp_remote_retrieve_response_code( $res );
            }
            
        } else {
            $this->error = '';
            $this->success = false;
        }        

    }
    
}