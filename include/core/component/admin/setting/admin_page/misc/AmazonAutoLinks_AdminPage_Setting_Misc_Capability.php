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
 * Adds the 'Capability' form section to the 'Misc' tab.
 * 
 * @since       3
 */
class AmazonAutoLinks_AdminPage_Setting_Misc_Capability extends AmazonAutoLinks_AdminPage_Section_Base {

    /**
     * @return array
     * @since   3.11.1
     */
    protected function _getArguments() {
        return array(
            'section_id'    => 'capabilities',       // avoid hyphen(dash), dots, and white spaces
            'capability'    => 'manage_options',
            'title'         => __( 'Access Rights', 'amazon-auto-links' ),
            'description'   => array(
                __( 'Set the access levels to the plugin setting pages.', 'amazon-auto-links' ),
            ),
        );
    }

    /**
     * A user constructor.
     * 
     * @since       3
     * @return      void
     */
    protected function _construct( $oFactory ) {}
    
    /**
     * Adds form fields.
     * @since       3
     * @return      void
     */
    protected function _addFields( $oFactory, $sSectionID ) {

        $oFactory->addSettingFields(
            $sSectionID, // the target section id    
            array(
                'field_id'      => 'setting_page_capability',
                'type'          => 'select',
                'title'         => __( 'Capability', 'amazon-auto-links' ),
                'tip'           => __( 'Select the user role that is allowed to access the plugin setting pages.', 'amazon-auto-links' ),
                'description'   => __( 'Default', 'amazon-auto-links' ) . ': ' . __( 'Administrator', 'amazon-auto-links' ),
                'capability'    => 'manage_options',
                'label'         => array(                        
                    'manage_options' => __( 'Administrator', 'amazon-auto-links' ),
                    'edit_pages'     => __( 'Editor', 'amazon-auto-links' ),
                    'publish_posts'  => __( 'Author', 'amazon-auto-links' ),
                    'edit_posts'     => __( 'Contributor', 'amazon-auto-links' ),
                    'read'           => __( 'Subscriber', 'amazon-auto-links' ),
                ),
            )
        );
    }
        
    
    /**
     * Validates the submitted form data.
     * 
     * @since       3
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