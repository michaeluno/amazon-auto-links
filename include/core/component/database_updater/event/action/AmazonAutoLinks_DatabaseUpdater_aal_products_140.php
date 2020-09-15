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
 * Updates the aal_products table.
 *
 * @since       4.3.0
 */
class AmazonAutoLinks_DatabaseUpdater_aal_products_140 extends AmazonAutoLinks_DatabaseUpdater_Base_aal_products {

    /**
     * @return boolean
     * @param string $sVersionFrom
     * @param string $sVersionTo
     * @param array $aResult
     * @since 4.3.0
     */
    protected function _shouldProceed( $sVersionFrom, $sVersionTo, $aResult ) {
        if ( version_compare( $sVersionFrom, '1.4.0', '>=' ) ) {
            return false;
        }
        return true;
    }

    /**
     * @since 4.3.0
     * @return void
     */
    protected function _doUpdates() {
        $_oProductTable = new AmazonAutoLinks_DatabaseTable_aal_products;
        $_sTableName    = $_oProductTable->getTableName();
        $_aQueries      = array(
            "UPDATE `{$_sTableName}`"
            . " SET product_id = CONCAT( SUBSTRING( asin_locale, 1, 10 ), '|', SUBSTRING( asin_locale, 12, 2 ), '|', preferred_currency, '|', language )"
            . " WHERE product_id IS NULL OR product_id = ''"
            . " ;",
            "UPDATE `{$_sTableName}`"
            . " SET asin = CONCAT( SUBSTRING( asin_locale, 1, 10 )"
            . " WHERE asin IS NULL OR asin = ''"
            . " ;",
            "ALTER TABLE `{$_sTableName}` MODIFY product_id varchar(128) UNIQUE AFTER object_id",
        );
        foreach( $_aQueries as $_sQuery ) {
            $_oProductTable->getVariable( $_sQuery );
        }
    }

}