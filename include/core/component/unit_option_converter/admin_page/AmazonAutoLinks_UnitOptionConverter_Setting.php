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
 * Loads the unit option converter component.
 * 
 * @package      Amazon Auto Links
 * @since        3.3.0
 */
class AmazonAutoLinks_UnitOptionConverter_Setting {
    
    /**
     * Sets up hooks.
     */
    public function __construct() {
       
        add_action( 
            'load_' . AmazonAutoLinks_Registry::$aAdminPages[ 'tool' ], 
            array( $this, 'replyToLoadPage' )
        );
                
    }
    
    /**
     * @return      void
     * @callback    action      load_{page slug}
     */
    public function replyToLoadPage( $oFactory ) {
        
        // Page meta boxes.
        new AmazonAutoLinks_UnitOptionConverter_Setting_PageMetaBox_Common(
            null,                                           // meta box id - passing null will make it auto generate
            __( 'Common', 'amazon-auto-links' ), // title
            array( // page slugs
                AmazonAutoLinks_Registry::$aAdminPages[ 'tool' ] => array(
                    'unit_option_converter'
                ),
            ),
            'normal',                                       // context
            'core'                                     // priority                    
        );

        new AmazonAutoLinks_UnitOptionConverter_Setting_PageMetaBox_Template(
            null,                                           // meta box id - passing null will make it auto generate
            __( 'Template', 'amazon-auto-links' ), // title
            array( // page slugs
                AmazonAutoLinks_Registry::$aAdminPages[ 'tool' ] => array(
                    'unit_option_converter'
                ),
            ),
            'normal',                                     // context (what kind of metabox this is)
            'default'                                     // priority - 'high', 'sorted', 'core', 'default', 'low'
        );        
        
        new AmazonAutoLinks_UnitOptionConverter_Setting_PageMetaBox_ProductFilter(
            null,                                           // meta box id - passing null will make it auto generate
            __( 'Product Filter', 'amazon-auto-links' ), // title
            array( // page slugs
                AmazonAutoLinks_Registry::$aAdminPages[ 'tool' ] => array(
                    'unit_option_converter'
                ),
            ),
            'normal',                                     // context (what kind of metabox this is)
            'low'                                     // priority - 'high', 'sorted', 'core', 'default', 'low'
        );

        new AmazonAutoLinks_UnitOptionConverter_Setting_PageMetaBox_ProductFilterAdvanced(
            null,                                           // meta box id - passing null will make it auto generate
            __( 'Advanced Product Filter', 'amazon-auto-links' ), // title
            array( // page slugs
                AmazonAutoLinks_Registry::$aAdminPages[ 'tool' ] => array(
                    'unit_option_converter'
                ),
            ),
            'normal',                                     // context (what kind of metabox this is)
            'low'                                     // priority - 'high', 'sorted', 'core', 'default', 'low'
        );

        new AmazonAutoLinks_UnitOptionConverter_Setting_PageMetaBox_Cache(
            null,                                           // meta box id - passing null will make it auto generate
            __( 'Cache', 'amazon-auto-links' ), // title
            array( // page slugs
                AmazonAutoLinks_Registry::$aAdminPages[ 'tool' ] => array(
                    'unit_option_converter'
                ),
            ),
            'side',                                       // context
            'high'                                     // priority                    
        );            
        
        new AmazonAutoLinks_UnitOptionConverter_Setting_PageMetaBox_CommonAdvanced(
            null,                                           // meta box id - passing null will make it auto generate
            __( 'Common Advanced', 'amazon-auto-links' ), // title
            array( // page slugs
                AmazonAutoLinks_Registry::$aAdminPages[ 'tool' ] => array(
                    'unit_option_converter'
                ),
            ),
            'side',                                       // context
            'high'                                     // priority                    
        );        
        
        // Tabs
        new AmazonAutoLinks_UnitOptionConverter_Setting_Tab( 
            $oFactory,
            AmazonAutoLinks_Registry::$aAdminPages[ 'tool' ]
        );        
                
    }            
            
}
