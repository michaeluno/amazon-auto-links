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
        $this->deleteExpiredTableItems();
    }   
    
}