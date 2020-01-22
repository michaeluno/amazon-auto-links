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
 * Adds the 'Edit Auto-insert' tab to the 'Add / Edit Auto-insert' page.
 * 
 * @since       3
 * @extends     AmazonAutoLinks_AdminPage_Tab_Base
 */
class AmazonAutoLinks_AutoInsertAdminPage_AutoInsert_Edit extends AmazonAutoLinks_AdminPage_Tab_Base {
        
    /**
     * Triggered when the tab is loaded.
     */
    public function replyToLoadTab( $oFactory ) {
        
        // Form Section - we use the default one ('_default'), meaning no section.
        $oFactory->addSettingSections(
            $this->sPageSlug, // target page slug
            array(
                'tab_slug'      => $this->sTabSlug,
                'section_id'    => '_default', 
                'description'   => array(
                    __( 'Define where you want to display units on the site.', 'amazon-auto-links' ),
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
       
        // field_definition_{instantiated class name}_{field ID} ho
        add_filter( 'field_definition_' . $oFactory->oProp->sClassName . '_unit_ids',  array( $this, 'replyToSetUnitLabels' ) );
             
    }
        /**
         * Returns a list of classes that define form fields.
         * @access      protected       The extended ..._New  class will override this mthoed.
         * @return      array
         */
        protected function getFormFieldClasses() {         

            return array(
                // 'AmazonAutoLinks_FormFields_AutoInsert_GoBack',
                'AmazonAutoLinks_FormFields_AutoInsert_Status',
                'AmazonAutoLinks_FormFields_AutoInsert_PostID',
                'AmazonAutoLinks_FormFields_AutoInsert_Area',
                'AmazonAutoLinks_FormFields_AutoInsert_Static',
                'AmazonAutoLinks_FormFields_AutoInsert_WhereToEnable',
                'AmazonAutoLinks_FormFields_AutoInsert_WhereToDisable',
                'AmazonAutoLinks_FormFields_AutoInsert_Save',
            );      
         
        }
        
    /**
     * Adds the label element to the `unit_ids` field.
     * @return      array
     * @since       3.3.0
     */
    public function replyToSetUnitLabels( $aFieldset ) {
        $aFieldset[ 'label' ] = $this->getPostsLabelsByPostType(
            AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ]
        );
        return $aFieldset;
    }
        
    /**
     * Form field validation.
     * 
     * @callback        validation_{page slug}_{tab_slug}
     */
    public function validate( $aInput, $aOldInput, $oFactory, $aSubmitInfo ) {

        // $_bVerified = ! $oFactory->hasFieldError();
        $_bVerified = true;
        $_aErrors   = array();
        $_oOption   = AmazonAutoLinks_Option::getInstance();
    
        // Check invalid values
        // Check necessary settings.
        if ( ! array_filter( $aInput[ 'built_in_areas' ] + $aInput[ 'static_areas' ] ) 
            && ! $aInput[ 'filter_hooks' ]
            && ! $aInput[ 'action_hooks' ]
        ) {
            
            $_sMessage = __( 'At least one area must be set.', 'amazon-auto-links' );
            $_aErrors[ 'autoinsert_built_in_areas' ] = $_sMessage;
            $_aErrors[ 'autoinsert_static_areas' ]   = $_sMessage;
            $_aErrors[ 'autoinsert_filter_hooks' ]   = $_sMessage;
            $_aErrors[ 'autoinsert_action_hooks' ]   = $_sMessage;
            $_bVerified = false;
            
        }
        if ( ! isset( $aInput[ 'unit_ids' ] ) ) {    // if no item is selected, the select input with the multiple attribute does not send the key.
            
            $_aErrors[ 'autoinsert_unit_ids' ] = __( 'A unit must be selected.', 'amazon-auto-links' );
            $_bVerified = false;
            
        } 
        
        // An invalid value is found. Set a field error array and an admin notice and return the old values.
        if ( ! $_bVerified ) {
            $oFactory->setFieldErrors( $_aErrors );     
            $oFactory->setSettingNotice( __( 'There was something wrong with your input.', 'amazon-auto-links' ) );
            return $aOldInput;
        }        
        
        // Update an auto-insert post.
        $_iPostID = isset( $aInput[ 'post_id' ] )
            ? $aInput[ 'post_id' ]
            : 0;
        $this->_updatePostMeta( 
            $_iPostID,
            $this->getSanitizedAutoInsertMeta( $aInput )
        );                            
        
        do_action( 'aal_action_update_active_auto_inserts' );
        
        return $aInput;
        
    }
    
        /**
         * 
         * @access      protected as the ..._New class extends this class and acess this method.
         */
        protected function _updatePostMeta( $iPostID, $aMeta ) {
            AmazonAutoLinks_WPUtility::updatePostMeta( 
                $iPostID, 
                $aMeta
            );            
        }
        
        /**
         * 
         * @access      protected as the ..._New class extends this class and acess this method.
         * @return      array
         */
        protected function getSanitizedAutoInsertMeta( $aMeta ) {
            
            $_oUtil = new AmazonAutoLinks_WPUtility;
            
            $_aDefinedKeys = array_keys( 
                AmazonAutoLinks_AutoInsertAdminPage::$aStructure_AutoInsertDefaultOptions 
            );
            
            // Drop keys not defined in the default structure.
            foreach( $aMeta as $_sKey => $_mValue ) {
                if ( in_array( $_sKey, $_aDefinedKeys ) ) {
                    continue;
                }
                unset( $aMeta[ $_sKey ] );
            }
            
            $aMeta[ 'filter_hooks' ]    = $_oUtil->trimDelimitedElements( $aMeta[ 'filter_hooks' ], ',' );
            $aMeta[ 'action_hooks' ]    = $_oUtil->trimDelimitedElements( $aMeta[ 'action_hooks' ], ',' );
            $aMeta[ 'enable_post_ids' ] = $_oUtil->trimDelimitedElements( $aMeta[ 'enable_post_ids' ], ',' );
            $aMeta[ 'diable_post_ids' ] = $_oUtil->trimDelimitedElements( $aMeta[ 'diable_post_ids' ], ',' );
            
            return $aMeta;
            
        }
    
    
}