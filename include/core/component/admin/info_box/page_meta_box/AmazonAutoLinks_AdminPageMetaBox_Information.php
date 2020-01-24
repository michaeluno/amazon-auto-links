<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2020 Michael Uno
 */
 
class AmazonAutoLinks_AdminPageMetaBox_Information extends AmazonAutoLinks_AdminPageFramework_PageMetaBox {
        
    /**
     * (optional) Use the setUp() method to define settings of this meta box.
     */
    public function setUp() {

        $_bProInstalled   = class_exists( 
            'AmazonAutoLinksPro_Registry', 
            false       // disable auto-load for performance
        );
        
        $_oOption = AmazonAutoLinks_Option::getInstance();
        $_bJoindAffiliate = $_oOption->get( 'miunosoft_affiliate', 'affiliate_id' );
        
        // Disable the meta box if the Pro version exists.
        if ( $_bProInstalled && $_bJoindAffiliate ) {
            $this->oProp->aPageSlugs = array();
        }        
        
    }    
    
    /**
     * The content filter callback method.
     * 
     * Alternatively use the `content_{instantiated class name}` method instead.
     */
    public function content( $sContent ) {
        return $this->___getProInfo()
            . $this->___getAffiliateInfo()
            . $this->___getAnnouncements()
            ;        
    }
        /**
         * @return      string
         */    
        private function ___getProInfo() {
            $_sProImage = esc_url( 
                AmazonAutoLinks_Registry::getPluginURL( 'asset/image/information/amazon-auto-links-pro-affiliate-250x250.jpg' ) 
            );            
            return class_exists( 'AmazonAutoLinksPro_Registry', false )
                ? ''
                : "<a href='https://store.michaeluno.jp/amazon-auto-links-pro/amazon-auto-links-pro' target='_blank'>"
                    . "<img style='max-width: 100%; max-width:250px;' src='{$_sProImage}'/>"
                . "</a>";            
        }
        
        /**
         * @return      string
         */
        private function ___getAffiliateInfo() {
            $_oOption = AmazonAutoLinks_Option::getInstance();
            $_bJoindAffiliate = $_oOption->get( 'miunosoft_affiliate', 'affiliate_id' );            
            $_sLink = 'https://store.michaeluno.jp/amazon-auto-links-pro/affiliate-area/';
            return $_bJoindAffiliate
                ? ''
                : "<h4>" 
                    . __( 'Join Affiliate Program ', 'admin-page-framework-loader' ) 
                . "</h4>"            
                . "<p>"
                    . __( 'Earn commissions by setting a credit link in the unit output.', 'amazon-auto-links' )
                    . ' ' . sprintf( 
                        __( '<a href="%1$s" target="_blank">Sign up</a> for the affiliate program first.', 'amazon-auto-links' ),
                        $_sLink
                    )
                . "</p>"
                . "<a href='{$_sLink}' target='_blank'>"
                    . "<img style='max-width:100%; max-width: 250px;' src='"
                        . esc_url( AmazonAutoLinks_Registry::getPluginURL( 'asset/image/tip/credit_link.jpg' ) )
                        . "' alt='" . __( 'Credit Link', 'amazon-auto-links' ) . "'/>"
                    . "</a>"
                    ;
        }

        /**
         * 
         */
        private function ___getAnnouncements() {
            $_sOutput = '';
            $_oRSS = new AmazonAutoLinks_RSSClient( 'http://feeds.feedburner.com/AmazonAutoLinks_Announcements?1=1' );
            $_aItems = $_oRSS->get();
            foreach( $_aItems as $_aItem ) {
                $_sOutput .= "<h4>" . $_aItem[ 'title' ] . "</h4>"
                    . $_aItem[ 'description' ];
            }
            return $_sOutput;
        }
 
}