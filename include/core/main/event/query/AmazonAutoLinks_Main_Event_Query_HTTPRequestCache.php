<?php
/**
 * Auto Amazon Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 */


/**
 * Outputs the HTTP request cache content.
 *
 * @package    Auto Amazon Links
 * @since      4.7.0
 */
class AmazonAutoLinks_Main_Event_Query_HTTPRequestCache extends AmazonAutoLinks_PluginUtility {

    /**
     * Sets up hooks and properties.
     */
    public function __construct() {

        if ( ! isset( $_GET[ 'aal-http-request-cache' ], $_GET[ 'name' ], $_GET[ 'nonce' ] ) ) {
            return;
        }

        if ( 1 !== wp_verify_nonce( $_GET[ 'nonce' ] , 'aal-nonce-http-request-cache-preview' ) ) {
            $_sMessage = $this->isDebugMode()
                ? 'Something went wrong with your request.'
                : '';
            exit( $_sMessage );
        }
        add_action( 'wp', array( $this, 'replyToPrint' ) );

    }

    /**
     * @see WP_Styles
     */
    public function replyToPrint() {

        $_oTable = new AmazonAutoLinks_DatabaseTable_aal_request_cache();
        $_aCache = $_oTable->getCache( sanitize_text_field( $_GET[ 'name' ] ) );
        $_aData  = $this->getElementAsArray( $_aCache, array( 'data' ) );
        http_response_code( ( integer ) $this->getElement( $_aData, array( 'response', 'code' ) ) );
        $_aoHeader = $this->getElement( $_aData, array( 'headers' ) );
        $_aIgnore  = array(
            'cache-control', 'content-encoding', 'access-control-allow-origin',
            'content-length',
        );
        if ( $this->___isIterable( $_aoHeader ) ) {
            foreach( $_aoHeader as $_sKey => $_sValue ) {
                if ( ! is_scalar( $_sValue ) ) {
                    continue;
                }
                if ( in_array( strtolower( $_sKey ), $_aIgnore, true ) ) {
                    continue;
                }
                header( "{$_sKey}: {$_sValue}" );
            }
        }
        $_sOutput = $this->getElement( $_aData, array( 'body' ) );
        if ( $this->hasPrefix( 'search_callback({', trim( $_sOutput ) ) ) {
            header( 'Content-Type: application/javascript' );
        }
        if ( strlen( $_sOutput ) ) {
            exit( $_sOutput );
        }
        wp_die( $this->isDebugMode() ? '(Empty)' : '' );

    }
        /**
         * @param  $v
         * @return boolean
         * @since  4.7.8
         * @see    https://stackoverflow.com/a/39030296
         */
        private function ___isIterable( $v ) {
          return is_array( $v ) || is_object( $v );
        }

}