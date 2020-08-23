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
class AmazonAutoLinks_DatabaseUpdater_aal_products_140 extends AmazonAutoLinks_DatabaseUpdater_aal_products_121 {

    /**
     * Fills the `product_id` column with generated text value with ASIN|locale|currency|language
     *
     * @param string $sVersionFrom
     * @param string $sVersionTo
     * @param array $aResult
     * @since   4.3.0
     */
    public function replyToUpdateDatabase( $sVersionFrom, $sVersionTo, $aResult ) {

        $_oProductTable = new AmazonAutoLinks_DatabaseTable_aal_products;
        $_sTableName    = $_oProductTable->getTableName();
        $_sQuery = "UPDATE {$_sTableName}"
            . " SET product_id = CONCAT( SUBSTRING( asin_locale, 1, 10 ), '|', SUBSTRING( asin_locale, 12, 2 ), '|', preferred_currency, '|', language )"
            . " WHERE product_id IS NULL OR product_id = ''"
            . " ;";
        $_oProductTable->getVariable( $_sQuery );

    }

}