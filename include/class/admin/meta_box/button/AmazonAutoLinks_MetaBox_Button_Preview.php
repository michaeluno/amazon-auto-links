<?php
/**
 * Amazon Auto Links
 * 
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2015 Michael Uno
 * 
 */

/**
 * Defines the meta box that shows a button preview.
 */
class AmazonAutoLinks_MetaBox_Button_Preview extends AmazonAutoLinks_MetaBox_Button {

    public function setUp() {
               
        $_oFIelds = new AmazonAutoLinks_FormFields_Button_Preview;
        $_aFields = $_oFIelds->get();
        foreach( $_aFields as $_aField ) {
            $this->addSettingFields( $_aField );
        }
        
    }   
    
}