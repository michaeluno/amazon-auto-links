<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2018 Michael Uno
 */


/**
 * Deals with the plugin admin pages.
 * 
 * @since       3.5.0
 */
class AmazonAutoLinks_ContextualUnitAdminPage extends AmazonAutoLinks_SimpleWizardAdminPage {
          
    /**
     * Sets the default option values for the setting form.
     * @callback    filter      options_{class name}
     * @return      array       The options array.
     */
    public function setOptions( $aOptions ) {
        
        $_oOption = AmazonAutoLinks_Option::getInstance();
        return $aOptions 
            + $this->_getLastUnitInputs()
            + $_oOption->get( 'unit_default' )
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
        new AmazonAutoLinks_ContextualUnitAdminPage_ContextualUnit(
            $this,
            array(
                'page_slug'     => AmazonAutoLinks_Registry::$aAdminPages[ 'contextual_unit' ],
                'title'         => __( 'Add Contextual Unit', 'amazon-auto-links' ),
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