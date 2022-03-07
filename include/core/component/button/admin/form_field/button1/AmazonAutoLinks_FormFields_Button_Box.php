<?php
/**
 * Provides the form fields definitions.
 * 
 * @since           3.3.0
 */
class AmazonAutoLinks_FormFields_Button_Box extends AmazonAutoLinks_FormFields_Base {

    /**
     * Returns field definition arrays.
     * 
     * Pass an empty string to the parameter for meta box options. 
     * 
     * @return      array
     */    
    public function get() {
        
        $_aFields       = array(
            array(
                'field_id'      => 'width',
                'type'          => 'number',
                'title'         => __( 'Width', 'amazon-auto-links' ),
                'attributes'    => array(
                    'data-property' => 'width',
                ),
                'default'       => 100,
            ),             
            array(
                'field_id'      => 'box_shadow_switch',
                'type'          => 'revealer',
                'select_type'   => 'radio',
                'title'         => __( 'Box Shadow Switch', 'amazon-auto-links' ),
                'label'         => array(
                    '.box_shadow_on'  => __( 'On', 'amazon-auto-links' ),
                    '.box_shadow_off' => __( 'Off', 'amazon-auto-links'),
                ),
                'attributes'    => array(
                    '.box_shadow_on'    => array(
                        'data-switch' => '.box_shadow_off',
                    ),                
                    '.box_shadow_off'   => array(
                        'data-switch' => '.box_shadow_on',
                    )                
                ),                
                'default'       => '.box_shadow_off',
            ),
            
            // Revealer item - .box_shadow_on
            array(
                'field_id'      => 'box_shadow_color',
                'type'          => 'color',
                'title'         => __( 'Box Shadow', 'amazon-auto-links' ),
                'class'         => array(
                    'fieldrow' => 'box_shadow_on',
                ),
                'default'       => '#666666',
                'attributes'    => array(
                    'data-property' => 'box-shadow-color',
                ),
            ),
            
            array(
                'field_id'      => 'individual_padding_switch',
                'type'          => 'revealer',
                'select_type'   => 'radio',
                'title'         => __( 'Individual Padding', 'amazon-auto-links' ),
                'label'         => array(
                    '.individual_padding_on'  => __( 'On', 'amazon-auto-links' ),
                    '.individual_padding_off' => __( 'Off', 'amazon-auto-links'),
                ),
                'attributes'    => array(
                    '.individual_padding_on'  => array(
                        'data-switch' => '.individual_padding_off',
                    ),                
                    '.individual_padding_off' => array(
                        // this is not '0'. Because the JavaScript script needs to enable the elements.
                        'data-switch' => '.individual_padding_on',
                    ),          
                ),                                
                'default'       => '.individual_padding_on',
            ),            
            
            // Revealer item - .individual_padding_off
            array(
                'field_id'      => 'padding',
                'type'          => 'number',
                'title'         => __( 'Padding', 'amazon-auto-links' ),
                'class'          => array(
                    'fieldrow'  => 'individual_padding_off',
                ),
                'attributes'    => array(
                    'data-property' => 'padding',
                ),
                'default'       => 8,
            ),            
            
            // Revealer item - .individual_padding_on
            array(
                'field_id'      => 'padding_top',
                'type'          => 'number',
                'title'         => __( 'Padding Top', 'amazon-auto-links' ),
                'class'          => array(
                    'fieldrow'  => 'individual_padding_on',
                ),      
                'attributes'    => array(
                    'data-property' => 'padding-top',
                ),                
                'default'       => 7,
            ),
            array(
                'field_id'      => 'padding_right',
                'type'          => 'number',
                'title'         => __( 'Padding Right', 'amazon-auto-links' ),
                'class'          => array(
                    'fieldrow'  => 'individual_padding_on',
                ),    
                'attributes'    => array(
                    'data-property' => 'padding-right',
                ),                                
                'default'       => 8,                
            ),
            array(
                'field_id'      => 'padding_bottom',
                'type'          => 'number',
                'title'         => __( 'Padding Bottom', 'amazon-auto-links' ),
                'class'          => array(
                    'fieldrow'  => 'individual_padding_on',
                ),                  
                'attributes'    => array(
                    'data-property' => 'padding-bottom',
                ),                                
                'default'       => 8,
            ),            
            array(
                'field_id'      => 'padding_left',
                'type'          => 'number',
                'title'         => __( 'Padding Left', 'amazon-auto-links' ),
                'class'          => array(
                    'fieldrow'  => 'individual_padding_on',
                ),                                
                'attributes'    => array(
                    'data-property' => 'padding-left',
                ),                                
                'default'       => 8,
            )
        );
        return $_aFields;
        
    }
      
}