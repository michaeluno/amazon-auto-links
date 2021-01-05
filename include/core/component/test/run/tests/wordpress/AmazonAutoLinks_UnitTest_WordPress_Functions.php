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
 * General tests regarding WordPress functions.
 *  
 * @package     Amazon Auto Links
 * @since       4.3.0
*/
class AmazonAutoLinks_UnitTest_WordPress_Functions extends AmazonAutoLinks_UnitTest_Base {

    /**
     * @return string
     * @tags url
     */
    public function testGetCurrentURL() {
        return $this->getCurrentURL();
    }

    /**
     * @return string
     * @tags url
     */
    public function test_URLPort() {
        return $this->_getURLPortSuffix( false );
    }
    /**
     * @return string
     * @tags url
     */
    public function test_URLHost() {
        $_sHost = isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME']);
        $_sHost = preg_replace( '/:.+/', '', $_sHost ); // remove the port part in case it is added.
        return $_sHost;
    }

    /**
     * @return mixed
     * @tags url
     */
    public function testRequestURI () {
        return $_SERVER['REQUEST_URI'];
    }


        static public function getCurrentURL() {
            $_bSSL = self::isSSL();
            $_sServerProtocol = strtolower($_SERVER['SERVER_PROTOCOL']);
            $_aProtocolSuffix = array(0 => '', 1 => 's',);
            $_sProtocol = substr($_sServerProtocol, 0, strpos($_sServerProtocol, '/'))
                . $_aProtocolSuffix[( int )$_bSSL];
            $_sPort = self::_getURLPortSuffix($_bSSL);
            $_sHost = isset($_SERVER['HTTP_X_FORWARDED_HOST'])
                ? $_SERVER['HTTP_X_FORWARDED_HOST']
                : (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME']);
            $_sHost = preg_replace( '/:.+/', '', $_sHost ); // remove the port part in case it is added.
            return $_sProtocol . '://' . $_sHost . $_sPort . $_SERVER['REQUEST_URI'];
        }
        static private function _getURLPortSuffix($_bSSL) {
            $_sPort = isset($_SERVER['SERVER_PORT'])
                ? ( string )$_SERVER['SERVER_PORT']
                : '';
            $_aPort = array(0 => ':' . $_sPort, 1 => '',);
            $_bPortSet = (!$_bSSL && '80' === $_sPort) || ($_bSSL && '443' === $_sPort);
            return $_aPort[( int )$_bPortSet];
        }
        static public function isSSL() {
            return array_key_exists('HTTPS', $_SERVER) && 'on' === $_SERVER['HTTPS'];
        }

    /**
     * @return bool
     * @tags url
     */
    public function test_add_query_args() {

        return add_query_arg(
            array(
                'page' => 'test-page',
                'tab' => 'test-tab',
            ),
            $this->getCurrentURL()
        );

    }

    /**
     * @purpose Checks if it is in the admin area.
     * @return bool
     * @tags url
     */
    public function test_remove_query_args() {

        $_sCustomURL = add_query_arg(
            array(
                'page' => 'test-page',
                'tab' => 'test-tab',
            ),
            $this->getCurrentURL()
        );

        return remove_query_arg(
            array( 'page', 'tab' ),
            $_sCustomURL
        );

    }

}