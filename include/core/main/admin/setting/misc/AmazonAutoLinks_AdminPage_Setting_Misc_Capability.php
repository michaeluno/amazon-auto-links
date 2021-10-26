<?php
/**
 * Auto Amazon Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
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

        $_aUserRoleLabels = $this->___getUserRoleTranslated( array(
            'manage_options' => 'Administrator',
            'edit_pages'     => 'Editor',
            'publish_posts'  => 'Author',
            'edit_posts'     => 'Contributor',
            'read'           => 'Subscriber',
        ) );

        $oFactory->addSettingFields(
            $sSectionID, // the target section id    
            array(
                'field_id'      => 'setting_page_capability',
                'type'          => 'select',
                'title'         => __( 'Settings', 'amazon-auto-links' ),
                'tip'           => __( 'Select the user role that is allowed to access the plugin setting pages.', 'amazon-auto-links' ),
                'description'   => __( 'Default', 'amazon-auto-links' ) . ': <code>' . __( 'Administrator', 'amazon-auto-links' ) . '</code>',
                'capability'    => 'manage_options',
                'label'         => $_aUserRoleLabels,
            ),
            array(
                'field_id'      => 'create_units',
                'type'          => 'select',
                'title'         => __( 'Creating Units', 'amazon-auto-links' ),
                'description'   => __( 'Default', 'amazon-auto-links' ) . ': <code>' . __( 'Editor', 'amazon-auto-links' ) . '</code>',
                'capability'    => 'manage_options',
                'default'       => 'edit_pages',
                'label'         => $_aUserRoleLabels,
            )
        );
    }

        /**
         * @param  array $aStructure
         * @return array
         * @since  4.6.13
         */
        private function ___getUserRoleTranslated( array $aStructure ) {
            $_aTranslated = array();
            foreach ( $aStructure as $_sLabelKey => $_sUserRole ) {
                $_sNameTranslated = translate_user_role( $_sUserRole );
                $_aTranslated[ $_sLabelKey ] = $_sNameTranslated;
            }
            return $_aTranslated;
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