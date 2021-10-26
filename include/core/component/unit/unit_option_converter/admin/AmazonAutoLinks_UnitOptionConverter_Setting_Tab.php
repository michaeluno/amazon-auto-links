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
        new AmazonAutoLinks_UnitOptionConverter_Setting_Section_Convert(
            $oAdminPage,
            $this->sPageSlug,
            array(
                'tab_slug'      => $this->sTabSlug,
            )
        );

        $oAdminPage->enqueueScript(
            apply_filters( 'aal_filter_admin_button_js_preview_src', '' ),
            AmazonAutoLinks_Registry::$aAdminPages[ 'tool' ], // page slug
            '', // tab slug
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

        add_filter( 'style_' . $oAdminPage->oProp->sClassName, array( $this, 'replyToSetStyle' ) );
        
    }

        /**
         * @return      string
         * @callback    action      style_{class name}
         */
        public function replyToSetStyle( $sCSSRules ) {
            return $sCSSRules . PHP_EOL
                . AmazonAutoLinks_ButtonResourceLoader::getButtonsCSS() . PHP_EOL;
        }    
    
        
}
