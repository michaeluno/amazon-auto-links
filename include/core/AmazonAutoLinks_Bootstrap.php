<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 */

/**
 * Loads the plugin.
 *
 * @since 3
 */
final class AmazonAutoLinks_Bootstrap extends AmazonAutoLinks_AdminPageFramework_PluginBootstrap {
    
    /**
     * User constructor.
     */
    protected function construct()  {
        
        if ( $this->bIsAdmin ) {
            $this->___checkCustomTables();
        }

        // [4.3.6] Stores the plugin load time so that the remained execution time can be calculated.
        AmazonAutoLinks_Utility::setObjectCache( 'load_time', microtime( true ) );
        
    }        
        
        /**
         * Checks if table version options exist and if not install it.
         */
        private function ___checkCustomTables() {

            $_aTableVersions = array();
            foreach( AmazonAutoLinks_Registry::$aOptionKeys[ 'table_versions' ] as $_sOptionKey ) {
                $_aTableVersions[ $_sOptionKey ] = get_option( $_sOptionKey, false );
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
     * @callback        function    register_activation_hook()
     * @callback        action      plugins_loaded
     * @callback        action      activate_{plugin_basename($file)}
     */
    public function replyToInstallCustomTables() {

        new AmazonAutoLinks_DatabaseTableInstall( true ); // install

    }

        
    /**
     * Register classes to be auto-loaded.
     * 
     * @since       3
     */
    public function getClasses() {
        return include( dirname( __FILE__ ) . '/class-map.php' );
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
     * @callback    function    register_activation_hook()      action hook: activate_{plugin_basename($file)}
     * @remark      This callback is called in wp-admin/plugins.php and after this callback, the script just exits with `exit()`.
     * Also custom post types are not registered by the time this is called.
     */    
    public function replyToPluginActivation() {

        $this->___checkRequirements();
        
        $this->replyToInstallCustomTables();

        do_action( 'aal_action_plugin_activated' );
        
    }
        
        /**
         * 
         * @since            3
         */
        private function ___checkRequirements() {

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
     * The plugin activation callback method.
     */    
    public function replyToPluginDeactivation() {
        
        // Clean transients.
        AmazonAutoLinks_WPUtility::cleanTransients( AmazonAutoLinks_Registry::TRANSIENT_PREFIX );
        AmazonAutoLinks_WPUtility::cleanTransients( 'apf_' );
        
    }        
    
        
    /**
     * Load localization files.
     * 
     * @callback    action      init
     */
    public function setLocalization() {
                
        load_plugin_textdomain( 
            'amazon-auto-links',
            false, 
            dirname( plugin_basename( $this->sFilePath ) ) . AmazonAutoLinks_Registry::TEXT_DOMAIN_PATH
        );

    }        
    
    /**
     * Loads the plugin specific components. 
     * 
     * @remark        All the necessary classes should have been already loaded.
     * @callback      plugins_loaded        Unless it is set in the constructor's third parameter.
     */
    public function setUp() {
        
        // This constant is set when uninstall.php is loaded.
        if ( defined( 'DOING_PLUGIN_UNINSTALL' ) && DOING_PLUGIN_UNINSTALL ) {
            return;
        }
            
        // Include PHP files.
        $this->___include();

        // Load components
        $this->___loadComponents();
                            
        // Trigger the action. 2.1.2+
        do_action( 'aal_action_loaded_plugin' );

    }
   
        /**
         * @since       3.3.0
         * @return      void
         */
        private function ___loadComponents() {

            // Main
            new AmazonAutoLinks_Main_Loader( $this->sFilePath );
            
            // Templates
            new AmazonAutoLinks_TemplateLoader( $this->sFilePath );

            // Units
            new AmazonAutoLinks_UnitLoader( $this->sFilePath );

            // Buttons
            new AmazonAutoLinks_ButtonLoader( $this->sFilePath );
                
            // Auto-insert        
            new AmazonAutoLinks_AutoInsertLoader( $this->sFilePath );                        
                        
            // Shortcode
            new AmazonAutoLinks_Shortcode;
                
            // Widgets
            new AmazonAutoLinks_WidgetsLoader;                 
            
            // Tools - Unit option converter. This component has an event handler so needs to be loaded in the front-end as well.
            new AmazonAutoLinks_UnitOptionConverterLoader;

            /// [4.2.0]
            new AmazonAutoLinks_Proxy_Loader;

            // [4.1.0]
            new AmazonAutoLinks_ThirdPartySupportLoader;

            // [3.8.10]
            new AmazonAutoLinks_Loader_LinkConverter;
            new AmazonAutoLinks_DatabaseUpdater_Loader;

            // [4.0.0]
            new AmazonAutoLinks_CustomOEmbed_Loader;

            new AmazonAutoLinks_Test_Loader; // [4.3.0]

            new AmazonAutoLinks_Log_Loader;  // [4.3.0]

        }

        /**
         * Includes additional files.
         */
        private function ___include() {
            include( dirname( __FILE__ ) . '/function/functions.php' );
        }
    
}