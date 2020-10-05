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
 * Tests AmazonAutoLinks_HTTPClient.
 *
 * @package Amazon Auto Links
 * @since   4.3.4
 * @see     AmazonAutoLinks_HTTPClient
 * @tags    http
*/
class Test_AmazonAutoLinks_HTTPClient extends AmazonAutoLinks_UnitTest_HTTPRequest_Base {

    /**
     * @return string
     * @tags head
     */
    public function test_head_method() {
        $_sURL        = 'https://www.amazon.co.uk/gp/customer-reviews/widgets/average-customer-review/popover/ref=dpx_acr_pop_?contextId=dpx&asin=B01B8R6V2E';
        $_aArguments  = array( 'method' => 'head' );
        $_oHTTP       = new AmazonAutoLinks_HTTPClient( $_sURL, 0, $_aArguments );
        $this->_outputDetails( 'cache name', $_oHTTP->getCacheName() );
        $_aoResponse1 = $_oHTTP->getRawResponse();
        $this->_outputDetails( 'AmazonAutoLinks_HTTPClient::getRawResponse()', $this->sLastRequestURL, $this->aLastArguments, $this->aoLastResponse );
        $_bResult = $this->hasPrefix( '2', $this->getElement( $_aoResponse1, array( 'response', 'code' ) ) );
        $this->_outputDetails( 'result', $_bResult );
        return $_bResult;
    }

    /**
     * @tags head, au
     */
    public function test_head_method2() {

        $_sURL        = 'https://affiliate-program.amazon.com.au/';
        $_aArguments  = array( 'method' => 'HEAD' );
        $_oHTTP       = new AmazonAutoLinks_HTTPClient( $_sURL, 86400, $_aArguments );
        $this->_outputDetails( 'cache name', $_oHTTP->getCacheName() );
        $_aoResponse1 = $_oHTTP->getRawResponse();
        $this->_outputDetails( 'AmazonAutoLinks_HTTPClient::getRawResponse()', $this->sLastRequestURL, $this->aLastArguments, $this->aoLastResponse );
        $this->_assertTrue( empty( $_aoResponse1[ 'body' ] ), 'Since the HEAD method, the body must be empty.', $this->aoLastResponse );
        $this->_assertPrefix( '2', $this->getElement( $_aoResponse1, array( 'response', 'code' ) ), 'The HTTP status code must begin with 2 such as 200.' );

    }

    /**
     * @tags wordpress.org
     */
    public function test_headMethodToWordPressDotORG() {
        $_sURL        = 'https://wordpress.org';
        $_aArguments  = array( 'method' => 'HEAD' );
        $_oHTTP       = new AmazonAutoLinks_HTTPClient( $_sURL, 0, $_aArguments );
        $_aoResponse  = $_oHTTP->getResponse();
        $this->_assertEmpty( wp_remote_retrieve_body( $_aoResponse ), 'With the HEAD method, the body should be empty.' );
    }

    /**
     * @tags arguments
     */
    public function test_CustomArguments(){

        $_sURL        = add_query_arg( array( 'aal_test' => __METHOD__ ), admin_url() );
        $_aArguments  = array(
            'method' => 'HEAD',
            'foo'    => 'bar',  // custom argument
        );
        // Perform an HTTP request and create a cache
        $_oHTTP       = new AmazonAutoLinks_HTTPClient( $_sURL, 100, $_aArguments );
        $_oHTTP->getResponse();

        // Now a cache should be created and it triggers the filter hook, `aal_filter_http_response_cache`.
        $_oHTTP       = new AmazonAutoLinks_HTTPClient( $_sURL, 100, $_aArguments );
        add_filter( "aal_filter_http_response_cache", array( $this, 'replyToCaptureCustomArguments' ), 10, 4 );
        $_oHTTP->getResponse();

        $this->_assertTrue( $this->bCalled, 'Is the callback method called?' );
        $this->_assertTrue(
            isset( $this->aLastCustomArguments[ 'foo' ] ) && 'bar' === $this->aLastCustomArguments[ 'foo' ],
            'The custom argument should be passed.',
            $this->aLastCustomArguments
        );
        $_oHTTP->deleteCache();
    }
        public $aLastCustomArguments = array();
        public $bCalled = false;
        public function replyToCaptureCustomArguments( $aCache, $iCacheDuration, $aArguments, $sRequestType ) {
            $this->aLastCustomArguments = $aArguments;
            $this->bCalled = true;
            remove_filter( "aal_filter_http_response_cache", array( $this, 'replyToCaptureCustomArguments' ), 10 );
            return $aCache;
        }

    /**
     * @purpose Tests the `interval` argument.
     * @tags interval
     */
    public function test_Argument_interval() {

        $_sURL        = add_query_arg( array( 'aal_test' => __METHOD__ ), admin_url() );
        $_aArguments  = array(
            'method'    => 'HEAD',
        );
        $_oHTTP       = new AmazonAutoLinks_HTTPClient( $_sURL, 0, $_aArguments );
        $_oHTTP->deleteCache();
        $_oHTTP->getResponse();

        $_fMicroTime1 = microtime( true );
        $_iInterval   = 2;
        $_aArguments  = $_aArguments + array(
            'interval'  => $_iInterval,
        );
        $_oHTTP       = new AmazonAutoLinks_HTTPClient( $_sURL, 100, $_aArguments );
        $_oHTTP->getResponse();
        $_oHTTP->deleteCache();
        $_fMicroTime2 = microtime( true );
        $_fElapsed    = $_fMicroTime2 - $_fMicroTime1;
        $this->_output( 'Elapsed: ' . $_fElapsed );
        $this->_assertTrue( $_fElapsed > $_iInterval, "More than {$_iInterval} second(s) should have passed.", array( 'start' => $_fMicroTime1, 'end' => $_fMicroTime2 ) );

    }

    /**
     * @return bool
     * @tags cookies
     * @see WP_HTTP_Requests_Response
     */
    public function test_checkCookies() {

        $_sURL        = 'https://www.amazon.co.jp/gp/customer-reviews/widgets/average-customer-review/popover/ref=dpx_acr_pop_?contextId=dpx&asin=B01B8R6V2E';
        $_oHTTP       = new AmazonAutoLinks_HTTPClient( $_sURL, 0 );
        $_oHTTP->deleteCache();
        $_aoResponse  = $_oHTTP->getRawResponse();
        $this->_outputDetails( 'is cache used', $_oHTTP->isCacheUsed(), $_oHTTP->getCacheName() );
        $this->_outputDetails( '$_oHTTP->getRawResponse()', $this->sLastRequestURL, $this->aLastArguments, $this->aoLastResponse );
        $_aCookies    = $this->getCookiesFromResponse( $_aoResponse );
        $_sURL2       = $_sURL . '&a=' . uniqid();
        $_oHTTP2      = new AmazonAutoLinks_HTTPClient( $_sURL2, 0, array( 'cookies' => $_aCookies ) );
        $_aoResponse2 = $_oHTTP2->getRawResponse();
        $this->_outputDetails( 'is cache used', $_oHTTP2->isCacheUsed(), $_oHTTP2->getCacheName() );
        $this->_outputDetails( '$_oHTTP2->getRawResponse()', $this->sLastRequestURL, $this->aLastArguments, $this->aoLastResponse );
        return $this->hasPrefix( '2', $this->getElement( $_aoResponse2, array( 'response', 'code' ) ) );

    }

    /**
     * @purpose Get cookies and use them for another request.
     * @return bool
     * @tags cookies, uk
     * @see WP_HTTP_Requests_Response
     */
    public function test_cookies2() {
        $_sURL        = 'https://affiliate-program.amazon.co.uk/';
        $_oHTTP       = new AmazonAutoLinks_HTTPClient( $_sURL );
        $_oHTTP->deleteCache();
        $_aoResponse  = $_oHTTP->getRawResponse();
        $_aCookies    = $this->getCookiesFromResponse( $_aoResponse );
        $this->_outputDetails( 'is cache used', $_oHTTP->isCacheUsed(), $_oHTTP->getCacheName() );
        $this->_outputDetails( 'cookies', $_aCookies );

        $_sURL        = 'https://www.amazon.co.uk/gp/customer-reviews/widgets/average-customer-review/popover/ref=dpx_acr_pop_?contextId=dpx&asin=B01B8R6V2E';
        $_oHTTP       = new AmazonAutoLinks_HTTPClient( $_sURL, 0, array( 'cookies' => $_aCookies ) );
        $_oHTTP->deleteCache();
        $_aoResponse  = $_oHTTP->getRawResponse();
        $this->_outputDetails( 'after setting cookies', $this->sLastRequestURL, $this->aLastArguments, $this->aoLastResponse );
        return $this->hasPrefix( '2', $this->getElement( $_aoResponse, array( 'response', 'code' ) ) );
    }

    /**
     *
     * @var array $_aoResponse
     * $_aoResponse = array(
     *   body
     *   response
     *   cookies
     *   filename
     *   http_response
     * );
     * @throws ReflectionException
     * @tags header
     */
    public function test_header() {

        $_sURL       = 'https://affiliate-program.amazon.com/';
        $_oMock      = new AmazonAutoLinks_MockClass( 'AmazonAutoLinks_HTTPClient', array( $_sURL ) );
        $_aoResponse = $_oMock->call( 'getRaw' );

        $_aHeader    = $this->getHeaderFromResponse( $_aoResponse );
        $this->_assertTrue( ! empty( $_aHeader ), 'The header item must not empty.', $_aHeader );
        $this->_assertTrue( isset( $_aHeader[ 'content-type' ] ), 'check if "content-type" is not missing', $_aHeader );

        // Character set
        $_sCharacterSet = $_oMock->call( '___getCharacterSetFromHeader', array( $_aHeader ) );
        $this->_assertTrue( 'utf-8' === strtolower( $_sCharacterSet ), 'The character set should be utf-8', $_sCharacterSet );

    }

    /**
     * @return bool
     * @throws ReflectionException
     * @tags cache
     */
    public function test____getCacheFromDatabase() {
        $_sURL       = 'https://www.amazon.co.uk/gp/customer-reviews/widgets/average-customer-review/popover/ref=dpx_acr_pop_?contextId=dpx&asin=B01B8R6V2E';
        $_oHTTP      = new AmazonAutoLinks_HTTPClient( $_sURL );
        $_oHTTP->get();
        $_oMocked    = new AmazonAutoLinks_MockClass( 'AmazonAutoLinks_HTTPClient', ( $_sURL ) );
        $_sCacheName = $_oMocked->oClass->getCacheName();
        $_aCache     = $_oMocked->call( '___getCacheFromDatabase', array( $_sCacheName, 8640, array() ) );
        $this->_output( 'cache name: ' . $_sCacheName );
        $this->_outputDetails( 'cache', $_aCache );
        return ! empty( $this->getElement( $_aCache, 'data' ) );
    }

    /**
     * @return string
     * @throws Exception
     * @tags cache, filter
     */
    public function test_filter_aal_filter_http_request_response() {

        $_sURL  = 'https://wordpress.org';

        // Fist request to create a cache
        $_oHTTP = new AmazonAutoLinks_HTTPClient( $_sURL );
        $_oHTTP->deleteCache();
        $_oHTTP->get();

        add_filter( 'aal_filter_http_request_response', array( $this, 'replyToCaptureResponse' ) );
        $_oHTTP = new AmazonAutoLinks_HTTPClient( $_sURL );
        $_oHTTP->get();
        remove_filter( 'aal_filter_http_request_response', array( $this, 'replyToCaptureResponse' ) );

        // Since it is cached, this should be empty.
        if ( ! empty( $this->___aoLastResponseFilterTest ) ) {
            $_sCacheName = $_oHTTP->getCacheName();
            $this->_throw( 'It is not cached: ' . $_sCacheName );
        }

    }
        private $___aoLastResponseFilterTest;
        public function replyToCaptureResponse( $aoResponse ) {
            remove_filter( 'aal_filter_http_request_response', array( $this, 'replyToCaptureResponse' ) );
            $this->___aoLastResponseFilterTest = $aoResponse;
            return $aoResponse;
        }

    /**
     * @return bool
     * @throws ReflectionException
     * @tags cache, local
     */
    public function test_deleteCache() {
        $_sURL       = 'https://localhost';
        $_oHTTP      = new AmazonAutoLinks_HTTPClient( $_sURL );
        $_oHTTP->get();
        $_oHTTP->deleteCache();
        $_oMocked    = new AmazonAutoLinks_MockClass( 'AmazonAutoLinks_HTTPClient', ( $_sURL ) );
        $_sCacheName = $_oMocked->oClass->getCacheName();
        $_aCache     = $_oMocked->call( '___getCacheFromDatabase', array( $_sCacheName, 8640, array() ) );
        return empty( $this->getElement( $_aCache, 'data' ) );
    }
}