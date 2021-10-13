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
 * An abstract base class for unit creation wizard pages.
 * 
 * @since 5.0.0
 */
abstract class AmazonAutoLinks_Unit_UnitType_Admin_Page_UnitCreationWizardBase extends AmazonAutoLinks_AdminPage_Page_Base {

    /**
     * @var string
     * @since 5.0.0
     */
    public $sUnitType = '';

    /**
     * @var string
     */
    public $sTabSlug = 'first';

    /**
     * Whether to add the Associate ID field.
     * @var boolean
     * @since 5.0.0
     */
    public $bAssociateIDField = false;

    /**
     * @return array
     * @since  5.0.0
     */
    protected function _getArguments() {
        $_oOption = AmazonAutoLinks_Option::getInstance();
        return array(
            'screen_icon'   => AmazonAutoLinks_Registry::getPluginURL( AmazonAutoLinks_Main_Loader::$sDirPath . "/asset/image/icon/screen_icon_32x32.png", true ),
            'capability'    => $_oOption->get( array( 'capabilities', 'create_units' ), 'edit_pages' ),
        );
    }

    /**
     * @param    $oFactory AmazonAutoLinks_AdminPageFramework
     * @since    5.0.0
     */ 
    protected function _loadPage( $oFactory ) {

        // Form Section - we use the default one ('_default'), meaning no section.
        $_oOption = AmazonAutoLinks_Option::getInstance();
        $oFactory->addInPageTabs(
            $this->sPageSlug,
            array(
                'tab_slug' => $this->sTabSlug,
                'title'    => $this->aArguments[ 'title' ],
                'capability'    => $_oOption->get( array( 'capabilities', 'create_units' ), 'edit_pages' ),
            )
        );
        $this->oFactory->setPageHeadingTabsVisibility( false );
        $this->oFactory->setPageTitleVisibility( false );
        $this->oFactory->setInPageTabsVisibility( false );

        // Form Section - we use the default one ('_default'), meaning no section.
        $oFactory->addSettingSections( $this->sPageSlug, $this->_getSectionArguments() );
        
        // Add Fields
        foreach( $this->_getFormFieldClasses() as $_sClassName ) {
            $_oFields = new $_sClassName( $oFactory );
            foreach( $_oFields->get() as $_aField ) {
                if ( ! $this->bAssociateIDField && 'associate_id' === $_aField[ 'field_id' ] ) {
                    continue;
                }
                $oFactory->addSettingFields(
                    '_default', // the target section id    
                    $_aField
                );
            }                    
        }
        
        // Custom messages
        $oFactory->setMessage(
            'allowed_maximum_number_of_fields',
            sprintf(
                __( 'Please upgrade to <a href="%1$s">Pro</a> to add more items!', 'amazon-auto-links' ),
                esc_url( AmazonAutoLinks_Registry::STORE_URI_PRO )
            )
        );        
        
    }
        /**
         * @return  array
         * @since   5.0.0
         */
        protected function _getSectionArguments() {
            return array(
                'tab_slug'      => $this->sTabSlug,
                'section_id'    => '_default',
                'description'   => array(
                    __( 'The search unit type allows you to search products with keywords.', 'amazon-auto-links' ),
                ),
            );
        }
        /**
         * @since  5.0.0
         * @return array
         */
        protected function _getFormFieldClasses() {
            return array(
                'AmazonAutoLinks_FormFields_Unit_Common',
                'AmazonAutoLinks_FormFields_Unit_Credit',
                'AmazonAutoLinks_FormFields_Unit_AutoInsert',
                'AmazonAutoLinks_FormFields_AdWidgetSearchUnit_Submit',
            );
        }
    
    /**
     * @since    5.0.0
     * @callback add_filter() validation + _ + page slug
     * @return   array
     */
    protected function _validate( $aInputs, $aOldInputs, $oFactory, $aSubmitInfo ) {

        $_aErrors         = array();
        $_oOption         = AmazonAutoLinks_Option::getInstance();
        $_oTemplateOption = AmazonAutoLinks_TemplateOption::getInstance();
        $_oUtil           = new AmazonAutoLinks_PluginUtility;

        // Check the limitation.
        if ( $_oOption->isUnitLimitReached() ) {
            $oFactory->setFieldErrors( $_aErrors + array( true ) );     // this prevents the submit-redirect routine
            $oFactory->setSettingNotice( AmazonAutoLinks_Message::getUpgradePromptMessageToAddMoreUnits() );
            return $aOldInputs;
        }

        // The feed unit type does not have this field.
        if ( isset( $aInputs[ 'country' ] ) ) {
            $aInputs[ 'associate_id' ] = $_oOption->getAssociateID( $aInputs[ 'country' ] );
        }

        $_bDoAutoInsert = $aInputs[ 'auto_insert' ];

        // Format the unit options to sanitize the data.
        $_sUnitType               = $this->getElement( $aInputs, array( 'unit_type' ), $this->sUnitType );
        $_sUnitOptionClass        = "AmazonAutoLinks_UnitOption_{$_sUnitType}";
        $_oUnitOptions            = new $_sUnitOptionClass( null, $aInputs );
        $aInputs                  = $_oUnitOptions->get();
        $aInputs[ 'template_id' ] = $_oTemplateOption->getDefaultTemplateIDByUnitType( $_sUnitType );

        // Store the inputs for the next time.
        update_user_meta( get_current_user_id(), AmazonAutoLinks_Registry::$aUserMeta[ 'last_inputs' ], $aInputs );

        // Create a unit post
        $_iNewPostID = $_oUtil->insertPost( $aInputs, AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ] );

        // Create an auto insert
        if ( $_bDoAutoInsert ) {
            $_oUtil->createAutoInsert( $_iNewPostID );
        }

        // Clean the temporary form options data.
        $_oUtil->deleteTransient( $GLOBALS[ 'aal_transient_id' ] );
        
        // Schedule pre-fetching the unit feed in the background
        // so that by the time the user opens the unit page, the cache will be ready.
        AmazonAutoLinks_Event_Scheduler::prefetch( $_iNewPostID );

        // Go to the post editing page and exit. This way the framework won't create a new form transient row.
        $_oUtil->goToPostDefinitionPage( $_iNewPostID, AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ] );
        
        // This won't be reached.
        return $aInputs;
        
    }   
            
}