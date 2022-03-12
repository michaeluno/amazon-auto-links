<?php
/**
 * Provides the form fields definitions.
 * 
 * @since 5.2.0
 */
class AmazonAutoLinks_Button_Image_FormFields_ButtonImage extends AmazonAutoLinks_FormFields_Base {

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
                'field_id'          => $sFieldIDPrefix . '_image_url',
                'type'              => 'image',
                'title'             => __( 'Select', 'amazon-auto-links' ),
                'show_title_column' => true,
                'attributes'        => array(
                    // 'name'               => '',
                    'input' => array(
                        'style'          => 'min-width: 460px;',
                        'data-property'  => 'background-image',      // the js script checks this value
                        'data-unchanged' => 1,
                    ),
                ),
                'class'     => array(
                    'field' => 'dynamic-button-field',
                    // 'input' => 'dynamic-button'  // causes the select and remove button broken
                ),
                'show_preview'      => false,
                'default'           => $this->getSRCFromPath( AmazonAutoLinks_Button_Loader::$sDirPath . '/asset/image/button/amazon-cart-rounded.png' ),
                'save'              => true,    // the saved value will be referred when rendering a button
            )
        );
    }
      
}