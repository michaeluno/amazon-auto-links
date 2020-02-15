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
 * @since       3.4.0
 */
class AmazonAutoLinks_AdminPage_Setting_Default_PageMetaBox_Template extends AmazonAutoLinks_AdminPage_Setting_Default_PageMetaBox_Base {
      

    /**
     * Sets up form fields.
     */ 
    public function setUp() {
        
        $this->addSettingSections(
            array(
                'section_id'    => $this->_sSectionID,
            )
        );        
        $this->addSettingFields( $this->_sSectionID );         

        // @deprecated 4.0.0 To support default item format options for each template
        // $_oFields = new AmazonAutoLinks_FormFields_Unit_Template;
        $_oFields = new AmazonAutoLinks_FormFields_Unit_Template_EachItemOptionSupport; // 4.0.0+
        $_aFields = $_oFields->get();
        foreach( $_aFields as $_aField ) {           
            $this->addSettingFields( $_aField );
        }
            
    }      
 
   
    /**
     * Validates submitted form data.
     */
    public function validate( $aInputs, $aOriginal, $oFactory ) {
        return $aInputs;
    }
    
}
