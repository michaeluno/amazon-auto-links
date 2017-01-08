<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2017 Michael Uno
 */

/**
 * Adds a setting page for creating tag units.
 * 
 * @since       3.2.0
 * @action      schedule        aal_action_unit_prefetch
 */
class AmazonAutoLinks_URLUnitAdminPage_URLUnit extends AmazonAutoLinks_AdminPage_Page_Base {
    
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
                    __( 'The URL unit type allows you to search products found in the page with specified urls.', 'amazon-auto-links' ),
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
        
        // Custom messages
        $oFactory->setMessage(
            'allowed_maximum_number_of_fields',
            sprintf(
                __( 'Please upgrade to <a href="%1$s">Pro</a> to add more items!', 'amazon-auto-links' ),
                'http://en.michaeluno.jp/amazon-auto-links-pro'
            )
        );        
        
    }
        /**
         * @since       3
         * @return      array
         */
        private function _getFormFieldClasses() {
            return array(
                'AmazonAutoLinks_FormFields_URLUnit_Main',
                'AmazonAutoLinks_FormFields_Unit_Common',
                'AmazonAutoLinks_FormFields_Unit_Credit',
                'AmazonAutoLinks_FormFields_Unit_AutoInsert',
                'AmazonAutoLinks_FormFields_URLUnit_Submit',
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
            $oFactory->setFieldErrors( $_aErrors + array( true ) );     // this prevents the submit redirect routine
            $oFactory->setSettingNotice( 
                sprintf( 
                    __( 'Please upgrade to <A href="%1$s">Pro</a> to add more units! Make sure to empty the <a href="%2$s">trash box</a> to delete the units completely!', 'amazon-auto-links' ), 
                    'http://en.michaeluno.jp/amazon-auto-links-pro/',
                    admin_url( 'edit.php?post_status=trash&post_type=' . AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ] )
                )
            );
            return $aOldInput;
        }        
        
        // Check if a url iset.
        $aInput[ 'urls' ] = $_oUtil->getAsArray( $aInput[ 'urls' ] );
        if ( empty( $aInput[ 'urls' ] ) ) {
            $_aErrors[ 'urls' ] = __( 'Please set a url.', 'amazon-auto-links' );
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
        
        $_bDoAutoInsert = $aInput[ 'auto_insert' ];
   
        // Format the unit options to sanitize the data.
        $_oUnitOptions = new AmazonAutoLinks_UnitOption_url(
            null,   // unit id
            $aInput
        );
        $aInput                  = $_oUnitOptions->get();
        $aInput[ 'template_id' ] = $_oTemplateOption->getDefaultTemplateIDByUnitType( 
            'url'
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
