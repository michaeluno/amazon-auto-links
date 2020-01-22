<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2020 Michael Uno
 */

/**
 * Defines the meta box for the button post type.
 */
class AmazonAutoLinks_PostMetaBox_Button_Text extends AmazonAutoLinks_PostMetaBox_Button {

    public function setUp() {        
    
        $_oFields = new AmazonAutoLinks_FormFields_Button_Text;
        foreach( $_oFields->get() as $_aField ) {            
            $this->addSettingFields( $_aField );
        }      
                
    }
    
    
}