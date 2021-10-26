<?php
/**
 * Auto Amazon Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
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
            $_oFields = new $_sClassName( $this );
            $_aFields = $_oFields->get();
            foreach( $_aFields as $_aField ) {           
                $this->addSettingFields( $_aField );
            }            
        }            

        $this->enqueueScript(
            apply_filters( 'aal_filter_admin_button_js_preview_src', '' ),
            $this->oProp->aPostTypes,
            array(  
                'handle_id'    => 'aalButtonPreview',
                'dependencies' => array( 'jquery' ),
                'translation'  => array(
                    'activeButtons' => AmazonAutoLinks_PluginUtility::getActiveButtonLabelsForJavaScript(),
                    'debugMode'     => defined( 'WP_DEBUG' ) && WP_DEBUG,
                ),
                'in_footer'    => true,
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
            $aFieldset[ 'label' ] = AmazonAutoLinks_PluginUtility::getActiveButtonLabelsForFields();
            return $aFieldset;
        }
    
        /**
         * @return      array
         * @deprecated  3.4.0
         */

        
    /**
     * Validates submitted form data.
     */
    public function validate( $aInput, $aOriginal, $oFactory ) {    
        return $aInput;        
    }
    
}