<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2020 Michael Uno
 */

/**
 * Handles moving deprecated plugin site temporary directory.
 *
 * @package      Amazon Auto Links
 * @since        4.3.8
 */
class AmazonAutoLinks_Main_Event_Action_MoveDeprecatedTemporaryPluginSiteDirectory extends AmazonAutoLinks_PluginUtility {

    /**
     * Sets up properties and hooks.
     */
    public function __construct() {
        add_action( 'aal_action_plugin_activated', array( $this, 'replyToMoveDeprecatedPluginSiteDirectory' ) );
    }

    /**
     * Moves the deprecated directory into the new location.
     */
    public function replyToMoveDeprecatedPluginSiteDirectory() {
        $_sSystemTempDirPath = untrailingslashit( wp_normalize_path( sys_get_temp_dir() ) );
        $_sDeprecatedDirPath = $_sSystemTempDirPath . '/WPAAL/' . md5( site_url() );
        if ( ! $this->doesDirectoryExist( $_sDeprecatedDirPath ) ) {
            return;
        }
        @rename( $_sDeprecatedDirPath, AmazonAutoLinks_Registry::getPluginSiteTempDirPath() );
    }

}