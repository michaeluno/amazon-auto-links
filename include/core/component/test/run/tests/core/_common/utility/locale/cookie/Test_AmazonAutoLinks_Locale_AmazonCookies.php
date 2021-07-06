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
 * Tests the AmazonAutoLinks_Locale_AmazonCookie class.
 *
 * @package Amazon Auto Links
 * @since   4.3.4
 * @see     AmazonAutoLinks_Locale_AmazonCookies
*/
class Test_AmazonAutoLinks_Locale_AmazonCookies extends AmazonAutoLinks_UnitTest_Base {

    /**
     * @var AmazonAutoLinks_MockClass
     */
    public $oMock;

    /**
     * @var AmazonAutoLinks_Locale
     */
    public $oLocale;

    /**
     * Test_AmazonAutoLinks_Locale_AmazonCookies constructor.
     * @throws ReflectionException
     */
    public function __construct() {
        $_oLocale      = new AmazonAutoLinks_Locale( 'US' );
        $this->oLocale = $_oLocale->get();
        $this->oMock   = new AmazonAutoLinks_MockClass( 'AmazonAutoLinks_Locale_AmazonCookies', array( $this->oLocale ) );
    }

    /**
     * @throws ReflectionException
     * @tags   domain, cookie
     * @see    WP_Http_Cookie
     * @see    AmazonAutoLinks_Locale_AmazonCookies::___getCookieDomain()     *
     */
    public function test____getCookieDomain() {

        $_sURL    = 'https://www.amazon.com/fajira/dp/reajfraera?tag=frjaifa';
        $_sDomain = $this->oMock->call( '___getCookieDomain', array( $_sURL ) );
        $this->_assertEqual( '.amazon.com', $_sDomain );

        $_sURL    = 'https://amazon.com/';
        $_sDomain = $this->oMock->call( '___getCookieDomain', array( $_sURL ) );
        $this->_assertEqual( '.amazon.com', $_sDomain );

        $_sURL    = 'https://www.amazon.com/';
        $_sDomain = $this->oMock->call( '___getCookieDomain', array( $_sURL ) );
        $this->_assertEqual( '.amazon.com', $_sDomain );

        $_sURL    = 'https://affiliate-program.amazon.com/';
        $_sDomain = $this->oMock->call( '___getCookieDomain', array( $_sURL ) );
        $this->_assertEqual( '.amazon.com', $_sDomain );

        $_sURL    = 'https://amazon.co.uk/';
        $_sDomain = $this->oMock->call( '___getCookieDomain', array( $_sURL ) );
        $this->_assertEqual( '.amazon.co.uk', $_sDomain );

        $_sURL    = 'https://affiliate-program.amazon.co.uk/';
        $_sDomain = $this->oMock->call( '___getCookieDomain', array( $_sURL ) );
        $this->_assertEqual( '.amazon.co.uk', $_sDomain );

        $_sURL    = 'https://www.amazon.co.uk/';
        $_sDomain = $this->oMock->call( '___getCookieDomain', array( $_sURL ) );
        $this->_assertEqual( '.amazon.co.uk', $_sDomain );

    }

    /**
     * @tags cookie, DE
     * @throws ReflectionException
     */
    public function test__getSessionIDCookie() {

        $_oLocaleHandler = new AmazonAutoLinks_Locale( 'DE' );
        $_oLocale        = $_oLocaleHandler->get();
        $_oMock          = new AmazonAutoLinks_MockClass( 'AmazonAutoLinks_Locale_AmazonCookies', array( $_oLocale ) );
        $_aCookies       = $_oMock->call( 'get' );
        $_sSessionID     = $_oMock->call( '_getSessionIDCookie', array( $_aCookies, $_oLocale->getMarketPlaceURL() ) );
        $_sDomain        = $_oMock->call( '___getCookieDomain', array( $_oLocale->getMarketPlaceURL() ) );
        $this->_assertNotEmpty( $this->getCookiesToParse( $_aCookies ) );
        $this->_assertNotEmpty( $_sSessionID );
        $this->_assertNotEmpty( $_sDomain );
        $this->_assertTrue( preg_match( '/^\d{3}-\d{7}-\d{7}$/', $_sSessionID ), 'Check whether it is a form of Amazon session cookie.', $_sSessionID );

    }

    /**
     * @tags US
     * @throws ReflectionException
     */
    public function test_get_US() {
        $this->_testSessionID( 'US' );
    }

    /**
     * @tags IT
     * @throws ReflectionException
     */
    public function test_get_IT() {
        $this->_testSessionID( 'IT' );
    }

    /**
     * @tags FR
     * @throws ReflectionException
     */
    public function test_get_FR() {
        $this->_testSessionID( 'FR' );
    }

    /**
     * @tags DE
     * @throws ReflectionException
     */
    public function test_get_DE() {
        $this->_testSessionID( 'DE' );
    }

    /**
     * @tags ES
     * @throws ReflectionException
     */
    public function test_get_ES() {
        $this->_testSessionID( 'ES' );
    }

    /**
     * @tags UK
     * @throws ReflectionException
     */
    public function test_get_UK() {
        $this->_testSessionID( 'UK' );
    }


        /**
         * @param  string $sLocale
         * @throws ReflectionException
         */
        protected function _testSessionID( $sLocale ) {
            $_oLocaleHandler = new AmazonAutoLinks_Locale( $sLocale );
            $_oLocale        = $_oLocaleHandler->get();
            $_oLocaleCookies = new AmazonAutoLinks_Locale_AmazonCookies( $_oLocale );
            $_aCookies       = $_oLocaleCookies->get();
            $_aParseCookies  = $this->getCookiesToParse( $_aCookies );
            $this->_assertNotEmpty( $_aParseCookies );
            $_oMock          = new AmazonAutoLinks_MockClass( 'AmazonAutoLinks_Locale_AmazonCookies', array( $_oLocale ) );
            $_sSessionID     = $_oMock->call( '_getSessionIDCookie', array( $_aCookies, $_oLocale->getMarketPlaceURL() ) );
            $this->_outputDetails( 'Session ID', $_sSessionID );
            $this->_assertTrue( preg_match( '/^\d{3}-\d{7}-\d{7}$/', $_sSessionID ), 'Check whether it is a form of Amazon session cookie.', $_sSessionID );
        }

}