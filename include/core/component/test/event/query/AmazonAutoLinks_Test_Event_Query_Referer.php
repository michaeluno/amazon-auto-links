<?php
/**
 * Auto Amazon Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2022 Michael Uno
 */

/**
 * Tests cookies.
 *
 * @since   4.3.4
 */
class AmazonAutoLinks_Test_Event_Query_Referer {

    public function __construct() {
        if ( ! isset( $_GET[ 'aal_test' ] ) ) {         // sanitization unnecessary
            return;
        }
        if ( 'referer' !== $_GET[ 'aal_test' ] ) {      // sanitization unnecessary
            return;
        }

        // echo '<h4>$_SERVER</h4>';
        // echo AmazonAutoLinks_Debug::getDetails( $_SERVER );
        echo esc_html( $_SERVER[ 'HTTP_REFERER' ] );
        exit();

    }

        private function ___setCookies() {
            $_aOptions = array (
                'expires'   => strtotime( '+30 days' ),
                'path'      => '/',
                'domain'    => '.localhost', // leading dot for compatibility or use subdomain
                'secure'    => true,     // or false
                'httponly'  => true,    // or false
                'samesite'  => 'None' // None || Lax  || Strict
            );
            setcookie( 'TestCookie', 'The Cookie Value', $_aOptions );
            foreach( $_COOKIE as $_sName => $_sValue ) {
                setcookie( $_sName, $_sValue, $_aOptions );
            }

        }

}