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
 * Loads the button component
 *  
 * @package Amazon Auto Links
 * @since   3.3.0
 * @since   4.4.0   Renamed from `AmazonAutoLinks_MainLoader`.
 */
class AmazonAutoLinks_Main_Loader extends AmazonAutoLinks_PluginUtility {

    /**
     * Stores the directory path of this component. Used to refer to asset item locations.
     * @var   string
     * @since 4.4.0
     */
    static public $sDirPath;

    /**
     * @var   string
     * @since 4.4.0
     */
    public $sScriptPath;

    /**
     * Sets up hooks and properties.
     * @param string $sScriptPath
     */
    public function __construct( $sScriptPath ) {
        
        if ( $this->hasBeenCalled( __METHOD__ ) ) {
            return;
        }

        self::$sDirPath = dirname( __FILE__ );
        $this->sScriptPath = $sScriptPath;

        // Front-end
        
        /**
         * Option Object - must be done before the template object.
         * The initial instantiation will handle formatting options from earlier versions of the plugin.
         */
        AmazonAutoLinks_Option::getInstance();            
        
        // Events
        new AmazonAutoLinks_Event;               
                
        // Outputs
        new AmazonAutoLinks_Credit;            
        
        // Back-end
        if ( is_admin() ) {
            $this->___loadAdminComponents();
        }

        // 3.8.0
        new AmazonAutoLinks_OptionUpdater_To380;

    }

        /**
         * @since 3.3.0
         * @since 4.4.0 Moved from `AmazonAutoLinks_Bootstrap`.
         */
        private function ___loadAdminComponents() {

            // Admin pages
            new AmazonAutoLinks_SettingsAdminPageLoader;
            new AmazonAutoLinks_ToolAdminPage( AmazonAutoLinks_Registry::$aOptionKeys[ 'tools' ], $this->sScriptPath );
            new AmazonAutoLinks_HelpAdminPage;
            new AmazonAutoLinks_ReportAdminPage;
            new AmazonAutoLinks_AdminPage( AmazonAutoLinks_Registry::$aOptionKeys[ 'main' ], $this->sScriptPath );
            new AmazonAutoLinks_InfoBoxLoader; // must be called after new AmazonAutoLinks_AdminPage(); otherwise, setting notices do not appear.

            /**
             * Delay the check with the below action so that the screen type can be determined.
             * Also in multi-site network, $GLOBALS[ 'pagenow' ] is not set properly until a certain point
             * so delaying the check is safer.
             */
            add_action( 'current_screen', array( $this, 'replyToSetUpHooks' ) );

        }
            /**
             * @param    WP_Screen $oWPScreen
             * @callback add_action() current_screen
             * @since    3.3.0
             */
            public function replyToSetUpHooks( $oWPScreen ) {

                if ( $this->isInPostEditingPage() ) {
                    add_filter( 'is_protected_meta', array( $this, 'replyToCheckProtectedPostMetaKey' ), 10, 3 );
                }

            }

        /**
         * Checks whether the post meta key should be displayed in the Custom Fields field in post editing page.
         * 
         * @since       3.3.0
         * @param       boolean $bProtected
         * @param       string  $sMetaKey
         * @param       string  $sMetaType
         * @return      boolean
         * @callback    add_filter()      is_protected_meta
         */
        public function replyToCheckProtectedPostMetaKey( $bProtected, $sMetaKey, $sMetaType ) {
            
            if ( 'post' !== $sMetaType ) {
                return $bProtected;
            }
            if ( in_array( $sMetaKey, $this->___getPluginCustomMetaKeys(), true ) ) {
                return true;
            }
            return $bProtected;
            
        }
    
            /**
             * @since       3.3.0
             * @return      array
             */
            private function ___getPluginCustomMetaKeys() {
                static $_aCache;
                
                if ( isset( $_aCache ) ) {
                    return $_aCache;
                }
                return ( array ) apply_filters( 'aal_filter_custom_meta_keys', array() );
            }    
    
}