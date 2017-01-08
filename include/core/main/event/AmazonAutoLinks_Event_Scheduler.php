<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2017 Michael Uno
 */

/**
 * Handles plugin event scheduling.
 * 
 * @since       3
 * @action      schedule        aal_action_api_get_product_info
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
        self::_scheduleTask(
            'aal_action_unit_prefetch',  // action name
            $_aArguments // arguments
        );

    }    
    
    /**
     * @since       3.3.0
     * @deprecated  Not used at the moment
     */
    static public function prefetchByArguments( $aArguments ) {

        self::_scheduleTask( 
            'aal_action_unit_prefetch_by_arguments',  // action name
            $aArguments // arguments
        );            
        
    }
    
    /**
     * @since       3.3.0
     * @action      schedule        aal_action_api_get_similar_products
     */
    static public function getSimilarProducts( $aASINs, $sASIN, $sLocale, $sAssociateID, $iCacheDuration ) {

        $_bScheduled = self::_scheduleTask( 
            'aal_action_api_get_similar_products',  // action name
            array( $aASINs, $sASIN, $sLocale, $sAssociateID, $iCacheDuration )
        );
        if ( ! $_bScheduled ) {
            return $_bScheduled;
        }
        
        // Loads the site in the background. The method takes care of doing it only once in the entire page load.
        AmazonAutoLinks_Shadow::see();
        return true;
    }
    
    /**
     * 
     * @action      schedule        aal_action_api_get_customer_review
     */
    static public function getCustomerReviews( $sURL, $sASIN, $sLocale, $iCacheDuration ) {

        $_bScheduled = self::_scheduleTask( 
            'aal_action_api_get_customer_review',  // action name
            array( $sURL, $sASIN, $sLocale, $iCacheDuration )
        );
        if ( ! $_bScheduled ) {
            return $_bScheduled;
        }
        
        // Loads the site in the background. The method takes care of doing it only once in the entire page load.
        AmazonAutoLinks_Shadow::see();
        return true;
    
    }

    /**
     * Schedules an action of getting product information in the background.
     * 
     * @since       3
     * @action      schedule        aal_action_api_get_product_info
     */
    static public function getProductInfo( $sAssociateID, $sASIN, $sLocale, $iCacheDuration ) {
    
        $_oOption   = AmazonAutoLinks_Option::getInstance();
        if ( ! $_oOption->isAPIConnected() ) {
            return false;
        }
        
        $_bScheduled = self::_scheduleTask( 
            'aal_action_api_get_product_info',  // action name
            array( $sAssociateID, $sASIN, $sLocale, $iCacheDuration )
        );
        if ( ! $_bScheduled ) {
            return $_bScheduled;
        }
        
        // Loads the site in the background. The method takes care of doing it only once in the entire page load.
        AmazonAutoLinks_Shadow::see();
        return true;
        
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
            
            // $_aArguments    = array( $_aParams[ 1 ] );
            $_aArguments    = $_aParams; // the rest 

            // If already scheduled, skip.
            if ( wp_next_scheduled( $_sActionName, $_aArguments ) ) {
                return false; 
            }
            
            self::$_aCounters[ $_sActionName ] = isset( self::$_aCounters[ $_sActionName ] )
                ? self::$_aCounters[ $_sActionName ] + 1
                : 1;
            
            wp_schedule_single_event( 
                time() + self::$_aCounters[ $_sActionName ] - 1, // now + registering counts, giving one second delay each to avoid timeouts when handling tasks and api rate limit runout.
                $_sActionName, // the AmazonAutoLinks_Event class will check this action hook and executes it with WP Cron.
                $_aArguments // must be enclosed in an array.
            );            

            return true;
            
        }    
    
}