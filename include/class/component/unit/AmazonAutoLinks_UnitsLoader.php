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
 * @since       3.1.0
*/
class AmazonAutoLinks_UnitsLoader {
    
    /**
     * Loads necessary components.
     */
    public function __construct( $sScriptPath ) {
        
        // Post types
        new AmazonAutoLinks_PostType_Unit( 
            AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ],  // slug
            null,   // post type argument. This is defined in the class.
            $sScriptPath   // script path
        );            
        new AmazonAutoLinks_PostType_UnitPreview;    
        
        
        if ( is_admin() ) {
            
            new AmazonAutoLinks_CategoryUnitAdminPage(
                array(
                    'type'      => 'transient',
                    'key'       => $GLOBALS[ 'aal_transient_id' ],
                    'duration'  => 60*60*24*2,
                ),
                $sScriptPath
            );        
            // @deprecated 
            /* new AmazonAutoLinks_TagUnitAdminPage(
                array(
                    'type'      => 'transient',
                    'key'       => $GLOBALS[ 'aal_transient_id' ],
                    'duration'  => 60*60*24*2,
                ),
                $sScriptPath             
            ); */
            new AmazonAutoLinks_SearchUnitAdminPage(
                array(
                    'type'      => 'transient',
                    'key'       => $GLOBALS[ 'aal_transient_id' ],
                    'duration'  => 60*60*24*2,
                ),
                $sScriptPath
            );
            
            new AmazonAutoLinks_URLUnitAdminPage(
                array(
                    'type'      => 'transient',
                    'key'       => $GLOBALS[ 'aal_transient_id' ],
                    'duration'  => 60*60*24*2,
                ),
                $sScriptPath
            );                         
            
            // Meta boxes
            $this->_registerPostMetaBoxes();
            
        }
        
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
                'side', // context (what kind of metabox this is)
                'high' // priority                                                            
            );         
            new AmazonAutoLinks_PostMetaBox_TagUnit_Main(
                null,
                __( 'Main', 'amazon-auto-links' ), // meta box title
                array( // post type slugs: post, page, etc.
                    AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ] 
                ), 
                'normal', // context (what kind of metabox this is)
                'high' // priority                                    
            );
            new AmazonAutoLinks_PostMetaBox_CategoryUnit_Main(
                null,  // meta box ID - can be null. If null is passed, the ID gets automatically generated and the class name with all lower case characters will be applied.
                __( 'Main', 'amazon-auto-links' ), // meta box title
                array( // post type slugs: post, page, etc.
                    AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ] 
                ), 
                'normal', // context (what kind of metabox this is)
                'high' // priority                        
            );            
            new AmazonAutoLinks_PostMetaBox_CategoryUnit_Submit(
                null, // meta box ID - can be null. If null is passed, the ID gets automatically generated and the class name with all lower case characters will be applied.
                __( 'Added Categories', 'amazon-auto-links' ), // title
                array( // post type slugs: post, page, etc.
                    AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ] 
                ), 
                'side', // context - e.g. 'normal', 'advanced', or 'side'
                'core' // priority - e.g. 'high', 'core', 'default' or 'low'
            );
            
            new AmazonAutoLinks_PostMetaBox_SearchUnit_Main(
                null,   // meta box ID - null for auto-generate
                __( 'Product Search Main', 'amazon-auto-links' ),
                array( // post type slugs: post, page, etc.
                    AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ] 
                ),                 
                'normal', // context - e.g. 'normal', 'advanced', or 'side'
                'core'  // priority - e.g. 'high', 'core', 'default' or 'low'
            );            
            new AmazonAutoLinks_PostMetaBox_SearchUnit_Advanced(
                null,   // meta box ID - null for auto-generate
                __( 'Product Search Advanced', 'amazon-auto-links' ),
                array( // post type slugs: post, page, etc.
                    AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ] 
                ),                 
                'normal', // context - e.g. 'normal', 'advanced', or 'side'
                'low' // priority - e.g. 'high', 'core', 'default' or 'low'
            );
            
            new AmazonAutoLinks_PostMetaBox_ItemLookupUnit_Main(
                null,   // meta box ID - null for auto-generate
                __( 'Item Look-up Main', 'amazon-auto-links' ),
                array( // post type slugs: post, page, etc.
                    AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ] 
                ),                 
                'normal', // context - e.g. 'normal', 'advanced', or 'side'
                'core'  // priority - e.g. 'high', 'core', 'default' or 'low'
            );                 
            new AmazonAutoLinks_PostMetaBox_ItemLookupUnit_Advanced(
                null,   // meta box ID - null for auto-generate
                __( 'Item Look-up Advanced', 'amazon-auto-links' ),
                array( // post type slugs: post, page, etc.
                    AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ] 
                ),                 
                'normal', // context - e.g. 'normal', 'advanced', or 'side'
                'low' // priority - e.g. 'high', 'core', 'default' or 'low'            
            );

            new AmazonAutoLinks_PostMetaBox_SimilarityLookupUnit_Main(
                null,   // meta box ID - null for auto-generate
                __( 'Similarity Look-up Main', 'amazon-auto-links' ),
                array( // post type slugs: post, page, etc.
                    AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ] 
                ),                 
                'normal', // context - e.g. 'normal', 'advanced', or 'side'
                'core'  // priority - e.g. 'high', 'core', 'default' or 'low'
            );   
            new AmazonAutoLinks_PostMetaBox_SimilarityLookupUnit_Advanced(
                null,   // meta box ID - null for auto-generate
                __( 'Similarity Look-up Advanced', 'amazon-auto-links' ),
                array( // post type slugs: post, page, etc.
                    AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ] 
                ),                 
                'normal', // context - e.g. 'normal', 'advanced', or 'side'
                'low' // priority - e.g. 'high', 'core', 'default' or 'low'
            );
            
            new AmazonAutoLinks_PostMetaBox_Template(
                null, // meta box ID - null to auto-generate
                __( 'Template', 'amazon-auto-links' ), // title
                array( // post type slugs: post, page, etc.
                    AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ] 
                ), 
                'advanced', // context - e.g. 'normal', 'advanced', or 'side'
                'low' // priority - e.g. 'high', 'core', 'default' or 'low'
            );  
  
            new AmazonAutoLinks_PostMetaBox_Unit_ProductFilter(
                null,  // meta box ID - can be null. If null is passed, the ID gets automatically generated and the class name with all lower case characters will be applied.
                __( 'Unit Product Filters', 'amazon-auto-links' ), // title
                array( // post type slugs: post, page, etc.
                    AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ] 
                ), 
                'advanced', // context (what kind of metabox this is)
                'low' // priority                                                
            );
            
            // Common meta boxes
            new AmazonAutoLinks_PostMetaBox_Cache(
                null,  // meta box ID - can be null. If null is passed, the ID gets automatically generated and the class name with all lower case characters will be applied.
                __( 'Cache', 'amazon-auto-links' ), // title
                array( // post type slugs: post, page, etc.
                    AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ] 
                ), 
                'side', // context (what kind of metabox this is)
                'default' // priority                        
            );                   

            new AmazonAutoLinks_PostMetaBox_Unit_CommonAdvanced(
                null,  // meta box ID - can be null. If null is passed, the ID gets automatically generated and the class name with all lower case characters will be applied.
                __( 'Common Advanced', 'amazon-auto-links' ), // title
                array( // post type slugs: post, page, etc.
                    AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ] 
                ), 
                'side', // context (what kind of metabox this is)
                'low' // priority                                    
            );            
            
            new AmazonAutoLinks_PostMetaBox_DebugInfo(
                null, // meta box ID - null to auto-generate
                __( 'Debug Information', 'amazon-auto-links' ),
                array( // post type slugs: post, page, etc.
                    AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ] 
                ), 
                'advanced', // context (what kind of metabox this is)
                'low' // priority                                        
            );                       
            
                    
        }    
    
}