<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 */

/**
 * Tests AmazonAutoLinks_HTTPClient.
 *
 * @package Amazon Auto Links
 * @since   4.3.5
 * @see     AmazonAutoLinks_HTTPClient
 * @tags    http
*/
class Test_AmazonAutoLinks_HTTPClient_Part2 extends AmazonAutoLinks_UnitTest_HTTPRequest_Base {

    /**
     * @purpose When a HTTP response contains an error, an old cache should be used.
     * @tags old-cache
     */
    public function testUsingOldCache() {

        // 1. Normal request
        $_sURL       = add_query_arg( array( 'aal_test' => 'captcha_error' ), admin_url() );
        $_oHTTP      = new AmazonAutoLinks_HTTPClient( $_sURL, 1, array( 'renew_cache' => true ), 'test' );
        $_sBody1     = $_oHTTP->getBody(); // will create a cache.

        sleep( 2 ); // now the cache should be expired.

        // 2
        add_filter( 'aal_filter_http_request_set_cache', array( $this, 'replyToReturnWPError' ), 1, 7 );
        $_oHTTP      = new AmazonAutoLinks_HTTPClient( $_sURL, 1, array( 'cookies' => array( 'second' => 'second' ) ), 'test' );
        $_sBody2     = $_oHTTP->getBody();  //
        $this->_assertNotEmpty( $_sBody2, 'this request is supposed to get an error. Then the old cache should continue to be used. So the output is an error and the cache is the old one. This should show an error.' );
        $this->_assertEqual( 'ERROR', $_sBody2 ); // should show a old cache
        remove_filter( 'aal_filter_http_request_set_cache', array( $this, 'replyToReturnWPError' ), 1 );

        // 3 Check cache
        $_sCacheName  = $_oHTTP->getCacheName();
        $_oCacheTable = new AmazonAutoLinks_DatabaseTable_aal_request_cache;
        $_aCache      = $_oCacheTable->getCache( $_sCacheName, 10 );
        $_sBody3      = $this->getElement( $_aCache, array( 'data', 'body' ) );
        $this->_assertEqual( $_sBody1, $_sBody3 ); // should show a old cache

        // 4. Clean up
        $_oHTTP->deleteCache();


    }

        public function replyToReturnWPError( $aoResponse, $sCacheName, $_sCharSet, $iCacheDuration, $sURL, $aArguments, $aOldCache ) {

            $this->_outputDetails( 'Called. Filter: ' . current_filter() . ' Cache Name: ' . $sCacheName );
            $this->_outputDetails( 'Cookies', $aArguments[ 'cookies' ] );
            $this->_outputDetails( 'HTTP Body', $this->getElement( $aoResponse, array( 'body' ) ) );
            return new WP_Error( 'TEST', 'This is a test.' ); // returning an error

        }
}