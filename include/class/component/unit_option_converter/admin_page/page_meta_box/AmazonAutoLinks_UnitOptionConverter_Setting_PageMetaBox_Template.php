<?php
/**
 * Amazon Auto Links
 * 
 * http://en.michaeluno.jp/amazon auto links/
 * Copyright (c) 2013-2015 Michael Uno
 * 
 */
 
 
class AmazonAutoLinks_UnitOptionConverter_Setting_PageMetaBox_Template extends AmazonAutoLinks_UnitOptionConverter_Setting_PageMetaBox_Base {
      

    /**
     * Sets up form fields.
     */ 
    public function setUp() {
        
        $_oFields = new AmazonAutoLinks_FormFields_Unit_Template;
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