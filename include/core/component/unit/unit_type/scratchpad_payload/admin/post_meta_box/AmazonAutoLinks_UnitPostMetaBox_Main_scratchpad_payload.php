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
 * Defines the meta box added to the 'feed' unit definition page.
 * @since       4.1.0
 */
class AmazonAutoLinks_UnitPostMetaBox_Main_scratchpad_payload extends AmazonAutoLinks_UnitPostMetaBox_Base {
    
    /**
     * Stores the unit type slug(s). 
     */    
    protected $aUnitTypes = array( 'scratchpad_payload' );
    
    /**
     * Sets up form fields.
     */ 
    public function setUp() {
        
        $_oFields = new AmazonAutoLinks_FormFields_ScratchPadPayloadUnit_Main( $this );
        foreach( $_oFields->get() as $_aField ) {
            if ( in_array( $_aField[ 'field_id' ], array( 'unit_title', 'country' ) ) ) {
                continue;
            }
            $this->addSettingFields( $_aField );
        }

        $this->enqueueStyle( AmazonAutoLinks_Registry::getPluginURL( 'asset/css/aal_add_scratchpad_payload_unit.css' ) );

    }

    public function load() {
        add_action( 'do_meta_boxes', array( $this, 'replyToRemoveLocaleMetaBox' ) );
    }

    /**
     * Validates submitted form data.
     */
    public function validate( $aInputs, $aOriginal, $oFactory ) {    
        
        $_aErrors   = array();
        $_bVerified = true;
        
        // Formats the options
        $_oUnitOption = new AmazonAutoLinks_UnitOption_scratchpad_payload(null, $aInputs );
        $_aFormatted  = $_oUnitOption->get();

        // Check if a payload is given.
        $_oUtil = new AmazonAutoLinks_PluginUtility();
        $aInputs[ 'payload' ] = trim( ( string ) $_oUtil->getElement( $aInputs, 'payload' ) );
        $aInputs[ 'payload' ] = stripslashes_deep( $aInputs[ 'payload' ] );
        if ( empty( $aInputs[ 'payload' ] ) || ! $_oUtil->isJSON( $aInputs[ 'payload' ] ) ) {
            $_aErrors[ 'payload' ] = __( 'Please set a valid payload JSON.', 'amazon-auto-links' );
            $_bVerified = false;
        }

        // An invalid value is found.
        if ( ! $_bVerified ) {
        
            // Set the error array for the input fields.
            $oFactory->setFieldErrors( $_aErrors );        
            $oFactory->setSettingNotice( __( 'There was an error in your input.', 'amazon-auto-links' ) );
            return $aInputs;
            
        }       

        // Drop unsent keys.
        foreach( $_aFormatted as $_sKey => $_mValue ) {
            if ( ! array_key_exists( $_sKey, $aInputs ) ) {
                unset( $_aFormatted[ $_sKey ] );
            }
        }
        
        // Schedule pre-fetch for the unit if the options have been changed.
        if ( $aInputs !== $aOriginal ) {
            AmazonAutoLinks_Event_Scheduler::prefetch(
                AmazonAutoLinks_PluginUtility::getCurrentPostID()
            );
        }
        
        return $_aFormatted + $aInputs;
        
    }
    
}