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
 * A base class for testing HTTP requests.
 *
 * @package Amazon Auto Links
 * @since   4.3.4
*/
abstract class AmazonAutoLinks_UnitTest_HTTPRequest_Base extends AmazonAutoLinks_UnitTest_Base {

    public $aoLastResponse;
    public $sLastRequestURL = '';
    public $aLastArguments;
    public $aLastHeader;
    public $aLastCookies;

    public function __construct() {
        add_action( 'http_api_debug', array( $this, 'replyToCaptureWPRemoteRequestResults' ), 10, 5 );
    }
        public function replyToCaptureWPRemoteRequestResults( $aoResponse, $sType, $sType2, $aArguments, $sURL ) {
            if (  'response' !== $sType || 'Requests' !== $sType2 ) {
                return;
            }
            remove_action( 'http_api_debug', array( $this, 'replyToCaptureWPRemoteRequestResults' ), 10 );
            $this->sLastRequestURL             = $sURL;
            $this->aoLastResponse              = $aoResponse;
            $this->aLastHeader                 = $this->getHeaderFromResponse( $aoResponse );
            $this->aLastCookies                = $this->getCookiesFromResponse( $aoResponse );
            $this->aLastArguments              = $aArguments;
        }

}