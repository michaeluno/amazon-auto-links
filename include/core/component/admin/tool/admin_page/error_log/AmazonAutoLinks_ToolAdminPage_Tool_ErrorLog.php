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
 * @since       3.9.0
 * @extends     AmazonAutoLinks_AdminPage_Tab_Base
 */
class AmazonAutoLinks_ToolAdminPage_Tool_ErrorLog extends AmazonAutoLinks_AdminPage_Tab_Base {

    /**
     * @return  array
     * @since   3.9.0
     */
    protected function _getArguments() {
        return array(
            'tab_slug'  => 'error_log',
            'title'     => __( 'Error Log', 'amazon-auto-links' ),
        );
    }

    /**
     * Triggered when the tab is loaded.
     */
    public function replyToLoadTab( $oAdminPage ) {
        
        // Form sections
        new AmazonAutoLinks_ToolAdminPage_Tool_ErrorLog_Log(
            $oAdminPage,
            $this->sPageSlug,
            array(
                'section_id'    => 'Log',
                'tab_slug'      => $this->sTabSlug,
                'title'         => __( 'Errors', 'amazon-auto-links' ),
            )
        );

    }
        
}
