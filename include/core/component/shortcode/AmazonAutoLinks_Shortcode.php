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
 * Handles plugin's shortcodes.
 * 
 * @since       2.0.0
 * @since       3       Extends `AmazonAutoLinks_WPUtility`.
 */
class AmazonAutoLinks_Shortcode extends AmazonAutoLinks_WPUtility {

    /**
     * Registers the shortcode(s).
     */
    public function __construct() {
        $_aShortcodes = array(
            AmazonAutoLinks_Registry::$aShortcodes[ 'main' ],
            AmazonAutoLinks_Registry::$aShortcodes[ 'v1' ],
        );
        foreach( $this->getAsArray( $_aShortcodes ) as $_sShortCode ) {
            add_shortcode( 
                $_sShortCode, 
                array( $this, '_replyToGetOutput' ) 
            );
        }
    }
    
    /**
     * Returns the output based on the shortcode arguments.
     * 
     * @since  2.0.0
     * @since  2.1.1  Change the name from `getOutput()`.
     * @param  array  $aArguments
     * @return string
     */
    public function _replyToGetOutput( $aArguments ) {
        return AmazonAutoLinks( $aArguments, false );
    }    

}