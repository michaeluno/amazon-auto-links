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
 * @since       3.4.0
 */
class AmazonAutoLinks_AdminPage_Setting_Default_PageMetaBox_CommonAdvanced extends AmazonAutoLinks_AdminPage_Setting_Default_PageMetaBox_Base {
    
    // public function start() {
        
    // }
    
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
            'field_definition_' . $this->oProp->sClassName . '_' . $this->_sSectionID . '_button_id', 
            array( $this, 'replyToSetActiveButtonLabels' ) 
        );
        
        // Resources for the button preview
        $_sFileBaseName = defined( 'WP_DEBUG' ) && WP_DEBUG
            ? 'button-preview-in-unit-definition-page.js'
            : 'button-preview-in-unit-definition-page.min.js';
        $this->enqueueScript(
            AmazonAutoLinks_Registry::$sDirPath . '/asset/js/' . $_sFileBaseName,
            $this->oProp->sPageSlug,
            'default',
            array(  
                'handle_id'    => 'aalButtonPreview',
                'dependencies' => array( 'jquery' ),
                'translation'  => array(
                    'activeButtons' => AmazonAutoLinks_PluginUtility::getActiveButtonLabelsForJavaScript(),
                    'debugMode'     => defined( 'WP_DEBUG' ) && WP_DEBUG,
                ),
            )
        );   
        add_filter( 'style_' . $this->oProp->sClassName, array( $this, 'replyToSetStyle' ) );
                
    }
    
        /**
         * @return      string
         * @callback    action      style_{class name}
         * @since       3.4.0
         */
        public function replyToSetStyle( $sCSSRules ) {
            return $sCSSRules . PHP_EOL
                . AmazonAutoLinks_ButtonResourceLoader::getButtonsCSS() . PHP_EOL;
        }    
        
        /**
         * Modifies the 'button_id' field to add labels for selection.
         * @return      array
         * @since       3.3.0
         */
        public function replyToSetActiveButtonLabels( $aFieldset ) {
            $aFieldset[ 'label' ] = AmazonAutoLinks_PluginUtility::getActiveButtonLabelsForFields();
            return $aFieldset;
        }

     
    /**
     * Validates submitted form data.
     */
    public function validate( $aInput, $aOriginal, $oFactory ) {    
        return $aInput;        
    }
 
}
