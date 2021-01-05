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
 * Tests the class, `AmazonAutoLinks_Event___Action_HTTPCacheRenewal`.
 *
 * @package Amazon Auto Links
 * @since   4.3.4
 * @see     AmazonAutoLinks_Event___Action_HTTPCacheRenewal
 * @tags    http
*/
class Test_AmazonAutoLinks_Event___Action_HTTPCacheRenewal extends AmazonAutoLinks_UnitTest_Base {

    public $aoLastResponse;
    public $sLastURL;
    public $aLastArguments;
    public $sLastRequestType;
    public $iLastCacheDuration;

    public function __construct() {
        add_filter( 'aal_filter_http_request_response', array( $this, 'replyToCaptureRequestData' ), 10, 5 );
    }
        public function replyToCaptureRequestData( $aoResponse, $sURL, $aArguments, $sRequestType, $iCacheDuration ) {
            $this->aoLastResponse     = $aoResponse;
            $this->sLastURL           = $sURL;
            $this->aLastArguments     = $aArguments;
            $this->sLastRequestType   = $sRequestType;
            $this->iLastCacheDuration = $iCacheDuration;
            return $aoResponse;
        }

    /**
     * @tags cache-renewal
     */
    public function test_HEADMethodRequest() {

        $_sURL  = add_query_arg( array( 'aal_test' => __METHOD__ ), admin_url() );

        // 1st request
        $_oHTTP = new AmazonAutoLinks_HTTPClient( $_sURL, 1, array( 'method' => 'HEAD', 'timeout' => 20 ) );
        $_oHTTP->deleteCache();
        $this->_assertFalse( is_wp_error( $_oHTTP->getResponse() ) );

        sleep( 2 );

        // 2nd request - the cache should be expired and a background routine should be scheduled.
        $_aArguments = array(
            'method' => 'HEAD',
            'timeout' => 20,
            'cookies' => array(
                new WP_Http_Cookie( array( 'name' => 'foo', 'value' => 'bar' ) ),
            ),
        );
        $_oHTTP = new AmazonAutoLinks_HTTPClient( $_sURL, 1, $_aArguments );
        $_oHTTP->getResponse();

        $this->aoLastResponse       = null;
        $this->sLastURL             = null;
        $this->aLastArguments       = null;
        $this->sLastRequestType     = null;
        $this->iLastCacheDuration   = null;

        // Now check and execute tasks - the cache renewal routine should be triggered.
        do_action( 'aal_action_check_tasks' );
        $this->aoLastResponse[ 'body' ] = '';
        $this->_assertNotEmpty( $this->aoLastResponse, 'Last URL: ' . $this->sLastURL );
        $this->_outputDetails(
            'Last Request Information',
            array(
                'arguments'      => $this->aLastArguments,
                'request_type'   => $this->sLastRequestType,
                'cache_duration' => $this->iLastCacheDuration,
            )
        );
        $_oHTTP->deleteCache();
        $this->_assertEqual( $_aArguments[ 'method' ],  $this->aLastArguments[ 'method' ],  'the arguments should be passed' );
        $this->_assertEqual( $this->getCookiesToParse( $_aArguments[ 'cookies' ] ), $this->getCookiesToParse( $this->aLastArguments[ 'cookies' ] ), 'the arguments should be passed' );

    }

}