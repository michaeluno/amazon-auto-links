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
 * Provides methods to generate credit links.
 * 
 * @since       3.2.3
 */
class AmazonAutoLinks_Credit extends AmazonAutoLinks_PluginUtility {

    /**
     * Sets up hooks.
     */
    public function __construct() {
        
        add_filter(
            'aal_filter_credit_comment',
            array( $this, 'replyToGetCreditComment' )
        );        
        
        add_filter( 
            'aal_filter_credit_link_0',
            array( $this, 'replyToGetCreditLink_0' ),
            10,
            2
        );

        add_filter( 
            'aal_filter_credit_link_1',
            array( $this, 'replyToGetCreditLink_1' ),
            10,
            2
        );

        add_filter( 
            'aal_filter_credit_link_2',
            array( $this, 'replyToGetCreditLink_2' ),
            10,
            2
        );
        
    }

    /**
     * @return      string
     * @since       3.2.3
     */
    public function replyToGetCreditComment( $sCredit ) {
        return $sCredit
            . self::getCommentCredit();
    }

    /**
     * @return      string
     */
    public function replyToGetCreditLink_0( $sCredit, $oOption ) {            
                            
        $_sVendorURL = $this->_getVendorURL( $oOption );
        return $sCredit
            . "<div class='amazon-auto-links-credit' style='width: 100%;'>"
                . "<span style='margin: 1em 0.4em; float: right; clear: both; background-image: url(" . esc_url( AmazonAutoLinks_Registry::getPluginUrl( 'asset/image/menu_icon_16x16.png' ) ) . "); background-repeat:no-repeat; background-position: 0% 50%; padding-left: 20px; font-size: smaller;'>"
                    ."<a href='" . esc_url( $_sVendorURL ) . "' title='" . esc_attr( AmazonAutoLinks_Registry::DESCRIPTION ) . "' rel='author' target='_blank' style='border: none;'>"
                        . AmazonAutoLinks_Registry::NAME
                    . "</a>"
                . "</span>"
            . "</div>";
            
    }
    public function replyToGetCreditLink_1( $sCredit, $oOption ) {            
                            
        $_sVendorURL = $this->_getVendorURL( $oOption );
        $_sName      = esc_attr( AmazonAutoLinks_Registry::NAME );
        $_sImageURL  = esc_url( AmazonAutoLinks_Registry::getPluginURL( 'asset/image/credit/amazon-auto-links-250x250.jpg' ) );
        return $sCredit
            . "<div class='amazon-auto-links-credit' style='width: 100%; max-width: 100%;'>"
                . "<a href='{$_sVendorURL}' target='_blank'>"
                    . "<img alt='{$_sName}' src='{$_sImageURL}' style='max-width: 100%; margin-left: auto; margin-right: auto; display:block;' />"
                . "</a>"
            . "</div>";
            
    }
    public function replyToGetCreditLink_2( $sCredit, $oOption ) {            

        $_sVendorURL = $this->_getVendorURL( $oOption );
        $_sName      = esc_attr( AmazonAutoLinks_Registry::NAME );
        $_sImageURL  = esc_url( AmazonAutoLinks_Registry::getPluginURL( 'asset/image/credit/amazon-auto-links-horizontal.jpg' ) );

        return $sCredit
            . "<div class='amazon-auto-links-credit' style='width: 100%; max-width: 100%;'>"
                . "<a href='{$_sVendorURL}' target='_blank'>"
                    . "<img alt='{$_sName}' src='{$_sImageURL}' style='max-width: 100%; margin-left: auto; margin-right: auto; display:block;' />"
                . "</a>"
            . "</div>";
            
    }
    
    /**
     * @return      string
     */
    private function _getVendorURL( $oOption ) {
        
        $_sQueryKey  = $oOption->get( 'query', 'cloak' );
        return add_query_arg(
            array(
                $_sQueryKey => 'vendor',
            ),
            site_url()
        );        
        
    }
    
        
}