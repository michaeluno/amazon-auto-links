<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2018 Michael Uno
 */

/**
 * Adds the `Tools` page.
 * 
 * @since       3
 */
class AmazonAutoLinks_HelpAdminPage  {
// class AmazonAutoLinks_HelpAdminPage extends AmazonAutoLinks_AdminPageFramework {

    public function __construct() {
        
        add_action( 
            'set_up_' .  'AmazonAutoLinks_AdminPage',
            array( $this, 'replyToSetUp' )
        );
        
    }

    /**
     * Sets up admin pages.
     */
    public function replyToSetUp( $oFactory ) {
        
        new AmazonAutoLinks_HelpAdminPage_Help( 
            $oFactory,
            array(
                'page_slug' => AmazonAutoLinks_Registry::$aAdminPages[ 'help' ],
                'title'     => __( 'Help', 'amazon-auto-links' ),
                'order'     => 1000, // to be the last menu item
                'style'     => array(
                    AmazonAutoLinks_Registry::getPluginURL( 'asset/css/admin.css' ),
                    AmazonAutoLinks_Registry::getPluginURL( '/asset/css/code.css' ),
                ),
            )                
        );          
        
    }
   
}