<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 */

/**
 * Loads the PhpZon support admin screen
 * 
 * @package      Amazon Auto Links
 * @since        4.1.0
 */
class AmazonAutoLinks_PhpZonSupport_Setting {
    
    /**
     * Sets up hooks.
     */
    public function __construct() {
       
        add_action( 
            'load_' . AmazonAutoLinks_Registry::$aAdminPages[ 'main' ] . '_3rd_party',
            array( $this, 'replyToLoadPage' )
        );
                
    }
    
    /**
     * @return      void
     * @callback    action      load_{page slug}_{tab slug}
     */
    public function replyToLoadPage( $oFactory ) {

        // Form sections
        new AmazonAutoLinks_PhpZonSupport_Setting_3rdParty_PhpZon( $oFactory, AmazonAutoLinks_Registry::$aAdminPages[ 'main' ] );

    }

            
}
