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