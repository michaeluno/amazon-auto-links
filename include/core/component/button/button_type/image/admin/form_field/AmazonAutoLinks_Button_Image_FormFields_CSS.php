<?php
/**
 * Provides the form fields definitions.
 * 
 * @since 5.2.0
 */
class AmazonAutoLinks_Button_Image_FormFields_CSS extends AmazonAutoLinks_Button_ButtonType_FormFields_CSS_Base {

    /**
     * @return array[]
     * @since  5.2.0
     */
    public function get() {
        $_aFieldsets   = parent::get();
        $_aFieldsets[] = array(
            'field_id' => '_button_type',
            'type'     => 'hidden',
            'value'    => 'image',
            'hidden'   => true,
        );
        return $_aFieldsets;
    }

}