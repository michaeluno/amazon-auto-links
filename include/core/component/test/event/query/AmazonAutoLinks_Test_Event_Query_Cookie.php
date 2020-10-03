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
 * Tests cookies.
 *
 * @since   4.3.4
 */
class AmazonAutoLinks_Test_Event_Query_Cookie {

    public function __construct() {
        if ( ! isset( $_GET[ 'aal_test' ] ) ) {
            return;
        }
        if ( 'cookie' !== $_GET[ 'aal_test' ] ) {
            return;
        }
        $this->___setCookies();
        echo '<h4>Server Cookies ($_COOKIE)</h4>';
        echo AmazonAutoLinks_Debug::getDetails( $_COOKIE );
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