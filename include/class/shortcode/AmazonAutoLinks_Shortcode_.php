<?php
/**
 * Handles plugin's shortcodes.
 * 
 * @package     Amazon Auto Links
 * @copyright   Copyright (c) 2013, Michael Uno
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since        2.0.0
*/
abstract class AmazonAutoLinks_Shortcode_ {

    public function __construct( $strShortCode ) {
                        
        // Add the shortcode.
        add_shortcode( $strShortCode, array( $this, 'getOutput' ) );
        
    }
    
    public function getOutput( $aArgs ) {
                        
        $_oUnits = new AmazonAutoLinks_Units( $aArgs );
        return $_oUnits->getOutput();
        
    }    

}