<?php
/**
 * Cleans up the plugin options.
 *    
 * @package      Amazon Auto Links
 * @copyright    Copyright (c) 2013-2020, <Michael Uno>
 * @author       Michael Uno
 * @authorurl    http://michaeluno.jp
 * @since        3
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

/* 
 * Plugin specific constant. 
 * We are going to load the main file to get the registry class. And in the main file, 
 * if this constant is set, it will return after declaring the registry class.
 **/
if ( ! defined( 'DOING_PLUGIN_UNINSTALL' ) ) {
    define( 'DOING_PLUGIN_UNINSTALL', true  );
}

/**
 * Set the main plugin file name here.
 */
$_sMainPluginFileName  = 'amazon-auto-links.php';
if ( file_exists( dirname( __FILE__ ). '/' . $_sMainPluginFileName ) ) {
   include( $_sMainPluginFileName );
}

if ( ! class_exists( 'AmazonAutoLinks_Registry' ) ) {
    return;
}

// 0. Delete the temporary directory
$_sTempDirPath      = rtrim( sys_get_temp_dir(), '/' ) . '/' . AmazonAutoLinks_Registry::$sTempDirName;
$_sTempDirPath_Site = $_sTempDirPath . '/' . md5( site_url() );
if ( file_exists( $_sTempDirPath_Site ) && is_dir( $_sTempDirPath_Site ) ) {
    AmazonAutoLinks_Utility::removeDirectoryRecursive( $_sTempDirPath_Site );

}
/// Consider other sites on the same server uses the plugin
if ( is_dir( $_sTempDirPath ) && AmazonAutoLinks_Utility::isDirectoryEmpty( $_sTempDirPath ) ) {
    AmazonAutoLinks_Utility::removeDirectoryRecursive( $_sTempDirPath );
}

// 1. Delete transients
$_aPrefixes = array(
    AmazonAutoLinks_Registry::TRANSIENT_PREFIX, // the plugin transients
    'apf_',      // the admin page framework transients
);
foreach( $_aPrefixes as $_sPrefix ) {
    if ( ! $_sPrefix ) { 
        continue; 
    }
    $GLOBALS['wpdb']->query( "DELETE FROM `" . $GLOBALS['table_prefix'] . "options` WHERE `option_name` LIKE ( '_transient_%{$_sPrefix}%' )" );
    $GLOBALS['wpdb']->query( "DELETE FROM `" . $GLOBALS['table_prefix'] . "options` WHERE `option_name` LIKE ( '_transient_timeout_%{$_sPrefix}%' )" );    
}

// 2. Delete options
$_aOptions = get_option( AmazonAutoLinks_Registry::$aOptionKeys[ 'main' ], array() );
$_bDelete  = isset( $_aOptions[ 'reset_settings' ][ 'reset_on_uninstall' ] ) 
    ? $_aOptions[ 'reset_settings' ][ 'reset_on_uninstall' ]
    : false;

if ( ! $_bDelete ) {
    return;
}
    
array_walk_recursive( 
    AmazonAutoLinks_Registry::$aOptionKeys, // subject array
    'delete_option'   // function name
);

// 3. Delete tables
new AmazonAutoLinks_DatabaseTableInstall( 
    false   // uninstall
);

// 4. [3.6.6+] Delete Custom Post Type Posts
foreach( AmazonAutoLinks_Registry::$aPostTypes as $_sKey => $_sPostTypeSlug ) {
    _deleteAmazonAutoLinksPosts( $_sPostTypeSlug );
}

/**
 * @since 3.6.6
 */
function _deleteAmazonAutoLinksPosts( $sPostTypeSlug ) {
    $_oResults   = new WP_Query(
        array(
            'post_type'      => $sPostTypeSlug,
            'posts_per_page' => -1,     // `-1` for all
            'fields'         => 'ids',  // return only post IDs by default.
        )
    );
    foreach( $_oResults->posts as $_iPostID ) {
        wp_delete_post( $_iPostID, true );
    }
}