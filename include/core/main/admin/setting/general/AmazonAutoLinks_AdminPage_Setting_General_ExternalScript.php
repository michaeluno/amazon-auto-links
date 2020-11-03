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
 * Adds the 'External Scripts' form section to the 'General' tab.
 * 
 * @since       3.1.0
 */
class AmazonAutoLinks_AdminPage_Setting_General_ExternalScript extends AmazonAutoLinks_AdminPage_Section_Base {

    /**
     * @return array
     * @since   3.11.1
     */
    protected function _getArguments() {
        return array(
            'section_id'    => 'external_scripts',
            'title'         => __( 'External Scripts', 'amazon-auto-links' ),
        );
    }

    /**
     * A user constructor.
     * 
     * @since       3.1.0
     * @return      void
     */
    protected function _construct( $oFactory ) {}
    
    /**
     * Adds form fields.
     * @since       3.1.0
     * @return      void
     */
    protected function _addFields( $oFactory, $sSectionID ) {

        $_oFields = new AmazonAutoLinks_FormFields_Setting_ExternalScript;
        foreach( $_oFields->get() as $_aField ) {
            $oFactory->addSettingFields(
                $sSectionID, // the target section id,
                $_aField
            );
        }
    
    }
        
    
    /**
     * Validates the submitted form data.
     * 
     * @since       3.1.0
     */
    public function validate( $aInput, $aOldInput, $oAdminPage, $aSubmitInfo ) {
    
        $_bVerified = true;
        $_aErrors   = array();
        
        // An invalid value is found. Set a field error array and an admin notice and return the old values.
        if ( ! $_bVerified ) {
            $oAdminPage->setFieldErrors( $_aErrors );     
            $oAdminPage->setSettingNotice( __( 'There was something wrong with your input.', 'amazon-auto-links' ) );
            return $aOldInput;
        }
                
        return $aInput;     
        
    }
   
}