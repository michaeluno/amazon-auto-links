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
 * A scratch class for Amazon cookies.
 *  
 * @package     Amazon Auto Links
 * @since       4.5.0
*/
class AmazonAutoLinks_Scratch_AmazonCookies extends AmazonAutoLinks_Scratch_Base {

    /**
     * @purpose Shows saved cookies of a specified locale.
     * @tags   show, cookies
     * @throws Exception
     */
    public function scratch_showSavedCookies() {

        $_aParameters = func_get_args() + array( '' );
        $_sLocale     = $_aParameters[ 0 ];
        if ( ! $_sLocale ) {
            throw new Exception( 'Pass a locale code to the first parameter. For the rest of the parameters, specify the types to skip from either of "file", "transient", and "direct".' );
        }
        $this->_output( "<strong>$_sLocale</strong>" );

        array_shift( $_aParameters );
        $_aTypes = array_map('strtolower', $_aParameters );
        if ( ! in_array( 'file', $_aTypes, true ) ) {
            $_oVersatileCookies = new AmazonAutoLinks_VersatileFileManager_AmazonCookies( $_sLocale );
            $this->_outputDetails( "File", $this->getCookiesToParse( $_oVersatileCookies->get() ) );
        }

        if ( ! in_array( 'transient', $_aTypes, true ) ) {
            $_sTransientPrefix = AmazonAutoLinks_Registry::TRANSIENT_PREFIX;
            $_sTransientKey    = "_transient_{$_sTransientPrefix}_cookies_{$_sLocale}";
            $this->_outputDetails( "Transient", $this->getCookiesToParse( $this->getAsArray( get_option( $_sTransientKey, array() ) ) ) );
        }

        // @deprecated
        // if ( ! in_array( 'direct', $_aTypes, true ) ) {
        //     $_oLocale        = new AmazonAutoLinks_Locale( $_sLocale );
        //     $_oAmazonCookies = new AmazonAutoLinks_Locale_AmazonCookies( $_oLocale->get() );
        //     $this->_outputDetails( "Direct", $this->getCookiesToParse( $_oAmazonCookies->get() ) );
        // }

    }

    /**
     * @purpose Delete cookies of the specified locale.
     * @tags   delete, cookies
     * @throws Exception
     * @return boolean
     */
    public function scratch_deleteCookiesOfSpecifiedLocale() {

        $_aParameters = func_get_args() + array( '' );
        $_sLocale     = $_aParameters[ 0 ];
        if ( ! $_sLocale ) {
            throw new Exception( 'Pass a locale code to the first parameter.' );
        }
        $this->_output( "<strong>$_sLocale</strong>" );

        $_oVersatileCookies = new AmazonAutoLinks_VersatileFileManager_AmazonCookies( $_sLocale );
        $_oVersatileCookies->delete();

        // Transients
        $_sTransientPrefix     = AmazonAutoLinks_Registry::TRANSIENT_PREFIX;
        $_sTransientKey        = "_transient_{$_sTransientPrefix}_cookies_{$_sLocale}";
        $_sTransientKeyTimeout = "_transient_timeout_{$_sTransientPrefix}_cookies_{$_sLocale}";
        $_baCookies = get_option( $_sTransientKey, false );
        if ( false === $_baCookies ) {
            $this->_output( 'The cookie transient does not exist.' );
        }
        $_biTimeout = get_option( $_sTransientKeyTimeout, false );
        if ( false === $_biTimeout ) {
            $this->_output( 'The cookie timeout transient does not exist.' );
        }
        if ( ! $_baCookies && ! $_biTimeout ) {
            return true;
        }


        $_bDeletedCookies = delete_option( $_sTransientKey );
        $_bDeletedTimeout = delete_option( $_sTransientKeyTimeout );
        $this->_outputDetails( 'Deleted Cookie Transient', $this->getAOrB( $_bDeletedCookies, 'Deleted', 'Failed to Delete' ) );
        $this->_outputDetails( 'Deleted Timeout Transient', $this->getAOrB( $_bDeletedTimeout, 'Deleted', 'Failed to Delete' ) );

        return $_bDeletedCookies && $_bDeletedTimeout;

    }


}