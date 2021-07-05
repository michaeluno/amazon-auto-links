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
 * A scratch class for HTTP requests using Web Page Dumper.
 *  
 * @package     Amazon Auto Links
 * @since       4.5.0
*/
class AmazonAutoLinks_Scratch_HttpRequests_WebPageDumper extends AmazonAutoLinks_Scratch_HttpRequests {

    /**
     * @purpose Performs HTTP requests.
     * @tags    http
     * @throws  Exception
     */
    public function scratch_requestHTTPWebPageDumper() {
        $_aParameters    = func_get_args() + array( '', 0 );
        $_sURL           = $_aParameters[ 0 ];
        $_iCacheDuration = ( integer ) $_aParameters[ 1 ];
        if ( ! filter_var( $_sURL, FILTER_VALIDATE_URL ) ) {
            throw new Exception( 'Set a URL in the argument input field.' );
        }

        $_sWebDumperURL = AmazonAutoLinks_Proxy_WebPageDumper_Utility::getWebPageDumperURL();
        $_sURLEndpoint  = untrailingslashit( $_sWebDumperURL ) . '/www/';
        $this->_output( '<strong>Web Page Dumper</strong>: <a href="' . esc_url( $_sWebDumperURL ) . '" target="_blank">' . $_sWebDumperURL . '</a>' );
        $_sURLImage = add_query_arg(
            array(
                'url'    => urlencode( $_sURL ),
                'output' => 'jpg',
                'reload' => 1,
                'screenshot' => array(
                    // 'encoding' => 'base64',
                    'quality'  => 80,
                ),
            ),
            $_sURLEndpoint
        );


        $_aArguments = array(
            'timeout' => 60,    // seconds
            'renew_cache' => true,
        );

        $_oHTTP       = new AmazonAutoLinks_Proxy_WebPageDumper_HTTPClient( $_sWebDumperURL, $_sURL, 86400, $_aArguments );
        $_sRequestURL = $_oHTTP->getRequestURL();
        $this->_output( '<strong>URL (Endpoint)</strong>: <a href="' . esc_url( $_sRequestURL ) . '" target="_blank">' . $_sRequestURL . '</a>' );
        $_aoResponse  = $_oHTTP->getResponse();
        $this->_outputDetails( 'Header', $this->getHeaderFromResponse( $_aoResponse ) );
        $this->_outputDetails( 'Cookies', $this->getCookiesToParseFromResponse( $_aoResponse ) );
        if ( is_wp_error( $_aoResponse ) ) {
            $this->_outputDetails( 'Error ' . $_aoResponse->get_error_code(),  $_aoResponse->get_error_message() );
            return false;
        }
        $_sHTML = wp_remote_retrieve_body( $_aoResponse );
        if ( ! $_sHTML ) {
            $this->_output( 'The response is empty.' );
            return false;
        }
        if ( false !== strpos( $_sHTML, '<html' ) ) {
            $this->_output( "<strong>Body</strong>" );
            $this->_output( $this->getHTMLBody( $_sHTML ) );
        }
        $this->_output( "<strong>Screenshot</strong>" );
        $this->_output( "<div class='screenshot'><img src='" . esc_url( $_sURLImage ) .  "'></div>" );

    }

}