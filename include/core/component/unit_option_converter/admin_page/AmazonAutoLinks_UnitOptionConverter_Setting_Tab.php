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
 * Adds an in-page tab to a setting page.
 * 
 * @since       3.3.0
 * @extends     AmazonAutoLinks_AdminPage_Tab_Base
 */
class AmazonAutoLinks_UnitOptionConverter_Setting_Tab extends AmazonAutoLinks_AdminPage_Tab_Base {

    /**
     * @return  array
     * @since   3.7.9
     */
    protected function _getArguments() {
        return array(
            'tab_slug'  => 'unit_option_converter',
            'title'     => __( 'Unit Option Converter', 'amazon-auto-links' ),
            'order'     => 20,
        );
    }

    /**
     * Triggered when the tab is loaded.
     */
    public function replyToLoadTab( $oAdminPage ) {
        
        // Form sections
        new AmazonAutoLinks_UnitOptionConverter_Setting_Tab_Convert( 
            $oAdminPage,
            $this->sPageSlug,
            array(
                'tab_slug'      => $this->sTabSlug,
            )
        );

        $_sFileBaseName = defined( 'WP_DEBUG' ) && WP_DEBUG
            ? 'button-preview-in-unit-definition-page.js'
            : 'button-preview-in-unit-definition-page.min.js';
        $oAdminPage->enqueueScript(
            AmazonAutoLinks_Registry::$sDirPath . '/asset/js/' . $_sFileBaseName,
            AmazonAutoLinks_Registry::$aAdminPages[ 'tool' ], // page slug
            '', // tab slug
            array(  
                'handle_id'    => 'aal_button_preview_labels',
                'dependencies' => array( 'jquery' ),
                'translation'  => AmazonAutoLinks_PluginUtility::getActiveButtonLabelsForJavaScript(),
            )
        );      

        add_filter( 'style_' . $oAdminPage->oProp->sClassName, array( $this, 'replyToSetStyle' ) );
        
    }

        /**
         * @return      array
         * @deprecated  3.4.0
         */
/*         private function _getActiveButtonLabelsForJavaScript() {
            
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
            
        }  */      
        
        /**
         * @return      string
         * @callback    action      style_{class name}
         */
        public function replyToSetStyle( $sCSSRules ) {
            return $sCSSRules . PHP_EOL
                . AmazonAutoLinks_ButtonResourceLoader::getButtonsCSS() . PHP_EOL;
        }    
    
        
}
