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
class AmazonAutoLinks_PostMetaBox_Button_Hover extends AmazonAutoLinks_PostMetaBox_Button {

    
    public function setUp() {       
    
        $this->addSettingFields(
            array(
                'field_id'      => 'hover_switch',
                'type'          => 'revealer',
                'select_type'   => 'radio',
                'title'         => __( 'Hover Switch', 'amazon-auto-links' ),
                'label'         => array(
                    '.hover_on'  => __( 'On', 'amazon-auto-links' ),
                    '.hover_off' => __( 'Off', 'amazon-auto-links' ),
                ),
                'attributes'    => array(
                    '.hover_on'  => array(
                        'data-switch' => '.hover_off',
                    ),                
                    '.hover_off' => array(
                        'data-switch' => '.hover_on',
                    ),
                ),                
                'default'       => '.hover_on',
            ),              
            array(
                'field_id'      => 'hover_background_type',
                'type'          => 'revealer',
                'select_type'   => 'radio',
                'title'         => __( 'Hover Background Type', 'amazon-auto-links' ),
                'label'         => array(
                    '.hover_gradient'  => __( 'Gradient', 'amazon-auto-links' ),
                    '.hover_solid'     => __( 'Solid', 'amazon-auto-links' ),
                ),
                'class'         => array(
                    'fieldrow'  => 'hover_on',
                ),
                'attributes'    => array(
                    '.hover_gradient' => array(
                        'data-property'        => 'background-gradient-hover',                    
                        'data-control-display' => 'hover-background-gradient',
                        'data-switch'          => '.hover_solid',
                    ),
                    '.hover_solid' => array( 
                        'data-property'        => 'background-solid-hover',
                        'data-control-display' => 'hover-background-solid',
                        'data-switch'          => '.hover_gradient',
                    ),                    
                ),
                'default'       => '.hover_gradient',
            ),
            array(
                'field_id'      => 'hover_background',
                'type'          => 'color',
                'title'         => __( 'Background Solid Color', 'amazon-auto-links' ),
                'class'         => array(
                    'fieldrow' => 'hover_solid hover_on',
                ),
                'attributes'    => array(
                    'data-property' => 'background-hover',
                ),                
                'default'       => '#3cb0fd', 
            ),
            
            array(
                'field_id'      => 'bg_start_gradient_hover',
                'type'          => 'color',
                'title'         => __( 'Gradient Start Color', 'amazon-auto-links' ),
                'class'         => array(
                    'fieldrow' => 'hover_gradient hover_on',
                ),                
                'attributes'    => array(
                    'data-property' => 'bg-start-gradient-hover',
                ),                                
                'default'       => '#3cb0fd', 
            ),
            array(
                'field_id'      => 'bg_end_gradient_hover',
                'type'          => 'color',
                'title'         => __( 'Gradient End Color', 'amazon-auto-links' ),
                'class'         => array(
                    'fieldrow' => 'hover_gradient hover_on',
                ),       
                'attributes'    => array(
                    'data-property' => 'bg-end-gradient-hover',
                ),                                                
                'default'      => '#3498db',
            )            
        );        
        
    }
        
}