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

        $_aArguments = is_scalar( $iaArguments )
            ? array( 'id' => $iaArguments )
            : ( array ) $iaArguments;

        // Otherwise, it's an argument.
        $_bScheduled = self::___scheduleTask(
            'aal_action_unit_prefetch',  // action name
            $_aArguments // arguments
        );

    }

    /**
     * @since       3.3.0
     * @sicne       3.5.0           Renamed from `getSimliarProducts()`.
     * @action      schedule        aal_action_api_get_similar_products
     * @deprecated  Similar products are deprecated in v3.9.0.
     */
    static public function scheduleSimilarProducts( $aSimilarProductASINs, $sASIN, $sLocale, $sAssociateID, $iCacheDuration, $bForceRenew ) {

         // @deprecated 3.8.5 Even there is no item, schedule a background task
         // so that an empty value will be set instead of `null` which prevents automatic task rescheduling.
//        if ( empty( $aSimilarProductASINs ) ) {
//            return false;
//        }

        $_bScheduled = self::___scheduleTask( 
            'aal_action_api_get_similar_products',  // action name
            array( $aSimilarProductASINs, $sASIN, $sLocale, $sAssociateID, $iCacheDuration, $bForceRenew )
        );
        if ( ! $_bScheduled ) {
            return false;
        }
        
        // Loads the site in the background. The method takes care of doing it only once in the entire page load.
        AmazonAutoLinks_Shadow::see();
        return true;
    }
    
    /**
     * Schedule a background task to retrieve customer reviews.
     *
     * @action      schedule        aal_action_api_get_customer_review
     * @since       unknown
     * @since       3.5.0           Renamed from `getCustomerReviews()` as there was a method with the same name.
     * @deprecated  3.9.0       No longer works with PA-API5
     */
    static public function scheduleCustomerReviews( $sURL, $sASIN, $sLocale, $iCacheDuration, $bForceRenew ) {

        if ( empty( $sURL ) ) {
            return false;
        }

        $_bScheduled = self::___scheduleTask( 
            'aal_action_api_get_customer_review',  // action name
            array( $sURL, $sASIN, $sLocale, $iCacheDuration, $bForceRenew )
        );
        if ( ! $_bScheduled ) {
            return false;
        }
        
        // Loads the site in the background. The method takes care of doing it only once in the entire page load.
        AmazonAutoLinks_Shadow::see();
        return true;
    
    }
    /**
     * Schedule a background task to retrieve customer reviews.
     *
     * @action      schedule        aal_action_api_get_customer_review2
     * @since       3.9.0
     */
    static public function scheduleCustomerReviews2( $sURL, $sASIN, $sLocale, $iCacheDuration, $bForceRenew, $sCurrency, $sLanguage ) {

        $_bScheduled = self::___scheduleTask(
            'aal_action_api_get_customer_review2',  // action name
            array( $sURL, $sASIN, $sLocale, $iCacheDuration, $bForceRenew, $sCurrency, $sLanguage )
        );
        if ( ! $_bScheduled ) {
            return false;
        }
        AmazonAutoLinks_Shadow::see();  // Loads the site in the background.
        return true;

    }

    /**
     * @var array
     * @since   3.7.7
     */
    static private $___aScheduledProductInformation = array();

    /**
     * Schedules an action of getting product information in the background.
     *
     * @param string    $sAssociateIDLocaleCurLang
     * @param string    $sASIN
     * @param integer   $iCacheDuration
     * @param boolean   $bForceRenew
     * @param string    $sItemFormat
     * @param array     $aAPIRawItem
     *
     * @return      void
     * @since       3
     * @since       3.5.0       Renamed from `getProductInfo()`.
     * @since       3.7.0       Added the `$sItemFormat` parameter so that the background routine can check whether to perform optional HTTP API requests.
     * @since       3.7.7       Changed the return value to `void` as no calls use it and this method is going to schedule multiple items at once.
     * @since       3.8.12      Added the `$aAPIRawItem` parameter so that the background routine can skip an API request.
     * @since       3.9.0       Changed the first parameter to be associate_id|locale|currency|language from associate_id|locale.
     * @since       3.9.0       Removed the `$sLocale` parameter
     */
    static public function scheduleProductInformation( $sAssociateIDLocaleCurLang, $sASIN, $iCacheDuration, $bForceRenew=false, $sItemFormat='', $aAPIRawItem=array() ) {

        $_oOption = AmazonAutoLinks_Option::getInstance();
        if ( ! $_oOption->isAPIConnected() ) {
            return;
        }

        // 4.0.1+ Case: the PA-API keys are set but not for the requested locale. This occurs with embedded links.
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
                1   // higher priority
            );
        }

        /**
         * These items need to be grouped by Associate ID + Locale as API requests cannot be done at once if these are different.
         * The user may be setting different Associate IDs between two units shown in one page. The same applies to the locale.
         */
        if ( ! isset( self::$___aScheduledProductInformation[ $sAssociateIDLocaleCurLang ] ) ) {
            self::$___aScheduledProductInformation[ $sAssociateIDLocaleCurLang ] = array();
        }
        self::$___aScheduledProductInformation[ $sAssociateIDLocaleCurLang ][ $sASIN ] = func_get_args();


    }
        /**
         * Schedules retrievals of product information at once.
         * @since       3.7.7
         * @callback    action      shutdown        With the priority of `1`.
         */
        static public function _replyToScheduleProductsInformation() {

            foreach( self::$___aScheduledProductInformation as $_sAssociateIDLocaleCurLang => $_aFetchingItems ) {
                self::___scheduleProductInformationAPIRequest( $_sAssociateIDLocaleCurLang, $_aFetchingItems );
            }
            AmazonAutoLinks_Shadow::see();  // loads the site in the background

        }
            /**
             * @param string $sAssociateIDLocaleCurLang
             * @param array $aFetchingItems
             * @since   3.7.7
             */
            static private function ___scheduleProductInformationAPIRequest( $sAssociateIDLocaleCurLang, array $aFetchingItems ) {

                // Sort the array in order to prevent unnecessary look-ups due to different orders
                // as `wp_next_scheduled()` stores and identify actions based on the serialized passed arguments.
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
         * @return      boolean
         */
        static private function ___scheduleTask( /* $_sActionName, $aArgument1, $aArgument2, ... */ ) {
             
            $_aParams       = func_get_args() + array( null, array() );
            $_sActionName   = array_shift( $_aParams ); // the first element

            $_aArguments    = $_aParams; // the rest 

            // If already scheduled, skip.
            if ( wp_next_scheduled( $_sActionName, $_aArguments ) ) {
                return false; 
            }

            self::$_aCounters[ $_sActionName ] = isset( self::$_aCounters[ $_sActionName ] )
                ? self::$_aCounters[ $_sActionName ] + 1
                : 1;
            $_bScheduled = wp_schedule_single_event(
                time() + self::$_aCounters[ $_sActionName ] - 1, // now + registering counts, giving one second delay each to avoid timeouts when handling tasks and api rate limit run out.
                $_sActionName,  // the AmazonAutoLinks_Event class will check this action hook and executes it with WP Cron.
                $_aArguments    // must be enclosed in an array.
            );
            return true;
            
        }    
    
}