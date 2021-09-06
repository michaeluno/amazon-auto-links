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
 * Tests the `AmazonAutoLinks_RSSClient` class.
 *
 * @see     AmazonAutoLinks_RSSClient
 * @since   4.7.5
*/
class Test_AmazonAutoLinks_RSSClient extends AmazonAutoLinks_UnitTest_Base {

    /**
     * @tags RSS
     */
    public function test_get() {
        $_oRSS = new AmazonAutoLinks_RSSClient( 'https://feeds.feedburner.com/AmazonAutoLinksTemplates', 0 );
        $_aItems = $_oRSS->get();
        $this->_assertNotEmpty( $this->getElementAsArray( $_aItems, 0 ) );
    }

    /**
     * @tags RSS, web page dumper
     */
    public function test_get_02() {
        $_oRSS = new AmazonAutoLinks_RSSClient( 'https://web-page-dumper.herokuapp.com/www/?url=https%3A%2F%2Ffeeds.feedburner.com%2FAmazonAutoLinksTemplates&output=text', 0 );
        $_aItems = $_oRSS->get();
        $this->_assertNotEmpty( $this->getElementAsArray( $_aItems, 0 ) );
    }

}