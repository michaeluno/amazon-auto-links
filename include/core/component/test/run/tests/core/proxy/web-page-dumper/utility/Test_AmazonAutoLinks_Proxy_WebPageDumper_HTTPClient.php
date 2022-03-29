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
 * Tests the `AmazonAutoLinks_Proxy_WebPageDumper_HTTPClient` class.
 *
 * @see     AmazonAutoLinks_Proxy_WebPageDumper_HTTPClient
 * @since   4.7.5
*/
class Test_AmazonAutoLinks_Proxy_WebPageDumper_HTTPClient extends AmazonAutoLinks_UnitTest_Base {

    /**
     * @purpose Tests whether RSS can be parsed properly.
     * @tags web page dumper, proxy, rss, RSS
     */
    public function test_RSS() {
        $_sWebDumperURL = 'https://web-page-dumper.herokuapp.com/';
        $_sURL          = 'https://feeds.feedburner.com/AmazonAutoLinksTemplates';
        $_aArguments    = array(
            'timeout' => 30,
        );
        $_oHTTP         = new AmazonAutoLinks_Proxy_WebPageDumper_HTTPClient( $_sWebDumperURL, $_sURL, 0, $_aArguments );
        $_sXML          = $_oHTTP->getBody();
        $this->_assertNotEmpty( $_sXML );
        $_oRSS          = new AmazonAutoLinks_RSSClient;
        $_aItems        = $_oRSS->getFromXML( $_sXML );
        $_aFirst        = reset( $_aItems );
        $this->_assertNotEmpty( $_aFirst );
    }

    /**
     * @purpose Tests whether JSONP can be parsed properly/
     * @tags web page dumper, proxy, jsonp, JSONP
     */
    public function test_JSONP() {
        $_sWebDumperURL = 'https://web-page-dumper.herokuapp.com/';
        $_sURL          = 'https://ws-na.amazon-adsystem.com/widgets/q?multipageCount=20&Operation=GetResults&Keywords=0593298942|B07284QZ59&SearchIndex=All&multipageStart=0&InstanceId=0&TemplateId=MobileSearchResults&ServiceVersion=20070822&MarketPlace=US';
        $_aArguments    = array(
            'timeout' => 30,
        );
        $_oHTTP         = new AmazonAutoLinks_Proxy_WebPageDumper_HTTPClient( $_sWebDumperURL, $_sURL, 0, $_aArguments );
        $_sJSONP        = $_oHTTP->getBody();
        $this->_assertNotEmpty( $_sJSONP );
        $_oAdWidgetAPI  = new AmazonAutoLinks_AdWidgetAPI_Search( 'US' );
        $_aJSON         = $_oAdWidgetAPI->getJSONFromJSONP( $_sJSONP );
        $this->_assertNotEmpty( $_aJSON );
        $this->_assertTrue( is_array( $_aJSON ) );
        $this->_assertNotEmpty( $this->getElement( $_aJSON, array( 'results', 0, 'ASIN' ) ) );
    }

}