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
 * Tests for plugin database tables.
 *  
 * @package     Amazon Auto Links
 * @since       4.3.0
*/
class AmazonAutoLinks_UnitTest_Database_aal_products extends AmazonAutoLinks_UnitTest_Base {

    /**
     * @return string|false
     */
    public function test_version() {
        return get_option( 'aal_products_version', false );
    }

    public function test_getVersion() {
        $_oTable     = new AmazonAutoLinks_DatabaseTable_aal_products;
        return $_oTable->getVersion();
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

    /**
     * @return mixed|void
     * @tags set
     * @throws Exception
     */
    public function test_setRows() {

        $this->test_deleteRows();   // might previous records are remained
        $_oTable = new AmazonAutoLinks_DatabaseTable_aal_products;
        $_aRows  = array(
            array(
                'product_id'            => 'B07GS6ZB7T|IT|EUR|it_IT',
                'asin_locale'           => 'B07GS6ZB7T_IT',
                'asin'                  => 'B07GS6ZB7T',
                'locale'                => 'IT',
                'language'              => 'it_IT',
                'preferred_currency'    => 'EUR',
                'title'                 => 'Mouse 2',
            ),
            array(
                'product_id'            => 'B085K5S6S5|IT|EUR|it_IT',
                'asin_locale'           => 'B085K5S6S5_IT',
                'asin'                  => 'B085K5S6S5',
                'locale'                => 'IT',
                'language'              => 'it_IT',
                'preferred_currency'    => 'EUR',
                'title'                 => 'Ghost of Tsushima 2',
            ),
        );
        $_mResult = $_oTable->setRows( $_aRows );
        if ( ! $_mResult ) {
            throw new Exception( AmazonAutoLinks_Debug::getDetails( $_mResult ) );
        }
        return $_mResult;

    }

    /**
     * @return bool
     * @tags method, public, get
     */
    public function test_getRowsByProductID() {
        $_oTable = new AmazonAutoLinks_DatabaseTable_aal_products;
        $_aRows  = $_oTable->getRowsByProductID(
            array( 'B085K5S6S5|IT|EUR|it_IT', 'B07GS6ZB7T|IT|EUR|it_IT' )
        );
        return 2 === count( $_aRows );
    }

    /**
     * @return bool
     * @tags method, public, get
     * @throws Exception
     */
    public function test_getRowsByASINLocaleCurLang() {
        $_oTable = new AmazonAutoLinks_DatabaseTable_aal_products;
        $_aRows  = $_oTable->getRowsByASINLocaleCurLang(
            array( 'B085K5S6S5_IT', 'B07GS6ZB7T_IT' ),
            'EUR',
            'it_IT'
        );
        $_iRows = count( $_aRows );
        if ( ! $_iRows ) {
            throw new Exception( $this->_getDetails( $_aRows ) );
        }
        return true;
    }

    /**
     * @return bool
     * @tags delete
     */
    public function test_deleteRows() {
        $_oTable = new AmazonAutoLinks_DatabaseTable_aal_products;
        $_oTable->delete( array( 'product_id' => 'B085K5S6S5|IT|EUR|it_IT' ) );
        $_oTable->delete( array( 'product_id' => 'B07GS6ZB7T|IT|EUR|it_IT' ) );
        $_aRows  = $_oTable->getRowsByProductID(
            array( 'B085K5S6S5|IT|EUR|it_IT', 'B07GS6ZB7T|IT|EUR|it_IT' )
        );
        return 0 === count( $_aRows );
    }

}