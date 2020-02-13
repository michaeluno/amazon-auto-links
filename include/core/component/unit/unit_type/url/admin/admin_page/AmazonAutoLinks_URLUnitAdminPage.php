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
 * @since       3.2.0
 * @remark      This class is not final as being extended by the feed unit type admin page class.
 * @see         AmazonAutoLinks_FeedUnitAdminPage
 */
class AmazonAutoLinks_URLUnitAdminPage extends AmazonAutoLinks_SimpleWizardAdminPage {

    /**
     * Sets up admin pages.
     */
    public function setUp() {
        
        // Page group root.
        $this->setRootMenuPageBySlug( 
            'edit.php?post_type=' . AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ]
        );
                    
        // Add pages
        $this->_addPages();
     
    }

        /**
         * @remark      Added for extended classes.
         * @since       4.0.0
         */
        protected function _addPages() {
            new AmazonAutoLinks_URLUnitAdminPage_URLUnit( $this );
        }
        
    /**
     * Page styling
     * @since       3
     * @return      void
     */
    public function doPageSettings() {                
        $this->setPageTitleVisibility( true ); // disable the page title of a specific page.   
    }

    public function load() {
        AmazonAutoLinks_Unit_Admin_Utility::checkAPIKeys( $this );
    }

}