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
 * Provides the form fields definitions.
 * 
 * @since           3
 * @since           3.5.0       Changed the parent class to `AmazonAutoLinks_FormFields_ContextualUnit_Main` from `AmazonAutoLinks_FormFields_Base`.
 * @extends         AmazonAutoLinks_FormFields_ContextualUnit_Main
 */
class AmazonAutoLinks_FormFields_Widget_ContextualProduct extends AmazonAutoLinks_FormFields_ContextualUnit_Main {

    /**
     * Returns field definition arrays.
     * 
     * Pass an empty string to the parameter for meta box options. 
     * 
     * @return      array
     */    
    public function get( $sFieldIDPrefix='' ) {

        $_aFields       = array(
            array(
                'field_id'      => $sFieldIDPrefix. 'title', 
                'type'          => 'text',
                'title'         => __( 'Title', 'amazon-auto-links' ),
            ),
            array(
                'field_id'      => $sFieldIDPrefix . 'show_title_on_no_result',
                'type'          => 'checkbox',
                'label'         => __( 'Show widget title on no result.', 'amazon-auto-links' ),
                'default'       => true,
            ),
        );
        return array_merge( $_aFields, parent::get( $sFieldIDPrefix ) );
           
    }
      
}