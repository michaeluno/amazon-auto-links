<?php
/**
 * Auto Amazon Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2022 Michael Uno
 */

/**
 * Provides the form fields definitions.
 * 
 * @since 5.2.0
 */
class AmazonAutoLinks_Button_Button2_FormFields_Preview extends AmazonAutoLinks_FormFields_Base {

    /**
     * Returns field definition arrays.
     * 
     * Pass an empty string to the parameter for meta box options.
     * @return array
     */    
    public function get( $sFieldIDPrefix='', $sUnitType='category' ) {
        return array(
            array(
                'field_id'          => $sFieldIDPrefix . 'button_preview',                'show_title_column' => false,
                // 'before_field'      => "<div style='margin: 3em 3em 3em 0; width:100%;'>"
                //     . "<div style='margin-left: auto; margin-right: auto; '>" // text-align:center;
                //         . '<p>Iframed button preview will be displayed here</p>'
                //     . "</div>"
                // . "</div>",
                'content'           => AmazonAutoLinks_Button_Utility::getIframeButtonPreview(
                    $this->getHTTPQueryGET( array( 'post' ), '___button_id___' ),
                    'button2',
                    __( 'Buy Now', 'amazon-auto-links' ),
                    array(),
                    array( 'id' => 'button-preview-button2', )
                ),
            )
        );
    }
      
}