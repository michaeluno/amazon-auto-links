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
 * Adds a setting page for creating tag units.
 * 
 * @since 3.2.0
 */
class AmazonAutoLinks_URLUnitAdminPage_URLUnit extends AmazonAutoLinks_Unit_UnitType_Admin_Page_UnitCreationWizardBase {

    /**
     * @return array
     * @since  3.11.1
     */
    protected function _getArguments() {
        $_oOption = AmazonAutoLinks_Option::getInstance();
        return array(
            'page_slug'     => AmazonAutoLinks_Registry::$aAdminPages[ 'url_unit' ],
            'title'         => __( 'Add Unit by URL', 'amazon-auto-links' ),
            'screen_icon'   => AmazonAutoLinks_Registry::getPluginURL( AmazonAutoLinks_Main_Loader::$sDirPath . "/asset/image/icon/screen_icon_32x32.png", true ),
            'capability'    => $_oOption->get( array( 'capabilities', 'create_units' ), 'edit_pages' ),
        );
    }

    /**
     * @return  array
     * @since   4.0.0
     */
    protected function _getSectionArguments() {
        return array(
            'tab_slug'      => $this->sTabSlug,
            'section_id'    => '_default',
            'description'   => array(
                __( 'The URL unit type allows you to search products found in the page with specified urls.', 'amazon-auto-links' ),
            ),
        );
    }
    
    /**
     * @since  3
     * @since  4.0.0   Changed the scope to protected as the feed unit type extends this class and uses this method.
     * @return array
     */
    protected function _getFormFieldClasses() {
        return array(
            'AmazonAutoLinks_FormFields_URLUnit_Main',
            'AmazonAutoLinks_FormFields_Unit_Common',
            'AmazonAutoLinks_FormFields_Unit_Credit',
            'AmazonAutoLinks_FormFields_Unit_AutoInsert',
            'AmazonAutoLinks_FormFields_URLUnit_Submit',
        );
    }

    /**
     * @callback add_filter()      validation + _ + page slug
     * @return   array
     */
    protected function _validate( $aInputs, $aOldInputs, $oFactory, $aSubmitInfo ) {

        $_bVerified       = true;
        $_aErrors         = array();
        $_oOption         = AmazonAutoLinks_Option::getInstance();

        // Check if a URL is set.
        $aInputs[ 'urls' ] = $this->getAsArray( $aInputs[ 'urls' ] );
        if ( empty( $aInputs[ 'urls' ] ) ) {
            $_aErrors[ 'urls' ] = __( 'Please set a url.', 'amazon-auto-links' );
            $_bVerified = false;
        }

        $aInputs[ 'associate_id' ] = $_oOption->getAssociateID( $aInputs[ 'country' ] );

        // An invalid value is found.
        if ( ! $_bVerified ) {
        
            // Set the error array for the input fields.
            $oFactory->setFieldErrors( $_aErrors );        
            $oFactory->setSettingNotice( __( 'There was an error in your input.', 'amazon-auto-links' ) );
            return $aInputs;
            
        }        

        return parent::_validate( $aInputs, $aOldInputs, $oFactory, $aSubmitInfo );

    }   
            
}