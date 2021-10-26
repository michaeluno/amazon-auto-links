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
 * Loads the component PhpZon Support
 *
 * @package      Auto Amazon Links
 * @since        4.1.0
 */
class AmazonAutoLinks_PhpZonSupportLoader {
        
    public function __construct() {

        if ( is_admin() ) {
            new AmazonAutoLinks_PhpZonSupport_Setting;
        }

        $_oOption           = AmazonAutoLinks_Option::getInstance();
        $_aPhpZonShortCodes = $_oOption->get( array( 'phpzon', 'shortcodes' ) );
        $_bPhpZon           = $_oOption->getElement( $_aPhpZonShortCodes, 'phpzon' );
        if ( $_bPhpZon ) {
            new AmazonAutoLinks_PhpZonSupport_Shortcode_phpzon;
        }
        $_bPhpBay           = $_oOption->getElement( $_aPhpZonShortCodes, 'phpbay' );
        if ( $_bPhpBay ) {
            new AmazonAutoLinks_PhpZonSupport_Shortcode_phpbay;
        }

    }

}