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
 * Adds a tab to a setting page.
 * 
 * @since 3
 */
class AmazonAutoLinks_SearchUnitAdminPage_SearchUnit_First extends AmazonAutoLinks_AdminPage_Tab_Base {

    /**
     * @return array
     * @since  3.11.1
     */
    protected function _getArguments() {
        return array(
            'tab_slug'      => 'first',
            'title'         => __( 'Add Unit by PA-API', 'amazon-auto-links' ),
            'description'   => __( 'Select the search type.', 'amazon-auto-links' ),
        );
    }

    protected function _construct( $oFactory ) {}
    
    /**
     * Triggered when the tab is loaded.
     * @param AmazonAutoLinks_AdminPageFramework $oFactory
     */
    public function replyToLoadTab( $oFactory ) {

        // Add form fields
        $oFactory->addSettingSections(
            $this->sPageSlug, // target page slug
            array(
                'tab_slug'      => $this->sTabSlug,
                'section_id'    => '_default', 
                'description'   => array(
                    __( 'Select a search type.', 'amazon-auto-links' ),
                ),
            )     
        );        
        
        // Add Fields
        foreach( $this->___getFormFieldClasses() as $_sClassName ) {
            $_oFields = new $_sClassName( $oFactory );
            foreach( $_oFields->get() as $_aField ) {
                $oFactory->addSettingFields(
                    '_default', // the target section id    
                    $_aField
                );
            }                    
        }
                        
    }
        /**
         * @return  array
         */
        private function ___getFormFieldClasses() {
            return array(
                'AmazonAutoLinks_FormFields_SearchUnit_SearchType',
                'AmazonAutoLinks_FormFields_SearchUnit_ProceedButton',
            );
        }    
    /**
     * 
     * @callback add_filter() validation_{page slug}_{tab slug}
     */ 
    public function validate( $aInputs, $aOldInputs, $oFactory, $aSubmitInfo ) {

        $_bVerified = ! $oFactory->hasFieldError();
        $_aErrors   = array();
        $_oOption   = AmazonAutoLinks_Option::getInstance();
    
        try {
            AmazonAutoLinks_Unit_Admin_Utility::tryCheckUnitCanBeCreated();
        } catch ( Exception $_oException ) {
            // must set a field error array which does not yield empty so that it won't be redirected.
            $oFactory->setFieldErrors( array( 'error' ) );
            $oFactory->setSettingNotice( AmazonAutoLinks_Message::getUpgradePromptMessageToAddMoreUnits() );
            return $aOldInputs;
        }

        $aInputs[ 'associate_id' ] = $_oOption->getAssociateID( $aInputs[ 'country' ] );

        // An invalid value is found. Set a field error array and an admin notice and return the old values.
        if ( ! $_bVerified ) {
            $oFactory->setFieldErrors( $_aErrors );     
            $oFactory->setSettingNotice( __( 'There was an error in your input.', 'amazon-auto-links' ) );
            return $aInputs;
        }        
        
        // This should be the last check in the entire page validation checks.
        if ( 
            $oFactory->oUtil->hasSuffix( 
                'submit_proceed',
                $aSubmitInfo[ 'field_id' ]
            )
        ) {
            // Will exit the script.
            unset( $aInputs[ 'submit_proceed' ] );
            $this->___goToNextPage( $aInputs );
        }

        return $aInputs;
        
    }
    
        /**
         * @remark      Will redirect the user to the next page and exits the script.
         */
        private function ___goToNextPage( $aInputs ) {
            $this->setTransient(
                $aInputs[ 'transient_id' ],  // key
                $aInputs, // data
                60*10*6*24 // 24 hours in seconds
            );
            $_sURLNextPage = apply_filters( 'aal_filter_admin_unit_paapi_unit_types_unit_creation_page_url', '', $aInputs );
            exit( wp_redirect( $_sURLNextPage ) );
        }    

}