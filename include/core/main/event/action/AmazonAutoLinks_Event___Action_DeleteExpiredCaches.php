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
 * Deletes expired caches stored in the plugin custom database tables.
 *
 * @package      Amazon Auto Links
 * @since        3.4.0
 */
class AmazonAutoLinks_Event___Action_DeleteExpiredCaches extends AmazonAutoLinks_Event___Action_Base {

    protected $_sActionHookName     = 'aal_action_delete_expired_caches';

    /**
     *
     */
    protected function _construct() {

        $this->___scheduleNext();
    }

        /**
         * @since   3.8.12
         */
        private function ___scheduleNext() {
            // Get the set interval in seconds.
            $_oOption   = AmazonAutoLinks_Option::getInstance();
            $_iSize     = ( integer ) $_oOption->get( array( 'cache', 'expired_cache_removal_interval', 'size' ), 7 );
            $_iUnit     = ( integer ) $_oOption->get( array( 'cache', 'expired_cache_removal_interval', 'unit' ), 86400 );
            $_iInterval = $_iSize * $_iUnit;
            $_iTime     = time() + $_iInterval;

            $this->scheduleSingleWPCronTask( $this->_sActionHookName, array(), $_iTime );
        }
    /**
     *
     */
    protected function _doAction() {

        // Expiry deletion
        $this->deleteExpiredTableItems();

        // Table truncation
        $_oOption = AmazonAutoLinks_Option::getInstance();

        // Keep the cache data base table size
        $this->truncateCacheTablesBySize(
            $_oOption->get( array( 'cache', 'table_size', 'products' ), '' ),
            $_oOption->get( array( 'cache', 'table_size', 'requests' ), '' )
        );

        $_oOption->update( array( 'cache', 'cache_removal_event_last_run_time' ), time() );
        $this->___scheduleNext();

    }

}