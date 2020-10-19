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
 * Tests the `AmazonAutoLinks_Locales` class.
 *
 * @package Amazon Auto Links
 * @since   4.3.5
 * @see     AmazonAutoLinks_Locales
*/
class Test_AmazonAutoLinks_Locales extends AmazonAutoLinks_UnitTest_Base {
    /**
     * @tags domain
     */
    public function test_getDomains() {
        $_aDomains = AmazonAutoLinks_Locales::getDomains();
        $this->_assertNotEmpty( $_aDomains );
        $_aValues = array_filter( array_values( $_aDomains ) );
        $this->_assertNotEmpty( $_aValues );
    }

    /**
     * @tags subdomain
     */
    public function test_getSubDomains() {
        $_aSubDomains = AmazonAutoLinks_Locales::getSubDomains();
        $this->_assertNotEmpty( $_aSubDomains );
        $_aValues = array_filter( array_values( $_aSubDomains ) );
        $this->_assertNotEmpty( $_aValues );
    }

    /**
     * @tags URL
     */
    public function test_getLocaleFromURL() {

        foreach( AmazonAutoLinks_Locales::getLocaleObjects() as $_sLocale => $_oLocale ) {
            $_sURL = $_oLocale->getMarketPlaceURL();
            $this->_outputDetails( 'URL', $_sURL );
            $this->_outputDetails( 'Domain', parse_url( $_sURL, PHP_URL_HOST ) );
            $_sDetectedLocale = AmazonAutoLinks_Locales::getLocaleFromURL( $_sURL );
            $this->_assertEqual( $_sLocale, $_sDetectedLocale );

            $_sURL = $_oLocale->getAssociatesURL();
            $this->_outputDetails( 'URL', $_sURL );
            $this->_outputDetails( 'Domain', parse_url( $_sURL, PHP_URL_HOST ) );
            $_sDetectedLocale = AmazonAutoLinks_Locales::getLocaleFromURL( $_sURL );
            $this->_assertEqual( $_sLocale, $_sDetectedLocale );

            $_sURL = $_oLocale->getBestSellersURL();
            $this->_outputDetails( 'URL', $_sURL );
            $this->_outputDetails( 'Domain', parse_url( $_sURL, PHP_URL_HOST ) );
            $_sDetectedLocale = AmazonAutoLinks_Locales::getLocaleFromURL( $_sURL );
            $this->_assertEqual( $_sLocale, $_sDetectedLocale );

        }

    }

}