<?php
/**
 * Amazon Auto Links
 * 
 * 
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2015 Michael Uno; Licensed GPLv2
 * 
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
                    __( 'If you set your affiliate ID of Amazon Auto Links Pro here, the credit text at the bottom of in unit oitputs will be linked to the Amazon Auto Links Pro product page.' ),
                ),
                'description'   => array(
                    __( 'Earn commisons by putting a link to the product page of Amazon Auto Links Pro in the credit link of unit outputs.', 'amazon-auto-links' ),
                    sprintf(
                        __( 'You need to <a href="%1$s" target="_blank">sign up</a> first for Amazon Auto Links Pro affiliate program to get commisions.', 'amazon-auto-links' ),
                        'http://store.michaeluno.jp/wp-signup.php'
                    ),
                    'e.g. <code>456</code>',
                ),
            ),
        );
        
    }
  
}