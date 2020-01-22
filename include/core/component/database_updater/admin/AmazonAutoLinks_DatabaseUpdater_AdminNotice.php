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
 * Displays admin notices to prompt users to update the database.
 *
 * @package      Amazon Auto Links
 * @since        3.8.0
 */
class AmazonAutoLinks_DatabaseUpdater_AdminNotice extends AmazonAutoLinks_PluginUtility {

    private $___sTableName = '';
    private $___sVersionTo = '';
    /**
     * Performs necessary set-ups.
     * @param   string  The database table name. The option key is assumed to be `{name}_version`.
     * @param   string  The version that upgrade to.
     */
    public function __construct( $sName, $sToVersion ) {

        if ( ! $this->isPluginAdminPage() ) {
            return;
        }

        $_sCurrentVersion = get_option( "{$sName}_version", 0 );
        if ( version_compare($_sCurrentVersion, $sToVersion, '>=')) {
            return;
        }

        // Properties
        $this->___sTableName = $sName;
        $this->___sVersionTo = $sToVersion;

        // Add the script
        add_action( 'admin_enqueue_scripts', array( $this, 'replyToSetScript' ) );

        new AmazonAutoLinks_AdminPageFramework_AdminNotice(
            sprintf(
                '<b>' . AmazonAutoLinks_Registry::NAME . '</b>: '
                    . __( 'Update the plugin database table by clicking <a href="%1$s">here</a>.', 'amazon-auto-links' ),
                add_query_arg(
                    $_GET + array( 'aal_action' => 'db_update',  ),
                    self::getPageNow()
                )
            ),
            array( 'class' => 'notice-info aal_db_update' )
        );

    }

    /**
     * @since   3.8.0
     * @callback    action      admin_enqueue_scripts
     */
    public function replyToSetScript() {

        $_sScriptHandle = 'aal_database_update';
        $_aScriptData   = array(
            'ajaxURL'       => admin_url( 'admin-ajax.php' ),
            'nonce'         => wp_create_nonce( 'aal_nonce_ajax_database_updater' ),
            'versionTo'     => $this->___sVersionTo,
            'tableName'     => $this->___sTableName,
            'spinnerURL'    => admin_url( 'images/loading.gif' ),
            'pluginName'    => AmazonAutoLinks_Registry::NAME,
            'requestFailed' => __( 'Something went wrong with the Ajax request.', 'amazon-auto-links' ),
        );
        wp_enqueue_script( 'jquery' );
        wp_enqueue_script(
            $_sScriptHandle,    // handle
            $this->getSRCFromPath( AmazonAutoLinks_DatabaseUpdater_Loader::$sComponentDirPath . '/asset/plugin-database-updater.js' ),
            array( 'jquery' ),
            true
        );
        wp_localize_script(
            $_sScriptHandle,
            'aalDBUpdater',        // variable name on JavaScript side
            $_aScriptData
        );

    }

}