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
 * @since   4.3.3
 * @see     AmazonAutoLinks_HTTPClient
 * @tags    http
*/
class Test_AmazonAutoLinks_HTTPClient_BestSellers extends AmazonAutoLinks_UnitTest_HTTPRequest_Base {

    /**
     * @tags cookies, bestseller
     * @deprecated
     */
    public function _test_BestSellerPages() {

        $_aLocales = array_keys( AmazonAutoLinks_Property::$aStoreDomains );
        foreach( $_aLocales as $_iIndex => $_sLocale ) {
            $_sURL       = AmazonAutoLinks_Unit_Utility::getBestSellerURL( $_sLocale );
            $this->_output( 'URL: ' . $_sURL );
            $_oHTTP      = new AmazonAutoLinks_HTTPClient( $_sURL, 86400 );
            $_aoResponse = $_oHTTP->getResponse();
            $_aCookies   = $this->getCookiesFromResponse( $_aoResponse );
            // $this->_outputDetails( 'cookies', $_aCookies );
            $this->_assertNotEmpty( $_aCookies, 'Check cookies.', $_aoResponse );
            $this->_assertPrefix( '2', $this->getElement( $_aoResponse, array( 'response', 'code' ) ), 'The HTTP status code must begin with 2 such as 200.' );
        }

    }

    /**
     * @tags bestseller
     * @deprecated
     */
    public function test_allLocales() {
        $_aLocales = array_keys( AmazonAutoLinks_Property::$aStoreDomains );
        foreach( $_aLocales as $_iIndex => $_sLocale ) {
            $this->___testBestSellerPageByLocale( $_sLocale );
        }
    }

    /**
     * @tags CA
     */
    public function test_CA() {
        $this->___testBestSellerPageByLocale( 'CA' );
    }
    /**
     * @tags IT
     */
    public function test_IT() {
        $this->___testBestSellerPageByLocale( 'IT' );
    }
        private function ___testBestSellerPageByLocale( $_sLocale ) {

            $_sURL              = AmazonAutoLinks_Unit_Utility::getBestSellerURL( $_sLocale );
            $this->_output( 'URL: ' . $_sURL );

            // First request
            $_oHTTP             = new AmazonAutoLinks_HTTPClient( $_sURL, 86400 );
            $_aoResponse        = $_oHTTP->getResponse();

            /// Parse response
            $_aHeader           = $this->getHeaderFromResponse( $_aoResponse );
            $this->_assertNotEmpty( $_aHeader, 'Check headers.', $_aoResponse );
            $_aResponseCookies  = ( array ) wp_remote_retrieve_header( $_aoResponse, 'set-cookie' );
            $_sSessionID        = $this->___getSessionIDExtracted( $_aResponseCookies );

            // Second request with retrieved cookies.
            $_aGeneratedCookies = $this->___getAssociatesRequestCookies( $_sLocale, $_sURL );
            $_aRequestCookies2  = $this->getRequestCookiesFromResponse( $_aoResponse );
            $_aRequestCookies2  = array_reverse( $_aRequestCookies2 ); // There are duplicate names and it seems the one having an actual value must be parsed late.
            $_aRequestCookies2  = array_merge( $_aRequestCookies2, $_aGeneratedCookies );
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
            $_aResponseCookies2 = ( array ) wp_remote_retrieve_header( $_aoResponse2, 'set-cookie' );
            $_sSessionID2       = $this->___getSessionIDExtracted( $_aResponseCookies2 );
            $this->_assertEqual( $_sSessionID, $_sSessionID2, 'The session ID must match.', array( '1st' => $_aResponseCookies, '2nd' => $_aResponseCookies2 ) );

            // Third request
            $_aRequestCookies3  = $this->getRequestCookiesFromResponse( $_aoResponse2 );
            $_aRequestCookies3  = array_reverse( $_aRequestCookies3 );
            $_oHTTP3            = new AmazonAutoLinks_HTTPClient( $_sURL2, 0, array( 'cookies' => $_aRequestCookies3 ) );
            $_aoResponse3       = $_oHTTP3->getResponse();
            $this->_assertNotEmpty( $this->getCookiesFromResponseToParse( $_aoResponse3 ), '3rd Request cookies' );
            $_aResponseCookies3 = ( array ) wp_remote_retrieve_header( $_aoResponse3, 'set-cookie' );
            $_sSessionID3       = $this->___getSessionIDExtracted( $_aResponseCookies3 );
            $this->_assertEqual( $_sSessionID2, $_sSessionID3, 'The session ID must match.', array( '2nd' => $_aResponseCookies2, '3rd' => $_aResponseCookies3 ) );

        }

            private function ___getAssociatesRequestCookies( $sLocale, $sURL ) {
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


            private function ___getSessionIDExtracted( $aCookies ) {
                foreach( $aCookies as $_sEntry ) {
                    if ( ! preg_match( '/session-id\=(\d{3}-\d{7}-\d{7})/', $_sEntry, $_aMatches ) ) {
                        continue;
                    }
                    return $_aMatches[ 1 ];
                }
                return '';
            }

}