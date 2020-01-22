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
 * Defines the meta box added to the 'contextual' unit definition page.
 */
class AmazonAutoLinks_UnitPostMetaBox_Main_contextual extends AmazonAutoLinks_UnitPostMetaBox_Base {
    
    /**
     * Stores the unit type slug(s). 
     */    
    protected $aUnitTypes = array( 'contextual' );
    
    /**
     * Sets up form fields.
     */ 
    public function setUp() {
        foreach( $this->___getFieldClasses() as $_sClassName ) {
            $_oFields = new $_sClassName;
            foreach ( $_oFields->get() as $_aField ) {
                if ( in_array( $_aField[ 'field_id' ], array( 'unit_title', 'country' ) ) ) {
                    continue;
                }
                $this->addSettingFields( $_aField );
            }
        }
    }

        /**
         * @return      array
         */
        private function ___getFieldClasses() {
            return array(
                'AmazonAutoLinks_FormFields_ContextualUnit_Basic',
                'AmazonAutoLinks_FormFields_ContextualUnit_Main',
            );
        }
    
    /**
     * Validates submitted form data.
     */
    public function validate( $aInputs, $aOldInputs, $oFactory ) {    
        
        $_aErrors   = array();
        $_bVerified = true;
        
        // Formats the options
        $_oUnitOption = new AmazonAutoLinks_UnitOption_contextual(
            null,
            $aInputs
        );
        $_aFormatted = $_oUnitOption->get();
        
        // An invalid value is found.
        if ( ! $_bVerified ) {
        
            // Set the error array for the input fields.
            $oFactory->setFieldErrors( $_aErrors );        
            $oFactory->setSettingNotice( __( 'There was an error in your input.', 'amazon-auto-links' ) );
            return $aInputs;
            
        }       

        // Schedule pre-fetch for the unit if the options have been changed.
        if ( $aInputs !== $aOldInputs ) {
            AmazonAutoLinks_Event_Scheduler::prefetch(
                AmazonAutoLinks_PluginUtility::getCurrentPostID()
            );
        }

        // Drop unset keys.
        foreach( $_aFormatted as $_sKey => $_mValue ) {
            if ( ! array_key_exists( $_sKey, $aInputs ) ) {
                unset( $_aFormatted[ $_sKey ] );
            }
        }
        return $_aFormatted + $aInputs;
        
    }
    
}