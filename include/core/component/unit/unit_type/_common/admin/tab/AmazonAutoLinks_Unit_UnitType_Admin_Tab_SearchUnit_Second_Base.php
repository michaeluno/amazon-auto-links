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
 * Provides some shared methods.
 * 
 * @since 3
 * @since 5.0.0 Renamed from `AmazonAutoLinks_SearchUnitAdminPage_SearchUnit_Second_Base`.
 */
abstract class AmazonAutoLinks_Unit_UnitType_Admin_Tab_SearchUnit_Second_Base extends AmazonAutoLinks_AdminPage_Tab_Base {

    /**
     * Triggered when the tab is loaded.
     */
    protected function _loadTab( $oFactory ) {

        // Add form fields
        $oFactory->addSettingSections(
            $this->sPageSlug, // target page slug
            array(
                'tab_slug'      => $this->sTabSlug,
                'section_id'    => '_default',
                'description'   => array(
                    __( 'Create a unit.', 'amazon-auto-links' ),
                ),
            )
        );

        // Add Fields
        $_aValues = $oFactory->getValue();
        foreach( $this->_getFormFieldClasses() as $_sClassName ) {
            $_oFields = new $_sClassName( $oFactory );
            foreach( $_oFields->get( '', $_aValues ) as $_aField ) {
                $oFactory->addSettingFields(
                    '_default', // the target section id
                    $_aField
                );
            }
        }

    }

    /**
     * @return array
     * @since  5.0.0
     */
    protected function _getFormFieldClasses() {
        return array(
            'AmazonAutoLinks_FormFields_Unit_Common',
            'AmazonAutoLinks_FormFields_Unit_Credit',
            'AmazonAutoLinks_FormFields_Unit_AutoInsert',
            'AmazonAutoLinks_FormFields_SearchUnit_CreateButton',
        );
    }

    /**
     * @return    array
     * @callback  add_filter() validation_{page slug}_{tab slug}
     */            
    public function validate( $aInputs, $aOldInputs, $oFactory, $aSubmitInfo ) {

        $_bVerified = ! $oFactory->hasFieldError();
        $_aErrors   = array();
        $_oOption   = AmazonAutoLinks_Option::getInstance();

        // An invalid value is found. Set a field error array and an admin notice and return the old values.
        if ( ! $_bVerified ) {
            $oFactory->setFieldErrors( $_aErrors );     
            $oFactory->setSettingNotice( __( 'There was something wrong with your input.', 'amazon-auto-links' ) );
            return $aInputs;
        }        

        // Create a unit - the method will take care of data sanitization.    
        $_iNewPostID = $this->___createSearchUnit( $aInputs );
        
        // Store the inputs for the next time.
        update_user_meta( get_current_user_id(), AmazonAutoLinks_Registry::$aUserMeta[ 'last_inputs' ], $aInputs );
        
        $this->_goToNextPage( $_iNewPostID );
        
        return $aInputs;
        
    }      
      
    protected function _goToNextPage( $iPostID ) {

        // Clean up form data
        $this->deleteTransient( $GLOBALS[ 'aal_transient_id' ] );
        
        // Schedule pre-fetch
        AmazonAutoLinks_Event_Scheduler::prefetch( $iPostID );
        
        // Go to the post definition page.
        $this->goToPostDefinitionPage(
            $iPostID,   // post id
            AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ] // post type slug
        );
        
    }
      
    /**
     * Creates a search unit type
     * 
     * @since       2.0.2
     * @return      integer     A newly created post id.
     */
    private function ___createSearchUnit( $aInputs ) {
        
        $_oTemplateOption   = AmazonAutoLinks_TemplateOption::getInstance();
        $_bDoAutoInsert     = $aInputs[ 'auto_insert' ];

        // Sanitize the unit options
        $_sClassName        = "AmazonAutoLinks_UnitOption_{$aInputs[ 'unit_type' ]}";
        $_oUnitOptions      = new $_sClassName(
            null,   // unit id
            $aInputs
        );
        $aInputs                  = $_oUnitOptions->get(); // will format the options.
        $aInputs[ 'template_id' ] = $_oTemplateOption->getDefaultTemplateIDByUnitType( 
            $aInputs[ 'unit_type' ]
        );                

        // Create a post.            
        $_iNewPostID = $this->insertPost(
            $aInputs, // data
            AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ]
        );        
        
        // Create an auto insert
        if ( $_bDoAutoInsert ) {
            $this->_createAutoInsert( $_iNewPostID );
        }     
        
        return $_iNewPostID;
                
    }
    
    /**
     * 
     * @since 3
     */
    protected function _createAutoInsert( $iNewPostID ) {
        
        // Construct a meta array.
        $_aAutoInsertOptions = array( 
            'unit_ids' => array( $iNewPostID ),
        ) + AmazonAutoLinks_AutoInsertAdminPage::$aStructure_AutoInsertDefaultOptions;
        
        // Insert a post.
        $this->insertPost(
            $_aAutoInsertOptions, 
            AmazonAutoLinks_Registry::$aPostTypes[ 'auto_insert' ]
        );            
        
    }    
            
}