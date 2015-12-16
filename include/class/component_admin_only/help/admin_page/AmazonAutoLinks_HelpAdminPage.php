<?php
/**
 * Amazon Auto Links
 * 
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2015 Michael Uno
 * 
 */

/**
 * Adds the `Tools` page.
 * 
 * @since       3
 */
class AmazonAutoLinks_HelpAdminPage extends AmazonAutoLinks_AdminPageFramework {

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
    
        new AmazonAutoLinks_HelpAdminPage_Help( 
            $this,
            array(
                'page_slug' => AmazonAutoLinks_Registry::$aAdminPages[ 'help' ],
                'title'     => __( 'Help', 'amazon-auto-links' ),
                'order'     => 1000, // to be the last menu item
                'style'     => array(
                    AmazonAutoLinks_Registry::getPluginURL( 'asset/css/admin.css' ),
                    AmazonAutoLinks_Registry::getPluginURL( '/asset/css/code.css' ),
                ),
            )                
        );          
        
        $this->_doPageSettings();
    }
        
        /**
         * Page styling
         * @since       3
         * @return      void
         */
        private function _doPageSettings() {
                        
            $this->setPageTitleVisibility( false ); // disable the page title of a specific page.
            $this->setInPageTabTag( 'h2' );                
            $this->setPluginSettingsLinkLabel( '' ); // pass an empty string to disable it.
            // $this->enqueueStyle( AmazonAutoLinks_Registry::getPluginURL( 'asset/css/admin.css' ) );
            // $this->enqueueStyle( AmazonAutoLinks_Registry::getPluginURL( '/asset/css/code.css' ) );

        }
    
   
}