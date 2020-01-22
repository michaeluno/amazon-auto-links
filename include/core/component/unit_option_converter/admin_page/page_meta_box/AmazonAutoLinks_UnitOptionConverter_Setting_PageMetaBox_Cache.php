<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2020 Michael Uno
 */
 
 
class AmazonAutoLinks_UnitOptionConverter_Setting_PageMetaBox_Cache extends AmazonAutoLinks_UnitOptionConverter_Setting_PageMetaBox_Base {
        
    /**
     * Sets up form fields.
     */ 
    public function setUp() {
        
        $_oFields = new AmazonAutoLinks_FormFields_Unit_Cache;
        $_aFields = $_oFields->get();
        foreach( $_aFields as $_aField ) {           
            $this->addSettingFields( $_aField );
        }
        
    }
    
    /**
     * Validates submitted form data.
     */
    public function validate( $aInput, $aOriginal, $oFactory ) {    
        return $aInput;        
    }
    
 
}