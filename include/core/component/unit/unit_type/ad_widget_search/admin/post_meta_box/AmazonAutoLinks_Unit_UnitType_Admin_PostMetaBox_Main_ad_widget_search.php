<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 */

/**
 * Defines the meta box added to the category unit definition page.
 */
class AmazonAutoLinks_Unit_UnitType_Admin_PostMetaBox_Main_ad_widget_search extends AmazonAutoLinks_UnitPostMetaBox_Base {
    
    /**
     * Stores the unit type slug(s). 
     */    
    protected $aUnitTypes = array( 'ad_widget_search' );
    
    /**
     * Sets up form fields.
     */ 
    public function setUp() {
        
        $_oFields = new AmazonAutoLinks_FormFields_AdWidgetSearchUnit_Main( $this );
        $_aSkip   = array( 'country', 'unit_title' );
        foreach( $_oFields->get() as $_aField ) {
            if ( in_array( $_aField[ 'field_id' ], $_aSkip, true ) ) {
                continue;
            }
            $this->addSettingFields( $_aField );
        }
                    
    }
    
    /**
     * Validates submitted form data.
     */
    public function validate( $aInputs, $aOriginal, $oFactory ) {    
        
        // Formats the options
        $_oCategoryUnitOption = new AmazonAutoLinks_UnitOption_ad_widget_search( null, $aInputs );
        $_aFormatted = $_oCategoryUnitOption->get();
        
        // Drop unsent keys.
        foreach( $_aFormatted as $_sKey => $_mValue ) {
            if ( ! array_key_exists( $_sKey, $aInputs ) ) {
                unset( $_aFormatted[ $_sKey ] );
            }
        }
        
        // Schedule pre-fetch for the unit if the options have been changed.
        if ( $aInputs !== $aOriginal ) {
            AmazonAutoLinks_Event_Scheduler::prefetch( AmazonAutoLinks_PluginUtility::getCurrentPostID() );
        }
        
        return $_aFormatted + $aInputs;
        
    }
    
}