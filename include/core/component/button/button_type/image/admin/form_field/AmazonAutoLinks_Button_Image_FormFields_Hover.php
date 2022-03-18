<?php
/**
 * Provides the form fields definitions.
 * 
 * @since 5.2.0
 */
class AmazonAutoLinks_Button_Image_FormFields_Hover extends AmazonAutoLinks_FormFields_Base {

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
                'field_id'          => $sFieldIDPrefix . '_hover_scale',
                'type'              => 'checkbox',
                'title'             => __( 'Scale', 'amazon-auto-links' ),
                'label'             => __( 'Slightly scale the button on mouse hover.', 'amazon-auto-links' ),
                'default'           => true,
                'class'     => array(
                    'field' => 'dynamic-button-field',
                ),
                'attributes' => array(
                    'data-property' => '_hover_scale',
                ),
            ),
             array(
                'field_id'          => $sFieldIDPrefix . '_hover_brightness',
                'type'              => 'checkbox',
                'title'             => __( 'Brightness', 'amazon-auto-links' ),
                'label'             => __( 'Slightly change the brightness of the button on mouse hover.', 'amazon-auto-links' ),
                'default'           => true,
                'class'     => array(
                    'field' => 'dynamic-button-field',
                ),
                'attributes' => array(
                    'data-property' => '_hover_brightness',
                ),
            ),
        );
    }
      
}