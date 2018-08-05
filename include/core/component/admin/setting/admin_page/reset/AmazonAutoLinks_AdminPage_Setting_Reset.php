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
 * Adds the 'Reset' tab to the 'Settings' page of the loader plugin.
 * 
 * @since       3
 * @extends     AmazonAutoLinks_AdminPage_Tab_Base
 */
class AmazonAutoLinks_AdminPage_Setting_Reset extends AmazonAutoLinks_AdminPage_Tab_Base {
    
    /**
     * Triggered when the tab is loaded.
     */
    public function replyToLoadTab( $oAdminPage ) {
        
        // Form sections
        new AmazonAutoLinks_AdminPage_Setting_Reset_RestSettings( 
            $oAdminPage,
            $this->sPageSlug, 
            array(
                'tab_slug'      => $this->sTabSlug,
                'section_id'    => 'reset_settings',
                'title'         => __( 'Reset Settings', 'amazon-auto-links' ),
                'description'   => array(
                    __( 'If you get broken options, initialize them by performing reset.', 'amazon-auto-links' ),
                ),
            )
        );

        // 3.6.6+
        new AmazonAutoLinks_AdminPage_Setting_Reset_Data(
            $oAdminPage,
            $this->sPageSlug,
            array(
                'tab_slug'      => $this->sTabSlug,
                'section_id'    => 'data',
                'title'         => __( 'Data', 'amazon-auto-links' ),
                'description'   => array(
                    __( 'Handles export/import plugin options.', 'amazon-auto-links' )
                    . ' ' . __( 'Units, auto-insert and buttons are not included.', 'amazon-auto-links' ),
                ),
            )
        );
     
    }
    
    public function replyToDoTab( $oFactory ) {
        echo "<div class='right-submit-button'>"
                . get_submit_button()  
            . "</div>";
    }    
            
}
