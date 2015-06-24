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
            $this->enqueueStyle( AmazonAutoLinks_Registry::getPluginURL( 'asset/css/admin.css' ) );
  
        }

}