<?php
/**
 * Amazon Auto Links
 * 
 * 
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2016 Michael Uno; Licensed GPLv2
 * 
 */

/**
 * Adds an in-page tab to a setting page.
 * 
 * @since       3
 * @extends     AmazonAutoLinks_AdminPage_Tab_Base
 */
class AmazonAutoLinks_ToolAdminPage_Tool_TemplateOptionConverter extends AmazonAutoLinks_AdminPage_Tab_Base {
    
    /**
     * Triggered when the tab is loaded.
     */
    public function replyToLoadTab( $oAdminPage ) {
        
        // Form sections
        new AmazonAutoLinks_ToolAdminPage_Tool_TemplateOptionConverter_Format( 
            $oAdminPage,
            $this->sPageSlug, 
            array(
                'section_id'    => 'formats',
                'tab_slug'      => $this->sTabSlug,
                'title'         => __( 'Template Options', 'amazon-auto-links' ),
                'description'   => array(
                    __( 'Convert template options in units with batch processing.', 'amazon-auto-links' ),
                ),
            )
        );
        
    }

    public function replyToDoTab( $oFactory ) {
        // echo "<div class='right-submit-button'>"
                // . get_submit_button( __( 'Convert', 'amazon-auto-links' ) )  
            // . "</div>";
    }    
        
}
