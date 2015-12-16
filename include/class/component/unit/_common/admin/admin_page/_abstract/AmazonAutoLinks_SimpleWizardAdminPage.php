<?php
/**
 * Amazon Auto Links
 * 
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2015 Michael Uno
 * 
 */

/**
 * Provides common methods for simple wizard form pages.
 * 
 * @since       3
 */
abstract class AmazonAutoLinks_SimpleWizardAdminPage extends AmazonAutoLinks_AdminPageFramework {

    /**
     * User constructor.
     */
    public function start() {
        
        if ( ! $this->oProp->bIsAdmin ) {
            return;
        }     
        add_filter( 
            "options_" . $this->oProp->sClassName,
            array( $this, 'setOptions' )
        );
        add_action( 
            "set_up_" . $this->oProp->sClassName,
            array( $this, 'registerFieldTypes' )
        );
        add_action( 
            "set_up_" . $this->oProp->sClassName,
            array( $this, 'doPageSettings' )
        );        
        
        $this->setPluginSettingsLinkLabel( '' ); // pass an empty string to disable it.           
        
        // Register custom filed type.
        new AmazonAutoLinks_RevealerCustomFieldType( $this->oProp->sClassName );        
        
    }
    
    /**
     * Sets the default option values for the setting form.
     * @callback    filter      `options_{class name}`
     * @return      array       The options array.
     */
    public function setOptions( $aOptions ) {
        return $aOptions;
    }
    /**
     * @return      array
     * @since       3.2.0
     */
    protected function _getLastUnitInputs() {
        $_aLastInputs = get_option( 
            AmazonAutoLinks_Registry::$aOptionKeys[ 'last_input' ],
            array()
        );           
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
        if ( 'post-new.php' !== $GLOBALS['pagenow'] ) {
            return false;
        }
        if ( 1 !== count( $_GET ) ) {
            return false;
        }
        if ( ! isset( $_GET[ 'post_type' ] ) ) {
            return false;
        }
        return $sPostTypeSlug === $_GET[ 'post_type' ];            
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
    public function doPageSettings() {}
        
}