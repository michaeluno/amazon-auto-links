<?php
/**
 * Auto Amazon Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2022 Michael Uno
 */

/**
 * Tests the `AmazonAutoLinks_DatabaseTable_aal_products` class.
 */
class Test_AmazonAutoLinks_DatabaseTable_aal_products extends AmazonAutoLinks_UnitTest_Base {

    /**
     * @tags update, setRows
     */
    public function test_setRows() {
        $_oTable = new AmazonAutoLinks_DatabaseTable_aal_products;
        $_aRows  = array(
            array(
                'product_id'            => 'B07GS6ZB7G|IT|EUR|it_IT',
                'asin_locale'           => 'B07GS6ZB7G_IT',
                'asin'                  => 'B07GS6ZB7G',
                'locale'                => 'IT',
                'language'              => 'it_IT',
                'preferred_currency'    => 'EUR',
                'title'                 => 'Mouse 2',
            ),
            array(
                'product_id'            => 'B07GS6ZB7H|IT|EUR|it_IT',
                'asin_locale'           => 'B07GS6ZB7H_IT',
                'asin'                  => 'B07GS6ZB7H',
                'locale'                => 'IT',
                'language'              => 'it_IT',
                'preferred_currency'    => 'EUR',
                'title'                 => 'Dummy',
            ),
        );
        $_mResult = $_oTable->setRows( $_aRows );
        $this->_assertNotEmpty( $_mResult );

        // Test override
        // $_aRows = array(
        //     array(
        //         'product_id'            => 'B07GS6ZB7H|IT|EUR|it_IT',
        //         'title'                 => '',
        //     ),
        // );
        // $_mResult = $_oTable->setRows( $_aRows );
        // $this->_assertNotEmpty( $_mResult );

        $_aASINLocales = array(
            'B07GS6ZB7G_IT', 'B07GS6ZB7H_IT'
        );
        $_aRows = $_oTable->getRowsByASINLocaleCurLang( $_aASINLocales, 'EUR', 'it_IT' );
        $this->_assertNotEmpty( $_aRows );

        // Clean up
        $_oTable->delete( array( 'product_id' => 'B07GS6ZB7G|IT|EUR|it_IT' ) );
        $_oTable->delete( array( 'product_id' => 'B07GS6ZB7H|IT|EUR|it_IT' ) );
        $_bExist = $_oTable->doesRowExist( array( 'product_id' => 'B07GS6ZB7H|IT|EUR|it_IT' ) );
        $this->_assertFalse( $_bExist, 'Check if a row just deleted does not exist.' );
    }


}