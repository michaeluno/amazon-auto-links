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
 * Provides shared utility methods.
 *
 * @since        4.6.0
 */
class AmaAmazonAutoLinks_Geotargeting_Utility extends AmazonAutoLinks_PluginUtility {


    /**
     * @param  string $sIP
     * @return boolean
     */
    static public function isLocalhost( $sIP ) {
        if ( '127.0.0.1' === $sIP ) {
            return true;
        }
        return '::1' === $sIP;
    }

    /**
     * @return string
     * @since  4.6.0
     */
    static public function getClientIPAddress() {
        $_sIP   = '';
        $_aKeys = array( 'HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR' );
        foreach( $_aKeys as $_sKey ) {
            if ( ! isset( $_SERVER[ $_sKey ] ) ) {
                continue;
            }
            $_sIP = self::___getIPAddressParsed( $_SERVER[ $_sKey ] );
            if ( $_sIP ) {
                return $_sIP;
            }
        }
        foreach( $_aKeys as $_sKey ) {
            $_bsIP = getenv( $_sKey );
            if ( ! $_bsIP ) {
                continue;
            }
            $_sIP = self::___getIPAddressParsed( $_bsIP );
            if ( $_sIP ) {
                return $_sIP;
            }
        }
        return $_sIP;
    }
        /**
         * @param  string $sIPAddress
         * @return string
         * @since  4.6.0
         */
        static private function ___getIPAddressParsed( $sIPAddress ) {
            $_sIP = '';
            foreach( explode(',', $sIPAddress ) as $_sIP ) {
                $_sIP = trim( $_sIP );
                if ( false !== filter_var( $_sIP, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE ) ) {
                    return $_sIP;
                }
            }
            return $_sIP;
        }

}