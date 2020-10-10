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
 * Tests the AmazonAutoLinks_Unit_Utility class methods.
 *
 * @package     Amazon Auto Links
 * @since       4.3.2
 * @see         AmazonAutoLinks_Unit_Utility
*/
class Test_AmazonAutoLinks_Unit_Utility extends AmazonAutoLinks_UnitTest_Base {

    /**
     * @var AmazonAutoLinks_Unit_Utility
     */
    public $oUtil;

    /**
     * @var AmazonAutoLinks_MockClass
     */
    public $oMock;

    /**
     * @var array An example product item imitating API resopnse item.
     */
    public $aItem = array(
        'ASIN' => 'ABCDEFGHIJ',
        'CustomerReviews' => array(
            'Count' => 1803,
            'StarRating' => array(
                'Value' => 4.6
            ),
        ),
    );

    public function __construct() {
        $this->oUtil = new AmazonAutoLinks_Unit_Utility;
        $this->oMock = new AmazonAutoLinks_MockClass( 'AmazonAutoLinks_Unit_Utility' );
    }

    /**
     * @throws Exception
     */
    public function test_getRatingFromItem() {
        return 46 === $this->oUtil->getRatingFromItem( $this->aItem );
    }

    public function test_getReviewCountFromItem() {
        return 1803 === $this->oUtil->getReviewCountFromItem( $this->aItem );
    }

    public function test_getFormattedRatingFromItem() {
        return $this->oUtil->getFormattedRatingFromItem( $this->aItem, 'US' );
    }

    public function test_getCustomerReviewURL() {
        return 'https://www.amazon.com/product-reviews/1234567890'
            === $this->oUtil->getCustomerReviewURL( '1234567890', 'US' );
    }

    /**
     * @throws ReflectionException
     * @tags   domain
     * @see    WP_Http_Cookie
     * @see    AmazonAutoLinks_Unit_Utility::___getCookieDomain()
     */
    public function test____getCookieDomain() {

        $_sURL    = 'https://www.amazon.com/fajira/dp/reajfraera?tag=frjaifa';
        $_sDomain = $this->oMock->call( '___getCookieDomain', array( $_sURL ) );
        $this->_assertEqual( '.amazon.com', $_sDomain );
        $_sURL    = 'https://amazon.com/';
        $_sDomain = $this->oMock->call( '___getCookieDomain', array( $_sURL ) );
        $this->_assertEqual( '.amazon.com', $_sDomain );
        $_sURL    = 'https://affiliate-program.amazon.com/';
        $_sDomain = $this->oMock->call( '___getCookieDomain', array( $_sURL ) );
        $this->_assertEqual( '.amazon.com', $_sDomain );

    }

    /**
     * @throws ReflectionException
     * @tags session-id
     * @see AmazonAutoLinks_Unit_Utility::___getSessionIDCookie()
     */
    public function test____getSessionIDCookie() {

        $_sLocale             = 'US';
        $_oLocale             = new AmazonAutoLinks_Locale( $_sLocale );
        $_aAssociatesCookies  = $this->oMock->call( '___getAssociatesResponseCookies', array( $_sLocale, '' ) );
        $this->_assertNotEmpty( $this->getCookiesToParse( $_aAssociatesCookies ) );
        $_sAssociatesURL      = $_oLocale->getAssociatesURL();
        $this->_assertNotEmpty( $_sAssociatesURL, 'Associates URL' );
        $_sSessionID1         = $this->oMock->call( '___getSessionIDCookie', array( $_aAssociatesCookies, $_sAssociatesURL ) );
        $this->_assertNotEmpty( $_sSessionID1 );

    }

    /**
     * @tags cookies
     * @see  AmazonAutoLinks_Unit_Utility::getAmazonSitesRequestCookies()
     */
    public function test_getAmazonSitesRequestCookies() {
        foreach( AmazonAutoLinks_Locales::getLocales() as $_sLocale ) {
            $_aRequestCookies = $this->oUtil->getAmazonSitesRequestCookies( $_sLocale );
            $this->_assertNotEmpty( $this->getCookiesToParse( $_aRequestCookies ), 'If blocked, usually it is empty.' );
        }
    }

}