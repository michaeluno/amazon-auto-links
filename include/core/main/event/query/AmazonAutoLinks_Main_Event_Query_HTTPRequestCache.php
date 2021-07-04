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
 * Outputs the HTTP request cache content.
 *
 * @package    Amazon Auto Links
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
        echo $this->getElement( $_aCache, array( 'data', 'body' ) );
        exit;

    }

}