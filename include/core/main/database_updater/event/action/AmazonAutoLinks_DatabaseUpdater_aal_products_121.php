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
 * Updates the plugin options.
 *
 * @since       3.10.0
 */
class AmazonAutoLinks_DatabaseUpdater_aal_products_121 extends AmazonAutoLinks_DatabaseUpdater_Base_aal_products {

    /**
     * @return boolean
     * @param string $sVersionFrom
     * @param string $sVersionTo
     * @param array $aResult
     * @since 4.3.0
     */
    protected function _shouldProceed( $sVersionFrom, $sVersionTo, $aResult ) {
        if ( version_compare( $sVersionFrom, '1.2.1', '>=' ) ) {
            return false;
        }
        return true;
    }

    /**
     * @since 4.3.0
     * @return void
     */
    protected function _doUpdate() {

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