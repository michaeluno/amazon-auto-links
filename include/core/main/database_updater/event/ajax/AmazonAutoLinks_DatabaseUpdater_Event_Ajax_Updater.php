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
 * Updates the plugin database table.
 * @since   3.8.0
 *
 */
class AmazonAutoLinks_DatabaseUpdater_Event_Ajax_Updater extends AmazonAutoLinks_AjaxEvent_Base {

    protected $_sActionHookSuffix = 'aal_action_update_database_tables';
    protected $_bLoggedIn = true;
    protected $_bGuest    = false;

    protected function _construct() {
        add_action( 'admin_enqueue_scripts', array( $this, 'replyToEnqueueScript' ) );
    }

    /**
     * @param  array $aPost Passed POST data.
     * @return array
     * @since  4.6.18
     */
    protected function _getPostSanitized( array $aPost ) {
        return array();
    }

    /**
     * @return boolean
     * @throws Exception Throws a string value of an error message.
     * @param  array     $aPost Unused at the moment.
     */
    protected function _getResponse( array $aPost ) {

        /**
         * The option updater uses this hook.
         */
        do_action( 'aal_action_update_plugin_database_tables' );

        $_aErrors = array();
        foreach( AmazonAutoLinks_Registry::$aDatabaseTables as $_sTableName => $_aTableInfo ) {
            $_sCurrentVersion = get_option( "{$_aTableInfo[ 'name' ]}_version", 0 );
            $_sToVersion      = $_aTableInfo[ 'version' ];
            $_boResult        = $this->___getDatabaseTableUpdated( $_aTableInfo[ 'name' ], $_sCurrentVersion, $_sToVersion );
            if ( is_wp_error( $_boResult ) ) {
                $_aErrors[]   = count( $_aErrors ) + 1 . ': '
                    . $_boResult->get_error_code() . ' ' . $_boResult->get_error_message();
            }
        }
        if ( ! empty( $_aErrors ) ) {
            throw new Exception( implode( '', $_aErrors ) );
        }

        return __( 'The database tables have been successfully updated.', 'amazon-auto-links' );

    }

        /**
         * @param  string $sTableName
         * @param  string $sCurrentVersion
         * @param  string $sVersionTo
         *
         * @return boolean|AmazonAutoLinks_Error
         */
        private function ___getDatabaseTableUpdated( $sTableName, $sCurrentVersion, $sVersionTo ) {

            if ( version_compare( $sCurrentVersion, $sVersionTo, '>=') ) {
                return false;
            }

            /**
             * @var $_oTable AmazonAutoLinks_DatabaseTable_Base
             */
            $_sClassName    = "AmazonAutoLinks_DatabaseTable_{$sTableName}";
            $_oTable        = new $_sClassName;
            $_aResult       = $_oTable->install( true );
            if ( empty( $_aResult ) ) {
                return new AmazonAutoLinks_Error(
                    'DATABASE_TABLE_UPDATE_FAILURE',
                    sprintf(
                        $sCurrentVersion
                            ? __( 'Failed to update the database table, %1$s.', 'amazon-auto-links' )
                            : __( 'Failed to install the database table, %1$s.', 'amazon-auto-links' )
                        ,
                        $sCurrentVersion
                            ? $sTableName . ' ' . $sCurrentVersion
                            : $sTableName
                    ),
                    array(),
                    true
                );
            }

            /**
             * Allows table specific routines to run.
             */
            do_action( 'aal_action_updated_plugin_database_table_' . $sTableName, $sCurrentVersion, $sVersionTo, $_aResult ); // 4.3.0
            return true; // succeeded

        }


    /**
     * @since       3.8.0
     * @since       4.3.0   Moved from AmazonAutoLinks_DatabaseUpdater_AdminNotice
     * @callback    action      admin_enqueue_scripts
     * @return      void
     */
    public function replyToEnqueueScript() {

        if ( ! $this->isPluginAdminPage() ) {
            return;
        }

        $_sScriptHandle = 'aal_database_update';
        $_aScriptData   = array(
            'ajaxURL'       => admin_url( 'admin-ajax.php' ),
            'actionHookSuffix' => $this->_sActionHookSuffix,
            'nonce'         =>  wp_create_nonce( $this->_sNonceKey ),
            'spinnerURL'    => admin_url( 'images/loading.gif' ),
            'pluginName'    => AmazonAutoLinks_Registry::NAME,
        );
        wp_enqueue_script( 'jquery' );
        wp_enqueue_script(
            $_sScriptHandle,    // handle
            $this->getSRCFromPath( $this->isDebugMode()
                ? AmazonAutoLinks_DatabaseUpdater_Loader::$sComponentDirPath . '/asset/plugin-database-updater.js'
                : AmazonAutoLinks_DatabaseUpdater_Loader::$sComponentDirPath . '/asset/plugin-database-updater.min.js'
            ),
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