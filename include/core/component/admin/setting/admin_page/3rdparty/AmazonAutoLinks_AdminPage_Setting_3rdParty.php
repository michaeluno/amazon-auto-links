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
 * Adds the '3rd Party' tab to the 'Settings' page of the loader plugin.
 * 
 * @since       3.11.0
 * @extends     AmazonAutoLinks_AdminPage_Tab_Base
 */
class AmazonAutoLinks_AdminPage_Setting_3rdParty extends AmazonAutoLinks_AdminPage_Tab_Base {

    /**
     * @return array
     * @since   3.11.1
     */
    protected function _getArguments() {
        return array(
            'tab_slug'  => '3rd_party',
            'title'     => __( '3rd Party', 'amazon-auto-links' ),
            'order'     => 60,
        );
    }

    /**
     * Triggered when the tab is loaded.
     */
    protected function _loadTab( $oAdminPage ) {}
            
    protected function _doTab( $oAdminPage ) {
        echo "<div class='right-submit-button'>"
                . get_submit_button()  
            . "</div>";
    }
    
}
