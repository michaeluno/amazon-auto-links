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
class AmazonAutoLinks_UnitTest_aal_products extends AmazonAutoLinks_UnitTest_Base {

    /**
     * @return bool
     */
    public function test_version() {
        return get_option( 'aal_products_version' );
    }

    public function test_exist_aal_products() {
        $_oTable     = new AmazonAutoLinks_DatabaseTable_aal_products;
        return $_oTable->tableExists();
    }

    public function test_uniqueColumn() {
        $_oTable        = new AmazonAutoLinks_DatabaseTable_aal_products;
        $_sTableName    = $_oTable->getTableName();
        $_sUniqueColumn = 'product_id';
        return $_oTable->getVariable( "SHOW INDEXES FROM {$_sTableName} WHERE Column_name='{$_sUniqueColumn}' AND NOT Non_unique" );
    }

}