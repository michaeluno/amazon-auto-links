<?php
/**
 * Deals with the plugin admin pages.
 * 
 * @package     Amazon Auto Links
 * @copyright   Copyright (c) 2013, Michael Uno
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since        2.0.5
 * 
 */
abstract class AmazonAutoLinks_AdminPage_SetUp extends AmazonAutoLinks_AdminPage_SetUp_Form {

    public function setUp() {

        // Set capability for admin pages.
        if ( isset( $this->oProps->arrOptions['aal_settings']['capabilities']['setting_page_capability'] ) 
            && ! empty( $this->oProps->arrOptions['aal_settings']['capabilities']['setting_page_capability'] )
        )    
            $this->setCapability( $this->oProps->arrOptions['aal_settings']['capabilities']['setting_page_capability'] );
    
        $this->_setUpPages();
        $this->_setUpForms();
                
        $this->addLinkToPluginDescription(  
            '<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=J4UJHETVAZX34">' . __( 'Donate', 'amazon-auto-links' ) . '</a>',
            '<a href="http://en.michaeluno.jp/contact/custom-order/?lang=' . ( defined( 'WPLANG' ) ? WPLANG : 'en' ) . '">' . __( 'Order custom plugin', 'amazon-auto-links' ) . '</a>'
        );                        
        
    }
        
}