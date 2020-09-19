<?php
/**
 * Provides a means of an alternative to WP Cron on the server that disables WP Cron.
 * 
 * @package     AmazonAutoLinks_Shadow
 * @copyright   Copyright (c) 2013, Michael Uno
 * @authorurl   https://github.com/michaeluno/WP_Shadow
*/
class AmazonAutoLinks_Shadow {
        
    /**
     * The interval in seconds that locks the background calls.
     * 
     * This is for preventing too many background calls to be performed.
     * 
     * @since   1.0.0
     * @var     integer
     */
    static private $___iLockInterval = 10;
        
    /**
     * The interval in seconds that locks the cron.
     * 
     * This is for preventing WordPress pseudo cron jobs from being performed too frequently. 
     * 
     * @since   1.0.0
     * @var     integer
     */
    static private $___iLockCronInterval = 10;    


    /**
     * The get method query array holding the key-value pairs.
     * @var array
     */
    static private $___aGet = array();
    
    /**
     * Checks if the page load is performed in the background
     * and if so, performs the given actions if scheduled.
     * 
     * @since   1.0.0
     * @param   array   $aActionHooks
     */
    public function __construct( array $aActionHooks ) {

        if ( empty( $aActionHooks ) ) {
            return;
        }

        if ( self::___isDoingWPCronOrAjax() ) {
            return;
        }
        if ( ! $this->___isBackground() ) {
            return;
        }

        // At this point, this is a background page load.

        self::___setBackgroundFlags();

        // Do not process if a delay is not set.
        // @deprecated 4.3.0
/*        if ( ! $this->___isBackgroundWithDelay() ) {
            $_iDelay = 2;
            $_aGet   = defined( 'WP_DEBUG' ) && WP_DEBUG
                ? array( 'delay' => $_iDelay ) + $_GET
                : $_GET;
            $this->___loadSiteInBackgroundWithDelay( $_iDelay, $_aGet );
            exit;
        }*/

        $this->___handleCronTasks( $aActionHooks );
        exit;
        
    }
    
    /**
     * Checks whether the page is loaded in the background.
     * 
     * @since   1.0.0
     * @since   4.3.0       Deprecated the `$bIsDelayed` parameter.
     * @return  boolean
     */    
    static private function ___isBackground() {
        $_sKey = md5( get_class() );
        return isset( $_COOKIE[ $_sKey ] );
    }

    /**
     * @return bool
     * @since   4.3.0
     * @deprecated 4.3.0
     */
/*    static private function ___isBackgroundWithDelay() {
        $_sKey = md5( get_class() );
        return isset( $_COOKIE[ 'delay' ], $_COOKIE[ $_sKey ] );
    }*/
    
    /**
     * Handles plugin cron tasks.
     * 
     * Called from the constructor. 
     * 
     * @since    1.0.0
     * @param    array   $aActionHooks
     */
    private function ___handleCronTasks( array $aActionHooks ) {

        $_sTransientName = md5( get_class() );
        $_aFlags         = AmazonAutoLinks_WPUtility::getTransientWithoutCacheAsArray( $_sTransientName ) + array(
            '_called'    => 0, '_locked'    => 0,
        );
        $_fNow           = microtime( true );

        // If it's still locked do nothing. Locked duration: 10 seconds.
        if ( $_aFlags[ '_locked' ] + self::$___iLockCronInterval > $_fNow ) {
            return;
        }

        // Newly retrieve the plugin cron scheduled tasks since some may be already triggered by WP Cron.
        $_aTasks = $this->___getScheduledWPCronTasksByActionName( $aActionHooks );
        if ( empty( $_aTasks ) ) {                    
            return;
        } 

        // Set/renew the locked time and leave `_called` to inherit the called time.
        $_aFlags[ '_locked' ] = $_fNow;

        // Lock the process.
        AmazonAutoLinks_WPUtility::setTransient( $_sTransientName, $_aFlags, AmazonAutoLinks_Utility::getAllowedMaxExecutionTime() );
        $this->___doTasks( $_aTasks );

    }

    /**
     * Performs the plugin-specific scheduled tasks in the background.
     * 
     * This should only be called when the md5( get_class() ) transient is present. 
     * 
     * @since 1.0.0
     * @param array $aTasks
     */
    private function ___doTasks( array $aTasks ) {
        
        foreach( $aTasks as $_iTimeStamp => $aCronHooks ) {

            // the '_locked' key flag element should be skipped
            if ( ! is_array( $aCronHooks ) ) {
                continue;
            }

            foreach( $aCronHooks as $sActionName => $_aActions ) {
                
                foreach( $_aActions as $sHash => $aArgs ) {
                                                                        
                    $sSchedule = $aArgs[ 'schedule' ];
                    if ( $sSchedule != false ) {
                        $aNewArgs = array( $_iTimeStamp, $sSchedule, $sActionName, $aArgs[ 'args' ] );
                        call_user_func_array( 'wp_reschedule_event', $aNewArgs );
                    }
                    wp_unschedule_event( $_iTimeStamp, $sActionName, $aArgs[ 'args' ] );
                    do_action_ref_array( $sActionName, $aArgs[ 'args' ] );
                
                }
            }

        }
        
    }
    
    /**
     * Sets plugin specific cron tasks by extracting plugin's cron jobs from the WP cron job array.
     *  
     * @since 1.0.0
     * @param array $aActionHooks
     * @return array A array holding tasks registered for WP_Cron.
     */
    private function ___getScheduledWPCronTasksByActionName( array $aActionHooks ) {
        
        $_aTheTasks = array();        
        $_aTasks    = _get_cron_array();

        // if the cron tasks array is empty, do nothing.
        if ( ! $_aTasks ) {
            return $_aTheTasks;
        }

        $_iGMTTime             = microtime( true );    // the current time stamp in micro seconds.
        $_aScheduledTimeStamps = array_keys( $_aTasks );
        if ( isset( $_aScheduledTimeStamps[ 0 ] ) && $_aScheduledTimeStamps[ 0 ] > $_iGMTTime ) {
            return $_aTheTasks; // the first element has the least number.
        }
                
        foreach ( $_aTasks as $_iTimeStamp => $_aScheduledActionHooks ) {
            if ( $_iTimeStamp > $_iGMTTime ) {
                break; // see the definition of the wp_cron() function.
            }
            foreach ( ( array ) $_aScheduledActionHooks as $_sScheduledActionHookName => $_aArguments ) {
                if ( in_array( $_sScheduledActionHookName, $aActionHooks ) ) {
                    $_aTheTasks[ $_iTimeStamp ][ $_sScheduledActionHookName ] = $_aArguments;
                }
            }
        }
        return $_aTheTasks;
                
    }

    /**
     * Accesses the site in the background.
     * 
     * @since   1.0.1
     * @param   array $aGet The GET HTTP method array.
     * @deprecated 4.3.0    Not used anymore.
     */
/*    static public function gaze( $aGet=array() ) {
        
        if ( self::___isDoingWPCronOrAjax() ) {
            return;
        }
        if ( AmazonAutoLinks_Utility::hasBeenCalled( __METHOD__ ) ) {
            return;
        }

        self::___loadSiteInBackgroundWithDelay( 0, $aGet );
        
    }*/
    
    /**
     * Accesses the site in the background at the end of the script execution.
     * 
     * This is used to trigger cron events in the background and sets a static flag so that it ensures it is done only once per page load.
     *
     * @since   1.0.0
     * @since   4.3.0   Deprecated the `$fIgnoreLock` parameter.
     * @param   array   $aGet
     */
    static public function see( array $aGet=array() ) {

        // Prevent recursive calls.
        if ( self::___isDoingWPCronOrAjax() ) {
            return;
        }
        if ( AmazonAutoLinks_Utility::hasBeenCalled( __METHOD__ ) ) {
            return;
        }
        
        // Store the static properties.
        self::$___aGet = ( array ) $aGet + self::$___aGet;

        if ( did_action( 'shutdown' ) ) {
            self::replyToLoadSiteInBackground();
            return;    // important as what the action has performed does not mean the action never will be fired again.
        }
        add_action( 'shutdown', array( __CLASS__, 'replyToLoadSiteInBackground' ), 999 );

    }    
        /**
         * @since       1.0.0
         * @callback    add_action  shutdown
         */
        static public function replyToLoadSiteInBackground() {

            // Retrieve the plugin scheduled tasks array.
            $_sTransientName = md5( get_class() );
            $_aFlags         = AmazonAutoLinks_WPUtility::getTransientWithoutCacheAsArray( $_sTransientName ) + array(
                '_called' => 0,
            );
            $_fNow           = microtime( true );
            
            // Prevent excessive background calls.
            // If called within the set interval seconds from the last time of calling this method,
            // do nothing to avoid excessive calls.
            if ( $_aFlags[ '_called' ] + self::$___iLockInterval > $_fNow ) {
                return;
            } 
            
            
            // Renew the called time.
            $_aFlags[ '_called' ] = $_fNow;

            // Set a lock so to prevent duplicated function calls with simultaneous accesses.
            AmazonAutoLinks_WPUtility::setTransient( $_sTransientName, $_aFlags, AmazonAutoLinks_Utility::getAllowedMaxExecutionTime() );

            // $_GET
            $_aGet = self::$___aGet;
            if ( defined( 'WP_DEBUG' ) ) {
                $_aGet[ 'debug' ] = 'aal_shadow';
            }
            unset( $_aGet[ 0 ] );
            
            // Load the site in the background.
            wp_remote_get(
                site_url(  '?' . http_build_query( $_aGet ) ), 
                array( 
                    'timeout'    => 0.01,
                    'sslverify'  => false, 
                    'cookies'    => $_aFlags + array(
                        $_sTransientName => true
                    ),
                ) 
            );    

        }    
        
        /**
         * Performs a delayed background page load.
         * 
         * This gives the server enough time to store transients to the database in case massive simultaneous accesses occur.
         * 
         * @since 1.0.0
         * @param integer $iSecond   How many seconds to delay.
         * @param array   $aGet      The HTTP GET method array.
         * @deprecated 4.3.0
         */
/*        static private function ___loadSiteInBackgroundWithDelay( $iSecond=1, array $aGet=array() ) {
            
            sleep( $iSecond );
            
            if ( defined( 'WP_DEBUG' ) ) {
                $aGet[ 'debug' ] = WP_DEBUG;
            }            
            wp_remote_get(
                site_url( '?' . http_build_query( $aGet ) ),
                array( 
                    'timeout'    => 0.01, 
                    'sslverify'  => false, 
                    'cookies'    => array(
                        md5( get_class() ) => true,
                        'delay' => true
                    ),
                ) 
            );

        }*/

    /**
     * @return boolean
     * @since 4.3.0
     */
    static private function ___isDoingWPCronOrAjax() {
        return self::___isDoingWPCron() || self::___isDoingAjax();
    }

    /**
     * @return boolean
     * @since 4.3.0
     */
    static private function ___isDoingWPCron() {
        if ( isset( $_GET[ 'doing_wp_cron' ] ) ) {
            return true;
        }
        if ( defined( 'DOING_CRON' ) && DOING_CRON ) {
            return true;
        }
        return false;
    }

    /**
     * @return boolean
     * @since 4.3.0
     */
    static private function ___isDoingAjax() {
        if ( isset( $GLOBALS[ 'pagenow' ] ) && $GLOBALS[ 'pagenow' ] == 'admin-ajax.php' ) {
            return true;
        }
        if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
            return true;
        }
        return false;
    }

    /**
     * Tells WordPress this is a background routine by setting the Cron flag.
     * @since   4.3.0
     */
    static private function ___setBackgroundFlags() {
        if ( ! defined( 'DOING_CRON' ) ) {
            define( 'DOING_CRON', true );
        }
        if ( ! defined( 'WP_USE_THEMES' ) ) {
            define( 'WP_USE_THEMES', false );
        }
        ignore_user_abort( true );
    }
                    
}