<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 */

/**
 * A scratch class for HTTP requests.
 *  
 * @package     Amazon Auto Links
 * @since       4.5.0
*/
class AmazonAutoLinks_Scratch_HttpRequests extends AmazonAutoLinks_Scratch_Base {

    /**
     * @purpose Performs HTTP requests.
     * @tags    http
     * @throws  Exception
     */
    public function scratch_requestHTTP() {
        $_aParameters    = func_get_args() + array( '', 0, false );
        $_sURL           = $_aParameters[ 0 ];
        $_iCacheDuration = ( integer ) $_aParameters[ 1 ];
        $_bEscapeHTML    = ( boolean ) $_aParameters[ 2 ];
        if ( ! filter_var( $_sURL, FILTER_VALIDATE_URL ) ) {
            throw new Exception( 'Set a URL in the argument input field.' );
        }

        $_aArguments = array(
            'timeout' => 30,    // seconds
        );

        add_action( 'requests-requests.before_request', array( $this, 'replyToCaptureRequestHeader' ), 10, 2 );
        $_oHTTP = new AmazonAutoLinks_HTTPClient( $_sURL, $_iCacheDuration, $_aArguments );
        $_aoResponse = $_oHTTP->getRawResponse();
        remove_action( 'requests-requests.before_request', array( $this, 'replyToCaptureRequestHeader' ), 10 );
        $this->___outputDetailsOfHTTPResponse( $_aoResponse, $_sURL, $_bEscapeHTML );

    }

    /**
     * @purpose Performs HTTP requests.
     * @tags    wp_remote_get
     * @throws  Exception
     */
    public function scratch_wp_remote_get() {

        $_aParameters    = func_get_args() + array( '', 0, false );
        $_sURL           = $_aParameters[ 0 ];
        if ( ! filter_var( $_sURL, FILTER_VALIDATE_URL ) ) {
            throw new Exception( 'Set a URL in the argument input field.' );
        }
        $this->_outputDetails( 'URL', $_sURL );
        $_aArguments = array(
            'timeout' => 30,    // seconds
        );

        add_action( 'requests-requests.before_request', array( $this, 'replyToCaptureRequestHeader' ), 10, 2 );
        $_aoResponse = wp_remote_get( $_sURL, $_aArguments );
        remove_action( 'requests-requests.before_request', array( $this, 'replyToCaptureRequestHeader' ), 10 );
        $this->___outputDetailsOfHTTPResponse( $_aoResponse, $_sURL, ( boolean ) $_aParameters[ 2 ] );

    }

    private function ___outputDetailsOfHTTPResponse( $aoResponse, $sURL, $bEscapeHTML ) {
        $this->_outputDetails( 'URL', $sURL );
        $this->_outputDetails( 'Response Status', ( integer ) $this->getElement( ( array ) $aoResponse, array( 'response', 'code' ) ) . ' ' . $this->getElement( $aoResponse, array( 'response', 'message' ) ) );
        $this->_outputDetails( 'Response Header', $this->getHeaderFromResponse( $aoResponse ) );
        if ( is_wp_error( $aoResponse ) ) {
            $this->_outputDetails( 'Error ' . $aoResponse->get_error_code(),  $aoResponse->get_error_message() );
        }
        $_sHTML = wp_remote_retrieve_body( $aoResponse );

        if ( $bEscapeHTML ) {
            $this->_outputDetails( 'Body', $this->getHTMLBody( $_sHTML ) );
        } else {
            $this->_output( '<strong>Body</strong>' );
            $this->_output( $this->getHTMLBody( $_sHTML ) );
        }

    }

    public function replyToCaptureRequestHeader( $aParameters, $aHeader ) {
        $this->_outputDetails( 'Request Parameters (hook: requests-requests.before_request)', $aParameters );
        $_sCookie = $this->getElement( $aHeader, 'Cookie' );
        if ( ! empty( $_sCookie ) ) {
            $aHeader[ 'Cookie' ] = $this->getStringIntoArray( $_sCookie, ';', '=' );
        }
        $this->_outputDetails( 'Request Header (hook: requests-requests.before_request)', $aHeader );
    }
}