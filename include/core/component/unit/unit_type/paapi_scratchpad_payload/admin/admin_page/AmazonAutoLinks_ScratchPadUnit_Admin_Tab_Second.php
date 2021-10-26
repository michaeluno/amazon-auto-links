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
 * Adds a tab to a setting page.
 * 
 * @since 5.0.0
 */
class AmazonAutoLinks_ScratchPadUnit_Admin_Tab_Second extends AmazonAutoLinks_Unit_UnitType_Admin_Tab_SearchUnit_Second_Base {

    /**
     * @return array
     * @since  5.0.0
     */
    protected function _getArguments() {
        return array(
            'tab_slug'      => 'custom_payload',
            'title'         => __( 'Add Unit by PA-API Custom Payload', 'amazon-auto-links' ),
            'description'   => __( 'Create a unit by custom payload.', 'amazon-auto-links' ),
            'style'         => AmazonAutoLinks_Unit_UnitType_Loader_scratchpad_payload::$sDirPath . '/asset/css/admin_custom_payload_unit.css',
        );
    }

    /**
     * @return array
     * @since  5.0.0
     */
    protected function _getFormFieldClasses() {
        return array(
            'AmazonAutoLinks_FormFields_ScratchPadPayloadUnit_Main',
            'AmazonAutoLinks_FormFields_Unit_Common',
            'AmazonAutoLinks_FormFields_Unit_Credit',
            'AmazonAutoLinks_FormFields_Unit_AutoInsert',
            'AmazonAutoLinks_FormFields_ScratchPadPayload_Submit',
        );
    }
        
    /**
     * @since    4.1.0
     * @since    5.0.0        Moved from `AmazonAutoLinks_ScratchPadPayloadUnitAdminPage_ScratchPadPayloadUnit`.
     * @callback add_filter() validation_{page slug}_{tab slug}
     */            
    public function validate( $aInputs, $aOldInputs, $oFactory, $aSubmitInfo ) {
        
        $_bVerified       = true;
        $_aErrors         = array();

        // Check if a payload is given.
        $aInputs[ 'payload' ] = trim( ( string ) $this->getElement( $aInputs, 'payload' ) );
        $aInputs[ 'payload' ] = stripslashes_deep( $aInputs[ 'payload' ] );
        if ( empty( $aInputs[ 'payload' ] ) || ! $this->isJSON( $aInputs[ 'payload' ] ) ) {
            $_aErrors[ 'payload' ] = __( 'Please set a valid payload JSON.', 'amazon-auto-links' );
            $_bVerified = false;
        }

        if ( empty( $aInputs[ 'associate_id' ] ) ) {
            $_aErrors[ 'associate_id' ] = __( 'The associate ID cannot be empty.', 'amazon-auto-links' );
            $_bVerified = false;
        }

        // An invalid value is found.
        if ( ! $_bVerified ) {

            // Set the error array for the input fields.
            $oFactory->setFieldErrors( $_aErrors );
            $oFactory->setSettingNotice( __( 'There was an error in your input.', 'amazon-auto-links' ) );
            return $aInputs;

        }
        
        return parent::validate( $aInputs, $aOldInputs, $oFactory, $aSubmitInfo );
        
    }

}