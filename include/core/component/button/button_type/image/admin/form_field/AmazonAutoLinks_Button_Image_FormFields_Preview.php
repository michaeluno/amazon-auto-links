<?php
/**
 * Provides the form fields definitions.
 * 
 * @since 5.2.0
 */
class AmazonAutoLinks_Button_Image_FormFields_Preview extends AmazonAutoLinks_FormFields_Base {

    /**
     * Returns field definition arrays.
     * 
     * Pass an empty string to the parameter for meta box options. 
     * 
     * @return array
     */    
    public function get( $sFieldIDPrefix='', $sUnitType='category' ) {
        return array(
            array(
                'field_id'          => $sFieldIDPrefix . '_button_preview',
                'show_title_column' => false,
                'content'           => AmazonAutoLinks_Button_Utility::getIframeButtonPreview(
                    $this->getHTTPQueryGET( array( 'post' ), '___button_id___' ),
                    'image',
                    '',
                    array(),
                    array( 'id' => 'button-image-preview', )
                ),
            )
        );
    }

}