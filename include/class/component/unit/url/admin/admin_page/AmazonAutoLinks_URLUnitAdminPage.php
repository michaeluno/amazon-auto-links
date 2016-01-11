<?php
/**
 * Amazon Auto Links
 * 
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2015 Michael Uno
 * 
 */


/**
 * Deals with the plugin admin pages.
 * 
 * @since       3.2.0
 */
class AmazonAutoLinks_URLUnitAdminPage extends AmazonAutoLinks_SimpleWizardAdminPage {
          
    /**
     * Sets the default option values for the setting form.
     * @callback    filter      options_{class name}
     * @return      array       The options array.
     */
    public function setOptions( $aOptions ) {
        return $aOptions 
            + $this->_getLastUnitInputs()
            + AmazonAutoLinks_UnitOption_tag::$aStructure_Default;
    }

    /**
     * Sets up admin pages.
     */
    public function setUp() {
        
        // Page group root.
        $this->setRootMenuPageBySlug( 
            'edit.php?post_type=' . AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ]
        );
                    
        // Add pages
        new AmazonAutoLinks_URLUnitAdminPage_URLUnit( 
            $this,
            array(
                'page_slug'     => AmazonAutoLinks_Registry::$aAdminPages[ 'url_unit' ],
                'title'         => __( 'Add Unit by URL', 'amazon-auto-links' ),
                'screen_icon'   => AmazonAutoLinks_Registry::getPluginURL( "asset/image/screen_icon_32x32.png" ),
                'style'         => AmazonAutoLinks_Registry::getPluginURL( 'asset/css/admin.css' ),
            )
        );        
     
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