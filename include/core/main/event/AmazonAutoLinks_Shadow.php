<?php
/**
 * Provides a means of an alternative to WP Cron on the server that disables WP Cron.
 * 
 * @package     AmazonAutoLinks_Shadow
 * @copyright   Copyright (c) 2013, Michael Uno
 * @authorurl    https://github.com/michaeluno/WP_Shadow
*/
class AmazonAutoLinks_Shadow {
        
    /**
     * The interval in seconds that locks the background calls.
     * 
     * This is for preventing too many background calls to be performed.
     * 
     * @since            1.0.0
     */
    static protected $_iLockBackgroundCallInterval = 10;
        
    /**
     * The interval in seconds that locks the cron.
     * 
     * This is for preventing WordPress pseudo cron jobs from being performed too frequently. 
     * 
     * @since            1.0.0
     */
    static protected $_iLockCronInterval = 10;    
        
    /**
     * If this flag is true, the background process will be triggered even when the protection interval is not expired.
     * 
     * @since            1.0.0
     */
    static protected $_fIgnoreLock = false;
    
    /**
     * The get method query array holding the key-value pairs.
     */
    static protected $_aGet = array();
    
    /**
      * Checks if the page load is performed in the background and if so, performs the given actions if scheduled.
     * 
     * @since            1.0.0
      */ 
    public function __construct( $aActionHooks ) {

        if ( empty( $aActionHooks ) ) { return; }
        $aActionHooks = ( array ) $aActionHooks;
        
        // If not called from the background, return.
        if ( isset( $_GET['doing_wp_cron'] ) ) {
            return;
        }   // WP Cron
        if ( isset( $GLOBALS['pagenow'] ) && $GLOBALS['pagenow'] == 'admin-ajax.php' ) {
            return;
        }    // WP Heart-beat API
        
        if ( ! $this->isBackground() ) { 
            return;
        }
    
        // Tell WordPress this is a background routine by setting the Cron flag.
        if ( ! defined( 'DOING_CRON' ) ) {
            define( 'DOING_CRON', true );
        }
        if ( ! defined( 'WP_USE_THEMES' ) ) {
            define( 'WP_USE_THEMES', false );
        }
        ignore_user_abort( true );        

        // Do not process if a delay is not set.
        if ( ! $this->isBackground( true ) ) {    
            exit( 
                $this->_loadBackgroundPageWithDelay( 
                    2,  // 2 seconds delay
                    $_GET 
                ) 
            );    
        }

        // At this point, the page is loaded in the background with some delays.
        $this->_handleCronTasks( $aActionHooks );
        
    }
    
    /**
     * Checks whether the page is loaded in the background.
     * 
     * @since            1.0.0
     */    
    static public function isBackground( $fIsDelayed=false ) {
        
        $_sKey = md5( get_class() );
        if ( ! $fIsDelayed )  
            return isset( $_COOKIE[ $_sKey ] );
            
        return isset( $_COOKIE[ 'delay' ], $_COOKIE[ $_sKey ] );
        
    }
    
    /**
     * Handles plugin cron tasks.
     * 
     * Called from the constructor. 
     * 
     * @since            1.0.0
     */
    protected function _handleCronTasks( $aActionHooks ) {

        $_sTransientName = md5( get_class() );
        $_aTasks = AmazonAutoLinks_WPUtility::getTransient( $_sTransientName );
        $_nNow = microtime( true );
        $_nCalledTime = isset( $_aTasks['called'] ) ? $_aTasks['called'] : 0;
        $_nLockedTime = isset( $_aTasks['locked'] ) ? $_aTasks['locked'] : 0;
        unset( $_aTasks['called'], $_aTasks['locked'] );    // leave only task elements.
        
        // If it's still locked do nothing. Locked duration: 10 seconds.
        if ( $_nLockedTime + self::$_iLockCronInterval > $_nNow ) {        
            return;
        }        
        
        // Retrieve the plugin cron scheduled tasks.
        if ( empty( $_aTasks ) ) {
            $_aTasks = $this->_getScheduledCronTasksByActionName( $aActionHooks );
        }
        // If the task is still empty,
        if ( empty( $_aTasks ) ) {                    
            return;
        } 
        
        $aFlagKeys = array(
            'locked'    =>    microtime( true ),    // set/renew the locked time
            'called'    =>    $_nCalledTime,        // inherit the called time
        );
        AmazonAutoLinks_WPUtility::setTransient( $_sTransientName, $aFlagKeys + $_aTasks, $this->getAllowedMaxExecutionTime() ); // lock the process.
        $this->_doTasks( $_aTasks );    

        // remove tasks but leave the flag element.
        AmazonAutoLinks_WPUtility::setTransient( $_sTransientName, $aFlagKeys, $this->getAllowedMaxExecutionTime() ); // lock the process.
        exit;
        
    }
            
    /**
     * Performs the plugin-specific scheduled tasks in the background.
     * 
     * This should only be called when the md5( get_class() ) transient is present. 
     * 
     * @since 1.0.0
     */
    protected function _doTasks( $aTasks ) {
        
        foreach( $aTasks as $iTimeStamp => $aCronHooks ) {
            
            if ( ! is_array( $aCronHooks ) ) continue;        // the 'locked' key flag element should be skipped
            foreach( $aCronHooks as $sActionName => $_aActions ) {
                
                foreach( $_aActions as $sHash => $aArgs ) {
                                                                        
                    $sSchedule = $aArgs['schedule'];
                    if ( $sSchedule != false ) {
                        $aNewArgs = array( $iTimeStamp, $sSchedule, $sActionName, $aArgs['args'] );
                        call_user_func_array( 'wp_reschedule_event', $aNewArgs );
                    }
                    wp_unschedule_event( $iTimeStamp, $sActionName, $aArgs['args'] );
                    do_action_ref_array( $sActionName, $aArgs['args'] );
                
                }
            }    
        }
        
    }
    
    /**
     * Sets plugin specific cron tasks by extracting plugin's cron jobs from the WP cron job array.
     *  
     * @since 1.0.0
     */
    protected function _getScheduledCronTasksByActionName( $aActionHooks ) {
        
        $_aTheTasks = array();        
        $_aTasks = _get_cron_array();
        if ( ! $_aTasks ) return $_aTheTasks;    // if the cron tasks array is empty, do nothing. 

        $_iGMTTime = microtime( true );    // the current time stamp in micro seconds.
        $_aScheduledTimeStamps = array_keys( $_aTasks );
        if ( isset( $_aScheduledTimeStamps[ 0 ] ) && $_aScheduledTimeStamps[ 0 ] > $_iGMTTime ) return $_aTheTasks; // the first element has the least number.
                
        foreach ( $_aTasks as $_iTimeStamp => $_aScheduledActionHooks ) {
            if ( $_iTimeStamp > $_iGMTTime ) break;    // see the definition of the wp_cron() function.
            foreach ( ( array ) $_aScheduledActionHooks as $_sScheduledActionHookName => $_aArgs ) {
                if ( in_array( $_sScheduledActionHookName, $aActionHooks ) ) {
                    $_aTheTasks[ $_iTimeStamp ][ $_sScheduledActionHookName ] = $_aArgs;
        
                }
            }
        }
        return $_aTheTasks;
                
    }
    
    /**
     * Retrieves the server set allowed maximum PHP script execution time.
     * 
     */
    static protected function getAllowedMaxExecutionTime( $iDefault=30, $iMax=120 ) {
        
        $iSetTime = function_exists( 'ini_get' ) && ini_get( 'max_execution_time' ) 
            ? ( int ) ini_get( 'max_execution_time' ) 
            : $iDefault;
        
        return $iSetTime > $iMax
            ? $iMax
            : $iSetTime;
        
    }
    
    /**
     * Accesses the site in the background.
     * 
     * @since            1.0.1
     */
    static public function gaze( $aGet=array() ) {

        if ( isset( $_GET['doing_wp_cron'] ) ) return;    // WP Cron
        if ( isset( $GLOBALS['pagenow'] ) && $GLOBALS['pagenow'] == 'admin-ajax.php' ) return;    // WP Heart-beat API    
        
        // Ensures the task is done only once in a page load.
        static $_bIsCalled;
        if ( $_bIsCalled ) return;
        $_bIsCalled = true;
        
        self::_loadBackgroundPageWithDelay( 0, $aGet );
        
    }
    
    /**
     * Accesses the site in the background at the end of the script execution.
     * 
     * This is used to trigger cron events in the background and sets a static flag so that it ensures it is done only once per page load.
     * 
     * @since            1.0.0
     */
    static public function see( $aGet=array(), $fIgnoreLock=false ) {

        // WP Cron
        if ( isset( $_GET['doing_wp_cron'] ) ) {
           return;    
        }
        // WP Heart-beat API
        if ( isset( $GLOBALS['pagenow'] ) && $GLOBALS['pagenow'] == 'admin-ajax.php' ) { 
            return;    
        }
    
        // Ensures the task is done only once in a page load.
        static $_bIsCalled;
        if ( $_bIsCalled ) { 
            return;
        }
        $_bIsCalled = true;        
        
        // Store the static properties.
        self::$_fIgnoreLock = $fIgnoreLock ? $fIgnoreLock : self::$_fIgnoreLock;
        self::$_aGet = ( array ) $aGet + self::$_aGet;

        if ( did_action( 'shutdown' ) ) {
            self::_replyToAccessSite();
            return;    // important as what the action has performed does not mean the action never will be fired again.
        }
        add_action( 'shutdown', array( __CLASS__, '_replyToAccessSite' ), 999 );

    }    
        /**
         * A callback for the accessSiteAtShutDown() method.
         * 
         * @since            1.0.0
         */
        static public function _replyToAccessSite() {

            // Retrieve the plugin scheduled tasks array.
            $_sTransientName = md5( get_class() );
            $_aTasks         = AmazonAutoLinks_WPUtility::getTransient( $_sTransientName );
            $_aTasks         = $_aTasks ? $_aTasks : array();
            $_nNow           = microtime( true );
            
            // Check the excessive background call protection interval 
            if ( ! self::$_fIgnoreLock ) {                
                $_nCalled = isset( $_aTasks['called'] ) 
                    ? $_aTasks['called'] 
                    : 0;
                if ( $_nCalled + self::$_iLockBackgroundCallInterval > $_nNow ) {    
                    return;    // if it's called within 10 seconds from the last time of calling this method, do nothing to avoid excessive calls.
                } 
            }
            
            // Renew the called time.
            $_aFlagKeys = array(
                'called'    =>    $_nNow,
            );
            // set a locked key so it prevents duplicated function calls due to too many calls caused by simultaneous accesses.
            AmazonAutoLinks_WPUtility::setTransient( 
                $_sTransientName, 
                $_aFlagKeys + $_aTasks, 
                self::getAllowedMaxExecutionTime() 
            );    
            
            // Compose a GET query array
            $_aGet = self::$_aGet;
            if ( defined( 'WP_DEBUG' ) ) {
                $_aGet[ 'debug' ] = WP_DEBUG;
            }
            unset( $_aGet[ 0 ] );
            
            // Load the site in the background.
            wp_remote_get(
                site_url(  '?' . http_build_query( $_aGet ) ), 
                array( 
                    'timeout'    => 0.01,
                    'sslverify'  => false, 
                    'cookies'    => $_aFlagKeys + array( $_sTransientName => true ),
                ) 
            );    

        }    
        
        /**
         * Performs a delayed background page load.
         * 
         * This gives the server enough time to store transients to the database in case massive simultaneous accesses occur.
         * 
         * @since            1.0.0
         */
        static private function _loadBackgroundPageWithDelay( $iSecond=1, $aGet=array() ) {
            
            sleep( $iSecond );
            
            if ( defined( 'WP_DEBUG' ) ) {
                $aGet[ 'debug' ] = WP_DEBUG;
            }            
            wp_remote_get(
                site_url(  '?' . http_build_query( $aGet ) ), 
                array( 
                    'timeout'    => 0.01, 
                    'sslverify'  => false, 
                    'cookies'    => array( md5( get_class() ) => true, 'delay' => true ),
                ) 
            );                
        }
                    
}