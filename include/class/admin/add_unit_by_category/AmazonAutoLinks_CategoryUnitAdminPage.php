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
 * @since       3
 */
class AmazonAutoLinks_CategoryUnitAdminPage extends AmazonAutoLinks_SimpleWizardAdminPage {

    /**
     * User constructor.
     */
    public function start() {
        
        parent::start();
        
        if ( ! $this->oProp->bIsAdmin ) {
            return;
        }     
                
        // For the create new unit page. Disable the default one.
        if ( $this->isUserClickedAddNewLink( AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ] ) ) {
            exit(
                wp_safe_redirect(
                    add_query_arg(
                        array( 
                            'post_type' => AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ],
                            'page'      => AmazonAutoLinks_Registry::$aAdminPages[ 'category_select' ],
                        ), 
                        admin_url( 'edit.php' )
                    )
                )
            );
        }

    }

        
    /**
     * Sets the default option values for the setting form.
     * @callback    filter      `options_{class name}`
     * @return      array       The options array.
     */
    public function setOptions( $aOptions ) {

        $_aUnitOptions = array();
        if ( isset( $_GET[ 'post' ] ) ) {
            $_aUnitOptions = AmazonAutoLinks_WPUtility::getPostMeta( $_GET[ 'post' ] );
        }
        
        // Set some items for the edit mode.
        $_iMode    = ! isset( $_GET['post'] ); // 0: edit, 1: new
        $_aOptions = array(
            'mode'       => $_iMode,
        );
        if ( ! $_iMode ) {
            $_aOptions[ 'bounce_url' ] = AmazonAutoLinks_WPUtility::getPostDefinitionEditPageURL(
                $_GET[ 'post' ],  // post id
                AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ]
            );
        }
        
        $_aOptions = $aOptions 
            + $_aOptions
            + $_aUnitOptions
            + $this->_getLastUnitInputs();
        return $_aOptions;
        
    }
        
    /**
     * Sets up admin pages.
     */
    public function setUp() {
        
        $this->setRootMenuPageBySlug( 
            'edit.php?post_type=' . AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ]
        );
                    
        // Add pages
        new AmazonAutoLinks_CategoryUnitAdminPage_CategorySelect( 
            $this,
            array(
                'page_slug'     => AmazonAutoLinks_Registry::$aAdminPages[ 'category_select' ],
                'title'         => __( 'Add Unit by Category', 'amazon-auto-links' ),
                'screen_icon'   => AmazonAutoLinks_Registry::getPluginURL( "asset/image/screen_icon_32x32.png" ),
                // 'show_in_menu'  => false,
            )
        );        
        
    }
    /**
     * Registers custom filed types of Admin Page Framework.
     */
    public function registerFieldTypes() {
        
        // @deprecated
        // new AmazonPAAPIAuthFieldType( 'AmazonAutoLinks_AdminPage' );
        
    }
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
        
        
// @todo examine whether this is necessary or not.            
$this->setDisallowedQueryKeys( array( 'aal-option-upgrade', 'bounce_url' ) );            
return;                          
        // Action Links (plugin.php)
        // $this->addLinkToPluginTitle(
            // $_sLink1,
            // $_sLink2
        // );

        
        $this->setPageHeadingTabsVisibility( false );        // disables the page heading tabs by passing false.
                     
        $this->setInPageTabTag( 'h3', 'aal_add_category_unit' );
        $this->setInPageTabsVisibility( false, 'aal_add_category_unit' );
        $this->setInPageTabsVisibility( false, 'aal_add_search_unit' );
        $this->setInPageTabsVisibility( false, 'aal_define_auto_insert' );
                
        
        $this->enqueueStyle( AmazonAutoLinks_Registry::getPluginURL( 'asset/css/select_categories.css' ), 'aal_add_category_unit', 'select_categories' );
        $this->enqueueStyle( AmazonAutoLinks_Registry::getPluginURL( 'asset/css/aal_add_search_unit.css' ), 'aal_add_search_unit' );
        
        $this->enqueueStyle( AmazonAutoLinks_Registry::getPluginURL( 'asset/css/aal_templates.css' ), 'aal_templates' );
        $this->enqueueStyle( AmazonAutoLinks_Registry::getPluginURL( 'asset/css/readme.css' ), 'aal_about' );
        $this->enqueueStyle( AmazonAutoLinks_Registry::getPluginURL( 'asset/css/readme.css' ), 'aal_help' );
        $this->enqueueStyle( AmazonAutoLinks_Registry::getPluginURL( 'asset/css/get_pro.css' ), 'aal_about', 'get_pro' );
        $this->enqueueStyle( AmazonAutoLinks_Registry::getPluginURL( 'template/preview/style-preview.css' ), 'aal_add_category_unit', 'select_categories' );

        
            
            
    }
        
}