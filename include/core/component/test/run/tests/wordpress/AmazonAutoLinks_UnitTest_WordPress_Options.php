<?php
/**
 * Auto Amazon Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 */

/**
 * WordPress-related option tests.
 *  
 * @package     Auto Amazon Links
 * @since       4.4.0
*/
class AmazonAutoLinks_UnitTest_WordPress_Options extends AmazonAutoLinks_UnitTest_Base {

    /**
     * @tags timezone, options
     */
    public function testTimeZone() {

        $_sTimeZone = get_option( 'timezone_string' );
        $this->_outputDetails( 'timezone_string', $_sTimeZone );
        $this->_assertTrue( is_string( $_sTimeZone ) );

        $_sOffset = get_option( 'gmt_offset' );
        $this->_outputDetails( 'gmt_offset', $_sOffset );
        $this->_assertTrue( is_numeric( $_sOffset ) ); // can be double/string/false

    }

}