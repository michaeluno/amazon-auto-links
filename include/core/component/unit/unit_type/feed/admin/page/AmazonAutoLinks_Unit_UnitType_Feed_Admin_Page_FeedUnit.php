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
 * Adds a setting page for creating tag units.
 * 
 * @since 4.0.0
 * @since 5.0.0 Renamed from `AmazonAutoLinks_FeedUnitAdminPage_FeedUnit`.
 */
class AmazonAutoLinks_Unit_UnitType_Feed_Admin_Page_FeedUnit extends AmazonAutoLinks_Unit_UnitType_Admin_Page_UnitCreationWizardBase {

    /**
     * @var string
     * @since 5.0.0
     */
    public $sUnitType = 'feed';

    /**
     * @var boolean
     * @since 5.0.0
     */
    public $bAssociateIDField = true;

    /**
     * @return  array
     * @since   4.0.0
     */
    protected function _getArguments() {
        $_oOption = AmazonAutoLinks_Option::getInstance();
        return array(
            'page_slug'     => AmazonAutoLinks_Registry::$aAdminPages[ 'feed_unit' ],
            'title'         => __( 'Add Feed Unit', 'amazon-auto-links' ),
            'screen_icon'   => AmazonAutoLinks_Registry::getPluginURL( AmazonAutoLinks_Main_Loader::$sDirPath . "/asset/image/icon/screen_icon_32x32.png", true ),
            'style'         => AmazonAutoLinks_Main_Loader::$sDirPath . '/asset/css/admin.css',
            'capability'    => $_oOption->get( array( 'capabilities', 'create_units' ), 'edit_pages' ),
            'order'         => 44,
        );
    }

    /**
     * @return  array
     * @since   4.0.0
     */
    protected function _getSectionArguments() {
        $_oOption = AmazonAutoLinks_Option::getInstance();
        return array(
            // 'tab_slug'      => $this->sTabSlug,
            'section_id'    => '_default',
            'description'   => array(
                __( 'The feed unit type allows you to import unit data of external sites that set up Auto Amazon Links. Make use of this unit to save API calls.', 'amazon-auto-links' ),
                __( 'After creating a unit on another site, copy the JSON feed URL found in the Manage Units page. Then paste the URL in the option field here.', 'amazon-auto-links' ),
            ),
            'capability'    => $_oOption->get( array( 'capabilities', 'create_units' ), 'edit_pages' ),
        );
    }

    /**
     * @since       4.0.0
     * @return      array
     */
    protected function _getFormFieldClasses() {
        return array(
            'AmazonAutoLinks_FormFields_FeedUnit_Main',
            'AmazonAutoLinks_FormFields_Unit_Common',
            'AmazonAutoLinks_FormFields_Unit_Credit',
            'AmazonAutoLinks_FormFields_Unit_AutoInsert',
            'AmazonAutoLinks_FormFields_FeedUnit_Submit',
        );
    }
    
    /**
     * @callback add_filter() validation + _ + page slug
     */
    protected function _validate( $aInputs, $aOldInputs, $oFactory, $aSubmitInfo ) {

        $_bVerified       = true;
        $_aErrors         = array();
        $_oOption         = AmazonAutoLinks_Option::getInstance();

        // Check if a url is set.
        $aInputs[ 'feed_urls' ] = $this->getAsArray( $aInputs[ 'feed_urls' ] );
        if ( empty( $aInputs[ 'feed_urls' ] ) ) {
            $_aErrors[ 'feed_urls' ] = __( 'Please set a url.', 'amazon-auto-links' );
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

        return parent::_validate( $aInputs, $aOldInputs, $oFactory, $aSubmitInfo );
        
    }   
            
}
