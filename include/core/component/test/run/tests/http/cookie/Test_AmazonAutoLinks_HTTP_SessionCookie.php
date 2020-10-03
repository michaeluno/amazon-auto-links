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
 * Tests sessions with wp_remote_request().
 *
 * @package Amazon Auto Links
 * @since   4.3.3
 * @see     AmazonAutoLinks_HTTPClient
 * @tags    http, cookie
*/
class Test_AmazonAutoLinks_HTTPClient_SessionCookie extends Test_AmazonAutoLinks_HTTPClient_BestSellers {

    /**
     * @break
     */
    public function test_SessionMatchWithDifferentSites_ALL() {
        foreach( array_keys( AmazonAutoLinks_Property::$aAssociatesURLs ) as $_sLocale ) {
            $this->___testSessionMatch( $_sLocale );
        }
    }
    /**
     * @tags SA
     */
    public function test_SessionMatchWithDifferentSites_SA() {
        $this->___testSessionMatch( 'SA' );
    }
    /**
     * @tags DE
     */
    public function test_SessionMatchWithDifferentSites_DE() {
        $this->___testSessionMatch( 'DE' );
    }
    /**
     * @tags IT
     */
    public function test_SessionMatchWithDifferentSites_IT() {
        $this->___testSessionMatch( 'IT' );
    }
    /**
     * @tags CN
     */
    public function test_SessionMatchWithDifferentSites_CN() {
        $this->___testSessionMatch( 'CN' );
    }
    /**
     * @tags UK
     */
    public function test_SessionMatchWithDifferentSites_UK() {
        $this->___testSessionMatch( 'UK' );
    }
        private function ___testSessionMatch( $sLocale ) {
            $_aoResponseAssociates  = $this->___getAssociatesResponse( $sLocale );
            $_aRequestCookies2      = $this->getRequestCookiesFromResponse( $_aoResponseAssociates );
            $this->_outputDetails( "2nd Request Cookies ({$sLocale}): ", $this->getCookiesToParse( $_aRequestCookies2 ) );
            $_sSessionID1           = $this->_getSessionIDExtracted( $_aoResponseAssociates );
            $_aoResponseBestSellers = $this->___getBestSellerResponse( $sLocale, $_aRequestCookies2 );
            $_sSessionID2           = $this->_getSessionIDExtracted( $_aoResponseBestSellers );
            $this->_assertEqual( $_sSessionID1, $_sSessionID2, "The session ID must match ({$sLocale}).", array( '1st' => $this->getHeaderFromResponse( $_aoResponseAssociates ), '2nd' => $this->getHeaderFromResponse( $_aoResponseBestSellers ) ) );
        }
            private function ___getAssociatesResponse( $sLocale ) {
                $_sURL = AmazonAutoLinks_Property::getAssociatesURLByLocale( $sLocale );
                $this->_output( "URL ({$sLocale}): " . $_sURL );
                $_aRequestCookies = $this->_getAssociatesRequestCookies( $sLocale, $_sURL );
                $this->_outputDetails( "1st Request Cookies ({$sLocale}): ", $this->getCookiesToParse( $_aRequestCookies ) );
                $_oHTTP = new AmazonAutoLinks_HTTPClient( $_sURL, 86400, array( 'cookies' => $_aRequestCookies, 'method' => 'HEAD' ) );
                $_oHTTP->deleteCache();
                return $_oHTTP->getResponse();
            }
            private function ___getBestSellerResponse( $sLocale, $aRequestCookies ) {
                $_sURL  = AmazonAutoLinks_Unit_Utility::getBestSellerURL( $sLocale );
                $_oHTTP = new AmazonAutoLinks_HTTPClient( $_sURL, 86400, array( 'cookies' => $aRequestCookies ) );
                $_oHTTP->deleteCache();
                return $_oHTTP->getResponse();
            }

}