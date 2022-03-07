<?php
/**
 * Auto Amazon Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 */

 
/**
 * Loads the button component.
 *  
 * @since 3.3.0
 */
class AmazonAutoLinks_Button_Loader extends AmazonAutoLinks_PluginUtility {

    /**
     * @var string
     */
    static public $sDirPath = '';

    /**
     * Sets up hooks and properties.
     */
    public function __construct( $sScriptPath ) {
        
        if ( $this->hasBeenCalled( __METHOD__ ) ) {
            return;
        }

        self::$sDirPath = dirname( __FILE__ );

        // Front-end
        
        /// Resource loader
        new AmazonAutoLinks_ButtonResourceLoader;
        
        /// Post type
        new AmazonAutoLinks_PostType_Button(
            AmazonAutoLinks_Registry::$aPostTypes[ 'button' ],  // slug
            null,   // post type argument. This is defined in the class.
            $sScriptPath   // script path               
        );

        /// Shortcode   4.3.0
        new AmazonAutoLinks_Button_Shortcode;

        /// Events      4.3.0
        new AmazonAutoLinks_Button_Event_Filter_Output;
        new AmazonAutoLinks_Button_Event_Query_ButtonPreview;
        new AmazonAutoLinks_Button_Event_Action_DefaultButtons;
        
        // Back-end
        if ( is_admin() ) {
            
            // Post meta boxes    
            $this->_registerPostMetaBoxes();
            
            add_filter( 'aal_filter_custom_meta_keys', array( $this, 'replyToAddProtectedMetaKeys' ) );

            add_filter( 'aal_filter_admin_button_js_translation', array( $this, 'replyToGetJSButtonTranslation' ) );
            add_filter( 'aal_filter_admin_button_js_preview_src', array( $this, 'replyToGetJSButtonPreviewPath' ) );

        }        

        // Update button post status change
        add_action( 'publish_' . AmazonAutoLinks_Registry::$aPostTypes[ 'button' ], array( $this, 'replyToCheckActiveItemStatusChange' ), 10, 2 );
        add_action( 'trash_' . AmazonAutoLinks_Registry::$aPostTypes[ 'button' ], array( $this, 'replyToCheckActiveItemStatusChange' ), 10, 2 );            
        add_action( 'aal_action_update_active_buttons', array( $this, 'replyToUpdateActiveItems' ) );
                    
    }
        
        /**
         * @remark      When a button is created or edited, this method will be called too early from the system.
         * However, this hook is also triggered when the user trashes the item from the action link in the post listing table. 
         * @since       3.3.0
         * @callback    filter      {new post status}_{post type slug}
         * @return      string
         */
        public function replyToCheckActiveItemStatusChange( $iPostID, $oPost ) {
            do_action( 'aal_action_update_active_buttons' );
        }
    
        /**
         * Updates the active auto-insert items.
         * @since           3.3.0
         * @callback        action      aal_action_update_active_buttons
         */
        public function replyToUpdateActiveItems() {
            $_aActiveIDs = AmazonAutoLinks_PluginUtility::getActiveButtonIDsQueried();
            update_option( 
                AmazonAutoLinks_Registry::$aOptionKeys[ 'active_buttons' ],
                $_aActiveIDs,
                true   // enable auto-load
            );            
        }        
        
        /**
         * @return      array
         * @since       3.3.0
         * @param       array       $aMetaKeys
         * @callback    add_filter  aal_filter_custom_meta_keys
         */
        public function replyToAddProtectedMetaKeys( $aMetaKeys ) {
            
            $_aClassNames = array(
                'AmazonAutoLinks_FormFields_Button_Preview',
                'AmazonAutoLinks_FormFields_Button_Selector',
                'AmazonAutoLinks_FormFields_Button_Box',
                'AmazonAutoLinks_FormFields_Button_Hover',
                'AmazonAutoLinks_FormFields_Button_Text',
                'AmazonAutoLinks_FormFields_Button_Background',
                'AmazonAutoLinks_FormFields_Button_Border',
                'AmazonAutoLinks_FormFields_Button_CSS',
            
            );
            foreach( $_aClassNames as $_sClassName ) {
                $_oFields = new $_sClassName();  // not passing a factory object as it's hard and not necessary only to get field IDs.
                $aMetaKeys = array_merge( $aMetaKeys, $_oFields->getFieldIDs() );
            }
            
            return $aMetaKeys;

        }
    

        /**
         * Adds post meta boxes.
         * @since       3.3.0
         */
        private function _registerPostMetaBoxes() {
            
            new AmazonAutoLinks_PostMetaBox_Button_Preview(
                null, // meta box ID - null to auto-generate
                __( 'Button Preview', 'amazon-auto-links' ),
                array( // post type slugs: post, page, etc.
                    AmazonAutoLinks_Registry::$aPostTypes[ 'button' ] 
                ),
                'side', // context (what kind of metabox this is)
                'high' // priority - 'high', 'sorted', 'core', 'default', 'low'
            );            
            new AmazonAutoLinks_PostMetaBox_Button_CSS(
                null, // meta box ID - null to auto-generate
                __( 'CSS', 'amazon-auto-links' ),
                array( // post type slugs: post, page, etc.
                    AmazonAutoLinks_Registry::$aPostTypes[ 'button' ] 
                ), 
                'side', // context (what kind of metabox this is)
                'high' // priority - 'high', 'sorted', 'core', 'default', 'low'
            );                        
            new AmazonAutoLinks_PostMetaBox_Button_Text(
                null, // meta box ID - null to auto-generate
                __( 'Text', 'amazon-auto-links' ),
                array( // post type slugs: post, page, etc.
                    AmazonAutoLinks_Registry::$aPostTypes[ 'button' ] 
                ), 
                'normal', // context (what kind of metabox this is)
                'default' // priority                        
            );
            new AmazonAutoLinks_PostMetaBox_Button_Box(
                null, // meta box ID - null to auto-generate
                __( 'Box', 'amazon-auto-links' ),
                array( // post type slugs: post, page, etc.
                    AmazonAutoLinks_Registry::$aPostTypes[ 'button' ] 
                ), 
                'normal', // context (what kind of metabox this is)
                'default' // priority                        
            );  
            new AmazonAutoLinks_PostMetaBox_Button_Border(
                null, // meta box ID - null to auto-generate
                __( 'Border', 'amazon-auto-links' ),
                array( // post type slugs: post, page, etc.
                    AmazonAutoLinks_Registry::$aPostTypes[ 'button' ] 
                ), 
                'normal', // context (what kind of metabox this is)
                'default' // priority                        
            );            
            new AmazonAutoLinks_PostMetaBox_Button_Background(
                null, // meta box ID - null to auto-generate
                __( 'Background', 'amazon-auto-links' ),
                array( // post type slugs: post, page, etc.
                    AmazonAutoLinks_Registry::$aPostTypes[ 'button' ] 
                ), 
                'normal', // context (what kind of metabox this is)
                'default' // priority                        
            );
            new AmazonAutoLinks_PostMetaBox_Button_Hover(
                null, // meta box ID - null to auto-generate
                __( 'Hover', 'amazon-auto-links' ),
                array( // post type slugs: post, page, etc.
                    AmazonAutoLinks_Registry::$aPostTypes[ 'button' ] 
                ), 
                'normal', // context (what kind of metabox this is)
                'default' // priority                        
            );              
            
        }

    /**
     * Returns the JavaScript script path of the button preview.
     * @since   4.3.0
     */
    public function replyToGetJSButtonPreviewPath() {
        $_sFileBaseName = defined( 'WP_DEBUG' ) && WP_DEBUG
            ? 'button-preview-in-unit-definition-page.js'
            : 'button-preview-in-unit-definition-page.min.js';
        return AmazonAutoLinks_Button_Loader::$sDirPath . '/asset/js/' . $_sFileBaseName;
    }

    /**
     * @since  4.3.0
     * @return array
     * @remark Seems not used
     */
    public function replyToGetJSButtonTranslation( $aLabels ) {
        $_aButtonIDs = $this->getActiveButtonIDs();
        $_aLabels    = $this->getAsArray( $aLabels );
        foreach( $_aButtonIDs as $_iButtonID ) {
            $_sButtonLabel = get_post_meta( $_iButtonID, 'button_label', true );
            $_sButtonLabel = $_sButtonLabel
                ? $_sButtonLabel
                : __( 'Buy Now', 'amazon-auto-links' );
            $_aLabels[ $_iButtonID ] = $_sButtonLabel;
        }
        return $_aLabels;
    }


}