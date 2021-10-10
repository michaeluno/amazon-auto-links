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
 * @since 4.1.0
 * @deprecated 5.0.0
 */
class AmazonAutoLinks_ScratchPadPayloadUnitAdminPage_ScratchPadPayloadUnit extends AmazonAutoLinks_URLUnitAdminPage_URLUnit {

    /**
     * @return  array
     * @since   4.1.0
     */
    protected function _getArguments() {
        $_oOption = AmazonAutoLinks_Option::getInstance();
        return array(
            'page_slug'     => AmazonAutoLinks_Registry::$aAdminPages[ 'scratchpad_payload' ],
            'title'         => __( 'Add Unit by PA-API Custom Payload', 'amazon-auto-links' ),
            'screen_icon'   => AmazonAutoLinks_Registry::getPluginURL( AmazonAutoLinks_Main_Loader::$sDirPath . "/asset/image/icon/screen_icon_32x32.png", true ),
            'style'         => AmazonAutoLinks_Main_Loader::$sDirPath . '/asset/css/admin.css',
            'capability'    => $_oOption->get( array( 'capabilities', 'create_units' ), 'edit_pages' ),
        );
    }

    /**
     * @return  array
     * @since   4.1.0
     */
    protected function _getSectionArguments() {
        return array(
            // 'tab_slug'      => $this->sTabSlug,
            'section_id'    => '_default',
            'description'   => array(
                sprintf(
                    __( 'The custom PA-API payload units allow you to import PA-API queries generated on <a href="%1$s" target="_blank">ScratchPad</a>.', 'amazon-auto-links' ),
                    'https://webservices.amazon.com/paapi5/scratchpad/'
                ),
            ),
        );
    }

    /**
     * @since       4.1.0
     * @return      array
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
     * 
     * @callback add_filter()      validation + _ + page slug
     */
    public function validate( $aInputs, $aOldInputs, $oFactory, $aSubmitInfo ) {

        $_bVerified       = true;
        $_aErrors         = array();
        $_oOption         = AmazonAutoLinks_Option::getInstance();
        $_oTemplateOption = AmazonAutoLinks_TemplateOption::getInstance();

        // Check the limitation.
        if ( $_oOption->isUnitLimitReached() ) {
            $oFactory->setFieldErrors( $_aErrors + array( true ) );     // this prevents the submit redirect routine
            $oFactory->setSettingNotice( AmazonAutoLinks_Message::getUpgradePromptMessageToAddMoreUnits() );
            return $aOldInputs;
        }        
        
        // Check if a payload is given.
        $_oUtil = new AmazonAutoLinks_PluginUtility();
        $aInputs[ 'payload' ] = trim( ( string ) $_oUtil->getElement( $aInputs, 'payload' ) );
        $aInputs[ 'payload' ] = stripslashes_deep( $aInputs[ 'payload' ] );
        if ( empty( $aInputs[ 'payload' ] ) || ! $_oUtil->isJSON( $aInputs[ 'payload' ] ) ) {
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
        
        $_bDoAutoInsert = $aInputs[ 'auto_insert' ];

        // Store the inputs for the next time.
        update_user_meta( get_current_user_id(), AmazonAutoLinks_Registry::$aUserMeta[ 'last_inputs' ], $aInputs );

        // Format the unit options to sanitize the data.
        $_oUnitOptions = new AmazonAutoLinks_UnitOption_scratchpad_payload(
            null,   // unit id
            $aInputs
        );
        $aInputs                  = $_oUnitOptions->get();
        $aInputs[ 'template_id' ] = $_oTemplateOption->getDefaultTemplateIDByUnitType( 'feed' );

        // Create a unit post
        $_iNewPostID = $this->insertPost( 
            $aInputs,
            AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ]
        );
                
        // Create an auto insert
        if ( $_bDoAutoInsert ) {
            $this->createAutoInsert( $_iNewPostID );
        }
        
        // Clean the temporary form options data.
        $this->deleteTransient(
            $GLOBALS[ 'aal_transient_id' ]
        );
        
        // Schedule pre-fetching the unit feed in the background
        // so that by the time the user opens the unit page, the cache will be ready.
        AmazonAutoLinks_Event_Scheduler::prefetch( $_iNewPostID );

        // Go to the post editing page and exit. This way the framework won't create a new form transient row.
        $this->goToPostDefinitionPage(
            $_iNewPostID,
            AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ]
        );        
        
        // This won't be reached.
        return $aInputs;
        
    }   
            
}