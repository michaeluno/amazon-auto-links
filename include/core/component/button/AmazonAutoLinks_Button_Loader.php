<?php
/**
 * Auto Amazon Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2022 Michael Uno
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
        new AmazonAutoLinks_Button_ResourceLoader;
        
        /// Post type
        new AmazonAutoLinks_PostType_Button(
            AmazonAutoLinks_Registry::$aPostTypes[ 'button' ],  // slug
            null,   // post type argument. This is defined in the class.
            $sScriptPath   // script path               
        );

        /// Shortcode [4.3.0]
        new AmazonAutoLinks_Button_Shortcode;

        /// Button types [5.2.0]
        new AmazonAutoLinks_Button_Classic_Loader;
        new AmazonAutoLinks_Button_Button2_Loader;
        new AmazonAutoLinks_Button_Image_Loader;

        /// Events [4.3.0]
        new AmazonAutoLinks_Button_Event_Filter_Output;
        new AmazonAutoLinks_Button_Event_Filter_FieldsetsUnitDefinition;    // [5.2.0]
        new AmazonAutoLinks_Button_Event_Query_ButtonPreview_ByID;          // [5.2.0]
        new AmazonAutoLinks_Button_Event_Query_ButtonPreview_Theme;
        new AmazonAutoLinks_Button_Event_Action_DefaultButtons;
        
        // Back-end
        if ( is_admin() ) {
            add_filter( 'aal_filter_admin_button_js_preview_src', array( $this, 'replyToGetJSButtonPreviewPath' ) );
            add_filter( 'aal_filter_admin_button_js_preview_enqueue_arguments', array( $this, 'replyToGetJSButtonPreviewEnqueueArguments' ) );
        }

        // Update button post status change
        add_action( 'publish_' . AmazonAutoLinks_Registry::$aPostTypes[ 'button' ], array( $this, 'replyToCheckActiveItemStatusChange' ), 10, 2 );
        add_action( 'trash_' . AmazonAutoLinks_Registry::$aPostTypes[ 'button' ], array( $this, 'replyToCheckActiveItemStatusChange' ), 10, 2 );            
        add_action( 'aal_action_update_active_buttons', array( $this, 'replyToUpdateActiveItems' ) );
                    
    }
        
        /**
         * @remark   When a button is created or edited, this method will be called too early from the system.
         * However, this hook is also triggered when the user trashes the item from the action link in the post listing table. 
         * @since    3.3.0
         * @callback add_filter() {new post status}_{post type slug}
         */
        public function replyToCheckActiveItemStatusChange( $iPostID, $oPost ) {
            do_action( 'aal_action_update_active_buttons' );
        }
    
        /**
         * Updates the active auto-insert items.
         * @since    3.3.0
         * @callback add_action() aal_action_update_active_buttons
         */
        public function replyToUpdateActiveItems() {
            update_option( 
                AmazonAutoLinks_Registry::$aOptionKeys[ 'active_buttons' ],
                AmazonAutoLinks_PluginUtility::getActiveButtonIDsQueried(), // active button IDs
                true   // enable auto-load
            );            
        }        

    /**
     * Returns the JavaScript script path of the button preview.
     * @since 4.3.0
     */
    public function replyToGetJSButtonPreviewPath() {
        $_sFileBaseName = defined( 'WP_DEBUG' ) && WP_DEBUG
            ? 'button-preview.js'
            : 'button-preview.min.js';
        return AmazonAutoLinks_Button_Loader::$sDirPath . '/asset/js/' . $_sFileBaseName;
    }

    /**
     * @calback add_filter() aal_filter_admin_button_js_preview_enqueue_arguments
     * @param   array        $aArguments
     * @return  array
     * @since   5.2.0
     */
    public function replyToGetJSButtonPreviewEnqueueArguments( array $aArguments ) {
        return array(
            'handle_id'    => 'aalButtonPreview',
            'dependencies' => array( 'jquery' ),
            'translation'  => array(
                'activeButtons'   => AmazonAutoLinks_PluginUtility::getActiveButtonLabelsForJavaScript(),
                'debugMode'       => defined( 'WP_DEBUG' ) && WP_DEBUG,
                'spinnerURL'      => admin_url( 'images/loading.gif' ),
                'frameSRC'        => add_query_arg(
                    array(
                        'aal-button-preview' => '_by_id',
                        'button-id'          => '___button_id___',
                        // 'button-label'       => '',  // @note do not set a button label as an empty value can be accepted as an empty label. To reflect the default label, unset the key.
                    ),
                    get_site_url()
                ),
                'nonce'           => wp_create_nonce( 'aal_button_preview_nonce' ),
            ),
           'in_footer'    => true,
        ) + $aArguments;
    }

}