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
 * Provides some shared methods.
 * 
 * @since       3
 * @extends     AmazonAutoLinks_AdminPage_Tab_Base
 */
abstract class AmazonAutoLinks_SearchUnitAdminPage_SearchUnit_Second_Base extends AmazonAutoLinks_AdminPage_Tab_Base {
      
    /**
     * 
     * @callback        filter      validation_{page slug}_{tab slug}
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
        update_option( 
            AmazonAutoLinks_Registry::$aOptionKeys[ 'last_input' ],
            $aInputs,
            false       // disable auto-load 
        );            
        
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
     * @since       3
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
