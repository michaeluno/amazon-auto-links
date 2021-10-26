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
 * A base class for testing HTTP requests.
 *
 * @package Auto Amazon Links
 * @since   4.3.4
*/
abstract class AmazonAutoLinks_UnitTest_HTTPRequest_Base extends AmazonAutoLinks_UnitTest_Base
{

    public $aoLastResponse;
    public $sLastRequestURL = '';
    public $aLastArguments;
    public $aLastHeader;
    public $aLastCookies;

    protected function _doBefore() {
        add_action( 'http_api_debug', array( $this, 'replyToCaptureWPRemoteRequestResults'), 10, 5 );
    }
    protected function _doAfter() {
        remove_action( 'http_api_debug', array( $this, 'replyToCaptureWPRemoteRequestResults' ), 10 );
    }

    public function replyToCaptureWPRemoteRequestResults( $aoResponse, $sType, $sType2, $aArguments, $sURL )
    {
        if ( 'response' !== $sType || 'Requests' !== $sType2 ) {
            return;
        }
        $this->sLastRequestURL = $sURL;
        $this->aoLastResponse  = $aoResponse;
        $this->aLastHeader     = $this->getHeaderFromResponse( $aoResponse );
        $this->aLastCookies    = $this->getCookiesToParseFromResponse( $aoResponse );
        $this->aLastArguments  = $aArguments;
    }

    /**
     * @param  string $sHTML
     * @return false|string
     * @since  4.3.4
     * @since  4.7.0    Moved from `Test_AmazonAutoLinks_HTTPClient_BestSellers` and changed the visibility scope from privte to protected.
     */
    protected function _getHTMLBody( $sHTML ) {
        $_oDOM       = new AmazonAutoLinks_DOM;
        $_oDoc       = $_oDOM->loadDOMFromHTML( $sHTML );
        $_oDOM->removeTags( $_oDoc, array( 'script', 'style', 'head' ) );
        $_oXPath     = new DOMXPath( $_oDoc );
        $_noBodyNode = $_oXPath->query( "/html/body" )->item( 0 );
        return $_noBodyNode
            ? $_oDoc->saveXml( $_noBodyNode, LIBXML_NOEMPTYTAG )
            : '[EMPTY STRING]';
    }

}