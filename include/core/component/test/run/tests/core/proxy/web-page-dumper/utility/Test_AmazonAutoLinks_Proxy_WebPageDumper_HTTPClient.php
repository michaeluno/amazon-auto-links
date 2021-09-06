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
 * Tests the `AmazonAutoLinks_Proxy_WebPageDumper_HTTPClient` class.
 *
 * @see     AmazonAutoLinks_Proxy_WebPageDumper_HTTPClient
 * @since   4.7.5
*/
class Test_AmazonAutoLinks_Proxy_WebPageDumper_HTTPClient extends AmazonAutoLinks_UnitTest_Base {

    /**
     * @tags web page dumper, proxy, rss
     */
    public function test_get() {
        $_sWebDumperURL = 'https://web-page-dumper.herokuapp.com/';
        $_sURL          = 'https://feeds.feedburner.com/AmazonAutoLinksTemplates';
        $_oHTTP         = new AmazonAutoLinks_Proxy_WebPageDumper_HTTPClient( $_sWebDumperURL, $_sURL, 0 );
        $_sXML          = $_oHTTP->getBody();
        $this->_assertNotEmpty( $_sXML );
        $_oRSS          = new AmazonAutoLinks_RSSClient;
        $_aItems        = $_oRSS->getFromXML( $_sXML );
        $_aFirst        = reset( $_aItems );
        $this->_assertNotEmpty( $_aFirst );
    }

}