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
 * A scratch class for Amazon cookies.
 *  
 * @package     Amazon Auto Links
 * @since       4.5.0
*/
class AmazonAutoLinks_Scratch_AmazonCookies extends AmazonAutoLinks_Scratch_Base {

    /**
     * @purpose Shows saved cookies of a specified locale.
     * @tags   cookies
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

        if ( ! in_array( 'direct', $_aTypes, true ) ) {
            $_oLocale        = new AmazonAutoLinks_Locale( $_sLocale );
            $_oAmazonCookies = new AmazonAutoLinks_Locale_AmazonCookies( $_oLocale->get() );
            $this->_outputDetails( "Direct", $this->getCookiesToParse( $_oAmazonCookies->get() ) );
        }

    }


}