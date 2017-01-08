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
 * Deals with the plugin admin pages.
 * 
 * @since       3
 */
class AmazonAutoLinks_SearchUnitAdminPage extends AmazonAutoLinks_SimpleWizardAdminPage {
          
    /**
     * Sets the default option values for the setting form.
     * @callback    filter      options_{class name}
     * @return      array       The options array.
     */
    public function setOptions( $aOptions ) {

        $_oOption = AmazonAutoLinks_Option::getInstance();
        return $aOptions 
            + $this->_getLastUnitInputs()
            + $_oOption->get( 'unit_default' )  // 3.4.0+
            ;
        
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
        new AmazonAutoLinks_SearchUnitAdminPage_SearchUnit( 
            $this,
            array(
                'page_slug'     => AmazonAutoLinks_Registry::$aAdminPages[ 'search_unit' ],
                'title'         => __( 'Add Unit by Search', 'amazon-auto-links' ),
                'screen_icon'   => AmazonAutoLinks_Registry::getPluginURL( "asset/image/screen_icon_32x32.png" ),
            )
        );        
        
    }
    /**
     * Registers custom filed types of Admin Page Framework.
     */
    public function registerFieldTypes() {}
    
    /**
     * Page styling
     * @since       3
     * @return      void
     */
    public function doPageSettings() {

        $this->setPageTitleVisibility( false ); // disable the page title of a specific page.
        $this->setInPageTabTag( 'h2' );                
        $this->setPluginSettingsLinkLabel( '' ); // pass an empty string to disable it.
        $this->enqueueStyle( AmazonAutoLinks_Registry::getPluginURL( 'asset/css/admin.css' ) );
        
                    
    }
        
}