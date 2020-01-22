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
 * @since           3.1.0
 */
class AmazonAutoLinks_FormFields_Setting_ExternalScript extends AmazonAutoLinks_FormFields_Base {

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
                'field_id'      => 'impression_counter_script',
                'type'          => 'checkbox',
                'title'         => __( 'Impression Counter', 'amazon-auto-links' ),
                'label'         => sprintf( 
                    __( 'Add the Amazon impression counter, the <a href="%1$s" target="_blank">Link Update JavaScript script</a>, to track link impressions.', 'amazon-auto-links' ),
                    'https://affiliate-program.amazon.com/gp/associates/tips/impressions.html'
                ),                
                'tip'           => array(
                    __( 'The script is not available for the following locales.', 'amazon-auto-links' )
                        . ' ' .  __( 'Italy', 'amazon-auto-links' )
                        . ', ' . __( 'Australia', 'amazon-auto-links' )
                        . ', ' . __( 'Spain', 'amazon-auto-links' )
                        . ', ' . __( 'Mexico', 'amazon-auto-links' )
                ),
            ),
        );
        
    }
  
}