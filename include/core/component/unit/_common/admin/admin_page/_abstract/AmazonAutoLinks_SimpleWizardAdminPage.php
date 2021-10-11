<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 */

/**
 * Provides common methods for simple wizard form pages.
 * 
 * @since 3
 */
abstract class AmazonAutoLinks_SimpleWizardAdminPage extends AmazonAutoLinks_AdminPageFramework {

    /**
     * User constructor.
     */
    public function start() {
        
        if ( ! $this->oProp->bIsAdmin ) {
            return;
        }     
        add_filter( 'options_' . $this->oProp->sClassName, array( $this, 'setOptions' ) );
        add_action( 'set_up_' . $this->oProp->sClassName, array( $this, 'registerFieldTypes' ) );
        add_action( 'load_' . $this->oProp->sClassName, array( $this, 'replyToRegisterFieldTypes' ) );
        add_action( 'load_' . $this->oProp->sClassName, array( $this, 'doPageSettings' ) );
                
        $this->setPluginSettingsLinkLabel( '' ); // pass an empty string to disable it.
        $_oOption = AmazonAutoLinks_Option::getInstance();
        $this->setCapability( $_oOption->get( array( 'capabilities', 'setting_page_capability' ), 'manage_options' ) );

    }

    /**
     * Sets up admin pages.
     * @since 5.0.0
     */
    public function setUp() {
        $this->setRootMenuPageBySlug( 'edit.php?post_type=' . AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ] );
        $this->setInPageTabTag( 'h2' );
        $this->setPageTitleVisibility( true ); // disable the page title of a specific page.
        $this->_addPages();
        $this->enqueueStyle( AmazonAutoLinks_Main_Loader::$sDirPath . '/asset/css/admin.css' );
    }

    /**
     * @since  5.0.0
     * @remark Extend this method in an extended class.
     */
    protected function _addPages() {}

    /**
     * Sets the default option values for the setting form.
     * @callback add_filter() options_{class name}
     * @return   array        The options array.
     */
    public function setOptions( $aOptions ) {
        $_oOption = AmazonAutoLinks_Option::getInstance();
        return $aOptions
            + $this->_getLastUnitInputs()
            + $_oOption->get( 'unit_default' );
    }

    /**
     * @return      array
     * @since       3.2.0
     */
    protected function _getLastUnitInputs() {
        $_aLastInputs = $this->oUtil->getAsArray( get_user_meta( get_current_user_id(), AmazonAutoLinks_Registry::$aUserMeta[ 'last_inputs' ], true ) );
        unset( 
            $_aLastInputs[ 'unit_title' ],
            $_aLastInputs[ 'Keywords' ],
            $_aLastInputs[ 'ItemId' ],
            $_aLastInputs[ 'tags' ],
            $_aLastInputs[ 'customer_id' ],
            $_aLastInputs[ 'urls' ]
        );
        return $_aLastInputs;
    }    
    
    /**
     * @return      boolean
     */
    protected function isUserClickedAddNewLink( $sPostTypeSlug ) {
        if ( 'post-new.php' !== $GLOBALS[ 'pagenow' ] ) {
            return false;
        }
        if ( 1 !== count( $_GET ) ) {                       // sanitization unnecessary as just checking
            return false;
        }
        if ( ! isset( $_GET[ 'post_type' ] ) ) {            // sanitization unnecessary as just checking
            return false;
        }
        return $sPostTypeSlug === $_GET[ 'post_type' ];     // sanitization unnecessary as just checking
    }

    /**
     * Registers custom filed types.
     * @callback add_action() load_{instantiated class name}
     */
    public function replyToRegisterFieldTypes() {
        new AmazonAutoLinks_RevealerCustomFieldType( $this->oProp->sClassName );
    }
        
    /**
     * Registers custom filed types of Admin Page Framework.
     */
    public function registerFieldTypes() {}
    
    /**
     * Page styling
     * @since 3
     */
    public function doPageSettings() {}
        
}