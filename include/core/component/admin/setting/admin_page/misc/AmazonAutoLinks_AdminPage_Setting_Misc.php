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
 * Adds the 'Misc' tab to the 'Settings' page of the loader plugin.
 * 
 * @since       3
 * @extends     AmazonAutoLinks_AdminPage_Tab_Base
 */
class AmazonAutoLinks_AdminPage_Setting_Misc extends AmazonAutoLinks_AdminPage_Tab_Base {
    
    /**
     * Triggered when the tab is loaded.
     */
    public function replyToLoadTab( $oAdminPage ) {
        
        // Form sections
        new AmazonAutoLinks_AdminPage_Setting_Misc_Capability( 
            $oAdminPage,
            $this->sPageSlug, 
            array(
                'tab_slug'      => $this->sTabSlug,
                'section_id'    => 'capabilities',       // avoid hyphen(dash), dots, and white spaces
                'capability'    => 'manage_options',
                'title'         => __( 'Access Rights', 'amazon-auto-links' ),
                'description'   => array(
                    __( 'Set the access levels to the plugin setting pages.', 'amazon-auto-links' ),
                ),
            )
        );           
        new AmazonAutoLinks_AdminPage_Setting_Misc_FormOption( 
            $oAdminPage,
            $this->sPageSlug, 
            array(
                'tab_slug'      => $this->sTabSlug,
                'section_id'    => 'form_options',       // avoid hyphen(dash), dots, and white spaces
                'capability'    => 'manage_options',
                'title'         => __( 'Form', 'amazon-auto-links' ),
                'description'   => __( 'Set allowed HTML tags etc.', 'amazon-auto-links' ),
            )
        );        
        new AmazonAutoLinks_AdminPage_Setting_Misc_Debug( 
            $oAdminPage,
            $this->sPageSlug, 
            array(
                'tab_slug'      => $this->sTabSlug,
                'section_id'    => 'debug', 
                'capability'    => 'manage_options',
                'title'         => __( 'Debug', 'amazon-auto-links' ),
                'description'   => __( 'For developers who need to see the internal workings of the plugin.', 'amazon-auto-links' ),
            )
        );
        
    }
            
    public function replyToDoTab( $oFactory ) {
        echo "<div class='right-submit-button'>"
                . get_submit_button()  
            . "</div>";
    }
    
}
