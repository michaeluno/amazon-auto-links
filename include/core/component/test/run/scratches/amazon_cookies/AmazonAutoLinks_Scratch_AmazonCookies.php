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
            throw new Exception( 'Pass a locale code to the first parameter.' );
        }
        $_oLocale        = new AmazonAutoLinks_Locale( $_sLocale );
        $_oAmazonCookies = new AmazonAutoLinks_Locale_AmazonCookies( $_oLocale->get() );
        $this->_outputDetails( $_sLocale, $this->getCookiesToParse( $_oAmazonCookies->get() ) );
    }


}