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
 * Tests cookies of AmazonAutoLinks_HTTPClient.
 *
 * @package Amazon Auto Links
 * @since   4.3.3
 * @see     AmazonAutoLinks_HTTPClient
 * @tags    http
*/
class Test_AmazonAutoLinks_HTTPClient_Cookie extends AmazonAutoLinks_UnitTest_HTTPRequest_Base {

    /**
     * @remark It seems the later items gets processes on the requested server.
     * @tags cookies, localhost
     */
    public function test_CookiesWithDuplicateNames() {

        $_sURL       = add_query_arg( array( 'aal_test' => 'cookie' ), admin_url() );
        $_aCookies   = array(
            'foo' => 'bar',
            new WP_Http_Cookie( array( 'name' => 'boo', 'value' => '-' ) ),   // duplicate name - not processed
            new WP_Http_Cookie( array( 'name' => 'boo', 'value' => 'woo' ) ), // processed
        );
        $_aArguments = array(
            'cookies' => $_aCookies,
            'timeout' => 20,
        );
        $_oHTTP      = new AmazonAutoLinks_HTTPClient( $_sURL, 0, $_aArguments );
        $_oHTTP->deleteCache();
        $_aoResponse = $_oHTTP->getResponse();
        $_aHeader    = $this->getHeaderFromResponse( $_aoResponse );
        $this->_assertNotEmpty( $_aHeader );
        $this->_output( $_oHTTP->getBody() );
        $_aCookiesToParse = $this->getCookiesFromResponseToParse( $_aoResponse );
        $_bFound = false;
        foreach( $_aCookiesToParse as $_aCookieToParse ) {
            if ( 'boo' !== $_aCookieToParse[ 'name' ] ) {
                continue;
            }
            if ( 'woo' !== $_aCookieToParse[ 'value' ] ) {
                continue;
            }
            $_bFound = true;
        }
        $this->_assertTrue( $_bFound, 'Find the cookie even its name is duplicated.' );

    }

}