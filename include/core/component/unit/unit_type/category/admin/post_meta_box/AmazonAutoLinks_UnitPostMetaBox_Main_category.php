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
 * Defines the meta box added to the category unit definition page.
 */
class AmazonAutoLinks_UnitPostMetaBox_Main_category extends AmazonAutoLinks_UnitPostMetaBox_Base {
    
    /**
     * Stores the unit type slug(s). 
     */    
    protected $aUnitTypes = array( 'category' );
    
    /**
     * Sets up form fields.
     */ 
    public function setUp() {
        
        $_oFields = new AmazonAutoLinks_FormFields_CategoryUnit_BasicInformation( $this );
        foreach( $_oFields->get() as $_aField ) {
            if ( 'country' === $_aField[ 'field_id' ] ) {
                continue;
            }
            $this->addSettingFields( $_aField );
        }
                    
    }
    
    /**
     * Validates submitted form data.
     */
    public function validate( $aInput, $aOriginal, $oFactory ) {    
        
        // Formats the options
        $_oCategoryUnitOption = new AmazonAutoLinks_UnitOption_category(
            null,
            $aInput
        );
        $_aFormatted = $_oCategoryUnitOption->get();
        
        // Drop unsent keys.
        foreach( $_aFormatted as $_sKey => $_mValue ) {
            if ( ! array_key_exists( $_sKey, $aInput ) ) {
                unset( $_aFormatted[ $_sKey ] );
            }
        }
        
        // Schedule pre-fetch for the unit if the options have been changed.
        if ( $aInput !== $aOriginal ) {
            AmazonAutoLinks_Event_Scheduler::prefetch(
                AmazonAutoLinks_PluginUtility::getCurrentPostID()
            );
        }
        
        return $_aFormatted + $aInput;
        
    }
    
}