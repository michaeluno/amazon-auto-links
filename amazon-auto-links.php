<?php
/**
 * Plugin Name:       Auto Amazon Links
 * Plugin URI:        https://en.michaeluno.jp/amazon-auto-links
 * Description:       Formerly, Amazon Auto Links. The plugin generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 * Author:            Michael Uno (miunosoft)
 * Author URI:        https://michaeluno.jp
 * Version:           5.4.3
 * Text Domain:       amazon-auto-links
 * Domain Path:       /language
 * GitHub Plugin URI: https://github.com/michaeluno/amazon-auto-links
 */

/**
 * Provides the basic information about the plugin.
 * 
 * @since 2.0.6
 * @since 3     Changed the name from `AmazonAutoLinks_Commons_Base`
 */
class AmazonAutoLinks_Registry_Base {
    const VERSION      = '5.4.3';    // <--- DON'T FORGET TO CHANGE THIS AS WELL!!
    const NAME         = 'Auto Amazon Links';
    const DESCRIPTION  = 'Formerly, Amazon Auto Links. The plugin generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.';
    const URI          = 'https://en.michaeluno.jp/amazon-auto-links';
    const AUTHOR       = 'miunosoft (Michael Uno)';
    const AUTHOR_URI   = 'https://en.michaeluno.jp/';
    const PLUGIN_URI   = 'https://en.michaeluno.jp/auto-amazon-links';
    const COPYRIGHT    = 'Copyright (c) 2013-2024, Michael Uno';
    const LICENSE      = 'GPL v2 or later';
    const CONTRIBUTORS = '';
 
}

// Do not load if accessed directly
if ( ! defined( 'ABSPATH' ) ) { 
    return; 
}

/**
 * Provides the common data shared among plugin files.
 * 
 * To use the class, first call the setUp() method, which sets up the necessary properties.
 * 
 * @copyright Copyright (c) 2013-2024, Michael Uno
 * @authorurl http://michaeluno.jp
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since     2.0.0
 * @since     3     Changed the name from `AmazonAutoLinks_Commons`.
*/
final class AmazonAutoLinks_Registry extends AmazonAutoLinks_Registry_Base {
    
    const TEXT_DOMAIN               = 'amazon-auto-links';
    const TEXT_DOMAIN_PATH          = '/language';
    
    /**
     * The hook slug used for the prefix of action and filter hook names.
     * 
     * @remark The ending underscore is not necessary.
     */    
    const HOOK_SLUG                 = 'aal';    // without trailing underscore
    
    /**
     * The transient prefix. 
     * 
     * @remark This is also accessed from uninstall.php so do not remove.
     * @remark Up to 8 characters as transient name allows 45 characters or less ( 40 for site transients ) so that md5 (32 characters) can be added
     */    
    const TRANSIENT_PREFIX          = 'AAL';

    /**
     * @var   string
     * @since 3.2.0
     */ 
    const STORE_URI_PRO             = 'https://store.michaeluno.jp/amazon-auto-links-pro';
        
    /**
     * @var   string
     * @since 2.0.6
     */
    static public $sFilePath;  
    
    /**
     * @var   string
     * @since 2.0.6
     */    
    static public $sDirPath;

    /**
     * @var   string The plugin base name retrieved with `plugin_basename()`.
     * @since 5.1.0
     */
    static public $sBaseName;

    /**
     * @since 3
     */
    static public $aOptionKeys = array(

        'main'                  => 'amazon_auto_links', // used to be const AdminOptionKey          
        'template'              => 'amazon_auto_links_templates',
        'button_css'            => 'amazon_auto_links_button_css',
        
        // 3.3.0+ Stores active auto-insert items.
        'active_auto_inserts'   => 'amazon_auto_links_active_auto_inserts',
        'active_buttons'        => 'amazon_auto_links_active_buttons',
        
        // 3.3.0+ The Tools page
        'tools'                 => 'amazon_auto_links_tools',
        
        // [3.2.0+]
        // @deprecated 4.7.6    Uses user meta
        'last_input'            => 'amazon_auto_links_last_input',
        
        'table_versions'        => array(
            // $aDatabaseTables property key => {table name}_version
            'product'       => 'aal_products_version',
            'request_cache' => 'aal_request_cache_version',
            'tasks'         => 'aal_tasks_version',
        ),

        // [3.9.0]
        // @deprecated 4.4.0 Uses a file.
        'error_log'             => 'amazon_auto_links_error_log',

        // [4.3.0]
        // @deprecated 4.4.0 Uses a file.
        'debug_log'             => 'amazon_auto_links_debug_log',

        // 4.4.0
        'paapi_request_counter' => array(
            'AU'    => 'aal_paapi_request_count_log_au',
            'BE'    => 'aal_paapi_request_count_log_be',
            'BR'    => 'aal_paapi_request_count_log_br',
            'CA'    => 'aal_paapi_request_count_log_ca',
            'EG'    => 'aal_paapi_request_count_log_eg',
            'FR'    => 'aal_paapi_request_count_log_fr',
            'DE'    => 'aal_paapi_request_count_log_de',
            'IN'    => 'aal_paapi_request_count_log_in',
            'IT'    => 'aal_paapi_request_count_log_it',
            'JP'    => 'aal_paapi_request_count_log_jp',
            'MX'    => 'aal_paapi_request_count_log_mx',
            'NL'    => 'aal_paapi_request_count_log_nl',
            'PL'    => 'aal_paapi_request_count_log_pl',
            'SG'    => 'aal_paapi_request_count_log_sg',
            'SA'    => 'aal_paapi_request_count_log_sa',
            'ES'    => 'aal_paapi_request_count_log_es',
            'SE'    => 'aal_paapi_request_count_log_se',
            'TR'    => 'aal_paapi_request_count_log_tr',
            'AE'    => 'aal_paapi_request_count_log_ae',
            'US'    => 'aal_paapi_request_count_log_us',
            'UK'    => 'aal_paapi_request_count_log_uk',
        ),

        // Legacy option keys - not used and should be deleted on uninstall.
        'v1'                    => 'amazonautolinks',
        'v2'                    => 'amazon_auto_links_admin',
        
    );

    /**
     * Used user meta keys.
     * @var   string[]
     * @since 4.6.0
     */
    static public $aUserMeta = array(
        // key => meta key
        'first_saved'                    => 'aal_first_saved',
        'last_inputs'                    => 'aal_last_inputs',                      // 4.7.6
        // Opt-in
        'load_new_templates'             => 'aal_load_new_templates',
        'surveys'                        => 'aal_surveys',                          // 4.7.0
        'announcements'                  => 'aal_announcements',                    // 4.7.0
        'developer_amazon_tag'           => 'aal_developer_amazon_tag',             // 4.7.0
        'usage_data'                     => 'aal_usage_data',                       // 4.7.0
        // Out-out
        'rated'                          => 'aal_rated',
        'never_ask_surveys'              => 'aal_never_ask_surveys',                // 4.7.0
        'never_ask_announcements'        => 'aal_never_ask_announcements',          // 4.7.0
        'never_ask_developer_amazon_tag' => 'aal_never_ask_developer_amazon_tag',   // 4.7.0
        'never_ask_usage_data'           => 'aal_never_ask_usage_data',             // 4.7.0
    );
        
    /**
     * Used admin pages.
     * @since 3
     */
    static public $aAdminPages = array(
        // key => 'page slug'        
        'main'                     => 'aal_settings',                      // Settings - used to be const PageSettingsSlug
        'category_select'          => 'aal_add_category_unit',             // Add Unit by Category
        'auto_insert'              => 'aal_define_auto_insert',            // Add Auto-insert
        'tag_unit'                 => 'aal_add_tag_unit',                  // Add Unit by Tag
        'search_unit'              => 'aal_search_unit',                   // 5.0.0+
        'paapi_search_unit'        => 'aal_add_paapi_unit',                // Add Unit by PA-API Search
        'url_unit'                 => 'aal_add_url_unit',                  // 3.2.0+
        'feed_unit'                => 'aal_add_feed_unit',                 // 4.0.0+
        'contextual_unit'          => 'aal_add_contextual_unit',           // 3.5.0+
        'email_unit'               => 'aal_add_email_unit',                // 3.5.0+
        'template'                 => 'aal_templates',
        'report'                   => 'aal_reports',                       // [4.4.0]
        'tool'                     => 'aal_tools',
        'help'                     => 'aal_help',
        'test'                     => 'aal_tests',                         // 4.3.0+
    );
    
    /**
     * Used post types.
     */
    static public $aPostTypes = array(

        // used to be const PostTypeSlug      
        'unit'        => 'amazon_auto_links',
        
        // used to be const PostTypeSlugAutoInsert
        'auto_insert' => 'aal_auto_insert',
        
        // 3+
        'button'      => 'aal_button',

    );
    
    /**
     * Used post types by meta boxes.
     */
    static public $aMetaBoxPostTypes = array(
        // 'page'      => 'page',
        // 'post'      => 'post',
    );
    
    /**
     * Used taxonomies.
     * @remark      
     */
    static public $aTaxonomies = array(
        // Used to be stored in the `TagSlug` class constant.
        'tag'   => 'amazon_auto_links_tag',
    );
    
    /**
     * Used shortcode slugs
     */
    static public $aShortcodes = array(
        'main'       => 'amazon_auto_links',
        'button'     => 'aal_button',        // 4.3.0
        'disclosure' => 'aal_disclosure',    // 4.7.0
        'v1'         => 'amazonautolinks',   // backward compatibility for v1
    );

    /**
     * Stores custom database table names.
     * @remark The below is the structure
     * array(
     *      'slug (part of database wrapper class file name)' => array(
     *          'version'   => '0.1',
     *          'name'      => 'table_name',    // serves as the table name suffix
     *      ),
     *      ...
     * )
     * @since  3.5.0
     * @since  3.8.0 Changed the version from 1.0.0 to 1.1.0.
     * @since  4.3.0 Changed the `aal_products` table version from 1.3.0 to 1.4.0.
     */
    static public $aDatabaseTables = array(
         'aal_products'        => array(
             'name'              => 'aal_products', // serves as the table name suffix
             'version'           => '1.4.0',
             'across_network'    => true,
             'class_name'        => 'AmazonAutoLinks_DatabaseTable_aal_products',
         ),
        'aal_request_cache'    => array(
            'name'              => 'aal_request_cache',  // serves as the table name suffix
            'version'           => '1.0.0',
            'across_network'    => true,
            'class_name'        => 'AmazonAutoLinks_DatabaseTable_aal_request_cache',
        ),
        // 4.3.0
        'aal_tasks'            => array(
            'name'              => 'aal_tasks',  // serves as the table name suffix
            'version'           => '1.0.1',
            'across_network'    => true,
            'class_name'        => 'AmazonAutoLinks_DatabaseTable_aal_tasks',
        ),
    );

    /**
     * Sets up class properties.
     * @param string $sPluginFilePath
     */
    static function setUp( $sPluginFilePath ) {
        self::$sFilePath = $sPluginFilePath;
        self::$sDirPath  = dirname( self::$sFilePath );
        self::$sBaseName = plugin_basename( $sPluginFilePath );
    }

    /**
     * @return string
     * @since  ?
     * @since  3.9.0   Added the `$bAbsolute` parameter.
     * @param  string  $sPath
     * @param  boolean $bAbsolute
     */
    public static function getPluginURL( $sPath='', $bAbsolute=false ) {
        $_sRelativePath = $bAbsolute
            ? ltrim( str_replace('\\', '/', str_replace( self::$sDirPath, '', $sPath ) ), '/' )
            : $sPath;
        if ( isset( self::$___sPluginURLCache ) ) {
            return self::$___sPluginURLCache . $_sRelativePath;
        }
        self::$___sPluginURLCache = trailingslashit( plugins_url( '', self::$sFilePath ) );
        return self::$___sPluginURLCache . $_sRelativePath;
    }
        /**
         * @since 3.9.0
         */
        static private $___sPluginURLCache;

    /**
     * Requirements.
     * @since 3
     */    
    static public $aRequirements = array(
        'php' => array(
            'version'   => '5.2.4',
            'error'     => 'The plugin requires the PHP version %1$s or higher.',
        ),
        'wordpress'         => array(
            'version'   => '3.4',   // uses $wpdb->delete()
            'error'     => 'The plugin requires the WordPress version %1$s or higher.',
        ),
        'mysql'             => array(
            'version'   => '5.0.3', // v5.0.3 uses VARCHAR(2083)
            'error'     => 'The plugin requires the MySQL version %1$s or higher.',
        ),
        /**
         * array(
         *   e.g. 'mblang' => 'The plugin requires the mbstring extension.',
         * ),
         */
        'functions'     => '', // disabled

        'classes'       => array(
            'DOMDocument' => 'The plugin requires the DOMXML extension.',
        ),
        /**
         * 'constants' => array(
         *     e.g. 'THEADDONFILE' => 'The plugin requires the ... addon to be installed.',
         *     e.g. 'APSPATH' => 'The script cannot be loaded directly.',
         * ),
         */
        'constants'     => '', // disabled
        /**
         * array(
         *     e.g. 'home/my_user_name/my_dir/scripts/my_scripts.php' => 'The required script could not be found.',
         * ),
         */
        'files'         => '', // disabled
    );

    /**
     * @param string $sMessage
     * @param string $sType       `error`, `updated`, `info`, and `bell` are accepted.
     * @param string $sDashIcon   The past part of a dash-icon class such as `warning` in `dashicons-warning`.
     * @param string $sExtra      Extra output placed outside the `<p>` tag for the message.
     * @since 3.11.0
     * @since 4.7.5  Added the `$sExtra` parameter.
     */
    static public function setAdminNotice( $sMessage, $sType='error', $sDashIcon='', $sExtra='' ) {
        self::$aAdminNotices[ $sMessage ] = array( 'message' => $sMessage, 'type' => $sType, 'icon' => $sDashIcon, 'extra' => $sExtra, );
        add_action( 'admin_notices', array( __CLASS__, 'replyToShowAdminNotices' ) );
    }
        static public $aAdminNotices = array();
        static public function replyToShowAdminNotices() {
            $_aColorsByType = array(
                'error'   => '#d63638', // red
                'updated' => '#00a32a', // green
                'info'    => '#0084ff', // blue
                'bell'    => '#969200', // gold
            );
            foreach( self::$aAdminNotices as $_aNotice ) {
                $_sColor      = isset( $_aColorsByType[ $_aNotice[ 'type' ] ] ) ? $_aColorsByType[ $_aNotice[ 'type' ] ] : $_aColorsByType[ 'updated' ];
                $_sIconStyle  = 'margin-left: -4px; vertical-align: middle;';
                $_sIconStyle .= "color:{$_sColor};";
                $_sIcon       = $_aNotice[ 'icon' ] ? "<span class='dashicons dashicons-" . esc_attr( $_aNotice[ 'icon' ] ) . "' style='" . esc_attr( $_sIconStyle ) . "'></span>" : '';
                $_sClass      = "notice is-dismissible {$_aNotice[ 'type' ]} hidden";
                $_sScript     = 'var _this=this.parentElement;setTimeout(function(){_this.style.display="block";_this.style["margin-top"]="15px";_this.style["border-left-color"]="' . $_sColor .  '"},3000);setTimeout(function(){_this.style.transition="opacity 1s";_this.style.opacity=1;},3010);';
                $_sExtra      = $_aNotice[ 'extra' ]
                    ? "<div class='extra'>" . $_aNotice[ 'extra' ] . "</div>"
                    : '';
                echo "<div class='" . esc_attr( $_sClass ) . "' style='opacity:0;'>"
                     . "<p>"
                        . $_sIcon
                        . '<strong>' . self::NAME . '</strong>: '
                        . $_aNotice[ 'message' ]
                     . "</p>"
                     . $_sExtra
                     . "<img src onerror='" . esc_js( $_sScript ) . "' style='display:none;'/>"
                     . "</div>";
            }
        }

    /**
     * @since 4.3.8
     * @var   string
     */
    static public $sTempDirNameSuffix = 'WPAAL_';

    /**
     * @var   string Caches the plugin site temporary directory path.
     * @since 4.3.8
     */
    static private $___sPluginSiteTempDirPath;

    /**
     * @remark Consider a case that the server hosts multiple WordPress sites. In that case, a temp directory needs to be created one per site.
     * It used to create a plugin specific parent directory,
     * but it caused a problem on a shared server that one creates it with the permission, 0755, with umask()
     * and other users became unable to create a directory inside it.
     * @since  4.3.4
     * @return string A temporary directory path for the site.
     */
    static public function getPluginSiteTempDirPath() {
        if ( isset( self::$___sPluginSiteTempDirPath ) ) {
            return self::$___sPluginSiteTempDirPath;
        }
        $_sSystemTempDirPath             = untrailingslashit( wp_normalize_path( sys_get_temp_dir() ) );
        self::$___sPluginSiteTempDirPath = $_sSystemTempDirPath . '/' . self::$sTempDirNameSuffix . md5( site_url() );
        return self::$___sPluginSiteTempDirPath;
    }

    /**
     * @var   array
     * @since 5.2.1
     */
    static public $aComponents = array(
        'main'      => array(
            'type'   => 'class',
            'loader' => 'AmazonAutoLinks_Main_Loader',
        ),
        'template' => array(
            'type'   => 'class',
            'loader' => 'AmazonAutoLinks_TemplateLoader',
        ),
        'unit'      => array(
            'type'   => 'class',
            'loader' => 'AmazonAutoLinks_Unit_Loader',
        ),
        'button'    => array(
            'type'   => 'class',
            'loader' => 'AmazonAutoLinks_Button_Loader',
        ),
        'auto_insert' => array(
            'type'   => 'class',
            'loader' => 'AmazonAutoLinks_AutoInsertLoader',
        ),
        'shortcode'   => array(
            'type'   => 'class',
            'loader' => 'AmazonAutoLinks_Shortcode',
        ),
        'widget' => array(
            'type'   => 'class',
            'loader' => 'AmazonAutoLinks_WidgetsLoader',
        ),
        'link_converter' => array( // [3.8.10]
            'type'   => 'class',
            'loader' => 'AmazonAutoLinks_Loader_LinkConverter',
        ),
        'database' => array( // [3.8.10]
            'type'   => 'class',
            'loader' => 'AmazonAutoLinks_DatabaseUpdater_Loader',
        ),
        'oembed' => array( // [4.0.0]
            'type'   => 'class',
            'loader' => 'AmazonAutoLinks_CustomOEmbed_Loader',
        ),
        'third_party' => array( // [4.1.0]
            'type'   => 'class',
            'loader' => 'AmazonAutoLinks_ThirdPartySupportLoader',
        ),
        'proxy' => array( // [4.2.0]
            'type'   => 'class',
            'loader' => 'AmazonAutoLinks_Proxy_Loader',
        ),
        'log' => array( // [4.3.0]
            'type'   => 'class',
            'loader' => 'AmazonAutoLinks_Log_Loader',
        ),
        'test' => array( // [4.3.0]
            'type'   => 'class',
            'loader' => 'AmazonAutoLinks_Test_Loader',
        ),
        'geo_targeting' => array( // [4.6.0]
            'type'   => 'class',
            'loader' => 'AmazonAutoLinks_Geotargeting_Loader',
        ),
        'opt' => array( // [4.7.0]
            'type'   => 'class',
            'loader' => 'AmazonAutoLinks_Opt_Loader',
        ),
        'disclosure' => array( // [4.7.0]
            'type'   => 'class',
            'loader' => 'AmazonAutoLinks_Disclosure_Loader',
        ),
        'gutenberg_block' => array( // [5.1.0]
            'type'   => 'class',
            'loader' => 'AmazonAutoLinks_GutenbergBlock_Loader',
        ),
    );

}
AmazonAutoLinks_Registry::setUp( __FILE__ );

include( dirname( __FILE__ ).'/include/library/apf/admin-page-framework.php' );
include( dirname( __FILE__ ).'/include/core/AmazonAutoLinks_Bootstrap.php' );
new AmazonAutoLinks_Bootstrap( AmazonAutoLinks_Registry::$sFilePath, AmazonAutoLinks_Registry::HOOK_SLUG );