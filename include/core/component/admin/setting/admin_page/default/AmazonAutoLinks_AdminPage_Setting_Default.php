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

    /**
     * Triggered when the tab is loaded.
     * 
     * @callback        action      load_{page slug}_{tab slug}
     * @return          void
     */
    protected function _loadTab( $oAdminPage ) {
                
        // Page meta boxes.        
        new AmazonAutoLinks_AdminPage_Setting_Default_PageMetaBox_Common(
            null,                                           // meta box id - passing null will make it auto generate
            __( 'Common', 'amazon-auto-links' ), // title
            array( // page slugs
                AmazonAutoLinks_Registry::$aAdminPages[ 'main' ] => array(
                    'default'
                ),
            ),
            'normal',                                       // context
            'core'                                     // priority                    
        );

        new AmazonAutoLinks_AdminPage_Setting_Default_PageMetaBox_Template(
            null,                                           // meta box id - passing null will make it auto generate
            __( 'Template', 'amazon-auto-links' ), // title
            array( // page slugs
                AmazonAutoLinks_Registry::$aAdminPages[ 'main' ] => array(
                    'default'
                ),
            ),
            'normal',                                     // context (what kind of metabox this is)
            'default'                                     // priority - 'high', 'sorted', 'core', 'default', 'low'
        );        
        
        new AmazonAutoLinks_AdminPage_Setting_Default_PageMetaBox_ProductFilter(
            null,                                           // meta box id - passing null will make it auto generate
            __( 'Product Filter', 'amazon-auto-links' ), // title
            array( // page slugs
                AmazonAutoLinks_Registry::$aAdminPages[ 'main' ] => array(
                    'default'
                ),
            ),
            'normal',                                     // context (what kind of metabox this is)
            'low'                                     // priority - 'high', 'sorted', 'core', 'default', 'low'
        );

        new AmazonAutoLinks_AdminPage_Setting_Default_PageMetaBox_ProductFilterAdvanced(
            null,                                           // meta box id - passing null will make it auto generate
            __( 'Advanced Product Filter', 'amazon-auto-links' ), // title
            array( // page slugs
                AmazonAutoLinks_Registry::$aAdminPages[ 'main' ] => array(
                    'default'
                ),
            ),
            'normal',                                     // context (what kind of metabox this is)
            'low'                                     // priority - 'high', 'sorted', 'core', 'default', 'low'
        );

        new AmazonAutoLinks_AdminPage_Setting_Default_PageMetaBox_Submit(
            null,
            __( 'Submit', 'amazon-auto-links' ), // title
            array( // page slugs
                AmazonAutoLinks_Registry::$aAdminPages[ 'main' ] => array(
                    'default'
                ),
            ),
            'side',                                       // context
            'high'                                     // priority                            
        );
        
        new AmazonAutoLinks_AdminPage_Setting_Default_PageMetaBox_Cache(
            null,                                           // meta box id - passing null will make it auto generate
            __( 'Cache', 'amazon-auto-links' ), // title
            array( // page slugs
                AmazonAutoLinks_Registry::$aAdminPages[ 'main' ] => array(
                    'default'
                ),
            ),
            'side',                                       // context
            'high'                                     // priority                    
        );            
        
        new AmazonAutoLinks_AdminPage_Setting_Default_PageMetaBox_CommonAdvanced(
            null,                                           // meta box id - passing null will make it auto generate
            __( 'Common Advanced', 'amazon-auto-links' ), // title
            array( // page slugs
                AmazonAutoLinks_Registry::$aAdminPages[ 'main' ] => array(
                    'default'
                ),
            ),
            'side',                                       // context
            'high'                                     // priority                    
        );        

    }
    
    /**
     * @callback    action     do_{page slug}
     * @return      void
     */
    protected function _doTab( $oFactory ) {
        
        echo "<h3>" 
                . __( 'Default Unit Options', 'amazon-auto-links' )
            . "</h3>";
            
        echo "<p>"
                . __( 'The unit options set here will be applied to the output of shortcodes and newly created units.', 'amazon-auto-links' )
            . "</p>";
        
    }
            
}
