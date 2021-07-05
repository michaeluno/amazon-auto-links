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
 * Provides utility methods for the Proxy component.
 *
 * @package      Amazon Auto Links
 * @since        4.2.0
 */
class AmazonAutoLinks_Proxy_Utility extends AmazonAutoLinks_PluginUtility {

    /**
     * @param string    $sProxy     e.g. `scheme://username:password@host:port`
     * @return array
     * @since   4.2.0
     */
    static public function getProxyArguments( $sProxy ) {

        $_aProxy          = parse_url( $sProxy );
        return array(
            'host'     => isset( $_aProxy[ 'scheme' ] )
                ? $_aProxy[ 'scheme' ] . '://' . $_aProxy[ 'host' ]
                : $_aProxy[ 'host' ],
            'port'     => $_aProxy[ 'port' ],
            'username' => isset( $_aProxy[ 'user' ] ) ? $_aProxy[ 'user' ] : null,
            'password' => isset( $_aProxy[ 'pass' ] ) ? $_aProxy[ 'pass' ] : null,
            'raw'      => $sProxy,
        );

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