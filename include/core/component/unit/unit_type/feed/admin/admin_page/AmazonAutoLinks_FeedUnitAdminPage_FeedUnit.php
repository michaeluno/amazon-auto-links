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
 * Adds a setting page for creating tag units.
 * 
 * @since       4.0.0
 * @action      schedule        aal_action_unit_prefetch
 */
class AmazonAutoLinks_FeedUnitAdminPage_FeedUnit extends AmazonAutoLinks_URLUnitAdminPage_URLUnit {

    /**
     * @return  array
     * @since   4.0.0
     */
    protected function _getArguments() {
        return array(
            'page_slug'     => AmazonAutoLinks_Registry::$aAdminPages[ 'feed_unit' ],
            'title'         => __( 'Add Feed Unit', 'amazon-auto-links' ),
            'screen_icon'   => AmazonAutoLinks_Registry::getPluginURL( "asset/image/screen_icon_32x32.png" ),
            'style'         => AmazonAutoLinks_Registry::getPluginURL( 'asset/css/admin.css' ),
        );
    }

    /**
     * @return  array
     * @since   4.0.0
     */
    protected function _getSectionArguments() {
        return array(
            // 'tab_slug'      => $this->sTabSlug,
            'section_id'    => '_default',
            'description'   => array(
                __( 'The feed unit type allows you to import unit data of external sites that set up Amazon Auto Links. Make use of this unit to save API calls.', 'amazon-auto-links' ),
                __( 'After creating a unit on another site, copy the JSON feed URL found in the Manage Units page. Then paste the URL in the option field here.', 'amazon-auto-links' ),
            ),
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
     * 
     * @callback        filter      validation + _ + page slug
     */
    public function validate( $aInputs, $aOldInputs, $oFactory, $aSubmitInfo ) {

        $_bVerified       = true;
        $_aErrors         = array();
        $_oOption         = AmazonAutoLinks_Option::getInstance();
        $_oTemplateOption = AmazonAutoLinks_TemplateOption::getInstance();

        // Check the limitation.
        if ( $_oOption->isUnitLimitReached() ) {
            $oFactory->setFieldErrors( $_aErrors + array( true ) );     // this prevents the submit redirect routine
            $oFactory->setSettingNotice( $this->getUpgradePromptMessageToAddMoreUnits() );
            return $aOldInputs;
        }        
        
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
        
        $_bDoAutoInsert = $aInputs[ 'auto_insert' ];

        // Store the inputs for the next time.
        update_option(
            AmazonAutoLinks_Registry::$aOptionKeys[ 'last_input' ],
            $aInputs,
            false       // disable auto-load
        );

        // Format the unit options to sanitize the data.
        $_oUnitOptions = new AmazonAutoLinks_UnitOption_feed(
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
