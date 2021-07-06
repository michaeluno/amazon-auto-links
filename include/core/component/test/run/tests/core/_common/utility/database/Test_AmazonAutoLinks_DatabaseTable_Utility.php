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
 * Tests the `AmazonAutoLinks_DatabaseTable_Utility` class.
 */
class Test_AmazonAutoLinks_DatabaseTable_Utility extends AmazonAutoLinks_UnitTest_Base {

    /**
     * @tags exist
     */
    public function test_doesRowExist() {

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
        );
        $_mResult = $_oTable->setRows( $_aRows );
        $this->_assertNotEmpty( $_mResult );

        $_bExist = $_oTable->doesRowExist( array( 'product_id' => 'B07GS6ZB7G|IT|EUR|it_IT' ) );
        $this->_assertTrue( $_bExist, 'Check if a row just created exists' );
        $_oTable->delete( array( 'product_id' => 'B07GS6ZB7G|IT|EUR|it_IT' ) );
        $_bExist = $_oTable->doesRowExist( array( 'product_id' => 'B07GS6ZB7G|IT|EUR|it_IT' ) );
        $this->_assertFalse( $_bExist, 'Check if a row just deleted does not exist.' );

    }

}