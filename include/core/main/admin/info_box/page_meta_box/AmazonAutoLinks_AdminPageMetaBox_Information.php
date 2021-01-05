<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 */
 
class AmazonAutoLinks_AdminPageMetaBox_Information extends AmazonAutoLinks_PageMetaBox_Base {
        
    /**
     * (optional) Use the setUp() method to define settings of this meta box.
     */
    public function setUp() {

        $_bProInstalled    = class_exists( 'AmazonAutoLinksPro_Registry', false );
        $_oOption          = AmazonAutoLinks_Option::getInstance();
        $_bJoinedAffiliate = $_oOption->get( 'miunosoft_affiliate', 'affiliate_id' );
        
        // Disable the meta box if the Pro version exists.
        if ( $_bProInstalled && $_bJoinedAffiliate ) {
            $this->oProp->aPageSlugs = array();
        }
        
    }
    
    /**
     * The content filter callback method.
     * 
     * Alternatively use the `content_{instantiated class name}` method instead.
     * @param  string $sContent
     * @return string
     */
    public function content( $sContent ) {
        return $this->___getProInfo()
            . $this->___getAffiliateInfo()
            . $this->___getAnnouncements();
    }
        /**
         * @return      string
         */    
        private function ___getProInfo() {
            $_sProImage = esc_url( AmazonAutoLinks_Registry::getPluginURL( AmazonAutoLinks_Main_Loader::$sDirPath . '/asset/image/information/amazon-auto-links-pro-affiliate-250x250.jpg', true ) );
            return class_exists( 'AmazonAutoLinksPro_Registry', false )
                ? ''
                : "<a href='https://store.michaeluno.jp/amazon-auto-links-pro/amazon-auto-links-pro' target='_blank'>"
                    . "<img style='max-width: 100%; max-width:250px;' src='{$_sProImage}' alt='Amazon Auto Links Pro'/>"
                . "</a>";            
        }
        
        /**
         * @return      string
         */
        private function ___getAffiliateInfo() {
            $_oOption = AmazonAutoLinks_Option::getInstance();
            $_bJoinedAffiliate = $_oOption->get( 'miunosoft_affiliate', 'affiliate_id' );            
            $_sLink = 'https://store.michaeluno.jp/amazon-auto-links-pro/affiliate-area/';
            return $_bJoinedAffiliate
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
                        . esc_url( AmazonAutoLinks_Registry::getPluginURL( AmazonAutoLinks_Main_Loader::$sDirPath . '/asset/image/tip/credit_link.jpg', true ) )
                        . "' alt='" . __( 'Credit Link', 'amazon-auto-links' ) . "'/>"
                    . "</a>"
                    ;
        }

        /**
         * @return string   The plugin announcement output.
         */
        private function ___getAnnouncements() {
            $_sOutput = '';
            $_oRSS    = new AmazonAutoLinks_RSSClient( 'https://feeds.feedburner.com/AmazonAutoLinks_Announcements' );
            $_aItems  = $_oRSS->get();
            foreach( $_aItems as $_aItem ) {
                $_sOutput .= "<h4>" . $_aItem[ 'title' ] . "</h4>"
                    . $_aItem[ 'description' ];
            }
            return $_sOutput;
        }
 
}