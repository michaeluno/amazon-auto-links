<?php
/**
 * Amazon Auto Links
 * 
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2015 Michael Uno
 * 
 */

/**
 * Loads the units component.
 *  
 * @package     Amazon Auto Links
 * @since       3.3.0
*/
class AmazonAutoLinks_UnitsLoader {
    
    /**
     * Loads necessary components.
     */
    public function __construct( $sScriptPath ) {
        
        // Post types
        new AmazonAutoLinks_PostType_Unit( 
            AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ],  // slug
            null,          // post type argument. This is defined in the class.
            $sScriptPath   // script path
        );            
        new AmazonAutoLinks_PostType_UnitPreview;    
        
        // Post meta boxes
        if ( is_admin() ) {
            $this->_registerPostMetaBoxes();
        }        
        
        // Unit types.
        new AmazonAutoLinks_UnitLoader_category( $sScriptPath );
        new AmazonAutoLinks_UnitLoader_tag( $sScriptPath );  
        new AmazonAutoLinks_UnitLoader_product_search( $sScriptPath );
        new AmazonAutoLinks_UnitLoader_item_lookup( $sScriptPath );
        new AmazonAutoLinks_UnitLoader_similarity_lookup( $sScriptPath );
        new AmazonAutoLinks_UnitLoader_url( $sScriptPath );
                
        do_action( 'aal_action_register_unit_types' );
        
    }    
    
        /**
         * Adds post meta boxes.
         */
        private function _registerPostMetaBoxes() {
            
            new AmazonAutoLinks_PostMetaBox_Unit_ViewLink(
                null,
                __( 'View', 'amazon-auto-links' ), // meta box title
                array( // post type slugs: post, page, etc.
                    AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ] 
                ), 
                'side', // context - e.g. 'normal', 'advanced', or 'side'
                'high'  // priority - e.g. 'high', 'core', 'default' or 'low'
            );                    
            
            new AmazonAutoLinks_PostMetaBox_Unit_Common(
                null,       // meta box ID - can be null. If null is passed, the ID gets automatically generated and the class name with all lower case characters will be applied.
                __( 'Common', 'amazon-auto-links' ), // title
                array(      // post type slugs: post, page, etc.
                    AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ] 
                ), 
                'normal',   // context - e.g. 'normal', 'advanced', or 'side'
                'core'      // priority - e.g. 'high', 'core', 'default' or 'low'
            );                   
            
            new AmazonAutoLinks_PostMetaBox_Template(
                null,       // meta box ID - null to auto-generate
                __( 'Template', 'amazon-auto-links' ), // title
                array(      // post type slugs: post, page, etc.
                    AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ] 
                ), 
                'advanced', // context - e.g. 'normal', 'advanced', or 'side'
                'low'       // priority - e.g. 'high', 'core', 'default' or 'low'
            );  
  
            new AmazonAutoLinks_PostMetaBox_Unit_ProductFilter(
                null,       // meta box ID - can be null. If null is passed, the ID gets automatically generated and the class name with all lower case characters will be applied.
                __( 'Unit Product Filters', 'amazon-auto-links' ), // title
                array(      // post type slugs: post, page, etc.
                    AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ] 
                ), 
                'advanced', // context - e.g. 'normal', 'advanced', or 'side'
                'low'       // priority - e.g. 'high', 'core', 'default' or 'low'
            );
            
            // Common meta boxes
            new AmazonAutoLinks_PostMetaBox_Cache(
                null,       // meta box ID - can be null. If null is passed, the ID gets automatically generated and the class name with all lower case characters will be applied.
                __( 'Cache', 'amazon-auto-links' ), // title
                array(      // post type slugs: post, page, etc.
                    AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ] 
                ), 
                'side',     // context - e.g. 'normal', 'advanced', or 'side'
                'default'   // priority - e.g. 'high', 'core', 'default' or 'low'
            );                   

            new AmazonAutoLinks_PostMetaBox_Unit_CommonAdvanced(
                null,       // meta box ID - can be null. If null is passed, the ID gets automatically generated and the class name with all lower case characters will be applied.
                __( 'Common Advanced', 'amazon-auto-links' ), // title
                array(      // post type slugs: post, page, etc.
                    AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ] 
                ), 
                'side',     // context - e.g. 'normal', 'advanced', or 'side'
                'low'       // priority - e.g. 'high', 'core', 'default' or 'low'
            );            
            
            new AmazonAutoLinks_PostMetaBox_DebugInfo(
                null,       // meta box ID - null to auto-generate
                __( 'Debug Information', 'amazon-auto-links' ),
                array(      // post type slugs: post, page, etc.
                    AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ] 
                ), 
                'advanced', // context - e.g. 'normal', 'advanced', or 'side'
                'low'       // priority - e.g. 'high', 'core', 'default' or 'low'
            );                       
            
                    
        }    
    
}