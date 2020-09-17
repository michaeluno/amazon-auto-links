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
 * Tests for plugin database tables.
 *  
 * @package     Amazon Auto Links
 * @since       4.3.0
*/
class AmazonAutoLinks_UnitTest_Database_aal_products extends AmazonAutoLinks_UnitTest_Base {

    /**
     * @return bool
     */
    public function test_version() {
        return get_option( 'aal_products_version' );
    }

    /**
     * @return bool
     * @purpose Checking whether the table exists.
     */
    public function test_exist_aal_products() {
        $_oTable     = new AmazonAutoLinks_DatabaseTable_aal_products;
        return $_oTable->tableExists();
    }

    /**
     * @return mixed|string
     * @purpose Checking whether the unique column exists.
     */
    public function test_uniqueColumn() {
        $_oTable        = new AmazonAutoLinks_DatabaseTable_aal_products;
        $_sTableName    = $_oTable->getTableName();
        $_sUniqueColumn = 'product_id';
        return $_oTable->getVariable( "SHOW INDEXES FROM {$_sTableName} WHERE Column_name='{$_sUniqueColumn}' AND NOT Non_unique" );
    }

    /**
     * @purpose Checking whether the `product_id` column contains null values, which should not.
     * @throws Exception
     * @tags    column
     */
    public function test_productIDHaveNull() {
        $_oTable        = new AmazonAutoLinks_DatabaseTable_aal_products;
        $_sTableName    = $_oTable->getTableName();
        $_sColumn       = 'product_id';
        $_iCountRow     = $_oTable->getVariable(
            "SELECT COUNT(*) FROM `{$_sTableName}` WHERE `{$_sColumn}` IS NULL OR `{$_sColumn}` = ' ';"
        );
        if ( $_iCountRow ) {
            throw new Exception( 'The `product_id` column contains a null value.' );
        }
        return true;
    }

}