<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 */

/**
 * Tests the AmazonAutoLinks_WPUtility_HTTP class.
 *  
 * @package     Amazon Auto Links
 * @since       4.3.5
 * @see         AmazonAutoLinks_WPUtility_HTTP
 * @tags        HTTP
*/
class Test_AmazonAutoLinks_WPUtility_HTTP extends AmazonAutoLinks_UnitTest_Base {

    /**
     * @tags header
     */
    public function test_getHeaderFromResponse() {
        $_oLocale    = new AmazonAutoLinks_Locale( 'FR' );
        $_sURL       = $_oLocale->getAssociatesURL();
        $_oHTTP      = new AmazonAutoLinks_HTTPClient( $_sURL, 86400, array( 'method' => 'HEAD' ), 'test' );
        $_aoResponse = $_oHTTP->getRawResponse();
        $this->_assertNotEmpty( $this->getHeaderFromResponse( $_aoResponse ) );
    }
    /**
     * @tags cookie
     */
    public function test_getCookiesToParseFromResponse() {
        $_oLocale    = new AmazonAutoLinks_Locale( 'DE' );
        $_sURL       = $_oLocale->getAssociatesURL();
        $_oHTTP      = new AmazonAutoLinks_HTTPClient( $_sURL, 86400, array(), 'test' );
        $_aoResponse = $_oHTTP->getRawResponse();
        $this->_assertNotEmpty( $this->getCookiesToParseFromResponse( $_aoResponse ) );
    }

    /**
     * @tags cookie, ES
     */
    public function test_hasSameCookie() {
        $_oLocale    = new AmazonAutoLinks_Locale( 'ES' );
        $_sURL       = $_oLocale->getAssociatesURL();
        $_oHTTP      = new AmazonAutoLinks_HTTPClient( $_sURL, 86400, array( 'method' => 'HEAD' ), 'test' );
        $_aoResponse = $_oHTTP->getRawResponse();
        $_aCookies   = $this->getRequestCookiesFromResponse( $_aoResponse, $_sURL );
        $this->_assertNotEmpty( $this->getCookiesToParse( $_aCookies ) );
        $_oCookie    = reset( $_aCookies );
        $this->_assertTrue( $this->hasSameCookie( $_aCookies, 0, $_oCookie, $_sURL ) );

        $_oCookie2       = new WP_Http_Cookie( array( 'name' => $_oCookie->name . '_TEST', 'value' => $_oCookie->value ), $_sURL );
        $this->_assertNotEmpty( $this->getCookiesToParse( array( $_oCookie2 ) ) );
        $this->_assertFalse( $this->hasSameCookie( $_aCookies, 0, $_oCookie2, $_sURL ) );

    }

    /**
     * @tags cookie, domain
     */
    public function test_hasSameCookie2() {

        $_sURL       = 'https://afiliados.amazon.es';
        $_sDomain    = 'afiliados.amazon.es';
        $_aCookies   = array(
            new WP_Http_Cookie( array( 'name' => 'foo', 'value' => 'bar', 'path' => '/', 'domain' => $_sDomain ) )
        );
        $this->_assertNotEmpty( $this->getCookiesToParse( $_aCookies ) );
        $_oCookie3   = new WP_Http_Cookie( array( 'name' => 'foo', 'value' => 'bar', 'path' => '/', 'domain' => null ) );
        $this->_assertNotEmpty( $this->getCookiesToParse( array( $_oCookie3 ) ) );
        $this->_assertTrue( $this->hasSameCookie( $_aCookies, 0, $_oCookie3, $_sURL ) );

    }

    /**
     * @tags cookie, header
     */
    public function test_getRequestCookiesFromResponse() {
        $_oLocale    = new AmazonAutoLinks_Locale( 'IT' );
        $_sURL       = $_oLocale->getBestSellersURL( 'tag=' . str_shuffle( 'abcdefgh' ). '=' . sprintf( '%02d', mt_rand( 20, 40 ) ) );
        $_oHTTP      = new AmazonAutoLinks_HTTPClient( $_sURL, 0, array(), 'test' );
        $_aoResponse = $_oHTTP->getRawResponse();
        $_aCookies   = AmazonAutoLinks_WPUtility::getRequestCookiesFromResponse( $_aoResponse );
        $this->_assertNotEmpty( $this->getHeaderFromResponse( $_aoResponse ) );
        $this->_assertNotEmpty( $this->getCookiesToParse( $_aCookies ) );
        $this->_assertNotWPError( $_oHTTP->getResponse(), $_sURL );
        $this->_assertFalse( $this->isBlockedByAmazonCaptcha( $_oHTTP->getBody(), $_sURL ), $_sURL );
    }

}