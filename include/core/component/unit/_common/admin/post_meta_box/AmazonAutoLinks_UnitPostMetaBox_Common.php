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
 * Defines the meta box that contains common advanced unit options.
 * 
 * @since       3.1.0
 */
class AmazonAutoLinks_UnitPostMetaBox_Common extends AmazonAutoLinks_UnitPostMetaBox_Base {
        
    /**
     * Sets up form fields.
     */ 
    public function setUp() {
        
        $_aClasses = array(
            'AmazonAutoLinks_FormFields_Unit_Common',
        );
        foreach( $_aClasses as $_sClassName ) {
            $_oFields = new $_sClassName;
            $_aFields = $_oFields->get();
            foreach( $_aFields as $_aField ) {           
                $this->addSettingFields( $_aField );
            }            
        }            
        
    }
    
    /**
     * Validates submitted form data.
     */
    public function validate( $aInputs, $aOriginal, $oFactory ) {    
        return $aInputs;
    }
    
}