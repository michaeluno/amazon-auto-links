<?php
/**
 * Amazon Auto Links
 * 
 * http://en.michaeluno.jp/amazon auto links/
 * Copyright (c) 2013-2016 Michael Uno
 * 
 */
 
/**
 * @since       3.4.0
 */
class AmazonAutoLinks_AdminPage_Setting_Default_PageMetaBox_CommonAdvanced extends AmazonAutoLinks_AdminPage_Setting_Default_PageMetaBox_Base {
        
    /**
     * Sets up form fields.
     */ 
    public function setUp() {
        
        $this->addSettingSections(
            array(
                'section_id'    => $this->_sSectionID,
            )
        );        
        $this->addSettingFields( $this->_sSectionID );        
        
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
        
        add_filter( 
            'field_definition_' . $this->oProp->sClassName . '_button_id', 
            array( $this, 'replyToSetActiveButtonLabels' ) 
        );
        
    }
        /**
         * Modifies the 'button_id' field to add labels for selection.
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
     * Validates submitted form data.
     */
    public function validate( $aInput, $aOriginal, $oFactory ) {    
        return $aInput;        
    }
 
}
