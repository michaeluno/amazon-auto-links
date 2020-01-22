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
 * Updates the plugin options.
 *
 * @since       3.10.0
 */
class AmazonAutoLinks_DatabaseUpdater_aal_products_121 extends AmazonAutoLinks_PluginUtility {

    public function __construct() {
        add_action( 'aal_action_updated_plugin_database_table', array( $this, 'replyToUpdateDatabase' ), 10, 4 );
    }

    /**
     * Called when the user clicks on the database table update link in the notification message
     * and the action hook is fired after database table update is done.
     *
     * Removes an index for the `asin_locale` column which has been present as it has been a unique value constraint.
     * As of v3.9.0, that constraint has been removed. So the index must be deleted as well.
     *
     * @param string $sTableName
     * @param string $sVersionFrom
     * @param string $sVersionTo
     * @param array $aResult
     */
    public function replyToUpdateDatabase( $sTableName, $sVersionFrom, $sVersionTo, $aResult ) {

        $_oProductTable = new AmazonAutoLinks_DatabaseTable_aal_products;

        // check if a unique index for the `asin_locale` column exists or not
        $_sQuery = "SHOW INDEXES "
            . "FROM {$_oProductTable->aArguments[ 'table_name' ]} "
            . "WHERE Column_name='asin_locale' "
            . "AND Non_unique=0";
        $_aResult = $_oProductTable->getRow( $_sQuery );

        // If not found, null will be returned; otherwise, an array of the row will be returned.
        if ( empty( $_aResult ) ) {
            return;
        }
        $_sKeyName = $this->getElement( $_aResult, array( 'Key_name' ) );
        if ( ! $_sKeyName ) {
            return;
        }

        // Now, there is a unique index and it should be dropped.
        $_sQuery = "ALTER TABLE {$_oProductTable->aArguments[ 'table_name' ]} "
            . "DROP INDEX `{$_sKeyName}`";
        $_oProductTable->getVariable( $_sQuery );

    }

}