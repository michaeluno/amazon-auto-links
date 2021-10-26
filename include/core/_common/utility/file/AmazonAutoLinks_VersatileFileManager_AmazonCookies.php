<?php
/**
 * Auto Amazon Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 */

/**
 * Manages creation and deletion of cookies of Amazon sites.
 *
 * @since   4.3.4
 */
class AmazonAutoLinks_VersatileFileManager_AmazonCookies extends AmazonAutoLinks_VersatileFileManager {

    /**
     * @var   string
     * @since 4.3.5
     */
    public $sLocale = '';

    /**
     * Sets up properties and create a temp directory.
     *
     * @param string  $sLocale          A Amazon locale.
     * @param integer $iTimeout         Timeout in seconds. The default is 604800 which is 7 days.
     * @param string  $sFileNamePrefix  A prefix to prepend to the file name.
     */
    public function __construct( $sLocale, $iTimeout=604800, $sFileNamePrefix='AALCookies_' ) {
        $this->sLocale   = $sLocale;
        $sFileNamePrefix = $sFileNamePrefix . $sLocale . '_';
        parent::__construct( "amazon-cookie:{$sLocale}", $iTimeout, $sFileNamePrefix );
    }

    /**
     * @return array
     * @since  4.3.4
     */
    public function get() {
        $_mValue = maybe_unserialize( parent::get() );
        return empty( $_mValue )    // false on error, empty string when the file not found.
            ? array()
            : ( array ) $_mValue;
    }

    /**
     * @param array  $aNewCookies
     * @param string $sURL
     * @since 4.3.4
     * @since 4.3.5  Added the `$sURL` required parameter,
     */
    public function setCache( array $aNewCookies, $sURL ) {

        $_aCached    = $this->get();
        $aNewCookies = $this->___getCookiesSanitized( $aNewCookies, $sURL );
        if ( $aNewCookies === $_aCached ) {
            return;
        }

        // Drop entries with the same name domain, and path.
        foreach( $_aCached as $_isIndexOrName => $_soCookie ) {
            if ( AmazonAutoLinks_WPUtility::hasSameCookie( $aNewCookies, $_isIndexOrName, $_soCookie, $sURL ) ) {
                unset( $_aCached[ $_isIndexOrName ] );
            }
        }

        $_aMerged    = array_merge( $_aCached, $aNewCookies );
        parent::set( serialize( $_aMerged ) );

    }
        /**
         * Sanitizes cookie items and drop entries with a different domain.
         * @param  array  $aCookies
         * @param  string $sURL
         * @return WP_Http_Cookie[]
         * @since  4.3.5
         */
        private function ___getCookiesSanitized( array $aCookies, $sURL ) {

            $_sSubDomain = AmazonAutoLinks_WPUtility::getSubDomain( $sURL );
            foreach( $aCookies as $_isIndexOrName => $_soCookie ) {
                $_oCookie = $_soCookie instanceof WP_Http_Cookie
                    ? $_soCookie
                    : AmazonAutoLinks_WPUtility::getWPHTTPCookieFromCookieItem( $_soCookie, $_isIndexOrName, $sURL );

                $aCookies[ $_isIndexOrName ] = $_oCookie;

                // [4.5.0] Allow an unset domain
                if ( ! isset( $_oCookie->domain ) ) {
                    continue;
                }

                if ( false === stripos( $_oCookie->domain, $_sSubDomain ) ) {
                    unset( $aCookies[ $_isIndexOrName ] );
                }
            }
            return array_reverse( array_values( $aCookies ) ); // drop associative keys
        
        }

}