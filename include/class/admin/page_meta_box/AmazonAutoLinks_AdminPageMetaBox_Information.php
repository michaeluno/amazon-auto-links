<?php
/**
 * Amazon Auto Links
 * 
 * http://en.michaeluno.jp/amazon auto links/
 * Copyright (c) 2013-2015 Michael Uno
 * 
 */
 
 
class AmazonAutoLinks_AdminPageMetaBox_Information extends AmazonAutoLinks_AdminPageFramework_PageMetaBox {
        
    /*
     * ( optional ) Use the setUp() method to define settings of this meta box.
     */
    public function setUp() {
        add_action( 
            'current_screen', 
            array( $this, 'replyToDecideToLoad' ), 
            1
        );        
    }
    public function replyToDecideToLoad( $oScreen ) {

        if ( ! $this->_isInThePage() ) {
            return;
        }

        // Disable the meta box if the Pro version exists.
        if ( class_exists( 'AmazonAutoLinksPro_Registry' ) ) {
            $this->oProp->aPageSlugs = array();
        }        
        
    }    
    
    /**
     * The content filter callback method.
     * 
     * Alternatively use the `content_{instantiated class name}` method instead.
     */
    public function content( $sContent ) {
        
        $_sProImage = esc_url( 
            AmazonAutoLinks_Registry::getPluginURL( 'asset/image/information/amazon-auto-links-pro-affiliate-250x250.jpg' ) 
        );
        return ''
            . "<a href='http://store.michaeluno.jp/amazon-auto-links-pro/amazon-auto-links-pro' target='_blank'>"
                . "<img src='{$_sProImage}'/>"
            . "</a>"
            . "<h4>" 
                . __( 'Join Affiliate Program ', 'admin-page-framework-loader' ) 
            . "</h4>"            
            . "<p>"
                . __( 'Earn 20% commissions by setting a credit link in the unit output.', 'amazon-auto-links' )
                . ' ' . sprintf( 
                    __( '<a href="%1$s" target="_blank">Sing up</a> for the affiliate program first.', 'amazon-auto-links' ),
                    'http://store.michaeluno.jp/wp-signup.php'
                )
            . "</p>"
            ;
        
    }
 
}