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
        $_aParameters    = func_get_args() + array( '', 0 );
        $_sURL           = $_aParameters[ 0 ];
        $_iCacheDuration = ( integer ) $_aParameters[ 1 ];
        if ( ! filter_var( $_sURL, FILTER_VALIDATE_URL ) ) {
            throw new Exception( 'Set a URL in the argument input field.' );
        }
        $this->_outputDetails( 'URL', $_sURL );
        $_aArguments = array(
            'timeout' => 30,    // seconds

        );
        $_oHTTP = new AmazonAutoLinks_HTTPClient( $_sURL, $_iCacheDuration, $_aArguments );
        $_aoResponse = $_oHTTP->getRawResponse();
        $this->_outputDetails( 'Status', $_oHTTP->getStatusCode() . ' ' . $_oHTTP->getStatusMessage() );
        $this->_outputDetails( 'Header', $this->getHeaderFromResponse( $_aoResponse ) );
        if ( is_wp_error( $_aoResponse ) ) {
            $this->_outputDetails( 'Error ' . $_aoResponse->get_error_code(),  $_aoResponse->get_error_message() );
        }
        $_sHTML = wp_remote_retrieve_body( $_aoResponse );
        $this->_output( '<strong>Body</strong>' );
        $this->_output( $this->getHTMLBody( $_sHTML ) );
    }

    /**
     * @purpose Performs HTTP requests.
     * @tags    wp_remote_get
     * @throws  Exception
     */
    public function scratch_wp_remote_get() {
        $_aParameters    = func_get_args() + array( '', 0 );
        $_sURL           = $_aParameters[ 0 ];
        if ( ! filter_var( $_sURL, FILTER_VALIDATE_URL ) ) {
            throw new Exception( 'Set a URL in the argument input field.' );
        }
        $this->_outputDetails( 'URL', $_sURL );
        $_aArguments = array(
            'timeout' => 30,    // seconds
        );

        $_aoResponse = wp_remote_get( $_sURL, $_aArguments );
        $this->_outputDetails( 'Status', ( integer ) $this->getElement( ( array ) $_aoResponse, array( 'response', 'code' ) ) . ' ' . $this->getElement( $_aoResponse, array( 'response', 'message' ) ) );
        $this->_outputDetails( 'Header', $this->getHeaderFromResponse( $_aoResponse ) );
        if ( is_wp_error( $_aoResponse ) ) {
            $this->_outputDetails( 'Error ' . $_aoResponse->get_error_code(),  $_aoResponse->get_error_message() );
        }
        $_sHTML = wp_remote_retrieve_body( $_aoResponse );
        $this->_output( '<strong>Body</strong>' );
        $this->_output( $this->getHTMLBody( $_sHTML ) );
    }

}