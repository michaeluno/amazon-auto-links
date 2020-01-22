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
 * Updates the plugin database table.
 * @since   3.8.0
 *
 */
class AmazonAutoLinks_DatabaseUpdater_Event_Ajax_Updater extends AmazonAutoLinks_AjaxEvent_Base {

    protected $_sActionHookName = 'wp_ajax_aal_action_update_database_table';

    protected $_sNonceKey = 'aal_nonce_ajax_database_updater';

    /**
     * @param  array $aPost
     *
     * @return boolean
     * @throws Exception        Throws a string value of an error message.
     */
    protected function _getResponse( array $aPost ) {

        $_sTableName = $this->getElement( $aPost, 'tableName' );
        $_sVersionTo = $this->getElement( $aPost, 'versionTo' );
        $_bResult    = $this->___getDatabaseTableUpdated( $_sTableName, $_sVersionTo );
        if ( ! $_bResult ) {
            throw new Exception( __( 'Could not update the database table.', 'amazon-auto-links' ) );
        }
        return __( 'The database table has been successfully updated.', 'amazon-auto-links' );

    }
        /**
         * @param   string $sTableName
         * @param   string $sVersionTo
         * @return  boolean
         */
        private function ___getDatabaseTableUpdated( $sTableName, $sVersionTo ) {

            $_sCurrentVersion = get_option( "{$sTableName}_version", 0 );
            /**
             * This action is used to update the plugin options as well.
             */
            do_action( 'aal_action_update_plugin_database_table', $sTableName, $_sCurrentVersion, $sVersionTo );
            if ( version_compare( $_sCurrentVersion, $sVersionTo, '>=') ) {
                throw new Exception( __( 'The database table is already up to date.', 'amazon-auto-links' ) );
            }

            $_sClassName    = "AmazonAutoLinks_DatabaseTable_{$sTableName}";
            $_oTable        = new $_sClassName;
            $_aResult       = $_oTable->install( true );
            do_action( 'aal_action_updated_plugin_database_table', $sTableName, $_sCurrentVersion, $sVersionTo, $_aResult ); // 3.10.0
            return empty( $_aResult ) ? false : true;
        }

}
