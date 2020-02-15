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
 * Defines the meta box that contains Template options.
 */
class AmazonAutoLinks_UnitPostMetaBox_Template extends AmazonAutoLinks_UnitPostMetaBox_Base {
        
    /**
     * Sets up form fields.
     */ 
    public function setUp() {

        // @deprecated 4.0.0
        // $_oFields = new AmazonAutoLinks_FormFields_Unit_Template;
        $_oFields = new AmazonAutoLinks_FormFields_Unit_Template_EachItemOptionSupport;
        $_aFields = $_oFields->get(
            '',     // field id prefix
            'category'  // unit type
        );
        foreach( $_aFields as $_aField ) {           
            $this->addSettingFields( $_aField );
        }
            
    }
    
    /**
     * Validates submitted form data.
     */
    public function validate( $aInputs, $aOldInputs, $oFactory ) {    
        
        // Sanitize format options.
        $_oItemFormatValidator = new AmazonAutoLinks_FormValidator_ItemFormat( $aInputs, $aOldInputs );
        $aInputs = $_oItemFormatValidator->get();        
                
        // Schedule pre-fetch for the unit if the options have been changed.
        if ( $aInputs !== $aOldInputs ) {
            AmazonAutoLinks_Event_Scheduler::prefetch( 
                AmazonAutoLinks_PluginUtility::getCurrentPostID()
            );
        }
        
        return $aInputs;
        
    }
    
}