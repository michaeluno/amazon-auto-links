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
class AmazonAutoLinks_UnitLoader_category {
    
    /**
     * Loads necessary components.
     */
    public function __construct( $sScriptPath ) {
        
        if ( is_admin() ) {

            new AmazonAutoLinks_CategoryUnitAdminPage(
                array(
                    'type'      => 'transient',
                    'key'       => $GLOBALS[ 'aal_transient_id' ],
                    'duration'  => 60*60*24*2,
                ),
                $sScriptPath
            );                           
                    
        
            // Post meta boxes
            $this->_registerPostMetaBoxes( $sScriptPath );
            
        }
        
        
    }    
    
        /**
         * Adds post meta boxes.
         * @since       3.3.0
         */
        private function _registerPostMetaBoxes( $sScriptPath ) {
            
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
                    
        }    
    
}