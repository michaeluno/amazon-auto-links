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
 * Tests for the plugin database.
 *  
 * @package     Amazon Auto Links
 * @since       4.3.0
*/
class AmazonAutoLinks_UnitTest_ProductDatabaseRows extends AmazonAutoLinks_UnitTest_Base {

    /**
     * @var AmazonAutoLinks_DatabaseTable_aal_products
     */
    public $oTable;

    public function __construct() {
        $this->oTable = new AmazonAutoLinks_DatabaseTable_aal_products;
    }

    /**
     * @return mixed|string
     * @purpose Setting rows.
     * @tags rows
     */
    public function test_setRows() {

        $_aRows  = array(
            array(
                'product_id'            => 'B00VLN9IC6|IT|EUR|it_IT',
                'asin_locale'           => 'B00VLN9IC6_IT',
                'asin'                  => 'B00VLN9IC6',
                'locale'                => 'IT',
                'language'              => 'it_IT',
                'preferred_currency'    => 'EUR',
                'title'                 => 'TESTING ROW 1'
            ),
            array(
                'product_id'            => 'XXXXXXXXXX|IT|EUR|it_IT',
                'asin_locale'           => 'XXXXXXXXXX_IT',
                'asin'                  => 'XXXXXXXXXX',
                'locale'                => 'IT',
                'language'              => 'it_IT',
                'preferred_currency'    => 'EUR',
                'title'                 => 'TESTING ROW 2'
            ),
        );
        return $this->oTable->setRows( $_aRows );

    }

    /**
     * @tags rows
     */
    public function test_retrievingProductDatabaseRows() {
        $_aASINLocaleCurLangs = array(
            'B00VLN9IC6|IT|EUR|it_IT' => array(
                'asin'      => 'B00VLN9IC6',
                'locale'    => 'IT',
                'currency'  => 'EUR',
                'language'  => 'it_IT',
            ),
        );
        $_oProducts = new AmazonAutoLinks_ProductDatabase_Rows( $_aASINLocaleCurLangs, 'EUR', 'it_IT' );
        return $_oProducts->get();
    }

    /**
     * @tags rows
     * @throws Exception
     */
    public function test_returnCache() {

        $this->oTable->delete( array( 'product_id' => 'B00VLN9IC6|IT|EUR|it_IT' ) );
        $_aRows = $this->oTable->getRowsByProductID( array( 'B00VLN9IC6|IT|EUR|it_IT' ) );
        if ( ! empty( $_aRows ) ) {
            throw new Exception( 'Rows were not deleted' );
        }

        $_aASINLocaleCurLangs = array(
            'B00VLN9IC6|IT|EUR|it_IT' => array(
                'asin'      => 'B00VLN9IC6',
                'locale'    => 'IT',
                'currency'  => 'EUR',
                'language'  => 'it_IT',
            ),
        );
        $_oProducts = new AmazonAutoLinks_ProductDatabase_Rows( $_aASINLocaleCurLangs, 'EUR', 'it_IT' );
        return $_oProducts->get(); // should return a cache
    }


    
}