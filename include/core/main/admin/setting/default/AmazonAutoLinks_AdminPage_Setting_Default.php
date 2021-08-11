<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 */

/**
 * Adds the 'Default' tab to the 'Settings' page of the loader plugin.
 * 
 * @since       3.4.0
 * @extends     AmazonAutoLinks_AdminPage_Tab_Base
 */
class AmazonAutoLinks_AdminPage_Setting_Default extends AmazonAutoLinks_AdminPage_Tab_Base {

    /**
     * @return array
     * @since   3.11.1
     */
    protected function _getArguments() {
        return array(
            'tab_slug'  => 'default',
            'title'     => __( 'Default', 'amazon-auto-links' ),
            'order'     => 30,
        );
    }

    protected function _construct( $oFactory ) {}

    /**
     * Triggered when the tab is loaded.
     * 
     * @callback        action      load_{page slug}_{tab slug}
     * @return          void
     */
    protected function _loadTab( $oAdminPage ) {

        $_aPageSlugs = array( // page slugs
            AmazonAutoLinks_Registry::$aAdminPages[ 'main' ] => array(
                'default'
            ),
        );

        // Page meta boxes.        
        new AmazonAutoLinks_AdminPage_Setting_Default_PageMetaBox_Common(
            null,                                           // meta box id - passing null will make it auto generate
            __( 'Common', 'amazon-auto-links' ), // title
            $_aPageSlugs,
            'normal',                                       // context
            'core'                                     // priority                    
        );

        new AmazonAutoLinks_AdminPage_Setting_Default_PageMetaBox_Template(
            null,                                           // meta box id - passing null will make it auto generate
            __( 'Template', 'amazon-auto-links' ), // title
            $_aPageSlugs,
            'normal',                                     // context (what kind of metabox this is)
            'default'                                     // priority - 'high', 'sorted', 'core', 'default', 'low'
        );        
        
        new AmazonAutoLinks_AdminPage_Setting_Default_PageMetaBox_ProductFilter(
            null,                                           // meta box id - passing null will make it auto generate
            __( 'Product Filter', 'amazon-auto-links' ), // title
            $_aPageSlugs,
            'normal',                                     // context (what kind of metabox this is)
            'low'                                     // priority - 'high', 'sorted', 'core', 'default', 'low'
        );

        new AmazonAutoLinks_AdminPage_Setting_Default_PageMetaBox_ProductFilterAdvanced(
            null,                                           // meta box id - passing null will make it auto generate
            __( 'Advanced Product Filter', 'amazon-auto-links' ), // title
            $_aPageSlugs,
            'normal',                                     // context (what kind of metabox this is)
            'low'                                     // priority - 'high', 'sorted', 'core', 'default', 'low'
        );

        new AmazonAutoLinks_AdminPage_Setting_Default_PageMetaBox_Submit(
            null,
            __( 'Submit', 'amazon-auto-links' ), // title
            $_aPageSlugs,
            'side',                                       // context
            'high'                                     // priority                            
        );

        new AmazonAutoLinks_AdminPage_Setting_Default_PageMetaBox_Cache(
            null,                                           // meta box id - passing null will make it auto generate
            __( 'Cache', 'amazon-auto-links' ), // title
            $_aPageSlugs,
            'side',                                       // context
            'default'
        );            

        // 4.3.0
        new AmazonAutoLinks_AdminPage_Setting_Default_PageMetaBox_Locale(
            null,
            __( 'Locale', 'amazon-auto-links' ),
            $_aPageSlugs,
            'side',
            'default'
        );

        new AmazonAutoLinks_AdminPage_Setting_Default_PageMetaBox_CommonAdvanced(
            null,                                           // meta box id - passing null will make it auto generate
            __( 'Common Advanced', 'amazon-auto-links' ), // title
            $_aPageSlugs,
            'side',                                       // context
            'default'
        );

    }
    
    /**
     * @callback    action     do_{page slug}
     * @return      void
     */
    protected function _doTab( $oFactory ) {
        echo "<h3>" 
                . esc_html__( 'Default Unit Options', 'amazon-auto-links' )
            . "</h3>";
        echo "<p>"
                . esc_html__( 'The unit options set here will be applied to the output of shortcodes and newly created units.', 'amazon-auto-links' )
            . "</p>";
    }

}
