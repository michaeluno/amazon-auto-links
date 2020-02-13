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
 * @since       3
 */
final class AmazonAutoLinks_CategoryUnitAdminPage extends AmazonAutoLinks_SimpleWizardAdminPage {

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
        $_iMode    = ! isset( $_GET[ 'post' ] ); // 0: edit, 1: new
        $_aOptions = array(
            'mode'       => $_iMode,
        );
        if ( ! $_iMode ) {
            $_aOptions[ 'bounce_url' ] = AmazonAutoLinks_WPUtility::getPostDefinitionEditPageURL(
                $_GET[ 'post' ],  // post id
                AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ]
            );
        }
        
        $_oOption = AmazonAutoLinks_Option::getInstance();
        
        $_aOptions = $aOptions 
            + $_aOptions
            + $_aUnitOptions
            + $this->_getLastUnitInputs()
            + $_oOption->get( 'unit_default' )  // 3.4.0+
            ;
        return $_aOptions;
        
    }
        
    /**
     * Sets up admin pages.
     */
    public function setUp() {
        
        $this->setRootMenuPageBySlug( 'edit.php?post_type=' . AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ] );
                    
        // Add pages
        new AmazonAutoLinks_CategoryUnitAdminPage_CategorySelect( $this );

    }

    /**
     * Page styling
     * @since       3
     * @return      void
     */
    public function doPageSettings() {
                    
        $this->setPageTitleVisibility( false ); // disable the page title of a specific page.
        $this->setInPageTabTag( 'h2' );                
        
        $this->enqueueStyle( AmazonAutoLinks_Registry::getPluginURL( 'asset/css/admin.css' ) );
                
        // @todo examine whether this is necessary or not.            
        $this->setDisallowedQueryKeys( array( 'aal-option-upgrade', 'bounce_url' ) );            

    }

    public function load() {
        // $this->___checkAPIKeys();    // @deprecated  3.9.0
    }
        /**
         * @deprecated  3.9.0
         */
/*        private function ___checkAPIKeys() {
            $_oOption = AmazonAutoLinks_Option::getInstance();
            if ( $_oOption->isAPIConnected() ) {
                return;
            }

            $this->setSettingNotice(
                __( 'You need to set API keys first to create a Search unit.', 'amazon-auto-links' ),
                'updated'
            );

            // Go to the Authentication tab of the Settings page.
            AmazonAutoLinks_PluginUtility::goToAPIAuthenticationPage();
        }*/
        
}