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
            
            add_filter( 'aal_filter_custom_meta_keys', array( $this, 'replyToAddProtectedMetaKeys' ) );
            
        }
    
    }    
    
        /**
         * @return      array
         * @since       3.3.0
         * @callback    filter      aal_filter_custom_meta_keys
         */
        public function replyToAddProtectedMetaKeys( $aMetaKeys ) {
            
            $_aClassNames = array(
                'AmazonAutoLinks_FormFields_AutoInsert_GoBack',
                'AmazonAutoLinks_FormFields_AutoInsert_Status',
                'AmazonAutoLinks_FormFields_AutoInsert_PostID',
                'AmazonAutoLinks_FormFields_AutoInsert_Area',
                'AmazonAutoLinks_FormFields_AutoInsert_Static',
                'AmazonAutoLinks_FormFields_AutoInsert_WhereToEnable',
                'AmazonAutoLinks_FormFields_AutoInsert_WhereToDisable',
                'AmazonAutoLinks_FormFields_AutoInsert_Save',
            
            );
            foreach( $_aClassNames as $_sClassName ) {
                $_oFields = new $_sClassName;
                $aMetaKeys = array_merge( $aMetaKeys, $_oFields->getFieldIDs() );
            }
            
            return $aMetaKeys;

        }    
    
}