<?php
/**
 * Provides the form fields definitions.
 * 
 * @since 5.2.0
 */
abstract class AmazonAutoLinks_Button_ButtonType_FormFields_CSS_Base extends AmazonAutoLinks_FormFields_Base {

    /**
     * @remark Set a button type slug so that `_button_type` meta hidden field will be added.
     * @since  5.2.0
     * @var    string Stores the button type slug.
     */
    protected $_sButtonType = '';

    /**
     * Returns field definition arrays.
     * 
     * Pass an empty string to the parameter for meta box options. 
     * 
     * @return array
     * @since  3.3.0
     * @since  5.2.0 Moved from `AmazonAutoLinks_FormFields_Button_CSS`.
     */    
    public function get() {
        
        $_aFields = array(
            array(
                'field_id'      => 'button_css',
                'type'          => 'textarea',
                'title'         => __( 'Generated CSS', 'amazon-auto-links' ),
                'description'   => __( 'The generated CSS rules will look like this.', 'amazon-auto-links' ),
                'attributes'    => array(
                    'style'    => 'width: 100%; height: 320px;',
                    'readonly' => 'readonly',
                ),
            ),
            array(
                'field_id'      => 'custom_css',
                'type'          => 'textarea',
                'title'         => __( 'Custom CSS', 'amazon-auto-links' ),
                'description'   => __( 'Enter additional CSS rules here.', 'amazon-auto-links' ),
                'attributes'    => array(
                    'style'         => 'width: 100%; height: 200px;',
                    'data-property' => '_custom_css',   // for JavaScript to generate a stylesheet for button previews
                ),
                'class'     => array(
                    'field' => 'dynamic-button-field',
                ),
            )   
        );
        if ( $this->_sButtonType ) {
            $_aFields[] = array(
                'field_id' => '_button_type',
                'type'     => 'hidden',
                'value'    => $this->_sButtonType,
                'hidden'   => true,
            );
        }
        return $_aFields;
    }

}