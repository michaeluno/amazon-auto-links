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
 * @since 5.2.0
 */
class AmazonAutoLinks_AdminPage_Setting_Default_PageMetaBox_Button extends AmazonAutoLinks_AdminPage_Setting_Default_PageMetaBox_Base {

    /**
     * @var array
     * @since 5.2.0
     */
    protected $_aFieldClasses = array(
        'AmazonAutoLinks_FormFields_Unit_Button',
    );

    /**
     * Sets up form fields.
     */ 
    public function setUp() {

        parent::setUp();

        add_filter( 'field_definition_' . $this->oProp->sClassName . '_' . $this->_sSectionID . '_button_id', array( $this, 'replyToSetActiveButtonLabels' ) );
        
        // Resources for the button preview
        $this->enqueueScript(
            apply_filters( 'aal_filter_admin_button_js_preview_src', '' ),
            $this->oProp->sPageSlug,
            'default',
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
        // @deprecated 5.2.0
        add_filter( 'style_' . $this->oProp->sClassName, array( $this, 'replyToSetStyle' ) );
                
    }
    
        /**
         * @return   string
         * @callback add_action() style_{class name}
         * @since    3.4.0
         * @since    5.2.0  Moved from `AmazonAutoLinks_AdminPage_Setting_Default_PageMetaBox_CommonAdvanced`.
         * @deprecated 5.2.0    Now uses iframe to preview button so no need to load CSS in the direct screen
         */
        public function replyToSetStyle( $sCSSRules ) {
            return $sCSSRules . PHP_EOL
                . AmazonAutoLinks_Button_ResourceLoader::getButtonsCSS() . PHP_EOL;
        }
        
        /**
         * Modifies the 'button_id' field to add labels for selection.
         * @return array
         * @since  3.3.0
         * @since  5.2.0  Moved from `AmazonAutoLinks_AdminPage_Setting_Default_PageMetaBox_CommonAdvanced`.
         */
        public function replyToSetActiveButtonLabels( $aFieldset ) {
            $aFieldset[ 'label' ] = AmazonAutoLinks_PluginUtility::getActiveButtonLabelsForFields();
            return $aFieldset;
        }

     
    /**
     * Validates submitted form data.
     */
    public function validate( $aInputs, $aOldInputs, $oFactory ) {
        return $aInputs;
    }
 
}