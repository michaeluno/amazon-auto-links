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
 * Defines the meta box,
 * @since       3.2.0
 */
class AmazonAutoLinks_PostMetaBox_URLUnit_Advanced extends AmazonAutoLinks_UnitPostMetaBox_Advanced_item_lookup {
    
    /**
     * Stores the unit type slug(s). 
     */    
    protected $aUnitTypes = array( 'url' );
    
    /**
     * Sets up form fields.
     */ 
    public function setUp() {
        
        $_oFields = new AmazonAutoLinks_FormFields_URLUnit_Advanced;
        foreach( $_oFields->get() as $_aField ) {
            if ( 'unit_title' === $_aField[ 'field_id' ] ) {
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
        $_oUnitOption = new AmazonAutoLinks_UnitOption_url(
            null,
            $aInput
        );
        $_aFormatted = $_oUnitOption->get();
        
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