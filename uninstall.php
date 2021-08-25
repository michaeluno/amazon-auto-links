<?php
/**
 * Cleans up the plugin options, custom post type posts, and custom database tables.
 *    
 * @package      Amazon Auto Links
 * @copyright    Copyright (c) 2013-2021, <Michael Uno>
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

/**
 * Plugin specific constant. 
 * We are going to load the main file to get the registry class. And in the main file, 
 * if this constant is set, it will return after declaring the registry class.
 */
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

// Delete the plugin temporary directory
$_aTempDirPaths = array(
    AmazonAutoLinks_Registry::getPluginSiteTempDirPath(),
    untrailingslashit( wp_normalize_path( sys_get_temp_dir() ) ) . '/WPAAL', // old deprecated one
);
foreach( $_aTempDirPaths as $_sDirPath ) {
    if ( AmazonAutoLinks_Utility::doesDirectoryExist( $_sDirPath ) ) {
        AmazonAutoLinks_Utility::removeDirectoryRecursive( $_sDirPath );
    }
}

// Delete transients
$_aPrefixes = array(
    AmazonAutoLinks_Registry::TRANSIENT_PREFIX, // the plugin transients
    'apf_',      // the admin page framework transients
);
foreach( $_aPrefixes as $_sPrefix ) {
    if ( ! $_sPrefix ) { 
        continue; 
    }
    $GLOBALS[ 'wpdb' ]->query( "DELETE FROM `" . $GLOBALS[ 'table_prefix' ] . "options` WHERE `option_name` LIKE ( '_transient_%{$_sPrefix}%' )" );
    $GLOBALS[ 'wpdb' ]->query( "DELETE FROM `" . $GLOBALS[ 'table_prefix' ] . "options` WHERE `option_name` LIKE ( '_transient_timeout_%{$_sPrefix}%' )" );
}

// Delete options
$_aOptions = get_option( AmazonAutoLinks_Registry::$aOptionKeys[ 'main' ], array() );
$_bDelete  = isset( $_aOptions[ 'reset_settings' ][ 'reset_on_uninstall' ] ) 
    ? $_aOptions[ 'reset_settings' ][ 'reset_on_uninstall' ]
    : false;
if ( ! $_bDelete ) {
    return;
}

// Delete Pages
$_iDisclosurePage = AmazonAutoLinks_WPUtility::getPostByGUID( 'https://aal-affiliate-disclosure-page', 'ID' );
if ( $_iDisclosurePage ) {
    wp_delete_post( $_iDisclosurePage, true );
}

// User Meta
foreach( AmazonAutoLinks_Registry::$aUserMeta as $_sUserMetaKey ) {
    delete_metadata(
        'user',        // the meta type
        0,              // this doesn't actually matter in this call
        $_sUserMetaKey,         // the meta key to be removed everywhere
        '',            // this also doesn't actually matter in this call
        true            // tells the function "yes, please remove them all"
    );
}

array_walk_recursive( 
    AmazonAutoLinks_Registry::$aOptionKeys, // subject array
    'delete_option'   // function name
);

// Delete tables
new AmazonAutoLinks_DatabaseTableInstall(
    false   // uninstall
);

// [3.6.6+] Delete Custom Post Type Posts
foreach( AmazonAutoLinks_Registry::$aPostTypes as $_sKey => $_sPostTypeSlug ) {
    _deleteAmazonAutoLinksPosts( $_sPostTypeSlug );
}

/**
 * @param string $sPostTypeSlug
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