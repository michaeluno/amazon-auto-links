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
 * Provides utility methods for the Proxy component.
 *
 * @since        4.2.0
 */
class AmazonAutoLinks_Proxy_Utility extends AmazonAutoLinks_PluginUtility {

    /**
     * @param string    $sProxy     e.g. `scheme://username:password@host:port`
     * @return array
     * @since   4.2.0
     */
    static public function getProxyArguments( $sProxy ) {

        $_aProxy = parse_url( $sProxy );
        $_sIP    = self::getIPFromHostName( $_aProxy[ 'host' ] );
        return array(
            'scheme'   => $_aProxy[ 'scheme' ],
            'ip'       => $_sIP,
            'host'     => isset( $_aProxy[ 'scheme' ] )
                ? $_aProxy[ 'scheme' ] . '://' . $_sIP  // cURL does not accept domain names
                : $_aProxy[ 'host' ],
            'port'     => $_aProxy[ 'port' ],
            'username' => isset( $_aProxy[ 'user' ] ) ? $_aProxy[ 'user' ] : null,
            'password' => isset( $_aProxy[ 'pass' ] ) ? $_aProxy[ 'pass' ] : null,
            'raw'      => $sProxy,
        );

    }

    /**
     * @since  5.4.3
     * @return string The looked-up IP address or unmodified host name
     */
    static public function getIPFromHostName( $sHostNameOrIP ) {

        // Return if it's an IP address
        // @remark This checks whether it is a valid IPv4 address.
        // @todo research a way to check whether IPv6 address
        if ( ( boolean ) ip2long( $sHostNameOrIP ) ) {
            return $sHostNameOrIP;
        }

        return gethostbyname( $sHostNameOrIP );

    }

    /**
     * Returns the formatted proxy as a string, generated from an array of proxy arguments.
     * @param array $aProxy A proxy argument array.
     * @since   4.2.0
     * @remark  format: `scheme://username:password@host:port`
     * @return  string
     * @deprecated  4.2.0   Not used anymore
     */
    static public function getProxyFromArguments( array $aProxy ) {
        $_aProxy = $aProxy + array(
            'scheme'    => null,
            'host'      => null,
            'port'      => null,
            'username'  => null,
            'password'  => null,
        );
        $_aHost             = parse_url( ( string ) $_aProxy[ 'host' ] );
        $_sScheme           = isset( $_aProxy[ 'scheme' ] )
            ? $_aProxy[ 'scheme' ]
            : $_aHost[ 'scheme' ];
        $_sUserNamePassword = $_aProxy[ 'username' ] && $_aProxy[ 'password' ]
            ? $_aProxy[ 'username' ] . ':' . $_aProxy[ 'password' ] . '@'
            : '';
        $_sHost              = $_aProxy[ 'host' ]
            . ( $_aProxy[ 'port' ]
                ? $_aProxy[ 'host' ] . ':' . $_aProxy[ 'port' ]
                : $_aProxy[ 'port' ]
            );
        return $_sScheme . '://' . $_sUserNamePassword . $_sHost;

    }

}