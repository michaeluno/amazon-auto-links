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
 * Tests sessions with wp_remote_request().
 *
 * @package Amazon Auto Links
 * @since   4.3.4
 * @see     AmazonAutoLinks_HTTPClient
 * @tags    http, cookie, session-id
 * @deprecated
*/
class Test_AmazonAutoLinks_HTTPClient_SessionCookie extends AmazonAutoLinks_UnitTest_HTTPRequest_Base {

    /**
     * @break
     */
    public function test_SessionMatchWithDifferentSites_ALL() {
        foreach( AmazonAutoLinks_Locales::getLocales() as $_sLocale ) {
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
        /**
         * @param  string $sLocale
         * @throws ReflectionException
         * @deprecated Used deprecated methods.
         */
        private function ___testSessionMatch( $sLocale ) {

            $_oLocale               = new AmazonAutoLinks_Locale( $sLocale );
            $_oLocale               = $_oLocale->get();
            $_oMock                 = new AmazonAutoLinks_MockClass( 'AmazonAutoLinks_Unit_Utility' );
            $_oVersatileCookies     = new AmazonAutoLinks_VersatileFileManager_AmazonCookies( $sLocale );
            $_aRequestCookies       = $_oVersatileCookies->get();
            $_aAssociatesCookies    = empty( $_aRequestCookies )
                ? $_oLocale->getHTTPRequestCookies()
                : $_aRequestCookies;

            $_sAssociatesURL        = $_oLocale->getAssociatesURL();

            $_oMockCookieGetter     = new AmazonAutoLinks_MockClass( 'AmazonAutoLinks_Locale_AmazonCookies', array( $_oLocale, '' ) );
            $_sSessionID1           = $_oMockCookieGetter->call( '_getSessionIDCookie', array( $_aAssociatesCookies, $_sAssociatesURL ) );

            $this->_outputDetails( "2nd Request Cookies ({$sLocale}): ", $this->getCookiesToParse( $_aAssociatesCookies ) );

            $_aBestSellersCookies1  = $_oMock->call( '___getBestSellersResponseCookies', array( $sLocale, $_aAssociatesCookies, false ) );
            $_sBestSellerURL        = $_oLocale->getBestSellersURL();
            $_sSessionID2           = $_oMockCookieGetter->call( '_getSessionIDCookie', array( $_aBestSellersCookies1, $_sBestSellerURL ) );

            if ( $_sSessionID1 === $_sSessionID2 ) {
                $this->_output( 'Matched: ' . $_sSessionID2 );
                return;
            }
            $_aBestSellersCookies2  = $_oMock->call( '___getBestSellersResponseCookies', array( $sLocale, $_aAssociatesCookies, true ) );
            $_sSessionID3           = $_oMockCookieGetter->call( '_getSessionIDCookie', array( $_aBestSellersCookies2, $_sBestSellerURL ) );
            if ( $_sSessionID2 === $_sSessionID3 ) {
                $this->_output( 'Matched: ' . $_sSessionID2 );
                return;
            }
            // Last attempt
            $this->_assertEqual(
                $_sSessionID1,
                $_sSessionID3,
                "The session IDs must match ({$sLocale}).",
                array(
                    '1st' => $this->getCookiesToParse( $_aAssociatesCookies ),
                    '2nd' => $this->getCookiesToParse( $_aBestSellersCookies1 ),
                    '3nd' => $this->getCookiesToParse( $_aBestSellersCookies2 ),
                )
            );

        }

}