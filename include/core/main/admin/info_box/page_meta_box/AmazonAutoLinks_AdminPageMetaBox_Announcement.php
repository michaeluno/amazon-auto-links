<?php
/**
 * Auto Amazon Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2022 Michael Uno
 */
 
class AmazonAutoLinks_AdminPageMetaBox_Announcement extends AmazonAutoLinks_PageMetaBox_Base {
        
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
        return "<div class='announcements'>"
                . $this->___getProInfo()
                . $this->___getAffiliateInfo()
                . $this->___getCooperatorsWanted()
            . "</div>";
            // . $this->___getAnnouncements();
    }
        /**
         * @return      string
         */    
        private function ___getProInfo() {
            $_sProImage = esc_url( AmazonAutoLinks_Registry::getPluginURL( AmazonAutoLinks_Main_Loader::$sDirPath . '/asset/image/information/auto-amazon-links-pro-banner-250x250.jpg', true ) );
            return class_exists( 'AmazonAutoLinksPro_Registry', false )
                ? ''
                : "<div class='announcement-item'><a href='https://store.michaeluno.jp/amazon-auto-links-pro/amazon-auto-links-pro' target='_blank'>"
                    . "<img style='max-width: 100%; max-width:250px;' src='{$_sProImage}' alt='Auto Amazon Links Pro'/>"
                . "</a></div>";
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
                : "<div class='announcement-item'>"
                    . "<h4>"
                        . __( 'Join Affiliate Program', 'amazon-auto-links' )
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
                    . "</div>";
        }

        private function ___getCooperatorsWanted() {
            $_sImageURL = AmazonAutoLinks_Registry::getPluginURL( AmazonAutoLinks_Main_Loader::$sDirPath .'/asset/image/information/cooperators-wanted.gif', true );
            $_sPageURL  = 'https://store.michaeluno.jp/amazon-auto-links-pro/cooperators-wanted/';
            return "<div class='announcement-item'>"
                    . "<a href='" . esc_url( $_sPageURL ) . "' target='_blank' class='no-text-decoration'>"
                        . "<img style='max-width:100%; max-width: 250px;' src='" . esc_url( $_sImageURL ) . "'/>"
                    . "</a>"
                    . "<h4 class='font-size-1point2'>"
                        . "<a href='" . esc_url( $_sPageURL ) . "' target='_blank' class='no-text-decoration'>"
                            . __( 'Cooperators Wanted!', 'amazon-auto-links' )
                        . "</a>"
                    . "</h4>"
                    . "<p>" . __( 'Be one of those and get discounts by contributing to the plugin development.', 'amazon-auto-links' ) . "</p>"
                    . "<ul>"
                        . "<li><strong>" . __( 'Testers', 'amazon-auto-links' ) . "</strong> - " . __( 'who can test the plugin and find problems.', 'amazon-auto-links' ) . "</li>"
                        . "<li><strong>" . __( 'Power Users', 'amazon-auto-links' ) . "</strong> - " . __( 'who can suggest useful features.', 'amazon-auto-links' ) . "</li>"
                        . "<li><strong>" . __( 'Template Designers', 'amazon-auto-links' ) . "</strong> - " . __( 'who can code and design plugin templates.', 'amazon-auto-links' ) . "</li>"
                        . "<li><strong>" . __( 'Graphic Designers', 'amazon-auto-links' ) . "</strong> - " . __( 'who can design visual elements such as images for plugin pages.', 'amazon-auto-links' ) . "</li>"
                        . "<li><strong>" . __( 'Translators', 'amazon-auto-links' ) . "</strong> - " . __( 'who can translate text used in the plugin.', 'amazon-auto-links' ) . "</li>"
                        . "<li>" . __( 'And possibly not listed!', 'amazon-auto-links' ) . "</li>"
                    . "</ul>"
                    . "<p>" . sprintf( __( 'For more details please visit <a href="%1$s" target="_blank">here</a>', 'amazon-auto-links' ), esc_url( $_sPageURL ) ) . "</p>"
                . "</div>"
            ;
        }

        /**
         * @return string   The plugin announcement output.
         * @deprecated 4.3.18
         */
        private function ___getAnnouncements() {
            $_sOutput = '';
            $_oRSS    = new AmazonAutoLinks_RSSClient( 'https://feeds.feedburner.com/AmazonAutoLinks_Announcements?1' );
            $_aItems  = $_oRSS->get();
            foreach( $_aItems as $_aItem ) {
                $_sOutput .= "<h4>" . $_aItem[ 'title' ] . "</h4>"
                    . $_aItem[ 'description' ];
            }
            return $_sOutput;
        }
 
}