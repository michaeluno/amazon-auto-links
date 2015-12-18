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
class AmazonAutoLinks_UnitPostMetaBox_CommonAdvanced extends AmazonAutoLinks_UnitPostMetaBox_Base {
    
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
                'translation'  => $this->_getActiveButtonLabelsForJavaScript(),
            )
        );         
        
        add_filter( 'field_definition_' . $this->oProp->sClassName . '_button_id', array( $this, 'replyToSetActiveButtonLabels' ) );
        
    }
        /**
         * Modifies the 'button_id' field to add lables for selection.
         * @return      array
         * @since       3.3.0
         */
        public function replyToSetActiveButtonLabels( $aFieldset ) {
            
            $aFieldset[ 'label' ] = $this->_getActiveButtonLabelsForFields();
            return $aFieldset;
            
        }
            /**
             * @return      array
             * @since       3.3.0
             */
            private function _getActiveButtonLabelsForFields() {
                
                $_aButtonIDs = AmazonAutoLinks_PluginUtility::getActiveButtonIDs();
                $_aLabels    = array();
                foreach( $_aButtonIDs as $_iButtonID ) {
                    $_aLabels[ $_iButtonID ] = get_the_title( $_iButtonID )
                        . ' - ' . get_post_meta( $_iButtonID, 'button_label', true );
                }
                return $_aLabels;           
                
            }
    
        /**
         * @return      array
         */
        private function _getActiveButtonLabelsForJavaScript() {
            
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