<?php
/**
 * Auto Amazon Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2022 Michael Uno
 */

/**
 * Provides the form fields definitions.
 * 
 * @since 5.2.0
 */
class AmazonAutoLinks_Button_Flat_FormFields_Border extends AmazonAutoLinks_FormFields_Base {

    /**
     * Returns field definition arrays.
     * 
     * Pass an empty string to the parameter for meta box options. 
     * 
     * @return array
     */    
    public function get( $sFieldIDPrefix='', $sUnitType='' ) {
        return array(
            array(
                'field_id'      => '_border_radius',
                'type'          => 'number',
                'title'         => __( 'Radius', 'amazon-auto-links' ),
                'default'       => 4,
                'attributes'    => array(
                    'min'           => 0,
                    'data-property' => 'border-radius',
                    'data-suffix'   => 'px',
                ),
                'class'         => array(
                    'field'     => 'dynamic-button-field',
                ),
            ),
            array(
                'field_id'      => '_border_style',
                'type'          => 'revealer',
                'title'         => __( 'Style', 'amazon-auto-links' ),
                'select_type'   => 'radio',
                'label'         => array(
                    'none'   => __( 'None', 'amazon-auto-links' ),
                    'solid'  => __( 'Solid', 'amazon-auto-links' ),
                    'dotted' => __( 'Dotted', 'amazon-auto-links' ),
                ),
                'selectors'     => array(
                    'none'   => '.none',
                    'solid'  => '.border-solid',
                    'dotted' => '.border-dotted',
                ),
                'attributes'    => array(
                    'data-property' => 'border-style',
                ),
                'class'         => array(
                    'field'     => 'dynamic-button-field',
                ),
                'default' =>    'none',
            ),
            array(
                'field_id'      => '_border_color',
                'type'          => 'color',
                'title'         => __( 'Color', 'amazon-auto-links' ),
                'class'         => array(
                    'fieldrow'  => 'border-solid border-dotted',
                    'field'     => 'dynamic-button-field',
                ),
                'attributes'    => array(
                    'data-property' => 'border-color',
                ),
                'default'       => '#1f628d',
            ),
            array(
                'field_id'      => '_border_width',
                'type'          => 'number',
                'title'         => __( 'Width', 'amazon-auto-links' ),
                'attributes'    => array(
                    'data-property' => 'border-width',
                    'data-suffix'   => 'px',
                ),
                'class'         => array(
                    'fieldrow'  => 'border-solid border-dotted',
                    'field'     => 'dynamic-button-field',
                ),
                'default'       => 1,
            )
        );
    }
      
}