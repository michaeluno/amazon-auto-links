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
class AmazonAutoLinks_SearchUnitAdminPage_SearchUnit_Second_search_products extends AmazonAutoLinks_SearchUnitAdminPage_SearchUnit_Second_Base {

    /**
     * @return array
     * @since   3.11.1
     */
    protected function _getArguments() {
        return array(
            'tab_slug'      => 'search_products',
            'title'         => __( 'Add Unit by Search', 'amazon-auto-links' )
                . ' - ' . __( 'Product Search', 'amazon-auto-links' ),
            'description'   => __( 'Create a search unit.', 'amazon-auto-links' ),
        );
    }

    protected function _construct( $oFactory ) {}
    
    /**
     * Triggered when the tab is loaded.
     */
    protected function _loadTab( $oFactory ) {
      
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
        $_aValues = $oFactory->getValue();
        foreach( $this->___getFormFieldClasses() as $_sClassName ) {
            $_oFields = new $_sClassName;
            foreach( $_oFields->get( '', $_aValues ) as $_aField ) {
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
        private function ___getFormFieldClasses() {
            return array(
                'AmazonAutoLinks_FormFields_SearchUnit_ProductSearch',
                'AmazonAutoLinks_FormFields_Unit_Common',
                'AmazonAutoLinks_FormFields_Unit_Credit',                
                'AmazonAutoLinks_FormFields_Unit_AutoInsert',
                'AmazonAutoLinks_FormFields_SearchUnit_CreateButton',
            );
        }        

}