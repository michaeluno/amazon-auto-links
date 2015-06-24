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
 * @since       2.0.5
 */
class AmazonAutoLinks_AdminPage extends AmazonAutoLinks_AdminPageFramework {

    /**
     * User constructor.
     */
    public function start() {
        
        if ( ! $this->oProp->bIsAdmin ) {
            return;
        }     
        add_filter( 
            "options_" . $this->oProp->sClassName,
            array( $this, 'replyToSetOptions' )
        );
        
           
    }
        /**
         * Sets the default option values for the setting form.
         * @return      array       The options array.
         */
        public function replyToSetOptions( $aOptions ) {
            
            $_oOption    = AmazonAutoLinks_Option::getInstance();
            return $aOptions + $_oOption->aDefault;
            
        }
    /**
     * Sets up admin pages.
     */
    public function setUp() {
        
        $this->setRootMenuPageBySlug( 
            'edit.php?post_type=' . AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ]
        );
                    
        // Add pages      
        new AmazonAutoLinks_AdminPage_Setting( 
            $this,
            array(
                'page_slug'     => AmazonAutoLinks_Registry::$aAdminPages[ 'main' ],
                'title'         => __( 'Settings', 'amazon-auto-links' ),
                'screen_icon'   => AmazonAutoLinks_Registry::getPluginURL( "asset/image/screen_icon_32x32.png" ),
            )
        );
        new AmazonAutoLinks_AdminPage_Template(
            $this,
            array(
                'page_slug' => AmazonAutoLinks_Registry::$aAdminPages[ 'template' ],
                'title'     => __( 'Templates', 'amazon-auto-links' ),
            )                
        );
        
        // $this->_registerFieldTypes();
        $this->_doPageSettings();
        
    }
        /**
         * Registers custom filed types of Admin Page Framework.
         * @deprecated
         */
        private function _registerFieldTypes() {}
        
        /**
         * Page styling
         * @since       3
         * @return      void
         */
        private function _doPageSettings() {
                        
            $this->setPageTitleVisibility( false ); // disable the page title of a specific page.
            $this->setInPageTabTag( 'h2' );                
            // $this->setPluginSettingsLinkLabel( '' ); // pass an empty string to disable it.
            $this->addLinkToPluginDescription(
                "<a href='https://wordpress.org/support/plugin/amazon-auto-links' target='_blank'>" . __( 'Support', 'amazon-auto-links' ) . "</a>"
            );         

            $this->enqueueStyle( AmazonAutoLinks_Registry::getPluginURL( 'asset/css/admin.css' ) );

            $this->setDisallowedQueryKeys( array( 'aal-option-upgrade', 'bounce_url' ) );            
        
        }
    
    /**
     * Modifies the menu items.
     */
    public function _replyToBuildMenu() {

        parent::_replyToBuildMenu();

        // Somehow the settings link in the plugin listing page points to the Create Rule by List page. So fix it to the Settings page.
        // Not sure if this is necessary.
        // $this->oProp->sDefaultPageSlug = AmazonAutoLinks_Registry::$aAdminPages[ 'main' ];

        // Remove the default post type menu item.
        $_sPageSlug = $this->oProp->aRootMenu[ 'sPageSlug' ];
        if ( ! isset( $GLOBALS['submenu'][ $_sPageSlug ] ) ) { 
            // logged-in users of an insufficient access level don't have the menu to be registered.
            return; 
        } 
        foreach ( $GLOBALS['submenu'][ $_sPageSlug ] as $_iIndex => $_aSubMenu ) {
                        
            if ( ! isset( $_aSubMenu[ 2 ] ) ) { 
                continue; 
            }
            
            // Remove the default Add New entry.
            if ( 'post-new.php?post_type=' . AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ] === $_aSubMenu[ 2 ] ) {
                unset( $GLOBALS['submenu'][ $_sPageSlug ][ $_iIndex ] );
                continue;
            }
            
            // Copy and remove the Tag menu element to change the position. 
            if ( 'edit-tags.php?taxonomy=' . AmazonAutoLinks_Registry::$aTaxonomies[ 'tag' ] . '&amp;post_type=' . AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ] === $_aSubMenu[ 2 ] ) {
                $_aMenuEntry_Tag = array( $GLOBALS['submenu'][ $_sPageSlug ][ $_iIndex ] );
                unset( $GLOBALS['submenu'][ $_sPageSlug ][ $_iIndex ] );
                continue;                
            }

        }
        
        // Second iteration.
        $_iMenuPos_Setting = -1;
        foreach ( $GLOBALS['submenu'][ $_sPageSlug ] as $_iIndex => $_aSubMenu ) {
            
            $_iMenuPos_Setting++;    
            if (  isset( $_aSubMenu[ 2 ] ) && AmazonAutoLinks_Registry::$aAdminPages[ 'main' ] === $_aSubMenu[ 2 ] ) {
                break;    // the position variable will now contain the position of the Setting menu item.
            }
    
        }
    
        // Insert the Tag menu item before the Setting menu item.
        if ( isset( $_aMenuEntry_Tag ) ) {
            array_splice( 
                $GLOBALS['submenu'][ $_sPageSlug ], // original array
                $_iMenuPos_Setting, // position
                0, // offset - should be 0
                $_aMenuEntry_Tag  // replacement array
            );        
        }

        // Unfortunately array_splice() will loose all the associated keys(index).
        
    }        
        
}