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
 * Tests the `AmazonAutoLinks_Locales` class.
 *
 * @package Auto Amazon Links
 * @since   4.3.5
 * @see     AmazonAutoLinks_Locales
*/
class Test_AmazonAutoLinks_Locales extends AmazonAutoLinks_UnitTest_Base {

    /**
     * @tags no-image
     */
    public function test_getNoImageURL() {
        foreach( AmazonAutoLinks_Locales::getLocaleObjects() as $_oLocale ) {
            $_sURL = $_oLocale->getNoImageURL();
            $this->_output( "<h4>" . $_oLocale->sSlug . "</h4><img src='" . esc_url( $_sURL ) . "' alt='" . esc_attr( $_oLocale->sSlug ) . "'/>" );
            $this->_assertTrue( $this->doesURLExist( $_sURL ) );
        }
    }

    /**
     * @tags image
     * @see  https://gist.github.com/Foo-x/c1b954b6601f40877e13687eead09135
     */
    public function test_LocaleNumber() {
        $_sASIN = '1408855658';
        foreach( AmazonAutoLinks_Locales::getLocaleObjects() as $_oLocale ) {
            $_sLocaleNumber = $_oLocale->getLocaleNumber();
            $_sURL          = "https://images-na.ssl-images-amazon.com/images/P/{$_sASIN}.{$_sLocaleNumber}.THUMBZZZ";
            $this->_output( "<h4>" . $_oLocale->sSlug . ": {$_sLocaleNumber}</h4><p>{$_sURL}</p><img src='" . esc_url( $_sURL ) . "' alt='" . esc_attr( $_oLocale->sSlug ) . "'/>" );
            $this->_assertTrue( $this->doesURLExist( $_sURL ) );
        }
    }

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