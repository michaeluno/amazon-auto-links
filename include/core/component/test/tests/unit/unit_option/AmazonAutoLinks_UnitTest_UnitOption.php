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
 * Tests for unit options.
 *  
 * @package     Amazon Auto Links
 * @since       4.3.0
*/
class AmazonAutoLinks_UnitTest_UnitOption extends AmazonAutoLinks_UnitTest_Base {

    /**
     * @return bool
     */
    public function test_Language() {
        $_oUnitOption = new AmazonAutoLinks_UnitOption_category( null );
        if ( ! $_oUnitOption->get( 'language' ) ) {
            throw new Exception( 'The `language` default unit option is not set.' );
        }
        return true;
    }
    /**
     * @return bool
     */
    public function test_PreferredCurrency() {
        $_oUnitOption = new AmazonAutoLinks_UnitOption_category( null );
        if ( ! $_oUnitOption->get( 'preferred_currency' ) ) {
            throw new Exception( 'The `preferred_currency` default unit option is not set.' );
        }
        return true;
    }
}