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
 * Adds the 'Widgets' form section to the 'General' tab.
 * 
 * @since       3.9.0
 */
class AmazonAutoLinks_AdminPage_Setting_General_Widgets extends AmazonAutoLinks_AdminPage_Section_Base {

    /**
     * @return array
     * @since   3.11.1
     */
    protected function _getArguments() {
        return array(
            'section_id'    => 'widget',
            'title'         => __( 'Widgets', 'amazon-auto-links' ),
        );
    }

    /**
     * A user constructor.
     * 
     * @since       3.9.0
     * @return      void
     */
    protected function _construct( $oFactory ) {}
    
    /**
     * Adds form fields.
     * @since       3.9.0
     * @return      void
     */
    protected function _addFields( $oFactory, $sSectionID ) {
    
        $oFactory->addSettingFields(
            $sSectionID, // the target section id
            array(
                'field_id'       => 'register',
                'title'          => __( 'Registered Widgets', 'amazon-auto-links' ),
                'label'          => array(
                    'contextual' => __( 'Contextual Products', 'amazon-auto-links' ),
                    'by_unit'    => __( 'By Unit', 'amazon-auto-links' ),
                ),
                'description'    => __( 'Check widgets which should be listed in the widget page.', 'amazon-auto-links' ),
                'type'           => 'checkbox',
//                'default'        => array(
//                    'contextual'    => true,
//                    'by_unit'       => true,
//                ),
            )  
        );
    
    }
        
    
    /**
     * Validates the submitted form data.
     * 
     * @since       3.9.0
     */
    public function validate( $aInputs, $aOldInputs, $oAdminPage, $aSubmitInfo ) {
    
        $_bVerified = true;
        $_aErrors   = array();
        
        // An invalid value is found. Set a field error array and an admin notice and return the old values.
        if ( ! $_bVerified ) {
            $oAdminPage->setFieldErrors( $_aErrors );     
            $oAdminPage->setSettingNotice( __( 'There was something wrong with your input.', 'amazon-auto-links' ) );
            return $aOldInputs;
        }
                
        return $aInputs;     
        
    }
        
}