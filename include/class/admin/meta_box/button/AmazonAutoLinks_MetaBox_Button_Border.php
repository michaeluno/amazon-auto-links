<?php
/**
 * Amazon Auto Links
 * 
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2015 Michael Uno
 * 
 */

/**
 * Defines the meta box for the button post type.
 */
class AmazonAutoLinks_MetaBox_Button_Border extends AmazonAutoLinks_MetaBox_Button {

    
    public function setUp() {    

        $this->addSettingFields(
            array(
                'field_id'      => 'border_radius',
                'type'          => 'number',
                'title'         => __( 'Border Radius', 'amazon-auto-links' ),
                'default'       => 4,
                'attributes'    => array(
                    'min'           => 0,
                    'data-property' => 'border-radius',
                ),
            ),
            array(
                'field_id'      => 'border_style_switch',
                'type'          => 'revealer',
                'select_type'   => 'radio',
                'title'         => __( 'Border Style Switch', 'amazon-auto-links' ),
                'label'         => array(
                    '.border_style_on' => __( 'On', 'amazon-auto-links' ),
                    '.border_style_off' => __( 'Off', 'amazon-auto-links' ),
                ),
                'attributes'    => array(
                    '.border_style_on' => array(
                        'data-switch' => '.border_style_off',
                    ),                
                    '.border_style_off' => array(
                        'data-switch' => '.border_style_on',
                    ), 
                ),                                          
                'default'       => '.border_style_off',
            ),
            // Revealer items
            array(
                'field_id'      => 'border_color',
                'type'          => 'color',
                'title'         => __( 'Border Color', 'amazon-auto-links' ),
                'class'         => array(
                    'fieldrow'  => 'border_style_on',
                ),              
                'attributes'    => array(
                    'data-property' => 'border-color',
                ),                
                'default'       => '#1f628d',
            ),
            array(
                'field_id'      => 'border_style',
                'type'          => 'select',
                'title'         => __( 'Border Style', 'amazon-auto-links' ),
                'label'         => array(
                    'solid'  => __( 'Solid', 'amazon-auto-links' ),
                    'dotted' => __( 'Dotted', 'amazon-auto-links' ),
                ),
                'attributes'    => array(
                    'select'    => array(
                        'data-property' => 'border-style',
                    )
                ),                  
                'class'         => array(
                    'fieldrow'  => 'border_style_on',
                ),
            ),
            array(
                'field_id'      => 'border_width',
                'type'          => 'number',
                'title'         => __( 'Border Width', 'amazon-auto-links' ),
                'attributes'    => array(
                    'data-property' => 'border-width',
                ),                       
                'class'         => array(
                    'fieldrow'  => 'border_style_on',
                ),       
                'default'       => 1,
            )
        );    
    
    }
    
    
}