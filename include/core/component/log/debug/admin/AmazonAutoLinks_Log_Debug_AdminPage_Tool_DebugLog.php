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
 * Adds an in-page tab to a setting page.
 * 
 * @since       4.3.0
 * @extends     AmazonAutoLinks_AdminPage_Tab_Base
 */
class AmazonAutoLinks_Log_Debug_AdminPage_Tool_DebugLog extends AmazonAutoLinks_Log_Error_AdminPage_Tool_ErrorLog {

    /**
     * @return  array
     * @since   4.3.0
     */
    protected function _getArguments() {
        return array(
            'tab_slug'  => 'debug_log',
            'title'     => 'Debug Log',
            'order'     => 10,
        );
    }

    /**
     * Triggered when the tab is loaded.
     * @param AmazonAutoLinks_AdminPageFramework $oAdminPage
     * @since   4.3.0
     */
    protected function _loadTab( $oAdminPage ) {

        // Form sections
        new AmazonAutoLinks_Log_Debug_AdminPage_Tool_DebugLog_Log( $oAdminPage, $this->sPageSlug, array( 'tab_slug' => $this->sTabSlug, ) );

        $this->_enqueueResources( $oAdminPage );

    }
        
}
