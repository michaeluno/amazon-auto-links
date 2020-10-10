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
     * @tags  bestseller
     * @break
     */
    public function test_allLocales() {
        foreach( AmazonAutoLinks_Locales::getLocaleObjects() as $_sLocale => $_oLocale ) {
            $this->_testUnblocked( $_oLocale->getBestSellersURL(), $_sLocale );
        }
    }

    /**
     * @tags CA
     */
    public function test_CA() {
        $_sLocale = 'CA';
        $_oLocale = new AmazonAutoLinks_Locale( $_sLocale );
        $this->_testSessionMatch( $_oLocale->getBestSellersURL(), 'CA' );
    }
    /**
     * @tags IT
     */
    public function test_IT() {
        $_sLocale = 'IT';
        $_oLocale = new AmazonAutoLinks_Locale( $_sLocale );
        $this->_testSessionMatch( $_oLocale->getBestSellersURL(), $_sLocale );
    }

        protected function _testUnblocked( $_sURL, $_sLocale ) {

            $this->_output( 'URL: ' . $_sURL );
            $this->_output( 'Locale: ' . $_sLocale );

            $_aRequestCookies   = AmazonAutoLinks_Unit_Utility::getAmazonSitesRequestCookies( $_sLocale, '' );
            $_oHTTP             = new AmazonAutoLinks_HTTPClient( $_sURL, 86400, array( 'cookies' => $_aRequestCookies ) );
            $_aoResponse        = $_oHTTP->getResponse();
            $_bResult = $this->_assertNotWPError( $_aoResponse, "{$_sLocale}: If blocked by Captcha, WP_Error will be returned." );
            if ( ! $_bResult ) {
                return;
            }
            $this->_assertPrefix( '2', $_oHTTP->getStatusCode(), 'The HTTP status code must begin with 2 such as 200.' );
            $this->_assertNotEmpty( $this->getCookiesToParseFromResponse( $_oHTTP->getRawResponse() ),"{$_sLocale}: If blocked, cookies are empty." );
            $this->_assertNotEmpty( $this->getASINs( $_oHTTP->getBody() ), "{$_sLocale}: Find ASINs in the page." );

        }

        protected function _testSessionMatch( $_sURL, $_sLocale ) {


            $this->_output( 'URL: ' . $_sURL );

            // First request
            $_aRequestCookies   = AmazonAutoLinks_Unit_Utility::getAssociatesRequestCookies( $_sLocale, '' );
            $this->_outputDetails( '1st Request Cookies: ', $this->getCookiesToParse( $_aRequestCookies ) );
            $_oHTTP             = new AmazonAutoLinks_HTTPClient( $_sURL, 86400, array( 'cookies' => $_aRequestCookies ) );
            $_oHTTP->deleteCache();
            $_aoResponse        = $_oHTTP->getResponse();
            $_aResponseCookies  = $this->getCookiesToParseFromResponse( $_aoResponse );

            /// Parse response
            $_aHeader1          = $this->getHeaderFromResponse( $_aoResponse );
            $this->_assertNotEmpty( $_aHeader1, 'Check headers.', $_aoResponse );
            $_sSessionID        = $this->___getSessionIDExtracted( $_aoResponse );

            // Second request with retrieved cookies.
            $_aRequestCookies2  = $this->getRequestCookiesFromResponse( $_aoResponse );
            $_aRequestCookies2  = array_reverse( $_aRequestCookies2 ); // There are duplicate names and it seems the one having an actual value must be parsed late.
            $this->_assertNotEmpty( $this->getCookiesToParse( $_aRequestCookies2 ), 'Request cookies' );
            $_sURL2             = $_sURL . '?tag='. uniqid();
            $_oHTTP2            = new AmazonAutoLinks_HTTPClient( $_sURL2, 0, array( 'cookies' => $_aRequestCookies2 ) );
            $_aoResponse2       = $_oHTTP2->getResponse(); // 2nd request
            $_aResponseCookies2 = $this->getCookiesToParseFromResponse( $_aoResponse2 );

            /// Parse response
            $_aHeader    = $this->getHeaderFromResponse( $_aoResponse2 );
            $this->_assertNotEmpty( $_aHeader, 'Check headers (2nd response)', $_aoResponse2 );
            $this->_assertNotEmpty( $this->sLastRequestURL, 'Last HTTP request URL' );
            $this->_assertNotEmpty( $this->aLastArguments, 'Last HTTP request arguments' );
            $this->_assertNotEmpty( $this->getCookiesToParseFromResponse( $_aoResponse2 ), '2nd response cookies' );
            $_sSessionID2       = $this->___getSessionIDExtracted( $_aoResponse2 );
            $_bResult    = $this->_assertEqual( $_sSessionID, $_sSessionID2, 'The session ID must match.', array( '1st' => $_aResponseCookies, '2nd' => $_aResponseCookies2 ) );
            if ( $_bResult ) {
                return;
            }

            // Third request
            $_aRequestCookies3  = $this->getRequestCookiesFromResponse( $_aoResponse2 );
            $_aRequestCookies3  = array_reverse( $_aRequestCookies3 );
            $_oHTTP3            = new AmazonAutoLinks_HTTPClient( $_sURL2, 0, array( 'cookies' => $_aRequestCookies3 ) );
            $_aoResponse3       = $_oHTTP3->getResponse();
            $_aResponseCookies3 = $this->getCookiesToParseFromResponse( $_aoResponse3 );
            $this->_assertNotEmpty( $_aResponseCookies3, '3rd Request cookies' );
            $_sSessionID3       = $this->___getSessionIDExtracted( $_aoResponse3 );
            $this->_assertEqual( $_sSessionID2, $_sSessionID3, 'The session ID must match.', array( '2nd' => $_aResponseCookies2, '3rd' => $_aResponseCookies3 ) );

        }
            private function ___getSessionIDExtracted( $aoResponse ) {
                $_aSetCookies  = ( array ) wp_remote_retrieve_header( $aoResponse, 'set-cookie' );
                return $this->___getSessionIDExtractedFromResponseHeader( $_aSetCookies );
            }
            private function ___getSessionIDExtractedFromResponseHeader( array $aSetCookies ) {
                foreach( $aSetCookies as $_sEntry ) {
                    if ( ! preg_match( '/session-id=(\d{3}-\d{7}-\d{7})/', $_sEntry, $_aMatches ) ) {
                        continue;
                    }
                    return $_aMatches[ 1 ];
                }
                return '';
            }

}