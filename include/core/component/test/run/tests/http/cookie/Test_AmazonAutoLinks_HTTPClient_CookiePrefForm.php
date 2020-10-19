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
 * Tests cookies of AmazonAutoLinks_HTTPClient.
 *
 * @package Amazon Auto Links
 * @since   4.3.4
 * @see     AmazonAutoLinks_HTTPClient
 * @tags    form
*/
class Test_AmazonAutoLinks_HTTPClient_CookiePrefForm extends AmazonAutoLinks_UnitTest_HTTPRequest_Base {

    /**
     * @purpose The US locale does not have that address. In that case, does it provide cookies?
     * @tags US
     */
    public function test_CookiePrefForm_US() {

        $_oLocale    = new AmazonAutoLinks_Locale( 'US' );
        $_sFormURL   = $_oLocale->getMarketPlaceURL( '/cookieprefs?ref_=portal_banner_all' );
        $_oHTTP      = new AmazonAutoLinks_HTTPClient(
            $_sFormURL,
            86400,
            array(
                'headers'       => array( 'Referer' => $_oLocale->getMarketPlaceURL() ),
            ),
            'test'
        );
        $this->_assertPrefix( '4', $_oHTTP->getStatusCode(), 'Should be 404 not found.', $_oHTTP->getStatusMessage() );
        $this->_assertEmpty( $_oHTTP->getCookiesParsable() );

    }

    /**
     * @tags FR
     */
    public function test_CookiePrefForm_FR() {

        $_oLocale    = new AmazonAutoLinks_Locale( 'FR' );
        $_sFormURL   = $_oLocale->getMarketPlaceURL( '/cookieprefs?ref_=portal_banner_all' );
        $_oHTTP      = new AmazonAutoLinks_HTTPClient(
            $_sFormURL,
            86400,
            array(
                'headers' => array( 'Referer' => $_oLocale->getMarketPlaceURL() ),
            ),
            'test'
        );
        $this->_assertPrefix( '2', $_oHTTP->getStatusCode(), 'Checking response HTTP status.', $_oHTTP->getStatusMessage() );
        $this->_assertSubString( 'anti-csrftoken-a2z', $_oHTTP->getBody(), 'Checking if the form exists', $_oHTTP->getBody() );
        $_oDOM       = new AmazonAutoLinks_DOM;
        $_oDoc       = $_oDOM->loadDOMFromHTML( $_oHTTP->getBody() );
        $_oXPath     = new DOMXPath( $_oDoc );
        $_noFormNode = $_oXPath->query( ".//form[@action='']" );
        $this->_assertTrue( 1 === $_noFormNode->length );

    }

}