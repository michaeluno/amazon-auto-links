<?php
/**
 * Provides the form fields definitions.
 * 
 * @since           3.3.0
 */
class AmazonAutoLinks_FormFields_Button_CSS extends AmazonAutoLinks_FormFields_Base {

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
                'field_id'      => 'button_css',
                'type'          => 'textarea',
                'title'         => __( 'Generated CSS', 'amazon-auto-links' ),
                'description'   => __( 'The generated CSS rules will look like this.', 'amazon-auto-links' ),
                'attributes'    => array(
                    'style' => 'width: 100%; height: 320px;',
                ),
            ),
            array(
                'field_id'      => 'custom_css',
                'type'          => 'textarea',
                'title'         => __( 'Custom CSS', 'amazon-auto-links' ),
                'description'   => __( 'Enter additional CSS rules here.', 'amazon-auto-links' ),
                'attributes'    => array(
                    'style' => 'width: 100%; height: 200px;',
                ),                
            )   
        );
        
    }
      
}