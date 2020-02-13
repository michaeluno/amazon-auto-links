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
 * Deals with the plugin admin pages.
 * 
 * @since       3.5.0
 */
final class AmazonAutoLinks_ContextualUnitAdminPage extends AmazonAutoLinks_SimpleWizardAdminPage {

    /**
     * Sets up admin pages.
     */
    public function setUp() {
        
        // Page group root.
        $this->setRootMenuPageBySlug( 
            'edit.php?post_type=' . AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ]
        );
                    
        // Add pages
        new AmazonAutoLinks_ContextualUnitAdminPage_ContextualUnit( $this );
     
    }
        
    /**
     * Page styling
     * @since       3
     * @return      void
     */
    public function doPageSettings() {                
        $this->setPageTitleVisibility( true ); // disable the page title of a specific page.   
    }
        
}