<?php
/**
 * Auto Amazon Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 */

/**
 * Defines the meta box that shows a button preview.
 *
 * @since 5.2.0
 */
class AmazonAutoLinks_Button_Image_PostMetaBox_CSS extends AmazonAutoLinks_Button_Image_PostMetaBox_Base {

    public function setUp() {
        $_oFields = new AmazonAutoLinks_Button_Image_FormFields_CSS( $this );
        foreach( $_oFields->get() as $_aField ) {            
            $this->addSettingFields( $_aField );
        }
    }
    
}