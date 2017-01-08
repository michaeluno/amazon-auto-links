<?php
/**
 * Handles plugin's shortcodes.
 * 
 * @package     Amazon Auto Links
 * @copyright   Copyright (c) 2013, Michael Uno
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.0.0
*/
class AmazonAutoLinks_Shortcode {

    /**
     * Regiesters the shortcode.
     */
    public function __construct( $sShortCode ) {
        add_shortcode( $sShortCode, array( $this, '_replyToGetOutput' ) );
    }
    
    /**
     * Returns the output based on the shortcode arguments.
     * 
     * @since       2.0.0
     * @since       2.1.1       Chagned the name from `getOutput()`.
     */
    public function _replyToGetOutput( $aArgs ) {
        return AmazonAutoLinks_Units::getInstance( $aArgs )->getOutput();
    }    

}