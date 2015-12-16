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
class AmazonAutoLinks_UnitLoader_tag {
    
    /**
     * Loads necessary components.
     */
    public function __construct( $sScriptPath ) {
        
        if ( is_admin() ) {

            // @deprecated 
            /* new AmazonAutoLinks_TagUnitAdminPage(
                array(
                    'type'      => 'transient',
                    'key'       => $GLOBALS[ 'aal_transient_id' ],
                    'duration'  => 60*60*24*2,
                ),
                $sScriptPath             
            ); */        
        
            // Post meta boxes
            $this->_registerPostMetaBoxes( $sScriptPath );
            
        }
        
        
    }    
    
        /**
         * Adds post meta boxes.
         * @since       3.3.0
         */
        private function _registerPostMetaBoxes( $sScriptPath ) {
            
            new AmazonAutoLinks_PostMetaBox_TagUnit_Main(
                null,
                __( 'Main', 'amazon-auto-links' ), // meta box title
                array( // post type slugs: post, page, etc.
                    AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ] 
                ), 
                'normal', // context (what kind of metabox this is)
                'high' // priority                                    
            );            

            
                    
        }    
    
}