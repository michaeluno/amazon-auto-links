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
 * Schedules the task of deleting expired caches.
 */
class AmazonAutoLinks_Event_Schedule_DeleteExpiredCaches {
    
    private $_sActionName = 'aal_action_delete_expired_caches';
    
    /**
     * Schedules the task that deletes expired caches.
     * @since       3.4.0
     */
    public function __construct() {
        
        // Do the action if it is scheduled.
        new AmazonAutoLinks_Event_Action_DeleteExpiredCaches(
            $this->_sActionName
        );    
        
        // At this point, we need to schedule the next task.
        $this->_schedule();        
        
    }
        /**
         * @since       3.4.0
         * @return      boolean
         */
        private function _schedule() {

            // If already scheduled, skip.
            $_aArguments = array();
            if ( wp_next_scheduled( $this->_sActionName, $_aArguments ) ) {
                return false; 
            }    
        
            // Get the set interval in seconds.
            $_oOption   = AmazonAutoLinks_Option::getInstance();
            $_iSize     = ( integer ) $_oOption->get( array( 'cache', 'expired_cache_removal_interval', 'size' ), 7 );
            $_iUnit     = ( integer ) $_oOption->get( array( 'cache', 'expired_cache_removal_interval', 'unit' ), 86400 );
            $_iInterval = $_iSize * $_iUnit;
            
            // Schedule
            wp_schedule_single_event( 
                time() + $_iInterval, // now + interval
                $this->_sActionName, // the AmazonAutoLinks_Event class will check this action hook and executes it with WP Cron.
                $_aArguments // must be enclosed in an array.
            );
            
            return true;
            
        }
    
}