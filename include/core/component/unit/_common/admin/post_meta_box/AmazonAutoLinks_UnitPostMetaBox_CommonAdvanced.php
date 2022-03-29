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
 * Defines the meta box that contains common advanced unit options.
 */
class AmazonAutoLinks_UnitPostMetaBox_CommonAdvanced extends AmazonAutoLinks_UnitPostMetaBox_Base {
    
    /**
     * Sets up form fields.
     */ 
    public function setUp() {
        
        $_aClasses = array(
            'AmazonAutoLinks_FormFields_Unit_CommonAdvanced',
        );
        foreach( $_aClasses as $_sClassName ) {
            $_oFields = new $_sClassName( $this );
            $_aFields = $_oFields->get();
            foreach( $_aFields as $_aField ) {           
                $this->addSettingFields( $_aField );
            }            
        }            

    }

    /**
     * Validates submitted form data.
     * @return array
     */
    public function validate( $aInputs, $aOldInputs, $oFactory ) {
        return $aInputs;
    }
    
}