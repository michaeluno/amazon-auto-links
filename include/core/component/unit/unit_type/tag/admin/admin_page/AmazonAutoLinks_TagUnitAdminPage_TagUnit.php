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
 * @since       3
 * @action      schedule        aal_action_unit_prefetch
 */
class AmazonAutoLinks_TagUnitAdminPage_TagUnit extends AmazonAutoLinks_AdminPage_Page_Base {

    /**
     * @return array
     * @since   3.11.1
     */
    protected function _getArguments() {
        return array(
            'page_slug'     => AmazonAutoLinks_Registry::$aAdminPages[ 'tag_unit' ],
            'title'         => __( 'Add Unit by Tag', 'amazon-auto-links' ),
            'screen_icon'   => AmazonAutoLinks_Registry::getPluginURL( "asset/image/screen_icon_32x32.png" ),
        );
    }

    /**
     * 
     * @callback        action      load_{page slug}
     */ 
    public function replyToLoadPage( $oFactory ) {
        
        // Form Section - we use the default one ('_default'), meaning no section.
        $oFactory->addSettingSections(
            $this->sPageSlug, // target page slug
            array(
                // 'tab_slug'      => $this->sTabSlug,
                'section_id'    => '_default', 
                'description'   => array(
                    __( 'Define where you want to display units on the site.', 'amazon-auto-links' ),
                ),
            )     
        );        
        
        // Add Fields
        foreach( $this->_getFormFieldClasses() as $_sClassName ) {
            $_oFields = new $_sClassName;
            foreach( $_oFields->get() as $_aField ) {
                $oFactory->addSettingFields(
                    '_default', // the target section id    
                    $_aField
                );
            }                    
        }
        
    }
        /**
         * @since       3
         * @return      array
         */
        private function _getFormFieldClasses() {
            return array(
                'AmazonAutoLinks_FormFields_TagUnit_Main',
                'AmazonAutoLinks_FormFields_Unit_Common',
                'AmazonAutoLinks_FormFields_Unit_Credit',                
                'AmazonAutoLinks_FormFields_Unit_AutoInsert',
                'AmazonAutoLinks_FormFields_TagUnit_Submit',
            );
        }
    
    /**
     * 
     * @callback        action      do_after_{page slug}
     */
    public function replyToDoAfterPage( $oFactory ) {
        $_oOption = AmazonAutoLinks_Option::getInstance();
        if ( ! $_oOption->isDebug() ) {
            return;
        }
        echo "<p>Debug</p>"
            . $oFactory->oDebug->get( 
                $oFactory->oProp->aOptions 
            );       
    }
    
    /**
     * 
     * @callback        filter      validation + _ + page slug
     */
    public function validate( $aInput, $aOldInput, $oFactory, $aSubmitInfo ) {

        $_bVerified       = true;
        $_aErrors         = array();
        $_oOption         = AmazonAutoLinks_Option::getInstance();
        $_oTemplateOption = AmazonAutoLinks_TemplateOption::getInstance();
        $_oUtil           = new AmazonAutoLinks_PluginUtility;

        // Check the limitation.
        if ( $_oOption->isUnitLimitReached() ) {
            $oFactory->setSettingNotice( $this->getUpgradePromptMessageToAddMoreUnits() );
            return $aOldInput;
        }        
        
        // If specified and the customer ID is not 13 characters,
        $aInput[ 'customer_id' ] = trim( $aInput[ 'customer_id' ] );
        if ( $aInput[ 'customer_id' ] && strlen( $aInput[ 'customer_id' ] ) != 13 ) {
            $_aErrors[ 'customer_id' ] = __( 'The customer ID must consist of 13 characters.', 'amazon-auto-links' ) . ' ';
            $_bVerified = false;
        }
        
        if ( empty( $aInput[ 'tags' ] ) && empty( $aInput[ 'customer_id' ] ) ) {
            $_aErrors[ 'tags' ]        = __( 'Either tags or customer ID has to be entered.', 'amazon-auto-links' );
            $_sMessage                 = __( 'Either tags or customer ID has to be entered.', 'amazon-auto-links' );
            $_aErrors[ 'customer_id' ] = isset( $_aErrors[ 'customer_id' ] )
                ? $_aErrors[ 'customer_id' ] . $_sMessage
                : $_sMessage;
            $_bVerified = false;            
        }
        
        if ( empty( $aInput[ 'associate_id' ] ) ) {
            $_aErrors[ 'associate_id' ] = __( 'The associate ID cannot be empty.', 'amazon-auto-links' );
            $_bVerified = false;
        }
        
        // An invalid value is found.
        if ( ! $_bVerified ) {
        
            // Set the error array for the input fields.
            $oFactory->setFieldErrors( $_aErrors );        
            $oFactory->setSettingNotice( __( 'There was an error in your input.', 'amazon-auto-links' ) );
            return $aInput;
            
        }        

        // Sanitize the tag input
        $aInput[ 'tags' ] = trim( 
            $_oUtil->trimDelimitedElements( 
                $aInput[ 'tags' ], 
                ',' 
            ) 
        ); 
        
        $_bDoAutoInsert = $aInput[ 'auto_insert' ];
   
        // Format the unit options to sanitize the data.
        $_oUnitOptions = new AmazonAutoLinks_UnitOption_tag(
            null,   // unit id
            $aInput
        );
        $aInput                  = $_oUnitOptions->get();
        $aInput[ 'template_id' ] = $_oTemplateOption->getDefaultTemplateIDByUnitType( 
            'tag'
        );

        // Create a unit post
        $_iNewPostID = $_oUtil->insertPost( 
            $aInput,
            AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ]
        );
                
        // Create an auto insert
        if ( $_bDoAutoInsert ) {
            $_oUtil->createAutoInsert( $_iNewPostID );
        }
        
        // Clean the temporary form options data.
        $_oUtil->deleteTransient(
            $GLOBALS[ 'aal_transient_id' ]
        );
        
        // Schedule pre-fetching the unit feed in the background
        // so that by the time the user opens the unit page, the cache will be ready.
        AmazonAutoLinks_Event_Scheduler::prefetch( $_iNewPostID );
        
        // Store the inputs for the next time.
        update_option( 
            AmazonAutoLinks_Registry::$aOptionKeys[ 'last_input' ],
            $aInput,
            false       // disable auto-load 
        );           
        
        // Go to the post editing page and exit. This way the framework won't create a new form transient row.
        $_oUtil->goToPostDefinitionPage(
            $_iNewPostID,
            AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ]
        );        
        
        // This won't be reached.
        return $aInput;
        
    }   
            
}
