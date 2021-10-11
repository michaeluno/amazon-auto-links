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
class AmazonAutoLinks_URLUnitAdminPage_URLUnit extends AmazonAutoLinks_AdminPage_Page_Base {

    /**
     * @var string
     */
    public $sTabSlug = 'first';

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
            'style'         => AmazonAutoLinks_Main_Loader::$sDirPath . '/asset/css/admin.css',
            'capability'    => $_oOption->get( array( 'capabilities', 'create_units' ), 'edit_pages' ),
        );
    }

    /**
     * @param    $oFactory  AmazonAutoLinks_AdminPageFramework
     * @callback add_action load_{page slug}
     */ 
    public function replyToLoadPage( $oFactory ) {

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
        $oFactory->addSettingSections(
            $this->sPageSlug, // target page slug
            $this->_getSectionArguments()
        );        
        
        // Add Fields
        foreach( $this->_getFormFieldClasses() as $_sClassName ) {
            $_oFields = new $_sClassName( $oFactory );
            foreach( $_oFields->get() as $_aField ) {
                if ( 'associate_id' === $_aField[ 'field_id' ] ) {
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
    public function validate( $aInput, $aOldInput, $oFactory, $aSubmitInfo ) {

        $_bVerified       = true;
        $_aErrors         = array();
        $_oOption         = AmazonAutoLinks_Option::getInstance();
        $_oTemplateOption = AmazonAutoLinks_TemplateOption::getInstance();
        $_oUtil           = new AmazonAutoLinks_PluginUtility;

        // Check the limitation.
        if ( $_oOption->isUnitLimitReached() ) {
            $oFactory->setFieldErrors( $_aErrors + array( true ) );     // this prevents the submit redirect routine
            $oFactory->setSettingNotice( AmazonAutoLinks_Message::getUpgradePromptMessageToAddMoreUnits() );
            return $aOldInput;
        }        
        
        // Check if a URL is set.
        $aInput[ 'urls' ] = $_oUtil->getAsArray( $aInput[ 'urls' ] );
        if ( empty( $aInput[ 'urls' ] ) ) {
            $_aErrors[ 'urls' ] = __( 'Please set a url.', 'amazon-auto-links' );
            $_bVerified = false;
        }

        $aInput[ 'associate_id' ] = $_oOption->getAssociateID( $aInput[ 'country' ] );

        // An invalid value is found.
        if ( ! $_bVerified ) {
        
            // Set the error array for the input fields.
            $oFactory->setFieldErrors( $_aErrors );        
            $oFactory->setSettingNotice( __( 'There was an error in your input.', 'amazon-auto-links' ) );
            return $aInput;
            
        }        
        
        $_bDoAutoInsert = $aInput[ 'auto_insert' ];

        // Store the inputs for the next time.
        update_user_meta( get_current_user_id(), AmazonAutoLinks_Registry::$aUserMeta[ 'last_inputs' ], $aInput );

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

        // Go to the post editing page and exit. This way the framework won't create a new form transient row.
        $_oUtil->goToPostDefinitionPage(
            $_iNewPostID,
            AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ]
        );        
        
        // This won't be reached.
        return $aInput;
        
    }   
            
}