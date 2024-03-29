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
 * Adds the 'Debug' form section to the 'Misc' tab.
 * 
 * @since 3
 */
class AmazonAutoLinks_AdminPage_Setting_Misc_Debug extends AmazonAutoLinks_AdminPage_Section_Base {

    /**
     * @return array
     * @since  3.11.1
     */
    protected function _getArguments() {
        return array(
            'section_id'    => 'debug',
            'capability'    => 'manage_options',
            'title'         => __( 'Debug', 'amazon-auto-links' ),
            'description'   => __( 'For developers who need to see the internal workings of the plugin.', 'amazon-auto-links' ),
        );
    }

    /**
     * A user constructor.
     * 
     * @since 3
     */
    protected function _construct( $oFactory ) {}
    
    /**
     * Adds form fields.
     * @since 3

     */
    protected function _addFields( $oFactory, $sSectionID ) {

        $oFactory->addSettingFields(
            $sSectionID, // the target section id    
            array(
                'field_id'      => 'debug_mode',
                'type'          => 'revealer',
                'select_type'   => 'radio',
                'title'         => __( 'Debug Mode', 'amazon-auto-links' ),
                'capability'    => 'manage_options',
                'label'         => array(
                    1 => __( 'On', 'amazon-auto-links' ),
                    0 => __( 'Off', 'amazon-auto-links' ),
                ),
                'selectors'     => array(
                    1 => '.debug-mode-visibility'
                ),
                'default'       => 0,
            ),
            array(
                'field_id'      => 'visibility',
                'type'          => 'checkbox',
                'label'         => array(
                    'font_end'  => __( 'Front-end', 'amazon-auto-links' ),
                    'js'        => __( 'Browser Console', 'amazon-auto-links' ),
                    'back_end'  => __( 'Back-end', 'amazon-auto-links' ),
                    'test'      => __( 'Tests', 'amazon-auto-links' ),
                    'log'       => __( 'Log', 'amazon-auto-links' ),
                ),
                'class'         => array(
                    'fieldrow' => 'debug-mode-visibility',
                ),
                'description'   => array(
                    __( 'Check debug components to enable.', 'amazon-auto-links' ),
                ),
            )
        );    
    
    }
        
    
    /**
     * Validates the submitted form data.
     * 
     * @since 3
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