<?php
/**
 * Amazon Auto Links
 * 
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2016 Michael Uno
 * 
 */

/**
 * Deletes expired caches stored in the plugin custom database tables.
 * @package      Amazon Auto Links
 * @since        3.4.0
 * @action       aal_action_delete_expired_caches
 */
class AmazonAutoLinks_Event_Action_DeleteExpiredCaches extends AmazonAutoLinks_Event_Action_Base {
        
    /**
     * 
     * @callback        action        aal_action_delete_expired_caches
     */
    public function doAction() {
        AmazonAutoLinks_PluginUtility::deleteExpiredTableItems();
    }   
    
}