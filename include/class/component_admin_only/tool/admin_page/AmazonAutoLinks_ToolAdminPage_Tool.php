<?php
/**
 * Amazon Auto Links
 * 
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2015 Michael Uno; Licensed GPLv2
 * 
 */

/**
 * Adds the `Tools` page.
 * 
 * @since       3
 */
class AmazonAutoLinks_ToolAdminPage_Tool extends AmazonAutoLinks_AdminPage_Page_Base {


    /**
     * A user constructor.
     * 
     * @since       3
     * @return      void
     */
    public function construct( $oFactory ) {
        
        // Tabs
        new AmazonAutoLinks_ToolAdminPage_Tool_TemplateOptionConverter( 
            $this->oFactory,
            $this->sPageSlug,
            array( 
                'tab_slug'  => 'template_option_converter',
                'title'     => __( 'Template Option Converter', 'amazon-auto-links' ),
            )
        );
     
    }   
    
    /**
     * Prints debug information at the bottom of the page.
     * 
     * @callback        action      do_after_{page slug}
     */
    public function replyToDoAfterPage( $oFactory ) {
            

    }
        
}
