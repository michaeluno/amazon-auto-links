<?php
/**
 * Amazon Auto Links
 * 
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2015 Michael Uno
 * 
 */

/**
 * Loads the Auto-insert component.
 *  
 * @package     Amazon Auto Links
 * @since       3.1.0
*/
class AmazonAutoLinks_AutoInsertLoader {
    
    /**
     * Loads necessary components.
     */
    public function __construct( $sScriptPath ) {
        
        // Post type
        new AmazonAutoLinks_PostType_AutoInsert(
            AmazonAutoLinks_Registry::$aPostTypes[ 'auto_insert' ],  // slug
            null,   // post type argument. This is defined in the class.
            $sScriptPath   // script path               
        );    
    
        // Outputs
        new AmazonAutoLinks_AutoInsertOutput;
    
        // Admin
        if ( is_admin() ) {
            
            new AmazonAutoLinks_AutoInsertAdminPage(
                '', // disable the options
                $sScriptPath            
            );        
            
        }
    
    }    
    
}