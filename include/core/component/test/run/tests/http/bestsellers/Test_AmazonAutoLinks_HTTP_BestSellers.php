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
 * Tests bestsellers sites.
 *
 * @package Amazon Auto Links
 * @since   4.3.4
 * @see     AmazonAutoLinks_HTTPClient
 * @tags    http
*/
class Test_AmazonAutoLinks_HTTPClient_BestSellers extends AmazonAutoLinks_UnitTest_HTTPRequest_Base {

    /**
     * @tags bestseller
     * @deprecated
     */
/*    public function test_allLocales() {
        $_aLocales = array_keys( AmazonAutoLinks_Property::$aStoreDomains );
        foreach( $_aLocales as $_iIndex => $_sLocale ) {
            $_sURL    = AmazonAutoLinks_Unit_Utility::getBestSellerURL( $_sLocale );
            $this->_testSessionMatch( $_sURL, $_sLocale );
        }
    }*/

    /**
     * @tags CA
     */
    public function test_CA() {
        $_sLocale = 'CA';
        $_sURL    = AmazonAutoLinks_Unit_Utility::getBestSellerURL( $_sLocale );
        $this->_testSessionMatch( $_sURL, 'CA' );
    }
    /**
     * @tags IT
     */
    public function test_IT() {
        $_sLocale = 'IT';
        $_sURL    = AmazonAutoLinks_Unit_Utility::getBestSellerURL( $_sLocale );
        $this->_testSessionMatch( $_sURL, $_sLocale );
    }
        protected function _testSessionMatch( $_sURL, $_sLocale ) {


            $this->_output( 'URL: ' . $_sURL );

            // First request
            $_aRequestCookies   = $this->_getAssociatesRequestCookies( $_sLocale, $_sURL );
            $this->_outputDetails( '1st Request Cookies: ', $this->getCookiesToParse( $_aRequestCookies ) );
            $_oHTTP             = new AmazonAutoLinks_HTTPClient( $_sURL, 86400, array( 'cookies' => $_aRequestCookies ) );
            $_oHTTP->deleteCache();
            $_aoResponse        = $_oHTTP->getResponse();

            /// Parse response
            $_aHeader1          = $this->getHeaderFromResponse( $_aoResponse );
            $this->_assertNotEmpty( $_aHeader1, 'Check headers.', $_aoResponse );
            $_sSessionID        = $this->_getSessionIDExtracted( $_aoResponse );

            // Second request with retrieved cookies.
            $_aRequestCookies2  = $this->getRequestCookiesFromResponse( $_aoResponse );
            $_aRequestCookies2  = array_reverse( $_aRequestCookies2 ); // There are duplicate names and it seems the one having an actual value must be parsed late.
            $this->_assertNotEmpty( $this->getCookiesToParse( $_aRequestCookies2 ), 'Request cookies' );
            $_sURL2             = $_sURL . '?tag='. uniqid();
            $_oHTTP2            = new AmazonAutoLinks_HTTPClient( $_sURL2, 0, array( 'cookies' => $_aRequestCookies2 ) );
            $_aoResponse2       = $_oHTTP2->getResponse(); // 2nd request

            /// Parse response
            $_aHeader    = $this->getHeaderFromResponse( $_aoResponse2 );
            $this->_assertNotEmpty( $_aHeader, 'Check headers (2nd response)', $_aoResponse2 );
            $this->_assertNotEmpty( $this->sLastRequestURL, 'Last HTTP request URL' );
            $this->_assertNotEmpty( $this->aLastArguments, 'Last HTTP request arguments' );
            $this->_assertNotEmpty( $this->getCookiesFromResponseToParse( $_aoResponse2 ), '2nd response cookies' );
            $_sSessionID2       = $this->_getSessionIDExtracted( $_aoResponse2 );
            $_bResult    = $this->_assertEqual( $_sSessionID, $_sSessionID2, 'The session ID must match.', array( '1st' => $_aResponseCookies, '2nd' => $_aResponseCookies2 ) );
            if ( $_bResult ) {
                return;
            }

            // Third request
            $_aRequestCookies3  = $this->getRequestCookiesFromResponse( $_aoResponse2 );
            $_aRequestCookies3  = array_reverse( $_aRequestCookies3 );
            $_oHTTP3            = new AmazonAutoLinks_HTTPClient( $_sURL2, 0, array( 'cookies' => $_aRequestCookies3 ) );
            $_aoResponse3       = $_oHTTP3->getResponse();
            $this->_assertNotEmpty( $this->getCookiesFromResponseToParse( $_aoResponse3 ), '3rd Request cookies' );
            $_sSessionID3       = $this->_getSessionIDExtracted( $_aoResponse3 );
            $this->_assertEqual( $_sSessionID2, $_sSessionID3, 'The session ID must match.', array( '2nd' => $_aResponseCookies2, '3rd' => $_aResponseCookies3 ) );

        }

            protected function _getAssociatesRequestCookies( $sLocale, $sURL ) {
                $_sLocaleKey = 'ubid-acb' . strtolower( $sLocale );
                $_sToken     = sprintf( '%03d', mt_rand( 1, 999 ) ) . '-' . sprintf( '%07d', mt_rand( 1, 9999999 ) ) . '-' . sprintf( '%07d', mt_rand( 1, 9999999 ) );
                $_iExpires   = time() + ( 86400 * 365 );
                $_sDomain    = $this->___getCookieDomain( $sURL );
                return array(
                    new WP_Http_Cookie( array( 'name' => 'ubid-main', 'value' => $_sToken, 'expires' => $_iExpires, 'domain' => $_sDomain, 'path' => '/' ) ),
                    new WP_Http_Cookie( array( 'name' => $_sLocaleKey, 'value' => $_sToken, 'expires' => $_iExpires, 'domain' => $_sDomain, 'path' => '/' ) ),
                );
            }
                private function ___getCookieDomain( $sURL ) {
                    $_sHost   = parse_url( $sURL, PHP_URL_HOST );
                    return preg_replace( '/^www/', '', $_sHost );
                }


    protected function _getSessionIDExtracted( $aoResponse ) {
        $_aResponseCookies  = ( array ) wp_remote_retrieve_header( $aoResponse, 'set-cookie' );
        foreach( $_aResponseCookies as $_sEntry ) {
            if ( ! preg_match( '/session-id\=(\d{3}-\d{7}-\d{7})/', $_sEntry, $_aMatches ) ) {
                continue;
            }
            return $_aMatches[ 1 ];
        }
        return '';
    }

}