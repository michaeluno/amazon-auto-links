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
 * Manages creation and deletion of cookies of Amazon sites.
 *
 * @since   4.3.4
 */
class AmazonAutoLinks_VersatileFileManager_AmazonCookies extends AmazonAutoLinks_VersatileFileManager {

    /**
     * Sets up properties and create a temp directory.
     *
     * @param string  $sLocale          A Amazon locale.
     * @param integer $iTimeout         Timeout in seconds. The default is 604800 which is 7 days.
     * @param string  $sFileNamePrefix  A prefix to prepend to the file name.
     */
    public function __construct( $sLocale, $iTimeout=604800, $sFileNamePrefix='AALCookies_' ) {
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
     * @param array $aNewCookies
     * @since 4.3.4
     */
    public function setCache( array $aNewCookies ) {
        $_aCached = $this->get();
        if ( $aNewCookies === $_aCached ) {
            return;
        }
        // Drop entries with the same name domain, and path.
        foreach( $_aCached as $_isIndexOrName => $_aoCookie ) {
            if ( AmazonAutoLinks_WPUtility::hasSameCookie( $aNewCookies, $_isIndexOrName, $_aoCookie ) ) {
                unset( $_aCached[ $_isIndexOrName ] );
            }
        }
        $aNewCookies = array_reverse( $aNewCookies );
        $_aMerged    = array_merge( $_aCached, $aNewCookies );
        parent::set( serialize( $_aMerged ) );
    }

}