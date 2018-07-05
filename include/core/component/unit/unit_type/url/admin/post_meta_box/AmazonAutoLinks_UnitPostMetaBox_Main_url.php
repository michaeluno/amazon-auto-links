<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2018 Michael Uno
 */

/**
 * Defines the meta box added to the 'url' unit definition page.
 */
class AmazonAutoLinks_UnitPostMetaBox_Main_url extends AmazonAutoLinks_UnitPostMetaBox_Base {
    
    /**
     * Stores the unit type slug(s). 
     */    
    protected $aUnitTypes = array( 'url' );
    
    /**
     * Sets up form fields.
     */ 
    public function setUp() {
        
        $_oFields = new AmazonAutoLinks_FormFields_URLUnit_Main;
        foreach( $_oFields->get() as $_aField ) {
            if ( 'unit_title' === $_aField[ 'field_id' ] ) {
                continue;
            }
            $this->addSettingFields( $_aField );
        }
        // Add meta box only fields.
        $this->addSettingFields(
            array(
                'field_id'      => '_found_items',
                'title'         => __( 'Found Products', 'amazon-auto-links' ),
                'type'          => 'textarea',
                'attributes'    => array(
                    'readonly' => 'readonly',
                ),
                'order'         => 14,
            )        
        );
    }
    
    /**
     * Validates submitted form data.
     */
    public function validate( $aInput, $aOriginal, $oFactory ) {    
        
        $_aErrors   = array();
        $_bVerified = true;
        
        // Formats the options
        $_oUnitOption = new AmazonAutoLinks_UnitOption_url(
            null,
            $aInput
        );
        $_aFormatted = $_oUnitOption->get();
        
        // Check if a url iset.
        $aInput[ 'urls' ] = $this->oUtil->getAsArray( $aInput[ 'urls' ] );
        if ( empty( $aInput[ 'urls' ] ) ) {
            $_aErrors[ 'urls' ] = __( 'Please set a url.', 'amazon-auto-links' );
            $_bVerified = false;
        }        
        
        // An invalid value is found.
        if ( ! $_bVerified ) {
        
            // Set the error array for the input fields.
            $oFactory->setFieldErrors( $_aErrors );        
            $oFactory->setSettingNotice( __( 'There was an error in your input.', 'amazon-auto-links' ) );
            return $aInput;
            
        }       

        // Sanitize
        foreach ( $aInput[ 'urls' ] as $_iIndex => $_sURL ) {
            $aInput[ 'urls' ][ $_iIndex ] = trim( $_sURL );
        }
        
        
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