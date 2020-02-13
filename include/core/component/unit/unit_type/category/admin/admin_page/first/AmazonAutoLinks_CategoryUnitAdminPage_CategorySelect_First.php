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
 * Adds the 'Add Unit by Category' tab to the 'Add Unit by Category' page of the loader plugin.
 * 
 * @since       3
 * @extends     AmazonAutoLinks_AdminPage_Tab_Base
 */
class AmazonAutoLinks_CategoryUnitAdminPage_CategorySelect_First extends AmazonAutoLinks_AdminPage_Tab_Base {
    
    protected function _construct( $oFactory ) {}
    
    /**
     * Triggered when the tab is loaded.
     */
    public function replyToLoadTab( $oAdminPage ) {
        
        // Form sections
        new AmazonAutoLinks_CategoryUnitAdminPage_CategorySelect_First_BasicInormation( 
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
     * @callback        filter      validation_{page slug}_{tab slug}
     */
    public function validate( $aInput, $aOldInput, $oFactory, $aSubmitInfo ) {

        $_bVerified = ! $oFactory->hasFieldError();
        $_aErrors   = array();
        $_oOption   = AmazonAutoLinks_Option::getInstance();
    
        // Check the limitation.
        if ( $_oOption->isUnitLimitReached() ) {

            // must set an field error array which does not yield empty so that it won't be redirected.
            $oFactory->setFieldErrors( array( 'error' ) );        
            $oFactory->setSettingNotice( $this->getUpgradePromptMessageToAddMoreUnits() );
            return $aOldInput;
            
        }   

        // An invalid value is found. Set a field error array and an admin notice and return the old values.
        if ( ! $_bVerified ) {
            $oFactory->setFieldErrors( $_aErrors );     
            $oFactory->setSettingNotice( __( 'There was something wrong with your input.', 'amazon-auto-links' ) );
            return $aInput;
        }        
             
        // $aInput[ 'transient_id' ] = isset( $_REQUEST[ 'transient_id' ] )
            // ? $_REQUEST[ 'transient_id' ]
            // : AmazonAutoLinks_Registry::TRANSIENT_PREFIX . '_CreateUnit_' . uniqid();
        $aInput[ 'bounce_url' ]   = add_query_arg(
            array(  
                'transient_id'  => $aInput[ 'transient_id' ],
                'aal_action'    => 'select_category',
                'page'          => $this->sPageSlug, // AmazonAutoLinks_Registry::$aAdminPages[ 'category_select' ],
                'tab'           => $this->sTabSlug, // 'first'
            ) 
            + $_GET,
            admin_url( $GLOBALS[ 'pagenow' ] )
        );        
        $oFactory->setSettingNotice( 
            __( 'Select a category from the below list.', 'amazon-auto-links' ),
            'updated'
         );
         
        // Store the inputs for the next time.
        update_option( 
            AmazonAutoLinks_Registry::$aOptionKeys[ 'last_input' ],
            $aInput,
            false       // disable auto-load 
        );
        
        return $aInput;
        
    }
    
    
        /**
         * 
         * @remark      Will redirect the user to the next page and exits the script.
         */
        private function _goToNextPage( $aInput ) {
                
            // Format the submitted data.
            $aInput[ 'transient_id' ] = isset( $_GET[ 'transient_id' ] )
                ? $_GET[ 'transient_id' ]
                : uniqid();
            $aInput[ 'bounce_url' ]   = add_query_arg(
                array(  
                    'transient_id'  => $aInput[ 'transient_id' ],
                    'aal_action'    => 'select_category',
                    'page'          => $this->sPageSlug, // AmazonAutoLinks_Registry::$aAdminPages[ 'category_select' ],
                    'tab'           => $this->sTabSlug, // 'first'
                ) 
                + $_GET,
                admin_url( $GLOBALS[ 'pagenow' ] )
            );
return $aInput;
            // Save the data in a temporary area of the databse.
            $_bSucceed = AmazonAutoLinks_WPUtility::setTransient(
                AmazonAutoLinks_Registry::TRANSIENT_PREFIX . '_CreateUnit_' . $aInput[ 'transient_id' ],
                $aInput,
                60*10*6*24 
            );
    

            // Go to the next page.
            exit( 
                wp_safe_redirect(
                    add_query_arg(
                        array(
                            'transient_id'  => $aInput[ 'transient_id' ],
                            'aal_action'    => 'select_category',
                            'tab'           => 'second',
                        )
                        + $_GET,
                        admin_url( $GLOBALS[ 'pagenow' ] )
                    )
                )
            );
        }    
    
}
