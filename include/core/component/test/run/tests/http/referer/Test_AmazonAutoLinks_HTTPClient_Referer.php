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
 * Tests referrer of AmazonAutoLinks_HTTPClient.
 * referrer
 *
 * @since   4.3.4
 * @see     AmazonAutoLinks_HTTPClient
 * @tags    http
*/
class Test_AmazonAutoLinks_HTTPClient_Referer extends AmazonAutoLinks_UnitTest_HTTPRequest_Base {

    /**
     * @tags referer, localhost
     */
    public function test_HeaderReferer() {

        $_sURL       = add_query_arg( array( 'aal_test' => 'referer' ), admin_url() );
        $_sMadeUpURL = 'https://TESTING.somewhere';
        $_aArguments = array(
            'headers' => array( 'Referer' => $_sMadeUpURL ),
            'timeout' => 20,
        );
        $_oHTTP      = new AmazonAutoLinks_HTTPClient( $_sURL, 0, $_aArguments );
        $_oHTTP->deleteCache();
        $this->_assertEqual( $_sMadeUpURL, $_oHTTP->getBody() );

    }

}