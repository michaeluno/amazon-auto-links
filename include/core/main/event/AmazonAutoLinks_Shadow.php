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
     * The get method query array holding key-value pairs.
     * @var array
     */
    static private $___aGet = array();

    /**
     * Extra data sent to the background routine with the POST HTTP method.
     * @var array
     */
    static private $___aPost = array();

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
        $_sKey = 'aal_' . md5( get_class() );
        return isset( $_COOKIE[ $_sKey ] );
    }

    /**
     * Handles plugin cron tasks.
     * 
     * Called from the constructor. 
     * 
     * @since    1.0.0
     * @param    array   $aActionHooks
     * @return   array   Executed WP Cron tasks.
     */
    static private function ___handleCronTasks( array $aActionHooks ) {

        $_sTransientName = 'aal_' . md5( get_class() );
        $_aFlags         = AmazonAutoLinks_WPUtility::getTransientWithoutCacheAsArray( $_sTransientName ) + array(
            '_called'    => 0, '_locked'    => 0,
        );
        $_fNow           = microtime( true );

        // If it's still locked do nothing. Locked duration: 10 seconds.
        if ( $_aFlags[ '_locked' ] + self::$___iLockCronInterval > $_fNow ) {
            return array();
        }

        // Newly retrieve the plugin cron scheduled tasks since some may be already triggered by WP Cron.
        $_aWPCronTasks = self::getScheduledWPCronTasksByActionName( $aActionHooks );
        if ( empty( $_aWPCronTasks ) ) {                    
            return array();
        } 

        // Set/renew the locked time and leave `_called` to inherit the called time.
        $_aFlags[ '_locked' ] = $_fNow;

        // Lock the process.
        AmazonAutoLinks_WPUtility::setTransient( $_sTransientName, $_aFlags, AmazonAutoLinks_Utility::getAllowedMaxExecutionTime() );
        return self::___doTasks( $_aWPCronTasks );

    }
        /**
         * Performs the plugin-specific scheduled tasks in the background.
         *
         * This should only be called when the "aal_{ md5( get_class() ) }" transient is present.
         *
         * @since 1.0.0
         * @param array $aWPCronTasks
         * @return array An array holding executed cron tasks.
         */
        static private function ___doTasks( array $aWPCronTasks ) {

            $_aExecuted =array();
            if ( empty( $aWPCronTasks ) ) {
                return $_aExecuted;
            }

            do_action( 'aal_action_do_plugin_cron', $aWPCronTasks );

            foreach( $aWPCronTasks as $_iTimeStamp => $aCronHooks ) {

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
                        $_aExecuted[ $sActionName ] = $aArgs;

                    }

                }

            }
            return $_aExecuted;

        }

    /**
     * Sets plugin specific cron tasks by extracting plugin's cron jobs from the WP cron job array.
     *  
     * @since 1.0.0
     * @param array $aActionHooks   Action hook names to trigger. If empty, all due cron tasks will be triggred.
     * @return array A array holding tasks registered for WP_Cron.
     */
    static public function getScheduledWPCronTasksByActionName( array $aActionHooks ) {
        
        $_aTheTasks     = array();
        $_aWPCronTasks  = _get_cron_array();

        // if the cron tasks array is empty, do nothing.
        if ( ! $_aWPCronTasks ) {
            return $_aTheTasks;
        }

        $_fNow                 = microtime( true );    // the current time stamp in micro seconds.
        $_aScheduledTimeStamps = array_keys( $_aWPCronTasks );
        if ( isset( $_aScheduledTimeStamps[ 0 ] ) && $_aScheduledTimeStamps[ 0 ] > $_fNow ) {
            return $_aTheTasks; // the first element has the least number.
        }
                
        foreach ( $_aWPCronTasks as $_iTimeStamp => $_aScheduledActionHooks ) {
            if ( $_iTimeStamp > $_fNow ) {
                break; // see the definition of the wp_cron() function.
            }
            foreach ( ( array ) $_aScheduledActionHooks as $_sScheduledActionHookName => $_aArguments ) {
                if ( empty( $aActionHooks ) || in_array( $_sScheduledActionHookName, $aActionHooks ) ) {
                    $_aTheTasks[ $_iTimeStamp ][ $_sScheduledActionHookName ] = $_aArguments;
                }
            }
        }
        return $_aTheTasks;
                
    }

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

        if ( self::___isDoingWPCronOrAjax() ) {
            return;
        }
        // Prevent recursive calls.
        if ( self::___isBackground() ) {
            return;
        }

        self::$___aGet    = ( array ) $aGet + self::$___aGet;
        if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
            self::$___aPost[ 'debug' ]   = isset( self::$___aPost[ 'debug' ] ) ? self::$___aPost[ 'debug' ] : array();
            self::$___aPost[ 'debug' ][] = array(
                'time'          => microtime( true ),
                'stacktrace'    => AmazonAutoLinks_Debug::getStackTrace( new Exception ),
                'page_load_id'  => AmazonAutoLinks_Utility::getPageLoadID(),
            );
        }

        if ( did_action( 'shutdown' ) ) {
            self::replyToLoadSiteInBackground();
            return;
        }
        add_action( 'shutdown', array( __CLASS__, 'replyToLoadSiteInBackground' ), 999 );

    }    
        /**
         * @since       1.0.0
         * @callback    add_action  shutdown
         */
        static public function replyToLoadSiteInBackground() {

            if ( AmazonAutoLinks_Utility::hasBeenCalled( __METHOD__ ) ) {
                return;
            }

            // Retrieve the plugin scheduled tasks array.
            $_sTransientName = 'aal_' . md5( get_class() );
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

            // Load the site in the background.
            $_sDebugContext = defined( 'WP_DEBUG' ) && WP_DEBUG
                ? '&debug[context]=aal_shadow'
                : '';
            wp_remote_get(
                site_url(  "?" . http_build_query( self::$___aGet ) . $_sDebugContext ),
                array( 
                    'timeout'    => 0.01,
                    'sslverify'  => false, 
                    'cookies'    => array(
                        $_sTransientName => true
                    ),
                    'body'       => empty( self::$___aPost ) ? null : self::$___aPost,
                    'method'     => empty( self::$___aPost ) ? 'GET' : 'POST',
                ) 
            );    

        }    

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

    /**
     * Manually triggers cron task actions.
     * @param array $aActionHooks The names of action hooks to trigger. If empty, all due WP Cron tasks will be triggered.
     * @since 4.3.0
     * @return array Executed WP Cron tasks.
     */
    static public function doTasks( array $aActionHooks=array() ) {
        return self::___handleCronTasks( $aActionHooks );
    }

}