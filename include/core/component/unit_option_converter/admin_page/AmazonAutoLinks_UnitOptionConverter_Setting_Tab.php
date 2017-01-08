<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2017 Michael Uno
 */

/**
 * Adds an in-page tab to a setting page.
 * 
 * @since       3.3.0
 * @extends     AmazonAutoLinks_AdminPage_Tab_Base
 */
class AmazonAutoLinks_UnitOptionConverter_Setting_Tab extends AmazonAutoLinks_AdminPage_Tab_Base {
    
    /**
     * Triggered when the tab is loaded.
     */
    public function replyToLoadTab( $oAdminPage ) {
        
        // Form sections
        new AmazonAutoLinks_UnitOptionConverter_Setting_Tab_Convert( 
            $oAdminPage,
            $this->sPageSlug, 
            array(
                'section_id'    => '_convert',
                'tab_slug'      => $this->sTabSlug,
                'title'         => __( 'Unit Options', 'amazon-auto-links' ),
                'description'   => array(
                    __( 'Convert unit options with batch processing.', 'amazon-auto-links' ),
                ),
                'save'          => false,
            )
        );
        
        $oAdminPage->enqueueScript(
            AmazonAutoLinks_Registry::$sDirPath . '/asset/js/button-preview-in-unit-definition-page.js',
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
