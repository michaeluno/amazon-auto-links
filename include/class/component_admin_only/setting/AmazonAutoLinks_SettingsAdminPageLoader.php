<?php
/**
 * Amazon Auto Links
 * 
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2015 Michael Uno
 * 
 */

/**
 * Loads the settings admin page component.
 *  
 * @package     Amazon Auto Links
 * @since       3.1.0
*/
class AmazonAutoLinks_SettingsAdminPageLoader {
    
    /**
     * Loads necessary components.
     */
    public function __construct() {
        
        add_action( 
            'set_up_' . 'AmazonAutoLinks_AdminPage',
            array( $this, 'replyToSetUpAdminPage' )
        );

    }    
    
    /**
     * 
     */
    public function replyToSetUpAdminPage( $oFactory ) {
       
        new AmazonAutoLinks_AdminPage_Setting( 
            $oFactory,
            array(
                'page_slug'     => AmazonAutoLinks_Registry::$aAdminPages[ 'main' ],
                'title'         => __( 'Settings', 'amazon-auto-links' ),
                'screen_icon'   => AmazonAutoLinks_Registry::getPluginURL( "asset/image/screen_icon_32x32.png" ),
                'order'         => 50,
            )
        );
       
        
    }
    
  
    
}