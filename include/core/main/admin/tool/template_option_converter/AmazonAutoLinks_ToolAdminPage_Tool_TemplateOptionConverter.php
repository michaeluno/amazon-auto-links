<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 */

/**
 * Adds an in-page tab to a setting page.
 * 
 * @since       3
 * @derprecated 4.4.0 Unused.
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

}
