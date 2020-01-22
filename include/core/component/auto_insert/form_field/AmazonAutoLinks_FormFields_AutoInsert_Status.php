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
 * @since           3  
 */
class AmazonAutoLinks_FormFields_AutoInsert_Status extends AmazonAutoLinks_FormFields_Base {

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
                'field_id'      => $sFieldIDPrefix . '_status_title',
                'type'          => 'hidden',
                'attributes'    => array(
                    'name' => '', // disables the value to be sent to the form
                ),
                'show_title_column'    => false,
                'before_field'  => "<h3>" 
                        .  __( 'Status', 'amazon-auto-links' )
                    . "</h3>"
                    . "<p>"
                        . __( 'Switch On / Off', 'amazon-auto-links' )
                    . "</p>",
            ),
            array(
                'field_id'      => $sFieldIDPrefix . 'status',
                'title'         => __( 'Toggle Status', 'amazon-auto-links' ),
                'type'          => 'radio',
                'label'         => array(
                    1 => __( 'On', 'amazon-auto-links' ),
                    0 => __( 'Off', 'amazon-auto-links' ),
                ),
                'default'       => 1,
                'description'   => __( 'Use this to temporarily disable this auto-insertion.', 'amazon-auto-links' ),
            ),
        );
    }
  
}