<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2018 Michael Uno
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
    public function validate( $aInput, $aOldInput, $oFactory, $aSubmitInfo ) {

        $_bVerified = ! $oFactory->hasFieldError();
        $_aErrors   = array();
        $_oOption   = AmazonAutoLinks_Option::getInstance();
    
     
        // An invalid value is found. Set a field error array and an admin notice and return the old values.
        if ( ! $_bVerified ) {
            $oFactory->setFieldErrors( $_aErrors );     
            $oFactory->setSettingNotice( __( 'There was something wrong with your input.', 'amazon-auto-links' ) );
            return $aInput;
        }        

        // Create a unit - the method will take care of data sanitization.    
        $_iNewPostID = $this->_createSearchUnit( $aInput );
        
        // Store the inputs for the next time.
        update_option( 
            AmazonAutoLinks_Registry::$aOptionKeys[ 'last_input' ],
            $aInput,
            false       // disable auto-load 
        );            
        
        $this->_goToNextPage( $_iNewPostID );
        
        return $aInput;
        
    }      
      
    protected function _goToNextPage( $iPostID ) {

        $_oUtil = new AmazonAutoLinks_PluginUtility;
        
        // Clean up form data
        $_oUtil->deleteTransient(
            $GLOBALS[ 'aal_transient_id' ]
        );
        
        // Schedule pre-fetch
        AmazonAutoLinks_Event_Scheduler::prefetch( $iPostID );
        
        // Go to the post definition page.
        $_oUtil->goToPostDefinitionPage(
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
    protected function _createSearchUnit( $aInput ) {

        $_oOption           = AmazonAutoLinks_Option::getInstance();
        $_oTemplateOption   = AmazonAutoLinks_TemplateOption::getInstance();
        $_oUtil             = new AmazonAutoLinks_PluginUtility;
        $_bDoAutoInsert     = $aInput[ 'auto_insert' ];
      
        // Sanitize the unit options
        $_sClassName   = "AmazonAutoLinks_UnitOption_{$aInput[ 'unit_type' ]}";
        $_oUnitOptions = new $_sClassName(
            null,   // unit id
            $aInput
        );
        $aInput                  = $_oUnitOptions->get(); // will format the options.
        $aInput[ 'template_id' ] = $_oTemplateOption->getDefaultTemplateIDByUnitType( 
            $aInput[ 'unit_type' ]
        );                

        // Create a post.            
        $_iNewPostID = $_oUtil->insertPost( 
            $aInput, // data
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
        AmazonAutoLinks_WPUtility::insertPost( 
            $_aAutoInsertOptions, 
            AmazonAutoLinks_Registry::$aPostTypes[ 'auto_insert' ]
        );            
        
    }    
            
}
