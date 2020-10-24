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
 * Provides methods to schedule events.
 * 
 * @since       3
 */
class AmazonAutoLinks_Event_Scheduler {

    /**
     * Schedules a pre-fetch task.
     * @since       3
     * @param       integer|array       a unit ID of integer or an output argument array.
     * @return      void
     * @action      aal_action_unit_prefetch
     */
    static public function prefetch( $iaArguments ) {
        
        if ( empty( $iaArguments ) ) {
            return;
        }
        $_aArguments = is_scalar( $iaArguments ) ? array( 'id' => $iaArguments ) : ( array ) $iaArguments;
        AmazonAutoLinks_PluginUtility::scheduleSingleWPCronTask( 'aal_action_unit_prefetch', array( $_aArguments ) );

    }

    /**
     * @var  array
     * @since 4.3.4
     */
    static private $___aReviewItemsIfNoPIR = array();

    /**
     * Schedules a background task to retrieve a product rating if a product information routine is not scheduled.
     * @param string  $sProductID       ASIN|locale|currency|language
     * @param integer $iCacheDuration
     * @param boolean $bForceRenew
     * @since 4.3.4
     * @return boolean|void
     */
    static public function scheduleReviewIfNoProductInformationRoutines( $sProductID, $iCacheDuration, $bForceRenew ) {
        if ( ! AmazonAutoLinks_Utility::hasBeenCalled( __METHOD__ ) ) {
            // must be called before `_replyToScheduleProductsInformation()`.
            add_action( 'shutdown', array( __CLASS__, 'replyToQueueReviewRetrievalIfNoPIR' ), 1 );
        }
        self::$___aReviewItemsIfNoPIR[] = func_get_args();
    }
        /**
         * Adds a background routine of product rating retrieval
         * only IF a product information routine for the product is not added.
         * @callback add_action() shutdown
         */
        static public function replyToQueueReviewRetrievalIfNoPIR() {
            if ( empty( self::$___aReviewItemsIfNoPIR ) ) {
                return;
            }
            foreach( self::$___aReviewItemsIfNoPIR as $_aParameters ) {
                $_aParameters = $_aParameters + array( null, null, null );
                $_sProductID = $_aParameters[ 0 ];
                // If the product information routine is added for this product, do not schedule this routine.
                if ( in_array( $_sProductID, self::$___aScheduledProductIDs ) ) {
                    continue;
                }
                self::scheduleReview( $_sProductID, $_aParameters[ 1 ], $_aParameters[ 2 ] );
            }
            AmazonAutoLinks_Shadow::see();  // Loads the site in the background.
        }    
    
    /**
     * @var   array
     * @since 4.3.4
     */
    static private $___aReviewItems = array();

    /**
     * Schedule a background task to retrieve customer reviews.
     *
     * @param  string  $sProductID  Format: {ASIN}|{locale}|{currency}|{language}
     * @param  integer $iCacheDuration
     * @param  boolean $bForceRenew
     * @return boolean|void true if scheduled. false if not scheduled. void when unknown whether scheduled or not.
     * @since  3.9.0
     * @since  4.3.4 Deprecated the `$sURL`, `$sASIN`, `$sLocale`, `$sCurrency`, and `$sLanguage` parameter.
     */
    static public function scheduleReview( $sProductID, $iCacheDuration, $bForceRenew ) {

        if ( version_compare( get_option( 'aal_tasks_version', '0' ), '1.0.0b01', '<' ) ) {
            $_bScheduled = self::___scheduleTask( 'aal_action_api_get_customer_review', $sProductID, $iCacheDuration, $bForceRenew );
            if ( ! $_bScheduled ) {
                return false;
            }
            AmazonAutoLinks_Shadow::see();  // Loads the site in the background.
            return true;
        }
        if ( empty( self::$___aReviewItems ) ) {
            add_action( 'shutdown', array( __CLASS__, 'replyToQueueReviewRetrieval' ), 20 );
        }
        self::$___aReviewItems[ $sProductID ] = func_get_args();
        return;    // whether scheduled or not is unknown.

    }
        /**
         * @callback add_action() shutdown
         * @since 4.3.4
         */
        static public function replyToQueueReviewRetrieval() {

            $_iCount    = 1;
            $_aTaskRows = array();
            foreach( self::$___aReviewItems as $_sProductID => $_aParameters ) {
                $_iNow        = time();
                $_aTaskRows[] = array(
                    'name'          => 'get_review_' . $_sProductID,  // (unique column)
                    'action'        => 'aal_action_api_get_customer_review',
                    'arguments'     => $_aParameters,
                    'creation_time' => date( 'Y-m-d H:i:s', $_iNow ),
                    'next_run_time' => date( 'Y-m-d H:i:s', $_iNow + ( $_iCount * 10 ) ),
                );
                $_iCount++;
            }
            $_oTaskTable = new AmazonAutoLinks_DatabaseTable_aal_tasks;
            $_aChunks    = array_chunk( $_aTaskRows, 50 );    // having too many rows may fail to set
            foreach( $_aChunks as $__aTaskRows ) {
                $_oTaskTable->insertRowsIgnore( $__aTaskRows );
            }
            self::scheduleTaskCheck( time() );
            AmazonAutoLinks_Shadow::see();  // Loads the site in the background.

        }

    /**
     * @var array Stores items holding product information including ASIN, locale, currency and language to update their ratings.
     * @since 4.3.4
     */
    static private $___aRatingItemsIfNoPIR = array();
    /**
     * Schedules a background task to retrieve a product rating if a product information routine is not scheduled.
     * @param string  $sProductID       ASIN|locale|currency|language
     * @param integer $iCacheDuration
     * @param boolean $bForceRenew
     * @since 4.3.4
     * @return boolean|void
     */
    static public function scheduleRatingIfNoProductInformationRoutines( $sProductID, $iCacheDuration, $bForceRenew ) {
        if ( ! AmazonAutoLinks_Utility::hasBeenCalled( __METHOD__ ) ) {
            // must be called before `_replyToScheduleProductsInformation()`.
            add_action( 'shutdown', array( __CLASS__, 'replyToQueueRatingRetrievalIfNoPIR' ), 1 );
        }
        self::$___aRatingItemsIfNoPIR[] = func_get_args();
    }
        /**
         * Adds a background routine of product rating retrieval
         * only IF a product information routine for the product is not added.
         * @callback add_action() shutdown
         */
        static public function replyToQueueRatingRetrievalIfNoPIR() {
            if ( empty( self::$___aRatingItemsIfNoPIR ) ) {
                return;
            }
            foreach( self::$___aRatingItemsIfNoPIR as $_aParameters ) {
                $_aParameters = $_aParameters + array( null, null, null );
                $_sProductID = $_aParameters[ 0 ];
                // If the product information routine is added for this product, do not schedule this routine.
                if ( in_array( $_sProductID, self::$___aScheduledProductIDs ) ) {
                    continue;
                }
                self::scheduleRating( $_sProductID, $_aParameters[ 1 ], $_aParameters[ 2 ] );
            }
            AmazonAutoLinks_Shadow::see();  // Loads the site in the background.
        }

    /**
     * @var array Stores items holding product information including ASIN, locale, currency and language to update their ratings.
     * @since 4.3.4
     */
    static private $___aRatingItems = array();
    /**
     * Schedules a background task to retrieve a product rating.
     * @param string  $sProductID       ASIN|locale|currency|language
     * @param integer $iCacheDuration
     * @param boolean $bForceRenew
     * @since 4.3.4
     * @return boolean|void
     */
    static public function scheduleRating( $sProductID, $iCacheDuration, $bForceRenew ) {
        if ( version_compare( get_option( 'aal_tasks_version', '0' ), '1.0.0b01', '<' ) ) {
            $_bScheduled = self::___scheduleTask(
                'aal_action_api_get_product_rating',  // action name
                $sProductID, $iCacheDuration, $bForceRenew
            );
            if ( ! $_bScheduled ) {
                return false;
            }
            AmazonAutoLinks_Shadow::see();  // Loads the site in the background.
            return true;
        }
        if ( empty( self::$___aRatingItems ) ) {
            add_action( 'shutdown', array( __CLASS__, 'replyToQueueRatingRetrieval' ), 20 );
        }
        self::$___aRatingItems[ $sProductID ] = func_get_args();
        return;    // whether scheduled or not is unknown.
    }
        /**
         * @callback add_action shutdown
         * @since 4.3.4
         */
        static public function replyToQueueRatingRetrieval() {

            $_iCount    = 1;
            $_aTaskRows = array();
            foreach( self::$___aRatingItems as $_sProductID => $_aParameters ) {
                $_iNow        = time();
                $_aTaskRows[] = array(
                    'name'          => 'get_rating_' . $_sProductID,  // (unique column)
                    'action'        => 'aal_action_api_get_product_rating',
                    'arguments'     => $_aParameters,
                    'creation_time' => date( 'Y-m-d H:i:s', $_iNow ),
                    'next_run_time' => date( 'Y-m-d H:i:s', $_iNow + ( $_iCount * 10 ) ),
                );
                $_iCount++;
            }
            $_oTaskTable = new AmazonAutoLinks_DatabaseTable_aal_tasks;
            $_aChunks    = array_chunk( $_aTaskRows, 50 );    // having too many rows may fail to set
            foreach( $_aChunks as $__aTaskRows ) {
                $_oTaskTable->insertRowsIgnore( $__aTaskRows );
            }
            self::scheduleTaskCheck( time() );
            AmazonAutoLinks_Shadow::see();  // Loads the site in the background.

        }

    /**
     * @var   array
     * @since 3.7.7
     */
    static private $___aScheduledProductInformation = array();
    /**
     * @var    array Stores product IDs of scheduled background routines of product information retrieval.
     * @remark Referred by `...IfNoProductInformationRoutine()` methods.
     * @since  4.3.4
     */
    static private $___aScheduledProductIDs = array();

    /**
     * Schedules an action of getting product information in the background.
     *
     * @param  string    $sAssociateIDLocaleCurLang
     * @param  string    $sASIN
     * @param  integer   $iCacheDuration
     * @param  boolean   $bForceRenew
     * @param  string    $sItemFormat
     * @return void
     * @since  3
     * @since  3.5.0     Renamed from `getProductInfo()`.
     * @since  3.7.0     Added the `$sItemFormat` parameter so that the background routine can check whether to perform optional HTTP API requests.
     * @since  3.7.7     Changed the return value to `void` as no calls use it and this method is going to schedule multiple items at once.
     * @since  3.8.12    Added the `$aAPIRawItem` parameter so that the background routine can skip an API request.
     * @since  3.9.0     Changed the first parameter to be associate_id|locale|currency|language from associate_id|locale.
     * @since  3.9.0     Removed the `$sLocale` parameter
     * @since  4.3.0     Deprecated the `$aAPIRawItem` parameter as it was not used.
     */
    static public function scheduleProductInformation( $sAssociateIDLocaleCurLang, $sASIN, $iCacheDuration, $bForceRenew=false, $sItemFormat='' ) {

        $_oOption = AmazonAutoLinks_Option::getInstance();
        if ( ! $_oOption->isAPIConnected() ) {
            return;
        }

        // 4.0.1 Case: the PA-API keys are set but not for the requested locale. This occurs with embedded links.
        $_aRequestBaseInfo = explode( '|', $sAssociateIDLocaleCurLang );
        $_sLocale          = $_aRequestBaseInfo[ 1 ]; // the 2nd item
        if ( ! $_oOption->isAPIKeySet( $_sLocale ) ) {
            return;
        }

        // Register a callback to an action hook only for the first time of calling this method.
        if ( empty( self::$___aScheduledProductInformation ) ) {
            add_action(
                'shutdown',
                array( __CLASS__, '_replyToScheduleProductsInformation' ),
                2   // higher priority
            );
        }

        /**
         * These items need to be grouped by Associate ID + Locale as API requests cannot be done at once if these are different.
         * The user may be setting different Associate IDs between two units shown in one page. The same applies to the locale.
         */
        if ( ! isset( self::$___aScheduledProductInformation[ $sAssociateIDLocaleCurLang ] ) ) {
            self::$___aScheduledProductInformation[ $sAssociateIDLocaleCurLang ] = array();
        }

        // [4.3.4]
        $_sCurrency = $_aRequestBaseInfo[ 2 ];
        $_sLanguage = $_aRequestBaseInfo[ 3 ];
        self::$___aScheduledProductIDs[] = "{$sASIN}|{$_sLocale}|{$_sCurrency}|{$_sLanguage}";

        self::$___aScheduledProductInformation[ $sAssociateIDLocaleCurLang ][ $sASIN ] = func_get_args() + (
            defined( 'WP_DEBUG' ) && WP_DEBUG
                ? array(
                    'debug' => array(
                        'time'         => microtime( true ),
                        'stacktrace'   => AmazonAutoLinks_Debug::getStackTrace(),
                        'page_load_id' => AmazonAutoLinks_Utility::getPageLoadID(),
                    ),
                )
                : array()
        );

    }
        /**
         * Schedules retrievals of product information at once.
         * @since       3.7.7
         * @callback    action      shutdown        With the priority of `2`.
         */
        static public function _replyToScheduleProductsInformation() {

            $_sTaskTableVersion = get_option( 'aal_tasks_version', '0' );
            if ( version_compare( $_sTaskTableVersion, '1.0.0b01', '>=' ) ) {
                self::___setTasksForProductInformationAPIRequest( self::$___aScheduledProductInformation );
            } else {
                foreach( self::$___aScheduledProductInformation as $_sAssociateIDLocaleCurLang => $_aFetchingItems ) {
                    self::___scheduleProductInformationAPIRequest( $_sAssociateIDLocaleCurLang, $_aFetchingItems );
                }
            }

            self::$___aScheduledProductInformation = array();   // clear
            AmazonAutoLinks_Shadow::see();  // loads the site in the background

        }
            /**
             * Sets tasks in the plugin task table.
             * @param   array $aScheduledProducts
             * @since 4.3.0
             */
            static private function ___setTasksForProductInformationAPIRequest( array $aScheduledProducts ) {

                if ( empty( $aScheduledProducts ) ) {
                    return;
                }

                $_aTaskRows = array();
                foreach( $aScheduledProducts as $_sAssociateIDLocaleCurLang => $_aItems ) {

                    $_aRequestInfo = explode( '|', $_sAssociateIDLocaleCurLang ) + array( '', '', '', '' );
                    $_sAssociateID = $_aRequestInfo[ 0 ];
                    $_sLocale      = $_aRequestInfo[ 1 ];
                    $_sCurrency    = $_aRequestInfo[ 2 ];
                    $_sLanguage    = $_aRequestInfo[ 3 ];

                    foreach( $_aItems as $_sASIN => $_aItem ) {
                        $_aDebugInfo  = AmazonAutoLinks_Utility::getElementAsArray( $_aItem, array( 'debug' ) );
                        $_aTaskRows[] = array(
                            // ASIN|Locale|Currency|Language
                            'name'          => $_aItem[ 1 ] . '|' . $_sLocale . '|' . $_sCurrency . '|' . $_sLanguage,  // product_id
                            'action'        => 'aal_action_api_get_products_info',
                            'arguments'     => array( array( $_aItem ), $_sAssociateID, $_sLocale, $_sCurrency, $_sLanguage, $_aDebugInfo ),
                            'creation_time' => date( 'Y-m-d H:i:s', time() ),
                            'next_run_time' => date( 'Y-m-d H:i:s', time() ),
                        );
                    }

                }
                $_oTaskTable = new AmazonAutoLinks_DatabaseTable_aal_tasks;
                $_aChunks    = array_chunk( $_aTaskRows, 50 );    // having too many rows may fail to set
                foreach( $_aChunks as $__aTaskRows ) {
                    $_oTaskTable->insertRowsIgnore( $__aTaskRows );
                }
                self::scheduleTaskCheck( time() );

            }

            /**
             * @param string $sAssociateIDLocaleCurLang
             * @param array $aFetchingItems
             * @since   3.7.7
             * @see https://webservices.amazon.com/paapi5/documentation/get-items.html
             */
            static private function ___scheduleProductInformationAPIRequest( $sAssociateIDLocaleCurLang, array $aFetchingItems ) {

                // Sort the array by key in order to prevent unnecessary look-ups due to different orders
                // as `wp_next_scheduled()` stores and identify actions based on the serialized passed arguments.
                // The keys are set by ASIN.
                ksort( $aFetchingItems );

                // Divide items by 10 as the `ItemLookup` operation API parameter accepts up to 10 items at a time.
                $_aChunks      = array_chunk( $aFetchingItems, 10 );

                $_aParts       = explode( '|', $sAssociateIDLocaleCurLang );
                $_sAssociateID = $_aParts[ 0 ];
                $_sLocale      = $_aParts[ 1 ];
                $_sCurrency    = $_aParts[ 2 ];
                $_sLanguage    = $_aParts[ 3 ];
                /**
                 * At this point the chunk array `$_aChunks` looks like this.
                 * The keys will be converted to numeric index.
                 *
                 * ```
                 * array(
                 *      0 => array(
                 *          0 => array( 0 => $sAssociateID|Locale|Cur|Lang, 1 => $sASIN,  2 => $iCacheDuration, 3 => $bForceRenew, 4 => $sItemFormat ),
                 *          1 ...
                 *          2 ...
                 *          10 => array( 0 => $sAssociateID|Locale|Cur|Lang, 1 => $sASIN,  2 => $iCacheDuration, 3 => $bForceRenew, 4 => $sItemFormat ),
                 *      ),
                 *      1 => array(
                 *          0 => array( 0 => $sAssociateID|Locale|Cur|Lang, 1 => $sASIN,  2 => $iCacheDuration, 3 => $bForceRenew, 4 => $sItemFormat ),
                 *          1 ...
                 *          2 ...
                 *      ),
                 * )
                 * ```
                 */
                foreach ( $_aChunks as $_iIndex => $_aItems ) {
                    self::___scheduleTask(
                        'aal_action_api_get_products_info',  // action name
                        $_aItems,
                        $_sAssociateID,
                        $_sLocale,
                        $_sCurrency,    // 3.9.0
                        $_sLanguage     // 3.9.0
                    );
                }
            }
    
        /**
         * Stores how many actions are schedules by action name.
         */
        static protected $_aCounters = array();

        /**
         * @remark      The difference between AmazonAutoLinks_PluginUtility::scheduleSingleWPCronTask() is
         * that this adds a delay to scheduled WP Cron items if they have same action hook name already registered.
         * @return      boolean
         */
        static private function ___scheduleTask( /* $_sActionName, $mArgument1, $mArgument2, ... */ ) {
             
            $_aParams       = func_get_args() + array( null, null );
            $_sActionName   = array_shift( $_aParams ); // the first element
            $_aArguments    = AmazonAutoLinks_Utility::getAsArray( $_aParams ); // the rest

            // If already scheduled, skip.
            if ( wp_next_scheduled( $_sActionName, $_aArguments ) ) {
                return false; 
            }

            self::$_aCounters[ $_sActionName ] = isset( self::$_aCounters[ $_sActionName ] )
                ? self::$_aCounters[ $_sActionName ] + 1
                : 1;
            wp_schedule_single_event(
                time() + self::$_aCounters[ $_sActionName ] - 1, // now + registering counts, giving one second delay each to avoid timeouts when handling tasks and api rate limit run out.
                $_sActionName,  // the AmazonAutoLinks_Event class will check this action hook and executes it with WP Cron.
                $_aArguments    // must be enclosed in an array.
            );
            return true;
            
        }

    /**
     * @param  integer $iTime
     * @param  boolean $bDuplicateCheck
     * @return boolean
     * @since  4.3.6
     */
    static public function scheduleTaskCheckResume( $iTime=0, $bDuplicateCheck=true ) {
        return self::scheduleTaskCheck( $iTime, $bDuplicateCheck, 'aal_action_resume_check_tasks' );
    }

    /**
     * Schedule a task check.
     * @param  integer iTime The scheduling time.
     * If a time is not given, it will schedule with the most closest scheduled time, regardless of whether a duplicate exists or not.
     * @param  boolean $bDuplicateCheck
     * @param  string  $sActionName
     * @return boolean true on success; otherwise, false.
     * @see    wp_schedule_single_event()
     */
    static public function scheduleTaskCheck( $iTime=0, $bDuplicateCheck=true, $sActionName='aal_action_check_tasks' ) {

        if ( version_compare( get_option( 'aal_tasks_version', '0' ), '1.0.0b01', '<' ) ) {
            return false;
        }

        if ( $iTime ) {
            $_aArguments = $bDuplicateCheck ? array() : array( $iTime );
            return AmazonAutoLinks_WPUtility::scheduleSingleWPCronTask( $sActionName, $_aArguments, $iTime );
        }

        $_oTaskTable = new AmazonAutoLinks_DatabaseTable_aal_tasks;
        $_aDueItems  = AmazonAutoLinks_Utility::getAsArray( $_oTaskTable->getDueItems() );
        $_aFirstTask = reset( $_aDueItems );
        if ( empty( $_aFirstTask ) ) {
            return false;
        }
        $_sTime = AmazonAutoLinks_Utility::getElement( $_aFirstTask, 'next_run_time', '' );
        if ( empty( $_sTime ) ) {
            return false;
        }
        $_iTime = strtotime( $_sTime );

        // Giving a unique argument so the duplicate check will pass.
        $_aArguments = $bDuplicateCheck ? array() : array( $_iTime );
        return AmazonAutoLinks_WPUtility::scheduleSingleWPCronTask( $sActionName, $_aArguments, $_iTime );

    }

}