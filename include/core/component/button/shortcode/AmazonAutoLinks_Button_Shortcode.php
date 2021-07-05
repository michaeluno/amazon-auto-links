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
 * Implements the button shortcode
 * 
 * @package     AmazonAutoLinks
 * @since       4.3.0
 */
class AmazonAutoLinks_Button_Shortcode extends AmazonAutoLinks_WPUtility {

    /**
     * Registers the shortcode(s).
     */
    public function __construct() {
        add_shortcode( AmazonAutoLinks_Registry::$aShortcodes[ 'button' ], array( $this, '_replyToGetOutput' ) );
    }
    
    /**
     * Returns the output based on the shortcode arguments.
     * 
     * @since       4.3.0
     */
    public function _replyToGetOutput( $aArguments ) {
        return apply_filters( 'aal_filter_linked_button', '', $this->getAsArray( $aArguments ) );
    }    

}