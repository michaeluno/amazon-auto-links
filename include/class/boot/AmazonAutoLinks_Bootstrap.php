<?php
/**
 * Amazon Auto Links
 * 
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2015 Michael Uno
 * 
 */

/**
 * Loads the plugin.
 * 
 * @action      do      aal_action_after_loading_plugin
 * @since       3
 */
final class AmazonAutoLinks_Bootstrap extends AmazonAutoLinks_AdminPageFramework_PluginBootstrap {
    
    /**
     * User constructor.
     */
    protected function construct()  {
        
        if ( $this->bIsAdmin ) {
            $this->checkCustomTables();
        }
        
    }        
        
        /**
         * Checks if table version options exist and if not install it.
         */
        private function checkCustomTables() {
            
            $_aTableVersions = array();
            foreach( AmazonAutoLinks_Registry::$aOptionKeys[ 'table_versions' ] as $_sOptionKey ) {
                $_aTableVersions[] = get_option( $_sOptionKey, false );
            }
            if ( ! in_array( false, $_aTableVersions, true ) ) {
                return;
            }
            
            // At this point, there is a value `false` in the array, 
            // which means there is a table that is not installed.            
            // Install tables.
            add_action( 
                'plugins_loaded', // action hook name
                array( $this, 'replyToInstallCustomTables' ), // callback 
                1       // priority
            );
            
        }
    
    
    /**
     * Installs plugin custom database tables.
     * @callback        plugins_loaded
     */
    public function replyToInstallCustomTables() {
        new AmazonAutoLinks_DatabaseTableInstall( 
            true    // install
        );        
    }
        
    /**
     * Register classes to be auto-loaded.
     * 
     * @since       3
     */
    public function getClasses() {
        
        // Include the include lists. The including file reassigns the list(array) to the $_aClassFiles variable.
        $_aClassFiles   = array();
        $_bLoaded       = include( dirname( $this->sFilePath ) . '/include/class-list.php' );
        if ( ! $_bLoaded ) {
            return $_aClassFiles;
        }
        return $_aClassFiles;
                
    }

    /**
     * Sets up constants.
     */
    public function setConstants() {
        
        // for backward compatibility.
        define( "AMAZONAUTOLINKSPLUGINFILEBASENAME", plugin_basename( $this->sFilePath ) ); 
        
    }    
    
    /**
     * Sets up global variables.
     */
    public function setGlobals() {
        
        if ( $this->bIsAdmin ) { 
        
            // The form transient key will be sent via the both get and post methods.
            $GLOBALS[ 'aal_transient_id' ] = isset( $_REQUEST[ 'transient_id' ] )
                ? $_REQUEST[ 'transient_id' ]
                : AmazonAutoLinks_Registry::TRANSIENT_PREFIX 
                    . '_Form' 
                    . '_' . get_current_user_id() 
                    . '_' . uniqid();

        }        
        
    }    
    
    /**
     * The plugin activation callback method.
     */    
    public function replyToPluginActivation() {

        $this->_checkRequirements();
        
        $this->replyToInstallCustomTables();
        
        $this->replyToCreateDefaultButton();
        
    }
        
        /**
         * 
         * @since            3
         */
        private function _checkRequirements() {

            $_oRequirementCheck = new AmazonAutoLinks_AdminPageFramework_Requirement(
                AmazonAutoLinks_Registry::$aRequirements,
                AmazonAutoLinks_Registry::NAME
            );
            
            if ( $_oRequirementCheck->check() ) {            
                $_oRequirementCheck->deactivatePlugin( 
                    $this->sFilePath, 
                    __( 'Deactivating the plugin', 'amazon-auto-links' ),  // additional message
                    true    // is in the activation hook. This will exit the script.
                );
            }        
             
        }    
        /**
         * 
         * @callback        action      plugins_loaded
         */
        public function replyToCreateDefaultButton() {
            new AmazonAutoLinks_DefaultButtonCreation;    
        }        
        
    /**
     * The plugin activation callback method.
     */    
    public function replyToPluginDeactivation() {
        
        // Clean transients.
        AmazonAutoLinks_WPUtility::cleanTransients( 
            AmazonAutoLinks_Registry::TRANSIENT_PREFIX
        );
        AmazonAutoLinks_WPUtility::cleanTransients( 
            'apf_'
        );
        
    }        
    
        
    /**
     * Load localization files.
     * 
     * @callback    action      init
     */
    public function setLocalization() {
                
        load_plugin_textdomain( 
            AmazonAutoLinks_Registry::TEXT_DOMAIN, 
            false, 
            dirname( plugin_basename( $this->sFilePath ) ) . '/' . AmazonAutoLinks_Registry::TEXT_DOMAIN_PATH
        );
        
    }        
    
    /**
     * Loads the plugin specific components. 
     * 
     * @remark        All the necessary classes should have been already loaded.
     */
    public function setUp() {
        
        // This constant is set when uninstall.php is loaded.
        if ( defined( 'DOING_PLUGIN_UNINSTALL' ) && DOING_PLUGIN_UNINSTALL ) {
            return;
        }
            
        // 1. Include PHP files.
        $this->_include();
            
        // 2. Option Object - must be done before the template object.
        // The initial instantiation will handle formatting options from earlier versions of the plugin.
        AmazonAutoLinks_Option::getInstance();
       
        // 3. Templates and Buttons
        new AmazonAutoLinks_TemplateLoader;
        new AmazonAutoLinks_ButtonStyleLoader;
        
        // 4. Post Types
        new AmazonAutoLinks_PostType_Unit( 
            AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ],  // slug
            null,   // post type argument. This is defined in the class.
            $this->sFilePath   // script path
        );            
        new AmazonAutoLinks_PostType_AutoInsert(
            AmazonAutoLinks_Registry::$aPostTypes[ 'auto_insert' ],  // slug
            null,   // post type argument. This is defined in the class.
            $this->sFilePath   // script path               
        );
        new AmazonAutoLinks_PostType_Button(
            AmazonAutoLinks_Registry::$aPostTypes[ 'button' ],  // slug
            null,   // post type argument. This is defined in the class.
            $this->sFilePath   // script path               
        );        
        new AmazonAutoLinks_PostType_UnitPreview;            
            
        // 5. Admin pages
        if ( $this->bIsAdmin ) {
                            
            new AmazonAutoLinks_AutoInsertAdminPage(
                '', // disable the options
                $this->sFilePath             
            );            
            new AmazonAutoLinks_CategoryUnitAdminPage(
                array(
                    'type'      => 'transient',
                    'key'       => $GLOBALS[ 'aal_transient_id' ],
                    'duration'  => 60*60*24*2,
                ),
                $this->sFilePath 
            );        
            // @deprecated 
            /* new AmazonAutoLinks_TagUnitAdminPage(
                array(
                    'type'      => 'transient',
                    'key'       => $GLOBALS[ 'aal_transient_id' ],
                    'duration'  => 60*60*24*2,
                ),
                $this->sFilePath             
            ); */
            new AmazonAutoLinks_SearchUnitAdminPage(
                array(
                    'type'      => 'transient',
                    'key'       => $GLOBALS[ 'aal_transient_id' ],
                    'duration'  => 60*60*24*2,
                ),
                $this->sFilePath                             
            );
            
            new AmazonAutoLinks_URLUnitAdminPage(
                array(
                    'type'      => 'transient',
                    'key'       => $GLOBALS[ 'aal_transient_id' ],
                    'duration'  => 60*60*24*2,
                ),
                $this->sFilePath             
            );             
            
            new AmazonAutoLinks_AdminPage( 
                AmazonAutoLinks_Registry::$aOptionKeys[ 'main' ], 
                $this->sFilePath 
            );
            new AmazonAutoLinks_ToolAdminPage(
                '', // no options
                $this->sFilePath 
            );
            new AmazonAutoLinks_HelpAdminPage(
                '', // no options
                $this->sFilePath 
            );

            // Meta boxes
            $this->_registerPageMetaBoxes();
            $this->_registerPostMetaBoxes();
            
        }
                
        // 6. Shortcode - e.g. [amazon_auto_links id="143"]
        new AmazonAutoLinks_Shortcode( 
            AmazonAutoLinks_Registry::$aShortcodes 
        );
            
        // 7. Widgets
        new AmazonAutoLinks_WidgetByID(
            sprintf( 
                __( '%1$s by Unit' ),
                AmazonAutoLinks_Registry::NAME
            )
        );
        new AmazonAutoLinks_ContextualProductWidget(
            AmazonAutoLinks_Registry::NAME . ' - ' . __( 'Contextual Products', 'amazon-auto-links' )
        );

        // 8. Auto-insert        
        new AmazonAutoLinks_AutoInsert;

        // 9. Events
        new AmazonAutoLinks_Event;    
                
        // 10. Outputs
        new AmazonAutoLinks_Credit;
                
        // 11. Trigger the action. 2.1.2+
        do_action( 'aal_action_loaded_plugin' );
        
    }
        /**
         * Includes additional files.
         */
        private function _include() {
            
            // Functions
            include( dirname( $this->sFilePath ) . '/include/function/functions.php' );
                        
        }
        
        private function _registerPageMetaBoxes() {

            new AmazonAutoLinks_AdminPageMetaBox_Information(
                null,                                           // meta box id - passing null will make it auto generate
                __( 'Information', 'amazon-auto-links' ), // title
                array( // page slugs
                    AmazonAutoLinks_Registry::$aAdminPages[ 'main' ],
                    AmazonAutoLinks_Registry::$aAdminPages[ 'tool' ],
                    AmazonAutoLinks_Registry::$aAdminPages[ 'help' ],
                ),
                'side',                                       // context
                'default'                                     // priority            
            );
            
            
        }
        
        /**
         * Adds meta boxes.
         */
        private function _registerPostMetaBoxes() {
            
            new AmazonAutoLinks_MetaBox_Unit_ViewLink(
                null,
                __( 'View', 'amazon-auto-links' ), // meta box title
                array( // post type slugs: post, page, etc.
                    AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ] 
                ), 
                'side', // context (what kind of metabox this is)
                'high' // priority                                                            
            );         
            new AmazonAutoLinks_MetaBox_TagUnit_Main(
                null,
                __( 'Main', 'amazon-auto-links' ), // meta box title
                array( // post type slugs: post, page, etc.
                    AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ] 
                ), 
                'normal', // context (what kind of metabox this is)
                'high' // priority                                    
            );
            new AmazonAutoLinks_MetaBox_CategoryUnit_Main(
                null,  // meta box ID - can be null. If null is passed, the ID gets automatically generated and the class name with all lower case characters will be applied.
                __( 'Main', 'amazon-auto-links' ), // meta box title
                array( // post type slugs: post, page, etc.
                    AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ] 
                ), 
                'normal', // context (what kind of metabox this is)
                'high' // priority                        
            );            
            new AmazonAutoLinks_MetaBox_CategoryUnit_Submit(
                null, // meta box ID - can be null. If null is passed, the ID gets automatically generated and the class name with all lower case characters will be applied.
                __( 'Added Categories', 'amazon-auto-links' ), // title
                array( // post type slugs: post, page, etc.
                    AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ] 
                ), 
                'side', // context - e.g. 'normal', 'advanced', or 'side'
                'high' // priority - e.g. 'high', 'core', 'default' or 'low'
            );
            
            new AmazonAutoLinks_MetaBox_SearchUnit_Main(
                null,   // meta box ID - null for auto-generate
                __( 'Product Search Main', 'amazon-auto-links' ),
                array( // post type slugs: post, page, etc.
                    AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ] 
                ),                 
                'normal', // context - e.g. 'normal', 'advanced', or 'side'
                'core'  // priority - e.g. 'high', 'core', 'default' or 'low'
            );            
            new AmazonAutoLinks_MetaBox_SearchUnit_Advanced(
                null,   // meta box ID - null for auto-generate
                __( 'Product Search Advanced', 'amazon-auto-links' ),
                array( // post type slugs: post, page, etc.
                    AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ] 
                ),                 
                'normal', // context - e.g. 'normal', 'advanced', or 'side'
                'low' // priority - e.g. 'high', 'core', 'default' or 'low'
            );
            
            new AmazonAutoLinks_MetaBox_ItemLookupUnit_Main(
                null,   // meta box ID - null for auto-generate
                __( 'Item Look-up Main', 'amazon-auto-links' ),
                array( // post type slugs: post, page, etc.
                    AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ] 
                ),                 
                'normal', // context - e.g. 'normal', 'advanced', or 'side'
                'core'  // priority - e.g. 'high', 'core', 'default' or 'low'
            );                 
            new AmazonAutoLinks_MetaBox_ItemLookupUnit_Advanced(
                null,   // meta box ID - null for auto-generate
                __( 'Item Look-up Advanced', 'amazon-auto-links' ),
                array( // post type slugs: post, page, etc.
                    AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ] 
                ),                 
                'normal', // context - e.g. 'normal', 'advanced', or 'side'
                'low' // priority - e.g. 'high', 'core', 'default' or 'low'            
            );

            new AmazonAutoLinks_MetaBox_SimilarityLookupUnit_Main(
                null,   // meta box ID - null for auto-generate
                __( 'Similarity Look-up Main', 'amazon-auto-links' ),
                array( // post type slugs: post, page, etc.
                    AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ] 
                ),                 
                'normal', // context - e.g. 'normal', 'advanced', or 'side'
                'core'  // priority - e.g. 'high', 'core', 'default' or 'low'
            );   
            new AmazonAutoLinks_MetaBox_SimilarityLookupUnit_Advanced(
                null,   // meta box ID - null for auto-generate
                __( 'Similarity Look-up Advanced', 'amazon-auto-links' ),
                array( // post type slugs: post, page, etc.
                    AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ] 
                ),                 
                'normal', // context - e.g. 'normal', 'advanced', or 'side'
                'low' // priority - e.g. 'high', 'core', 'default' or 'low'
            );
            
            new AmazonAutoLinks_MetaBox_Template(
                null, // meta box ID - null to auto-generate
                __( 'Template', 'amazon-auto-links' ), // title
                array( // post type slugs: post, page, etc.
                    AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ] 
                ), 
                'advanced', // context - e.g. 'normal', 'advanced', or 'side'
                'low' // priority - e.g. 'high', 'core', 'default' or 'low'
            );  
  
            new AmazonAutoLinks_MetaBox_Unit_ProductFilter(
                null,  // meta box ID - can be null. If null is passed, the ID gets automatically generated and the class name with all lower case characters will be applied.
                __( 'Unit Product Filters', 'amazon-auto-links' ), // title
                array( // post type slugs: post, page, etc.
                    AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ] 
                ), 
                'advanced', // context (what kind of metabox this is)
                'low' // priority                                                
            );
            
            // Common meta boxes
            new AmazonAutoLinks_MetaBox_Cache(
                null,  // meta box ID - can be null. If null is passed, the ID gets automatically generated and the class name with all lower case characters will be applied.
                __( 'Cache', 'amazon-auto-links' ), // title
                array( // post type slugs: post, page, etc.
                    AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ] 
                ), 
                'side', // context (what kind of metabox this is)
                'default' // priority                        
            );                   

            new AmazonAutoLinks_MetaBox_Unit_CommonAdvanced(
                null,  // meta box ID - can be null. If null is passed, the ID gets automatically generated and the class name with all lower case characters will be applied.
                __( 'Common Advanced', 'amazon-auto-links' ), // title
                array( // post type slugs: post, page, etc.
                    AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ] 
                ), 
                'side', // context (what kind of metabox this is)
                'low' // priority                                    
            );            
            
            new AmazonAutoLinks_MetaBox_DebugInfo(
                null, // meta box ID - null to auto-generate
                __( 'Debug Information', 'amazon-auto-links' ),
                array( // post type slugs: post, page, etc.
                    AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ] 
                ), 
                'advanced', // context (what kind of metabox this is)
                'low' // priority                                        
            );                       
            
            // Button Post Type
            new AmazonAutoLinks_MetaBox_Button_Preview(
                null, // meta box ID - null to auto-generate
                __( 'Button Preview', 'amazon-auto-links' ),
                array( // post type slugs: post, page, etc.
                    AmazonAutoLinks_Registry::$aPostTypes[ 'button' ] 
                ), 
                'side', // context (what kind of metabox this is)
                'high' // priority - 'high', 'sorted', 'core', 'default', 'low'
            );            
            new AmazonAutoLinks_MetaBox_Button_CSS(
                null, // meta box ID - null to auto-generate
                __( 'CSS', 'amazon-auto-links' ),
                array( // post type slugs: post, page, etc.
                    AmazonAutoLinks_Registry::$aPostTypes[ 'button' ] 
                ), 
                'side', // context (what kind of metabox this is)
                'high' // priority - 'high', 'sorted', 'core', 'default', 'low'
            );                        
            new AmazonAutoLinks_MetaBox_Button_Text(
                null, // meta box ID - null to auto-generate
                __( 'Text', 'amazon-auto-links' ),
                array( // post type slugs: post, page, etc.
                    AmazonAutoLinks_Registry::$aPostTypes[ 'button' ] 
                ), 
                'normal', // context (what kind of metabox this is)
                'default' // priority                        
            );
            new AmazonAutoLinks_MetaBox_Button_Box(
                null, // meta box ID - null to auto-generate
                __( 'Box', 'amazon-auto-links' ),
                array( // post type slugs: post, page, etc.
                    AmazonAutoLinks_Registry::$aPostTypes[ 'button' ] 
                ), 
                'normal', // context (what kind of metabox this is)
                'default' // priority                        
            );  
            new AmazonAutoLinks_MetaBox_Button_Border(
                null, // meta box ID - null to auto-generate
                __( 'Border', 'amazon-auto-links' ),
                array( // post type slugs: post, page, etc.
                    AmazonAutoLinks_Registry::$aPostTypes[ 'button' ] 
                ), 
                'normal', // context (what kind of metabox this is)
                'default' // priority                        
            );            
            new AmazonAutoLinks_MetaBox_Button_Background(
                null, // meta box ID - null to auto-generate
                __( 'Background', 'amazon-auto-links' ),
                array( // post type slugs: post, page, etc.
                    AmazonAutoLinks_Registry::$aPostTypes[ 'button' ] 
                ), 
                'normal', // context (what kind of metabox this is)
                'default' // priority                        
            );
            new AmazonAutoLinks_MetaBox_Button_Hover(
                null, // meta box ID - null to auto-generate
                __( 'Hover', 'amazon-auto-links' ),
                array( // post type slugs: post, page, etc.
                    AmazonAutoLinks_Registry::$aPostTypes[ 'button' ] 
                ), 
                'normal', // context (what kind of metabox this is)
                'default' // priority                        
            );                        
        }
    
}