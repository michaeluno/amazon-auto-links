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
 * Provides the definitions of form fields.
 * 
 * @since           3.2.0
 */
class AmazonAutoLinks_FormFields_Setting_MiunosoftAffiliate extends AmazonAutoLinks_FormFields_Base {

    /**
     * Returns field definition arrays.
     * 
     * Pass an empty string to the parameter for meta box options. 
     * 
     * @return      array
     */    
    public function get( $sFieldIDPrefix='' ) {
                        
        return array(
            array(
                'field_id'      => 'affiliate_id',
                'type'          => 'text',
                'title'         => __( 'Amazon Auto Links Pro Affiliate ID', 'amazon-auto-links' ),
                'tip'           => array(
                    "<img style='float: left; margin: 1em 1.6em 1em 0.5em; text-align:center; max-height: 200px;' src='"
                        . esc_url( AmazonAutoLinks_Registry::getPluginURL( 'asset/image/tip/credit_link.jpg' ) )
                        . "' alt='" . __( 'Credit Link', 'amazon-auto-links' ) . "'/>",
                    __( 'If you set your affiliate ID of Amazon Auto Links Pro here, the credit text at the bottom of unit outputs will be linked to the Amazon Auto Links Pro product page.', 'amazon-auto-links' ),
                ),
                'description'   => array(
                    __( 'Earn commissions by putting a link to the product page of Amazon Auto Links Pro in the credit link of unit outputs.', 'amazon-auto-links' ),
                    sprintf(
                        __( 'You need to <a href="%1$s" target="_blank">sign up</a> first for Amazon Auto Links Pro affiliate program to get commisions.', 'amazon-auto-links' ),
                        'https://store.michaeluno.jp/amazon-auto-links-pro/affiliate-area/'
                    ),
                    'e.g. <code>456</code>',
                ),
            )
        );
        
    }
  
}