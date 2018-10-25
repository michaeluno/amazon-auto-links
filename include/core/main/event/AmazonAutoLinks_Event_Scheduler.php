<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2018 Michael Uno
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
        $_bScheduled = self::_scheduleTask(
            'aal_action_unit_prefetch',  // action name
            $_aArguments // arguments
        );

    }

    /**
     * @since       3.3.0
     * @sicne       3.5.0           Renamed from `getSimliarProducts()`.
     * @action      schedule        aal_action_api_get_similar_products
     */
    static public function scheduleSimilarProducts( $aSimilarProductASINs, $sASIN, $sLocale, $sAssociateID, $iCacheDuration, $bForceRenew ) {

        if ( empty( $aSimilarProductASINs ) ) {
            return false;
        }

        $_bScheduled = self::_scheduleTask( 
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
     */
    static public function scheduleCustomerReviews( $sURL, $sASIN, $sLocale, $iCacheDuration, $bForceRenew ) {

        $_bScheduled = self::_scheduleTask( 
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
     * @var array
     * @since   3.7.7
     */
    static private $___aScheduledProductInformation = array();
    static private $___bCalledScheduleProductInformation = false;
    /**
     * Schedules an action of getting product information in the background.
     * 
     * @since       3
     * @since       3.5.0       Renamed from `getProductInfo()`.
     * @return      void
     * @todo        Do this at once at the shutdown action and pass multiple ASINs to query at once to save a number of API requests.
     * @since       3.7.0       Added the `$sItemFormat` parameter so that the background routine can check whether to perform optional HTTP API requests.
     * @since       3.7.7       Changed the return value to `void` as no calls use it and this method is going to schedule multiple items at once.
     */
    static public function scheduleProductInformation( $sAssociateID, $sASIN, $sLocale, $iCacheDuration, $bForceRenew=false, $sItemFormat='' ) {
    
        $_oOption   = AmazonAutoLinks_Option::getInstance();
        if ( ! $_oOption->isAPIConnected() ) {
            return;
        }

        /**
         * These items need to be grouped by Associate ID + Locale as API requests cannot be done at once if these are different.
         * The user may be setting different Associate IDs between two units shown in one page. The same applies to the locale.
         */
        if ( ! isset( self::$___aScheduledProductInformation[ $sAssociateID . $sLocale ] ) ) {
            self::$___aScheduledProductInformation[ $sAssociateID . $sLocale ] = array();
        }
        self::$___aScheduledProductInformation[ $sAssociateID . '|' . $sLocale ][ $sASIN ] = func_get_args();

        if ( ! self::$___bCalledScheduleProductInformation ) {
            add_action(
                'shutdown',
                array( __CLASS__, '_replyToScheduleProductsInformation' ),
                1   // higher priority
            );
        }
        self::$___bCalledScheduleProductInformation = true;

        // @deprecated  3.7.7 Now they are queried all at once.
        $_bScheduled = self::_scheduleTask(
            'aal_action_api_get_product_info',  // action name
            array( $sAssociateID, $sASIN, $sLocale, $iCacheDuration, $bForceRenew, $sItemFormat )
        );
        if ( ! $_bScheduled ) {
            return;
        }
        
        // Loads the site in the background. The method takes care of doing it only once in the entire page load.
        AmazonAutoLinks_Shadow::see();
        return;
        
    }
        /**
         * Schedules retrievals of product information at once.
         * @since       3.7.7
         * @callback    shutdown        With the priority of `1`.
         */
        public function _replyToScheduleProductsInformation() {
return;
            foreach( self::$___aScheduledProductInformation as $_sAssociateIDLocale => $_aFetchingItems ) {
                $this->___scheduleProductInformationAPIRequest( $_sAssociateIDLocale, $_aFetchingItems );
            }
            AmazonAutoLinks_Shadow::see();  // loads the site in the background

        }
            /**
             * @param $sAssociateIDLocale
             * @param array $aParameters
             * @since   3.7.7
             */
            private function ___scheduleProductInformationAPIRequest( $sAssociateIDLocale, array $_aFetchingItems ) {

                // Sort the array in order to prevent unnecessary look-ups due to different orders
                // as `wp_next_scheduled()` stores and identify actions based on the serialized passed arguments.
                ksort( $_aFetchingItems );

                // Divide items by 10 as `ItemLookup` operation accepts up to 10 items at a time.
                $_aChunks = array_chunk( $_aFetchingItems, 10 );

                $_aParts = explode( '|', $sAssociateIDLocale );
                $_sAssociateID = $_aParts[ 0 ];
                $_sLocale      = $_aParts[ 1 ];
                /**
                 * At this point the chunk array $_aChunks looks like this.
                 * The keys will be converted to numeric index.
                 *
                 * ```
                 * array(
                 *      0 => array(
                 *          0 => array( 0 => $sAssociateID, 1 => $sASIN,  2 => $sLocale, 3 => $iCacheDuration, 4 => $bForceRenew, 5 => $sItemFormat ),
                 *          1 ...
                 *          2 ...
                 *          10 => array( 0 => $sAssociateID, 1 => $sASIN,  2 => $sLocale, 3 => $iCacheDuration, 4 => $bForceRenew, 5 => $sItemFormat ),
                 *      ),
                 *      1 => array(
                 *          0 => array( 0 => $sAssociateID, 1 => $sASIN,  2 => $sLocale, 3 => $iCacheDuration, 4 => $bForceRenew, 5 => $sItemFormat ),
                 *          1 ...
                 *          2 ...
                 *      ),
                 * )
                 * ```
                 */
                foreach ( $_aChunks as $_iIndex => $_aItems ) {
                    self::_scheduleTask(
                        'aal_action_api_get_products_info',  // action name
                        $_aItems,
                        $_sAssociateID,
                        $_sLocale
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
        static private function _scheduleTask( /* $_sActionName, $aArgument1, $aArgument2, ... */ ) {
             
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
            
            wp_schedule_single_event( 
                time() + self::$_aCounters[ $_sActionName ] - 1, // now + registering counts, giving one second delay each to avoid timeouts when handling tasks and api rate limit run out.
                $_sActionName, // the AmazonAutoLinks_Event class will check this action hook and executes it with WP Cron.
                $_aArguments // must be enclosed in an array.
            );            

            return true;
            
        }    
    
}