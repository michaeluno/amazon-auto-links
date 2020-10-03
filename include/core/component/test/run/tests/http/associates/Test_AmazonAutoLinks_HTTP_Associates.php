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
class Test_AmazonAutoLinks_HTTPClient_Associates extends AmazonAutoLinks_UnitTest_HTTPRequest_Base {

    /**
     * @tags head, associates
     */
    public function test_CookiesOfAmazonAssociates() {
        $_aURLs = AmazonAutoLinks_Property::$aAssociatesURLs;
        foreach( $_aURLs as $_sLocale => $_sURL ) {
            $_aArguments = array( 'method' => 'HEAD' );
            $_oHTTP      = new AmazonAutoLinks_HTTPClient( $_sURL, 86400, $_aArguments );
            $_aoResponse = $_oHTTP->getRawResponse();
            $_aCookies   = $this->getCookiesFromResponseToParse( $_aoResponse );
            $this->_output( 'URL: ' . $_sURL );
            $this->_outputDetails( 'Cookies: ' . $_sLocale, $_aCookies );
            $this->_assertNotEmpty( $_aCookies, 'They should return cookies.', $this->aoLastResponse );
            $this->_assertPrefix( '2', $this->getElement( $_aoResponse, array( 'response', 'code' ) ) );
        }
    }

}