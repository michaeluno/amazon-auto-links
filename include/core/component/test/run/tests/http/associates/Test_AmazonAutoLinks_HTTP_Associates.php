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
 * Tests Amazon Associates sites.
 *
 * @package Amazon Auto Links
 * @since   4.3.3
 * @see     AmazonAutoLinks_HTTPClient
 * @tags    http
*/
class Test_AmazonAutoLinks_HTTPClient_Associates extends Test_AmazonAutoLinks_HTTPClient_BestSellers {

    /**
     * @tags IT
     */
    public function test_sessionMatch_IT() {
        $_sLocale = 'IT';
        $_sURL    = AmazonAutoLinks_Property::getAssociatesURLByLocale( $_sLocale );
        $this->_testSessionMatch( $_sURL, $_sLocale );
    }

    /**
     * @tags UK
     */
    public function test_sessionMatch_UK() {
        $_sLocale = 'UK';
        $_sURL    = AmazonAutoLinks_Property::getAssociatesURLByLocale( $_sLocale );
        $this->_testSessionMatch( $_sURL, $_sLocale );
    }

}