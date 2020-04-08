<?php
/**
 *    Plugin Name:    Amazon Auto Links
 *    Plugin URI:     http://en.michaeluno.jp/amazon-auto-links
 *    Description:    Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *    Author:         Michael Uno (miunosoft)
 *    Author URI:     http://michaeluno.jp
 *    Version:        4.0.5
 *    Text Domain:    amazon-auto-links
 *    Domain Path:    /language
 */

/**
 * Provides the basic information about the plugin.
 * 
 * @since       2.0.6
 * @since       3       Changed the name from `AmazonAutoLinks_Commons_Base`
 */
class AmazonAutoLinks_Registry_Base {
 
    const VERSION        = '4.0.5';    // <--- DON'T FORGET TO CHANGE THIS AS WELL!!
    const NAME           = 'Amazon Auto Links';
    const DESCRIPTION    = 'Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.';
    const URI            = 'http://en.michaeluno.jp/amazon-auto-links';
    const AUTHOR         = 'miunosoft (Michael Uno)';
    const AUTHOR_URI     = 'http://en.michaeluno.jp/';
    const PLUGIN_URI     = 'http://en.michaeluno.jp/amazon-auto-links';
    const COPYRIGHT      = 'Copyright (c) 2013-2020, Michael Uno';
    const LICENSE        = 'GPL v2 or later';
    const CONTRIBUTORS   = '';
 
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
 * @package     Amazon Auto Links
 * @copyright   Copyright (c) 2013-2020, Michael Uno
 * @authorurl   http://michaeluno.jp
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.0.0
 * @since       3           Changed the name from `AmazonAutoLinks_Commons`.
*/
final class AmazonAutoLinks_Registry extends AmazonAutoLinks_Registry_Base {
    
    const TEXT_DOMAIN               = 'amazon-auto-links';
    const TEXT_DOMAIN_PATH          = '/language';
    
    /**
     * The hook slug used for the prefix of action and filter hook names.
     * 
     * @remark      The ending underscore is not necessary.
     */    
    const HOOK_SLUG                 = 'aal';    // without trailing underscore
    
    /**
     * The transient prefix. 
     * 
     * @remark      This is also accessed from uninstall.php so do not remove.
     * @remark      Up to 8 characters as transient name allows 45 characters or less ( 40 for site transients ) so that md5 (32 characters) can be added
     */    
    const TRANSIENT_PREFIX          = 'AAL';
    
   
    // const SectionID_License         = 'pro_license';
    // const FieldID_LicenseKey        = 'pro_license_key';

    /**
     * @since       3.2.0
     */ 
    const STORE_URI_PRO             = 'https://store.michaeluno.jp/amazon-auto-links-pro/amazon-auto-links-pro/';
        
    /**
     * 
     * @since       2.0.6
     */
    static public $sFilePath;  
    
    /**
     * 
     * @since       2.0.6
     */    
    static public $sDirPath;

    /**
     * @since   3.7.7
     * @var string
     */
    static public $sTempDirName = 'WPAAL';

    /**
     * @since       3
     */
    static public $aOptionKeys = array(

        'main'                  => 'amazon_auto_links', // used to be const AdminOptionKey          
        'template'              => 'amazon_auto_links_templates',
        'button_css'            => 'amazon_auto_links_button_css',
        
        // 3.3.0+ Stores active auto-insert items.
        'active_auto_inserts'   => 'amazon_auto_links_active_auto_inserts',
        'active_buttons'        => 'amazon_auto_links_active_buttons',
        
        // 3.3.0+ The Tools page - to remember last inputs
        'tools'                 => 'amazon_auto_links_tools',
        
        // 3.2.0+
        'last_input'            => 'amazon_auto_links_last_input',
        
        'table_versions'        => array(
            // $aDatabaseTables property key => {table name}_version
            'product'       => 'aal_products_version',
            'request_cache' => 'aal_request_cache_version',
        ),

        // 3.9.0
        'error_log'             => 'amazon_auto_links_error_log',
        
        // Legacy option keys - not used and should be deleted on uninstall.
        'v1'                    => 'amazonautolinks',
        'v2'                    => 'amazon_auto_links_admin',
        
    );
        
    /**
     * Used admin pages.
     * @since       3
     */
    static public $aAdminPages = array(
        // key => 'page slug'        
        'main'              => 'aal_settings',                  // Settings - used to be const PageSettingsSlug
        'category_select'   => 'aal_add_category_unit',         // Add Unit by Category
        'auto_insert'       => 'aal_define_auto_insert',        // Add Auto-insert
        'tag_unit'          => 'aal_add_tag_unit',              // Add Unit by Tag
        'search_unit'       => 'aal_add_search_unit',           // Add Unit by Search
        'url_unit'          => 'aal_add_url_unit',              // 3.2.0+
        'feed_unit'         => 'aal_add_feed_unit',             // 4.0.0+
        'contextual_unit'   => 'aal_add_contextual_unit',       // 3.5.0+
        'email_unit'        => 'aal_add_email_unit',            // 3.5.0+
        'template'          => 'aal_templates',
        'tool'              => 'aal_tools',
        'help'              => 'aal_help',
    );
    
    /**
     * Used post types.
     */
    static public $aPostTypes = array(

        // used to be const PostTypeSlug      
        'unit'        => 'amazon_auto_links',
        
        // use to be const PostTypeSlugAutoInsert
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
        'main'  => 'amazon_auto_links',
        'v1'    => 'amazonautolinks',   // backward compatibility for v1
    );

    /**
     * Stores custom database table names.
     * @remark      The below is the structure
     * array(
     *      'slug (part of database wrapper class file name)' => array(
     *          'version'   => '0.1',
     *          'name'      => 'table_name',    // serves as the table name suffix
     *      ),
     *      ...
     * )
     * @since       3.5.0
     * @since       3.8.0       Changed the version from 1.0.0 to 1.1.0.
     */
    static public $aDatabaseTables = array(
         'aal_products'        => array(
             'name'              => 'aal_products', // serves as the table name suffix
             'version'           => '1.3.0',
             'across_network'    => true,
             'class_name'        => 'AmazonAutoLinks_DatabaseTable_aal_products',
         ),
        'aal_request_cache'    => array(
            'name'              => 'aal_request_cache',  // serves as the table name suffix
            'version'           => '1.0.0',
            'across_network'    => true,
            'class_name'        => 'AmazonAutoLinks_DatabaseTable_aal_request_cache',
        ),
    );

    /**
     * Sets up class properties.
     * @return      void
     */
    static function setUp( $sPluginFilePath ) {

        self::$sFilePath = $sPluginFilePath; 
        self::$sDirPath  = dirname( self::$sFilePath );  
        
    }

    /**
     * @return      string
     * @since       ?
     * @since       3.9.0       Added the `$bAbsolute` parameter.
     */
    public static function getPluginURL( $sPath='', $bAbsolute=false ) {
        $_sRelativePath = $bAbsolute
            ? str_replace('\\', '/', str_replace( self::$sDirPath, '', $sPath ) )
            : $sPath;
        if ( isset( self::$_sPluginURLCache ) ) {
            return self::$_sPluginURLCache . $_sRelativePath;
        }
        self::$_sPluginURLCache = trailingslashit( plugins_url( '', self::$sFilePath ) );
        return self::$_sPluginURLCache . $_sRelativePath;
    }
        /**
         * @since    3.9.0
         */
        static private $_sPluginURLCache;

    /**
     * Requirements.
     * @since           3
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
            'version'   => '5.0.3', // uses VARCHAR(2083) 
            'error'     => 'The plugin requires the MySQL version %1$s or higher.',
        ),
        'functions'     => '', // disabled
        // array(
            // e.g. 'mblang' => 'The plugin requires the mbstring extension.',
        // ),
        'classes'       => array(
            'DOMDocument' => 'The plugin requires the DOMXML extension.',
        ),
        'constants'     => '', // disabled
        // array(
            // e.g. 'THEADDONFILE' => 'The plugin requires the ... addon to be installed.',
            // e.g. 'APSPATH' => 'The script cannot be loaded directly.',
        // ),
        'files'         => '', // disabled
        // array(
            // e.g. 'home/my_user_name/my_dir/scripts/my_scripts.php' => 'The required script could not be found.',
        // ),
    );

    /**
     * @param   string $sMessage
     * @param   string $sType
     * @since   3.11.0
     */
    static public function setAdminNotice( $sMessage, $sType='error' ) {
        self::$aAdminNotices[ $sMessage ] = array( 'message' => $sMessage, 'type' => $sType );
        add_action( 'admin_notices', array( __CLASS__, 'replyToShowAdminNotices' ) );
    }
        static public $aAdminNotices = array();
        static public function replyToShowAdminNotices() {
            foreach( self::$aAdminNotices as $_aNotice ) {
                $_sType = esc_attr( $_aNotice[ 'type' ] );
                echo "<div class='{$_sType}'>"
                     . "<p>"
                        . '<strong>' . self::NAME . '</strong>: '
                        . $_aNotice[ 'message' ]
                     . "</p>"
                     . "</div>";
            }
        }

}
AmazonAutoLinks_Registry::setUp( __FILE__ );

include( dirname( __FILE__ ).'/include/library/apf/admin-page-framework.php' );
include( dirname( __FILE__ ).'/include/AmazonAutoLinks_Bootstrap.php' );
new AmazonAutoLinks_Bootstrap( AmazonAutoLinks_Registry::$sFilePath, AmazonAutoLinks_Registry::HOOK_SLUG );