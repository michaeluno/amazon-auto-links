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
 * Adds a tab to a setting page.
 * 
 * @since       3
 * @extends     AmazonAutoLinks_AdminPage_Tab_Base
 */
class AmazonAutoLinks_SearchUnitAdminPage_SearchUnit_First extends AmazonAutoLinks_AdminPage_Tab_Base {

    /**
     * @return array
     * @since   3.11.1
     */
    protected function _getArguments() {
        return array(
            'tab_slug'      => 'first',
            'title'         => __( 'Add Unit by Search', 'amazon-auto-links' ),
            'description'   => __( 'Select the search type.', 'amazon-auto-links' ),
        );
    }

    protected function _construct( $oFactory ) {}
    
    /**
     * Triggered when the tab is loaded.
     */
    public function replyToLoadTab( $oFactory ) {
      
        // Add form fields
        $oFactory->addSettingSections(
            $this->sPageSlug, // target page slug
            array(
                'tab_slug'      => $this->sTabSlug,
                'section_id'    => '_default', 
                'description'   => array(
                    __( 'Select a search type.', 'amazon-auto-links' ),
                ),
            )     
        );        
        
        // Add Fields
        foreach( $this->getFormFieldClasses() as $_sClassName ) {
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
         * @return  array
         */
        private function getFormFieldClasses() {
            return array(
                'AmazonAutoLinks_FormFields_SearchUnit_SearchType',
                'AmazonAutoLinks_FormFields_SearchUnit_ProceedButton',
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
        
        if ( empty( $aInput[ 'associate_id' ] ) ) {
            
            $_aErrors[ 'associate_id' ] = __( 'The associate ID cannot be empty.', 'amazon-auto-links' );
            $_bVerified = false;                            
            
        }        

        // An invalid value is found. Set a field error array and an admin notice and return the old values.
        if ( ! $_bVerified ) {
            $oFactory->setFieldErrors( $_aErrors );     
            $oFactory->setSettingNotice( __( 'There was an error in your input.', 'amazon-auto-links' ) );
            return $aInput;
        }        
        
        // This should be the last check in the entire page validation checks.
        if ( 
            $oFactory->oUtil->hasSuffix( 
                'submit_proceed',
                $aSubmitInfo[ 'field_id' ]
            )
        ) {
            // Will exit the script.
            unset( $aInput[ 'submit_proceed' ] );
            $this->___goToNextPage( $aInput );
        }
        
        return $aInput;
        
    }
    
    
        /**
         * 
         * @remark      Will redirect the user to the next page and exits the script.
         */
        private function ___goToNextPage( $aInput ) {
            
            // Set the unit type based on the chosen one.
            // Redirect to the appropriate page by the search type.
            switch( $aInput[ 'Operation' ] ) {
                default:
                case 'ItemSearch':
                    $_sTabSlug = 'search_products';                
                    break;
                case 'ItemLookup':
                    $_sTabSlug = 'item_lookup';
                    break;
                case 'SimilarityLookup':
                    $_sTabSlug = 'similarity_lookup';
                    break;
            }                

            $this->setTransient(
                $aInput[ 'transient_id' ],  // key
                $aInput, // data
                60*10*6*24 // seconds 
            );

            // Go to the next page.
            exit( 
                wp_redirect( 
                    add_query_arg( 
                        array( 
                            'tab'          => $_sTabSlug, 
                            'transient_id' => $aInput[ 'transient_id' ],
                       ) + $_GET,
                       $aInput[ 'bounce_url' ] 
                    ) 
                )
            );
        }    

}