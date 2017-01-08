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
 * Adds the `Tools` page.
 * 
 * @since       3
 */
class AmazonAutoLinks_ToolAdminPage extends AmazonAutoLinks_AdminPageFramework {

    /**
     * User constructor.
     */
    public function start() {  
    }

    /**
     * Sets up admin pages.
     */
    public function setUp() {
        
        $this->setRootMenuPageBySlug( 
            'edit.php?post_type=' . AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ]
        );
    
        new AmazonAutoLinks_ToolAdminPage_Tool( 
            $this,
            array(
                'page_slug' => AmazonAutoLinks_Registry::$aAdminPages[ 'tool' ],
                'title'     => __( 'Tools', 'amazon-auto-links' ),
                'order'     => 900,
            )                
        );          
        
        $this->setPluginSettingsLinkLabel( '' ); // pass an empty string to disable it.      
        
    }

}