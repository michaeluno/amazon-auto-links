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
class AmazonAutoLinks_SearchUnitAdminPage_SearchUnit_Second_similarity_lookup extends AmazonAutoLinks_SearchUnitAdminPage_SearchUnit_Second_Base {

    /**
     * @return array
     * @since   3.11.1
     */
    protected function _getArguments() {
        return array(
            'tab_slug'      => 'similarity_lookup',
            'title'         => __( 'Add Unit by Search', 'amazon-auto-links' )
                . ' - ' . __( 'Similarity Look-up', 'amazon-auto-links' ),
            'description'   => __( 'Create a search unit.', 'amazon-auto-links' ),
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
                'AmazonAutoLinks_FormFields_SimilarityLookupUnit_Main',
                'AmazonAutoLinks_FormFields_Unit_Common',
                'AmazonAutoLinks_FormFields_Unit_Credit',
                'AmazonAutoLinks_FormFields_Unit_AutoInsert',
                'AmazonAutoLinks_FormFields_SearchUnit_CreateButton',
            );
        }   
        
    /**
     * 
     * @since       3.4.0
     * @callback    filter      validation_{page slug}_{tab slug}
     */            
    public function validate( $aInputs, $aOldInputs, $oFactory, $aSubmitInfo ) {
        
        // Find ASINs from the user input.
        $aInputs[ 'ItemId' ] = $this->_getItemIdSanitized( $aInputs, $oFactory );        
        
        return parent::validate( $aInputs, $aOldInputs, $oFactory, $aSubmitInfo );
        
    }
        /**
         * @since       3.4.0
         * @return      string
         */
        private function _getItemIdSanitized( $aInputs, $oFactory ) {
            
            return AmazonAutoLinks_PluginUtility::getASINsExtracted( 
                $oFactory->oUtil->getElement( $aInputs, array( 'ItemId' ), '' ), 
                PHP_EOL 
            );
            
        }        
    
}