<?php
/**
 * Provides the form fields definitions.
 * 
 * @since           3.3.0
 */
class AmazonAutoLinks_FormFields_Button_Background extends AmazonAutoLinks_FormFields_Base {

    /**
     * Returns field definition arrays.
     * 
     * Pass an empty string to the parameter for meta box options. 
     * 
     * @return      array
     */    
    public function get() {
        
        return array(
            array(
                'field_id'      => 'background_switch',
                'type'          => 'revealer',
                'select_type'   => 'radio',
                'title'         => __( 'Background Switch', 'amazon-auto-links' ),
                'label'         => array(
                    '.background_on'  => __( 'On', 'amazon-auto-links' ),
                    '.background_off' => __( 'Off', 'amazon-auto-links' ),
                ),
                'attributes'    => array(
                    '.background_on'  => array(
                        'data-switch' => '.background_off',
                    ),                
                    '.background_off' => array(
                        'data-switch' => '.background_on',
                    ), 
                ),       
                'default'       => '.background_on',
            ),        
            array(
                'field_id'      => 'background_type',
                'type'          => 'revealer',
                'select_type'   => 'radio',
                'title'         => __( 'Background Type', 'amazon-auto-links' ),
                'label'         => array(
                    '.gradient'  => __( 'Gradient', 'amazon-auto-links' ),
                    '.solid'     => __( 'Solid', 'amazon-auto-links' ),
                ),
                'class'         => array(
                    'fieldrow'  => 'background_on',
                ),                 
                'attributes'    => array(
                    '.gradient' => array(
                        'data-property'         => 'background-gradient',
                        'data-control-display'  => 'background-gradient',
                        'data-switch'           => '.solid',
                    ),
                    '.solid' => array(
                        'data-property'         => 'background-solid',
                        'data-control-display'  => 'background-solid',
                        'data-switch'           => '.gradient',
                    ),                    
                ),                   
                'default'       => '.gradient',
            ),
            array(
                'field_id'      => 'background',
                'type'          => 'color',
                'title'         => __( 'Background Solid Color', 'amazon-auto-links' ),
                'class'         => array(
                    'fieldrow' => 'solid background_on',
                ),
                'attributes'    => array(
                    'data-property' => 'background',
                ),
                'default'       => '#4997e5', // '#3498db',
            ),
            
            array(
                'field_id'      => 'bg_start_gradient',
                'type'          => 'color',
                'title'         => __( 'Gradient Start Color', 'amazon-auto-links' ),
                'class'         => array(
                    'fieldrow' => 'gradient background_on',
                ),            
                'attributes'    => array(
                    'data-property' => 'bg-start-gradient',
                ),
                'default'       => '#4997e5', // '#3498db',      
            ),
            array(
                'field_id'      => 'bg_end_gradient',
                'type'          => 'color',
                'title'         => __( 'Gradient End Color', 'amazon-auto-links' ),
                'class'         => array(
                    'fieldrow' => 'gradient background_on',
                ),      
                'attributes'    => array(
                    'data-property' => 'bg-end-gradient',
                ),                
                'default'       => '#3f89ba', // '#2980b9',
            )         
        );
        
    }
      
}