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

        // Admin
        if ( is_admin() ) {
            
            new AmazonAutoLinks_AutoInsertOutput_StaticInsertion;       // 3.4.10+
            
            new AmazonAutoLinks_AutoInsertAdminPage(
                '', // disable the options
                $sScriptPath            
            );        
            
            add_filter( 'aal_filter_custom_meta_keys', array( $this, 'replyToAddProtectedMetaKeys' ) );

        }
        // Front-end
        else {
            new AmazonAutoLinks_AutoInsertOutput_Frontend;
        }
    
        // Update auto-insert status change
        add_action( 'publish_' . AmazonAutoLinks_Registry::$aPostTypes[ 'auto_insert' ], array( $this, 'replyToCheckActiveAutoInsertStatusChange' ), 10, 2 );
        add_action( 'trash_' . AmazonAutoLinks_Registry::$aPostTypes[ 'auto_insert' ], array( $this, 'replyToCheckActiveAutoInsertStatusChange' ), 10, 2 );    
        add_action( 'aal_action_update_active_auto_inserts', array( $this, 'replyToUpdateActiveAutoInsert' ) );
    
    }    
        /**
         * @remark      When an auto-insert is created or edited, this method will be called too early from the system.
         * However, this hook is also triggered when the user trashes the auto-insert item from the action link in the post listing table. 
         * @since       3.3.0
         * @callback    filter      "{new post status}_{post type slug}"
         * @return      string
         */
        public function replyToCheckActiveAutoInsertStatusChange( $iPostID, $oPost ) {           
            do_action( 'aal_action_update_active_auto_inserts' );
        }
    
        /**
         * Updates the active auto-insert items.
         * @since           3.3.0
         * @callback        action      aal_action_update_active_auto_inserts
         */
        public function replyToUpdateActiveAutoInsert() {
            $_aActiveIDs = AmazonAutoLinks_PluginUtility::getActiveAutoInsertIDsQueried();
            update_option( 
                AmazonAutoLinks_Registry::$aOptionKeys[ 'active_auto_inserts' ],    // key
                $_aActiveIDs, // data
                true   // enable auto-load
            );            
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
