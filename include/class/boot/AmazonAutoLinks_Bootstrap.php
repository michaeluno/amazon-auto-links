<?php
/**
 * Handles the initial set-up for the plugin.
 * 
 * @package     Amazon Auto Links
 * @copyright   Copyright (c) 2013, Michael Uno
 * @authorurl    http://michaeluno.jp
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since        2.0.0
*/

/**
 * 
 * @action      schedule    aal_action_setup_transients     The cron event hook that sets up transients.
 * @action      do          aal_action_loaded_plugin        Triggered after all the plugin components are loaded.
 * @filter      apply       aal_filter_classes              Applies to the loading class array.
 */
final class AmazonAutoLinks_Bootstrap {
    
    /**
     * Indicates wheher the class has been instantiated or not. 
     * 
     * The bootstrap class can only be instantiated per page load.
     * 
     * @since   2.1.1
     */
    static private $_bLoaded = false;
    
    /**
     * Sets up hooks and properties.
     */
    public function __construct( $sPluginFilePath ) {
        
        if ( self::$_bLoaded ) {
            return;
        }
        self::$_bLoaded = true;        
        
        $this->_bIsAdmin    = is_admin();
        $this->_sFilePath   = $sPluginFilePath;
        
        // 0. Define constants.
        $this->_defineConstants();
        
        // 1. Set global variables.
        $this->_setGlobals();
        
        // 2. Set up auto-load classes.
        $this->_loadClasses( $this->_sFilePath );
        
        // 3. Load the class that holds the common plugin info.
        // AmazonAutoLinks_Commons::setUp( $this->_sFilePath );
        
        // 4. Set up activation hook.
        register_activation_hook( $this->_sFilePath, array( $this, '_replyToDoWhenPluginActivates' ) );
        
        // 5. Set up deactivation hook.
        register_deactivation_hook( $this->_sFilePath, array( $this, '_replyToDoWhenPluginDeactivates' ) );
        // register_uninstall_hook( $this->_sFilePath, 'self::_replyToDoWhenPluginUninstalled' );
        
        // 6. Schedule to call start-up functions after all the plugins are loaded.
        add_action( 'plugins_loaded', array( $this, '_replyToLoadPlugin' ), 999, 1 );

        // 7. Plugin requirement check. 
        $this->_checkRequirements();
        
    }    
    
    /**
     * Loads the plugin full components.
     * 
     * The callback method triggered with the 'plugins_loaded' hook.
     * 
     */
    public function _replyToLoadPlugin() {
        
        // All the necessary classes have been already loaded.
        // 1. Set up localization.
        $this->_localize();
        
        // 2. Load Necessary libraries
        include( dirname( $this->_sFilePath ) . '/include/library/admin-page-framework-for-amazon-auto-links.php' );

        // 3. Include functions.
        include( dirname( $this->_sFilePath ) . '/include/function/functions.php' );
        
        // 4. Option Object
        $GLOBALS['oAmazonAutoLinks_Option'] = new AmazonAutoLinks_Option( AmazonAutoLinks_Commons::AdminOptionKey );

        // 5. Templates
        $GLOBALS['oAmazonAutoLinks_Templates'] = new AmazonAutoLinks_Templates;        
        $GLOBALS['oAmazonAutoLinks_Templates']->loadFunctionsOfActiveTemplates();
        add_action( 'wp_enqueue_scripts', array( $GLOBALS['oAmazonAutoLinks_Templates'], 'enqueueActiveTemplateStyles' ) );
        if ( $this->_bIsAdmin ) {
            $GLOBALS['oAmazonAutoLinks_Templates']->loadSettingsOfActiveTemplates();
        }
            
        // 6. Admin pages
        if ( $this->_bIsAdmin ) {
            new AmazonAutoLinks_AdminPage( AmazonAutoLinks_Commons::AdminOptionKey, $this->_sFilePath );        
        }

        // 7. Post Types
        new AmazonAutoLinks_PostType( AmazonAutoLinks_Commons::PostTypeSlug, null, $this->_sFilePath );     // post type slug
        new AmazonAutoLinks_PostType_AutoInsert( AmazonAutoLinks_Commons::PostTypeSlugAutoInsert, null, $this->_sFilePath );     // post type slug
        new AmazonAutoLinks_PostType_UnitPreview;
        
        // 8. Meta Boxes
        if ( $this->_bIsAdmin ) {
            $this->_registerMetaBoxes();
        }
                
        // 9. Shortcode - e.g. [amazon_auto_links id="143"]
        new AmazonAutoLinks_Shortcode( AmazonAutoLinks_Commons::ShortCode );    // amazon_auto_links
        new AmazonAutoLinks_Shortcode( 'amazonautolinks' );     // backward compatibility with v1.x. This will be deprecated later at some point.
            
        // 10. Widgets
        add_action( 'widgets_init', 'AmazonAutoLinks_WidgetByID::registerWidget' );
        // add_action( 'widgets_init', 'AmazonAutoLinks_WidgetByTag::registerWidget' );
                
        // 11. Auto-insert        
        new AmazonAutoLinks_AutoInsert;
        
        // 12. Events
        new AmazonAutoLinks_Event;    
        
        // 13. MISC
        if ( $this->_bIsAdmin ) {
            $GLOBALS['oAmazonAutoLinksUserAds'] = isset( $GLOBALS['oAmazonAutoLinksUserAds'] ) ? $GLOBALS['oAmazonAutoLinksUserAds'] : new AmazonAutoLinks_UserAds;
        }
        
        // 14. Trigger the action. 2.1.2+
        do_action( 'aal_action_loaded_plugin' );
        
    }
    
        /**
         * Registers the plugin meta boxes
         * 
         * @since            2.0.3
         */
        private function _registerMetaBoxes() {
            
            $GLOBALS['strAmazonAutoLinks_UnitType'] = AmazonAutoLinks_Option::getUnitType();
            $_sUnitType       = $GLOBALS['strAmazonAutoLinks_UnitType'];
            $_bIsUpdatingUnit = ( empty( $_GET ) && 'post.php' === $GLOBALS['pagenow'] );    // when saving the meta data, the GET array is empty
            if ( $_sUnitType == 'category' || $_bIsUpdatingUnit ) {    
                new AmazonAutoLinks_MetaBox_CategoryOptions(
                    'amazon_auto_links_category_unit_options_meta_box',    // meta box ID
                    __( 'Category Unit Options', 'amazon-auto-links' ),        // meta box title
                    array( AmazonAutoLinks_Commons::PostTypeSlug ),    // post, page, etc.
                    'normal',
                    'default'
                );    
                new AmazonAutoLinks_MetaBox_Categories;
            }
            // Do not use  else here for the meta box saving process
            if ( $_sUnitType == 'tag' || $_bIsUpdatingUnit ) {
                new AmazonAutoLinks_MetaBox_TagOptions(
                    'amazon_auto_links_tag_unit_options_meta_box',    // meta box ID
                    __( 'Tag Unit Options', 'amazon-auto-links' ),        // meta box title
                    array( AmazonAutoLinks_Commons::PostTypeSlug ),    // post, page, etc.
                    'normal',
                    'default'
                );                    
            }
            // Do not use  else here for the meta box saving process
            if ( $_sUnitType == 'search' || $_bIsUpdatingUnit ) {
                new AmazonAutoLinks_MetaBox_SearchOptions(
                    'amazon_auto_links_search_unit_options_meta_box',    // meta box ID
                    __( 'Search Unit Options', 'amazon-auto-links' ),        // meta box title
                    array( AmazonAutoLinks_Commons::PostTypeSlug ),    // post, page, etc.
                    'normal',
                    'default'            
                );    
                new AmazonAutoLinks_MetaBox_SearchOptions_Advanced(
                    'amazon_auto_links_advanced_search_unit_options_meta_box',    // meta box ID
                    __( 'Advanced Search Options', 'amazon-auto-links' ),        // meta box title
                    array( AmazonAutoLinks_Commons::PostTypeSlug ),    // post, page, etc.
                    'normal',
                    'default'            
                );    
            }
            // Do not use else here for the meta box saving process
            if ( $_sUnitType == 'item_lookup' || $_bIsUpdatingUnit ) {    // the second condition is for when updating the unit.
                new AmazonAutoLinks_MetaBox_ItemLookupOptions(
                    'amazon_auto_links_item_lookup_unit_options_meta_box',    // meta box ID
                    __( 'Item Look-up Options', 'amazon-auto-links' ),        // meta box title
                    array( AmazonAutoLinks_Commons::PostTypeSlug ),    // post, page, etc.
                    'normal',
                    'default'            
                );
                new AmazonAutoLinks_MetaBox_ItemLookupOptions_Advanced(
                    'amazon_auto_links_advanced_item_lookup_unit_options_meta_box',    // meta box ID
                    __( 'Advanced Item Look-up Options', 'amazon-auto-links' ),        // meta box title
                    array( AmazonAutoLinks_Commons::PostTypeSlug ),    // post, page, etc.
                    'normal',
                    'default'                
                );
            }            
            // Do not use else here for the meta box saving process
            if ( $_sUnitType == 'similarity_lookup' || $_bIsUpdatingUnit ) {    // the second condition is for when updating the unit.
                new AmazonAutoLinks_MetaBox_SimilarityLookupOptions(
                    'amazon_auto_links_similarity_lookup_unit_options_meta_box',    // meta box ID
                    __( 'Similarity Look-up Options', 'amazon-auto-links' ),        // meta box title
                    array( AmazonAutoLinks_Commons::PostTypeSlug ),    // post, page, etc.
                    'normal',
                    'default'            
                );
                new AmazonAutoLinks_MetaBox_SimilarityLookupOptions_Advanced(
                    'amazon_auto_links_advanced_similarity_lookup_unit_options_meta_box',    // meta box ID
                    __( 'Advanced Similarity Look-up Options', 'amazon-auto-links' ),        // meta box title
                    array( AmazonAutoLinks_Commons::PostTypeSlug ),    // post, page, etc.
                    'normal',
                    'default'                
                );
            }                
            
            
            new AmazonAutoLinks_MetaBox_Template(
                'amazon_auto_links_template_meta_box',    // meta box ID
                __( 'Template', 'amazon-auto-links' ),        // meta box title
                array( AmazonAutoLinks_Commons::PostTypeSlug ),    // post, page, etc.
                'normal',    // side 
                'default'
            );
            
            new AmazonAutoLinks_MetaBox_Misc;        
            
        }

    
    /**
     * Defines plugin specific constants.
     */
    protected function _defineConstants() {
        
        define( "AMAZONAUTOLINKSPLUGINFILEBASENAME", plugin_basename( $this->_sFilePath ) );    // for backward compatibility.
    
    }
    
    /**
     * Declares plugin specific global variables.
     */
    protected function _setGlobals() {
        
        // Stores the option object
        $GLOBALS['oAmazonAutoLinks_Option'] = null;    
        
        // Stores the template object
        $GLOBALS['oAmazonAutoLinks_Templates'] = null;    
        
        // Stores custom registering class paths
        $GLOBALS['arrAmazonAutoLinks_Classes'] = isset( $GLOBALS['arrAmazonAutoLinks_Classes'] ) && is_array( $GLOBALS['arrAmazonAutoLinks_Classes'] ) ? $GLOBALS['arrAmazonAutoLinks_Classes'] : array();
                
        // Stores request url's transient info.
        $GLOBALS['arrAmazonAutoLinks_APIRequestURIs'] = array();
    
        // Stores the current unit type in admin pages. This will be set in the method that loads meta boxes.
        $GLOBALS['strAmazonAutoLinks_UnitType'] = '';
        
        // ASINs blacklist 
        $GLOBALS['arrBlackASINs'] = array();
        
    }
    
    /**
     * Register class files to be auto-loaded.
     */
    protected function _loadClasses( $sFilePath ) {
        
        $_aClassFiles = array();    // this variable will be updated in the included file.
        include( dirname( $sFilePath ) . '/include/amazon-auto-links-include-class-file-list-boot.php' );
        new AmazonAutoLinks_RegisterClasses( '', array(), $_aClassFiles );
        
        // Schedule to register regular classes when all the plugins are loaded. This allows other scripts to modify the loading class files.
        add_action( 'plugins_loaded', array( $this, '_replyToLoadClasses') );
        
    }
        /**
         * Register class files to be auto-loaded with a delay.
         */
        public function _replyToLoadClasses() {
                        
            // For the backward compatibility. The old versions store elements with the key of file base name including its file extension.
            // Here it sets the key without its file extension.
            $_aAmazonAutoLinksClasses = array();
            foreach( ( array ) $GLOBALS['arrAmazonAutoLinks_Classes'] as $_sBaseName => $_sFilePath ) {
                $_aAmazonAutoLinksClasses[ pathinfo( $_sFilePath, PATHINFO_FILENAME ) ] = $_sFilePath;
            }
            $_aAmazonAutoLinksClasses = apply_filters( 'aal_filter_classes', $_aAmazonAutoLinksClasses );
            
            $_sPluginDir    = dirname( $this->_sFilePath );            
            
                // @todo This block shuold be done with the id_admin() check. However, some components such as auto-insert needs to read array structures defined in form classes.
                // So classes that reside in the admin directory also need to be loaded in the front end at the moment.
                // Move those structure definitions into the options class then this should be able to be avoided.
                $_aAdminClassFiles  = array();
                include( $_sPluginDir . '/include/amazon-auto-links-include-class-file-list-admin.php' );
                new AmazonAutoLinks_RegisterClasses( '', array(), $_aAmazonAutoLinksClasses + $_aAdminClassFiles );
         
            $_aClassFiles = array();
            include( $_sPluginDir . '/include/amazon-auto-links-include-class-file-list.php' );
            new AmazonAutoLinks_RegisterClasses( '', array(), $_aAmazonAutoLinksClasses + $_aClassFiles );
            
        }

    /**
     * A callback method triggered when the plugin is activated.
     */
    public function _replyToDoWhenPluginActivates() {
        
        // Schedule transient set-ups
        wp_schedule_single_event( time(), 'aal_action_setup_transients' );        
        
    }
    
    /**
     * A callback method triggered when the plugin is deactivated.
     */
    public function _replyToDoWhenPluginDeactivates() {
        AmazonAutoLinks_WPUtilities::cleanTransients();
    }    
    
    /**
     * A callback method triggered when the plugin is uninstalled.
     * @remark            currently not used yet.
     */
    public static function _replyToDoWhenPluginUninstalled() {
        AmazonAutoLinks_WPUtilities::cleanTransients();    
    }
    
    /**
     * Registers localization files.
     */
    protected function _localize() {
        
        load_plugin_textdomain( 
            AmazonAutoLinks_Commons::TextDomain, 
            false, 
            dirname( plugin_basename( $this->_sFilePath ) ) . '/language/'
        );
        
        if ( is_admin() ) {
            load_plugin_textdomain( 
                'admin-page-framework', 
                false, 
                dirname( plugin_basename( $this->_sFilePath ) ) . '/language/'
            );        
        }
        
    }        
        
    /**
     * Performs plugin requirements check.
     * 
     * This is triggered with the admin_init hook. Do not use this with register_activation_hook(), which does not work.
     * 
     */    
    protected function _checkRequirements() {
        
        // Requirement Check
        new AmazonAutoLinks_Requirements( 
            $this->_sFilePath,
            array(
                'php' => array(
                    'version' => '5.2.4',
                    'error' => __( 'The plugin requires the PHP version %1$s or higher.', 'amazon-auto-links' ),
                ),
                'wordpress' => array(
                    'version' => '3.3',
                    'error' => __( 'The plugin requires the WordPress version %1$s or higher.', 'amazon-auto-links' ),
                ),
                'functions' => array(
                    'mb_substr' => sprintf( __( 'The plugin requires the <a href="%2$s">%1$s</a> to be installed.', 'amazon-auto-links' ), __( 'the Multibyte String library', 'amazon-auto-links' ), 'http://www.php.net/manual/en/book.mbstring.php' ),
                    'curl_version' => sprintf( __( 'The plugin requires the %1$s to be installed.', 'amazon-auto-links' ), __( 'the cURL library', 'amazon-auto-links' ) ),
                ),
                'classes' => array(
                    'DOMDocument' => sprintf( __( 'The DOMDocument class could not be found. The plugin requires the <a href="%1$s">libxml</a> extension to be activated.', 'amazon-auto-links' ), 'http://www.php.net/manual/en/book.libxml.php' ),
                    'DomXpath' => sprintf( __( 'The DomXpath class could not be found. The plugin requires the <a href="%1$s">libxml</a> extension to be activated.', 'amazon-auto-links' ), 'http://www.php.net/manual/en/book.libxml.php' ),
                ),
                'constants'    => array(),
            ),
            true,             // if it fails it will deactivate the plugin
            'admin_init'
        );    

    }    
    
}