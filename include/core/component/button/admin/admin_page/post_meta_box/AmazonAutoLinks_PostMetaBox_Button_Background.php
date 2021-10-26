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
 * Defines the meta box for the button post type.
 */
class AmazonAutoLinks_PostMetaBox_Button_Background extends AmazonAutoLinks_PostMetaBox_Button {

    
    public function setUp() {        

        $_oFields = new AmazonAutoLinks_FormFields_Button_Background( $this );
        foreach( $_oFields->get() as $_aField ) {            
            $this->addSettingFields( $_aField );
        }          
    
    }
    
}