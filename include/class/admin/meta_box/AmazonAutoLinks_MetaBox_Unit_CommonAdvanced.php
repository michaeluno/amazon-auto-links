<?php
/**
 * Amazon Auto Links
 * 
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2015 Michael Uno
 * 
 */

/**
 * Defines the meta box that contains common advanced unit options.
 */
class AmazonAutoLinks_MetaBox_Unit_CommonAdvanced extends AmazonAutoLinks_MetaBox_Base {
    
    /**
     * Stores the unit type slug(s). 
     */    
    protected $aUnitTypes = array( 
        'category', 
        'similarity_lookup',
        'item_lookup',
        'search',
        'tag',        
    );    
    
    /**
     * Sets up form fields.
     */ 
    public function setUp() {
        
        $_aClasses = array(
            'AmazonAutoLinks_FormFields_Unit_CommonAdvanced',
            'AmazonAutoLinks_FormFields_Button_Selector',
        );
        foreach( $_aClasses as $_sClassName ) {
            $_oFields = new $_sClassName;
            $_aFields = $_oFields->get();
            foreach( $_aFields as $_aField ) {           
                $this->addSettingFields( $_aField );
            }            
        }            
        
        $this->enqueueScript(
            AmazonAutoLinks_Registry::$sDirPath . '/asset/js/button-preview-in-unit-definition-page.js',
            $this->oProp->aPostTypes,
            array(  
                'handle_id'    => 'aal_button_preview_labels',
                'dependencies' => array( 'jquery' ),
                'translation'  => $this->_getActiveButtonLabels(),
            )
        );         
        
    }
    
        /**
         * @return      array
         */
        private function _getActiveButtonLabels() {
            
            $_aButtonIDs = AmazonAutoLinks_PluginUtility::getActiveButtonIDs();
            $_aLabels    = array();
            foreach( $_aButtonIDs as $_iButtonID ) {
                $_sButtonLabel = get_post_meta( $_iButtonID, 'button_label', true );
                $_sButtonLabel = $_sButtonLabel
                    ? $_sButtonLabel
                    : __( 'Buy Now', 'amazon-auto-links' );
                $_aLabels[ $_iButtonID ] = $_sButtonLabel;
            }
            return $_aLabels;
            
        }    
    /**
     * Validates submitted form data.
     */
    public function validate( $aInput, $aOriginal, $oFactory ) {    
        return $aInput;        
    }
    
}