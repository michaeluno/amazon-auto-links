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
class AmazonAutoLinks_Button_Image_FormFields_Dimensions extends AmazonAutoLinks_Button_ButtonType_FormFields_Dimensions_Base {

    /**
     * Returns field definition arrays.
     * 
     * Pass an empty string to the parameter for meta box options. 
     * 
     * @return array
     */    
    public function get( $sFieldIDPrefix='', $sUnitType='' ) {
        $_aFieldsets = parent::get( $sFieldIDPrefix, $sUnitType );
        $this->unsetDimensionalArrayElement( $_aFieldsets, array( 0, 'title', ) );
        $this->setMultiDimensionalArray(
            $_aFieldsets,
            array( 0, 'content', 0, 'default', ),
            true
        );
        $this->setMultiDimensionalArray(
            $_aFieldsets,
            array( 0, 'content', 2, 'default', ),
            true
        );
        return $_aFieldsets;
    }
      
}