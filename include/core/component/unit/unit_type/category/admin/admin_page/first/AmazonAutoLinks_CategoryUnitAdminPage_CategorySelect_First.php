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
 * Adds the 'Add Unit by Category' tab to the 'Add Unit by Category' page of the loader plugin.
 * 
 * @since       3
 */
class AmazonAutoLinks_CategoryUnitAdminPage_CategorySelect_First extends AmazonAutoLinks_AdminPage_Tab_Base {
    
    protected function _construct( $oFactory ) {}

    /**
     * @return array
     */
    protected function _getArguments() {
        return array(
            'tab_slug'      => 'first',
            'title'         => __( 'Add Unit by Category', 'amazon-auto-links' ),
            'description'   => __( 'Fill basic information', 'amazon-auto-links' ),
        );
    }

    /**
     * Triggered when the tab is loaded.
     */
    public function replyToLoadTab( $oAdminPage ) {

        new AmazonAutoLinks_Select2CustomFieldType( $oAdminPage->oProp->sClassName );

        // Form sections
        new AmazonAutoLinks_CategoryUnitAdminPage_CategorySelect_First_BasicInformation(
            $oAdminPage,
            $this->sPageSlug, 
            array(
                'tab_slug'      => $this->sTabSlug,
                'section_id'    => '_default', 
                'title'         => __( 'Basic Information', 'amazon-auto-links' ),
                'description'   => array(
                    __( 'Fill the basic information and proceed the form to select categories.', 'amazon-auto-links' ),
                ),
            )
        );           
        new AmazonAutoLinks_CategoryUnitAdminPage_CategorySelect_First_AutoInsert( 
            $oAdminPage,
            $this->sPageSlug, 
            array(
                'tab_slug'      => $this->sTabSlug,
                'section_id'    => '_default', 
            )
        );                
        
    }
    
    /**
     * 
     * @callback add_filter() validation_{page slug}_{tab slug}
     * @return   array
     */
    public function validate( $aInputs, $aOldInputs, $oFactory, $aSubmitInfo ) {

        $_bVerified = ! $oFactory->hasFieldError();
        $_aErrors   = array();
        $_oOption   = AmazonAutoLinks_Option::getInstance();
    
        // Check the limitation.
        if ( $_oOption->isUnitLimitReached() ) {

            // must set a field error array which does not yield empty so that it won't be redirected.
            $oFactory->setFieldErrors( array( 'error' ) );        
            $oFactory->setSettingNotice( AmazonAutoLinks_Message::getUpgradePromptMessageToAddMoreUnits() );
            return $aOldInputs;
            
        }   

        // An invalid value is found. Set a field error array and an admin notice and return the old values.
        if ( ! $_bVerified ) {
            $oFactory->setFieldErrors( $_aErrors );     
            $oFactory->setSettingNotice( __( 'There was something wrong with your input.', 'amazon-auto-links' ) );
            return $aInputs;
        }

        $aInputs[ 'bounce_url' ]   = add_query_arg(
            array(  
                'transient_id'  => $aInputs[ 'transient_id' ],
                'aal_action'    => 'select_category',
                'page'          => $this->sPageSlug, // AmazonAutoLinks_Registry::$aAdminPages[ 'category_select' ],
                'tab'           => $this->sTabSlug, // 'first'
            ) 
            + $this->getHTTPQueryGET(),
            admin_url( $GLOBALS[ 'pagenow' ] )
        );

        $oFactory->setSettingNotice( '' ); // disable the message
         
        // Store the inputs for the next time.
        update_user_meta( get_current_user_id(), AmazonAutoLinks_Registry::$aUserMeta[ 'last_inputs' ], $aInputs );
        
        return $aInputs;
        
    }

}