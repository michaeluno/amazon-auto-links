<?php
/**
 * Auto Amazon Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2022 Michael Uno
 */

/**
 * Loads the settings admin page component.
 *  
 * @since       3.1.0
*/
class AmazonAutoLinks_SettingsAdminPageLoader {

    static public $sDirPath = '';

    /**
     * Loads necessary components.
     */
    public function __construct() {

        self::$sDirPath = dirname( __FILE__ );

        add_action( 
            'set_up_' . 'AmazonAutoLinks_AdminPage',
            array( $this, 'replyToSetUpAdminPage' ),
            5   // fairly higher priority so that the plugin action link points to this page
        );

        new AmazonAutoLinks_Admin_Settings_Event_Ajax_LocaleSelect;
        new AmazonAutoLinks_Admin_Settings_Event_Ajax_PAAPICheck;
        new AmazonAutoLinks_Admin_Settings_Event_Contact;   // [4.7.0+]
    }    
    
    /**
     * 
     */
    public function replyToSetUpAdminPage( $oFactory ) {
       
        new AmazonAutoLinks_AdminPage_Setting( $oFactory );
        
        if ( 'plugins.php' === $oFactory->oProp->sPageNow ) {
            $oFactory->setPluginSettingsLinkLabel( '' ); // pass an empty string to disable it.      
            $_sSettingsURL = add_query_arg(
                array(
                    'post_type' => AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ],
                    'page'      => AmazonAutoLinks_Registry::$aAdminPages[ 'main' ],
                ),
                admin_url( 'edit.php' )
            );
            $oFactory->addLinkToPluginTitle(
                "<a href='" . esc_url( $_sSettingsURL ) . "'>" 
                        . __( 'Settings', 'amazon-auto-links' ) 
                    . "</a>"
            );
            $oFactory->addLinkToPluginDescription(
                "<a href='" . esc_url( 'https://en.michaeluno.jp/custom-order' ) . "'>"
                        . __( 'Custom Order', 'amazon-auto-links' )
                    . "</a>"
            );
        }
            
    }
 
}