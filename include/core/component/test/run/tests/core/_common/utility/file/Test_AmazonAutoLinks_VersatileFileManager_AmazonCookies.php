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
 * Tests the `AmazonAutoLinks_VersatileFileManager_AmazonCookies` class.
 *
 * @package Auto Amazon Links
 * @since   4.3.5
 * @see     AmazonAutoLinks_VersatileFileManager_AmazonCookies
*/
class Test_AmazonAutoLinks_VersatileFileManager_AmazonCookies extends AmazonAutoLinks_UnitTest_Base {

    public function test_get() {
        foreach( AmazonAutoLinks_Locales::getLocaleObjects() as $_sLocale => $_oLocale ) {
            $_sURL              = $_oLocale->getBestSellersURL();
            $this->_outputDetails( $_sLocale, $_sURL );
            $_oHTTP             = new AmazonAutoLinks_HTTPClient( $_oLocale->getBestSellersURL() );
            $_oHTTP->get();
            $_oVersatileCookies = new AmazonAutoLinks_VersatileFileManager_AmazonCookies( $_sLocale );
            $_aSavedCookies     = $_oVersatileCookies->get();
            $this->___checkDomains( $_aSavedCookies, $_sURL );
        }
    }
        private function ___checkDomains( array $aCookies, $sURL ) {
            $_sSubDomain = $this->getSubDomain( $sURL );
            foreach( $aCookies as $_oCookie ) {
                $this->_assertTrue( false !== strpos( $_oCookie->domain, $_sSubDomain ), "Searching {$_sSubDomain} in {$_oCookie->domain}" );
            }
        }


}