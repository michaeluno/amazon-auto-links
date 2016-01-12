<?php
/**
 * Amazon Auto Links
 * 
 * 
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2016 Michael Uno; Licensed GPLv2
 * 
 */

/**
 * Adds a tab to a setting page.
 * 
 * @since       3
 * @extends     AmazonAutoLinks_AdminPage_Tab_Base
 */
class AmazonAutoLinks_SearchUnitAdminPage_SearchUnit_Second_item_lookup extends AmazonAutoLinks_SearchUnitAdminPage_SearchUnit_Second_Base {
    
    public function construct( $oFactory ) {}
    
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
                    __( 'Create a unit.', 'amazon-auto-links' ),
                ),
            )     
        );        
        
        // Add Fields
        foreach( $this->_getFormFieldClasses() as $_sClassName ) {
            $_oFields = new $_sClassName;
            foreach( $_oFields->get( '', $oFactory->getValue() ) as $_aField ) {
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
        private function _getFormFieldClasses() {
            return array(
                'AmazonAutoLinks_FormFields_ItemLookupUnit_Main',
                'AmazonAutoLinks_FormFields_Unit_Common',
                'AmazonAutoLinks_FormFields_Unit_Credit',                
                'AmazonAutoLinks_FormFields_Unit_AutoInsert',
                'AmazonAutoLinks_FormFields_SearchUnit_CreateButton',
            );
        }        
        
   
    
}
