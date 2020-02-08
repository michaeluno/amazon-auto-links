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
 * Loads the unit option converter component.
 * 
 * @package      Amazon Auto Links
 * @since        3.11.0
 */
class AmazonAutoLinks_AALBSupport_Setting {
    
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

        // Setting Notification
        $_sMessage      = '';
        $_oOption       = AmazonAutoLinks_Option::getInstance();
        $_sAssociateID  = trim( ( string ) $_oOption->get( 'unit_default', 'associate_id' ) );
        if ( ! $_sAssociateID ) {
            $_sMessage  = "<strong>" . AmazonAutoLinks_Registry::NAME . "</strong>: "
                . __( 'A default Associate ID needs to be set.', 'amazon-auto-links' );
        }
        $_sCountry      = trim( ( string ) $_oOption->get( 'unit_default', 'country' ) );
        if ( ! $_sCountry ) {
            $_sMessage .= ' ' . __( 'A default country needs to be set.', 'amazon-auto-links' );
        }
        if ( $_sMessage ) {
            $_sMessage .= ' ' . sprintf(
                __( 'Go to <a href="%1$s">set</a>.', 'amazon-auto-links' ),
                esc_url( $this->___getDefaultSettingPageURL() )
            );
            $oFactory->setAdminNotice( $_sMessage );
        }

        // Form sections
        new AmazonAutoLinks_AALBSupport_Setting_3RdParty_AALB( $oFactory, AmazonAutoLinks_Registry::$aAdminPages[ 'main' ] );

    }

        private function ___getDefaultSettingPageURL() {
            return add_query_arg(
            array(
                    'post_type' => AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ],
                    'page'      => AmazonAutoLinks_Registry::$aAdminPages[ 'main' ],
                    'tab'       => 'default',
                ),
                'edit.php'
            );
        }
            
}
