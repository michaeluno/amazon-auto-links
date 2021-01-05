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
 * Displays admin notices to prompt users to update the database.
 *
 * @package      Amazon Auto Links
 * @since        3.8.0
 */
class AmazonAutoLinks_DatabaseUpdater_AdminNotice extends AmazonAutoLinks_PluginUtility {

    /**
     * Performs necessary set-ups.
     * @param   string  The database table name. The option key is assumed to be `{name}_version`.
     * @param   string  The version that upgrade to.
     * @since   3.8.0
     * @since   4.3.0       Removed the parameters.
     */
    public function __construct() {

        if ( ! $this->isPluginAdminPage() ) {
            return;
        }

        if ( ! $this->___hasTableUpdate() ) {
            return;
        }

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
         * @return bool
         */
        private function ___hasTableUpdate() {
            foreach( AmazonAutoLinks_Registry::$aDatabaseTables as $_sTableName => $_aTableInfo ) {
                $_sCurrentVersion = get_option( "{$_aTableInfo[ 'name' ]}_version", 0 );
                $_sToVersion      = $_aTableInfo[ 'version' ];
                if ( version_compare( $_sCurrentVersion, $_sToVersion, '<' ) ) {
                    return true;
                }
            }
            return false;
        }

}