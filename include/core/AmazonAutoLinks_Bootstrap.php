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
            
            // At this point, there is a value `false` in the array, which means there is a table that is not installed. So install tables.
            add_action( 'plugins_loaded', array( $this, 'replyToInstallCustomTables' ), 1 );
            
        }
    
    /**
     * Installs plugin custom database tables.
     * @callback register_activation_hook()
     * @callback add_action() plugins_loaded
     * @callback add_action() activate_{plugin_basename($file)}
     */
    public function replyToInstallCustomTables() {
        $_oTables = new AmazonAutoLinks_DatabaseTables();
        $_oTables->installAll();
    }

        
    /**
     * Register classes to be auto-loaded.
     * 
     * @since 3
     */
    public function getClasses() {
        return include( dirname( __FILE__ ) . '/class-map.php' );
    }

    /**
     * Sets up constants.
     */
    public function setConstants() {
        define( "AMAZONAUTOLINKSPLUGINFILEBASENAME", AmazonAutoLinks_Registry::$sBaseName );    // For backward-compatibility.
    }    
    
    /**
     * Sets up global variables.
     */
    public function setGlobals() {
        if ( $this->bIsAdmin ) {
            // The form transient key will be sent via the both get and post methods.
            $GLOBALS[ 'aal_transient_id' ] = isset( $_REQUEST[ 'transient_id' ] )
                ? sanitize_text_field( $_REQUEST[ 'transient_id' ] )
                : AmazonAutoLinks_Registry::TRANSIENT_PREFIX 
                    . '_Form' 
                    . '_' . get_current_user_id() 
                    . '_' . uniqid();
        }
    }    
    
    /**
     * The plugin activation callback method.
     * @callback register_activation_hook()      action hook: activate_{plugin_basename($file)}
     * @remark   This callback is called in wp-admin/plugins.php and after this callback, the script just exits with `exit()`.
     * Also, custom post types are not registered by the time this is called.
     */    
    public function replyToPluginActivation() {
        $this->___checkRequirements();
        $this->replyToInstallCustomTables();
        do_action( 'aal_action_plugin_activated' );
    }
        /**
         * @since 3
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
     * @callback add_action() init
     */
    public function setLocalization() {
        load_plugin_textdomain( 
            'amazon-auto-links',
            false, 
            dirname( AmazonAutoLinks_Registry::$sBaseName ) . AmazonAutoLinks_Registry::TEXT_DOMAIN_PATH
        );
    }
    
    /**
     * Loads the plugin specific components. 
     * 
     * @remark   All the necessary classes should have been already loaded.
     * @callback add_action() plugins_loaded        Unless it is set in the constructor's third parameter.
     */
    public function setUp() {
        
        // This constant is set when uninstall.php is loaded.
        if ( defined( 'DOING_PLUGIN_UNINSTALL' ) && DOING_PLUGIN_UNINSTALL ) {
            return;
        }
        $this->___loadComponents();
        do_action( 'aal_action_loaded_plugin' ); // Trigger the action. 2.1.2+

    }
        /**
         * @since 3.3.0
         */
        private function ___loadComponents() {
            add_filter( 'aal_filter_loading_components', array( $this, 'replyToGetLoadingComponents' ) );
            $_aComponents = apply_filters( 'aal_filter_loading_components', AmazonAutoLinks_Registry::$aComponents );
            $_aTypes      = array(
                'file'    => '___loadComponent_file',
                'class'   => '___loadComponent_class',
                'unknown' => '___loadComponent_unknown',
            );
            foreach( $_aComponents as $_aComponent ) {
                $_aComponent  = $_aComponent + array( 'type' => null, 'loader' => null );
                $_sMethodName = isset( $_aTypes[ $_aComponent[ 'type' ] ] )
                    ? $_aTypes[ $_aComponent[ 'type' ] ]
                    : $_aTypes[ 'unknown' ];
                $this->{$_sMethodName}( $_aComponent[ 'loader' ] );
            }
        }
            /**
             * @param $sFile
             * @since 5.2.0
             */
            private function ___loadComponent_file( $sFile ) {
                include( $sFile );
            }
            /**
             * @param $sClassName
             * @since 5.2.0
             */
            private function ___loadComponent_class( $sClassName ) {
                new $sClassName;
            }
            /**
             * @param $thing
             * @since 5.2.0
             */
            private function ___loadComponent_unknown( $thing ) {}  // do nothing for unknown types

            /**
             * @since 5.2.1
             */
            public function replyToGetLoadingComponents( $aLoadingComponents ) {
                $aLoadingComponents[ 'functions' ] = array(
                    'type'   => 'file',
                    'loader' => dirname( __FILE__ ) . '/function/functions.php',
                );
                // [4.6.19] Released versions don't include these
                if ( ! file_exists( dirname( __FILE__ ) . '/component/test' ) ) {
                    unset( $aLoadingComponents[ 'test' ] );
                }
                return $aLoadingComponents;
            }
    
}