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
 *  Loads the button component
 *  
 *  @package    Amazon Auto Links
 *  @since      3.3.0
 */
class AmazonAutoLinks_MainLoader extends AmazonAutoLinks_PluginUtility {

    /**
     * Sets up hooks and properties.
     */
    public function __construct( $sScriptPath ) {
        
        if ( $this->hasBeenCalled( __METHOD__ ) ) {
            return;
        }
        
        // Front-end
        
        /**
         * Option Object - must be done before the template object.
         * The initial instantiation will handle formatting options from earlier versions of the plugin.
         */
        AmazonAutoLinks_Option::getInstance();            
        
        // Events
        new AmazonAutoLinks_Event;               
                
        // Outputs
        new AmazonAutoLinks_Credit;            
        
        // Back-end
        if ( is_admin() ) {
            
            // Create the main admin page.
            new AmazonAutoLinks_AdminPage( 
                AmazonAutoLinks_Registry::$aOptionKeys[ 'main' ], 
                $sScriptPath
            );
            
            /**
             * Delay the check with the below action so that the screen type can be determined.
             * Also in multi-site network, $GLOBALS[ 'pagenow' ] is not set properly until a certain point 
             * so delaying the check is safer.
             */
            add_action( 'current_screen', array( $this, 'replyToSetUpHooks' ) );

        }

        // 3.8.0
        new AmazonAutoLinks_OptionUpdater_To380;

    }
    
        /**
         * @callback    action      current_screen
         * @since       3.3.0
         */
        public function replyToSetUpHooks( $oScreen ) {
            
            if ( $this->isInPostEditingPage() ) {
                add_filter( 'is_protected_meta', array( $this, 'replyToCheckProtectedPostMetaKey' ), 10, 3 );
            }
            
        }
    
        /**
         * Checks whether the post meta key should be displayed in the Custom Fields field in post editing page.
         * 
         * @since       3.3.0
         * @return      boolean
         * @callback    filter      is_protected_meta
         */
        public function replyToCheckProtectedPostMetaKey( $bProtected, $sMetaKey, $sMetaType ) {
            
            if ( 'post' !== $sMetaType ) {
                return $bProtected;
            }
            
            if ( in_array( $sMetaKey, $this->_getPluginCustomMetaKeys(), true ) ) {
                return true;
            }
            
            return $bProtected;
            
        }
    
            /**
             * @since       3.3.0
             * @return      array
             */
            private function _getPluginCustomMetaKeys() {
                static $_aCache;
                
                if ( isset( $_aCache ) ) {
                    return $_aCache;
                }
                $_aCache = ( array ) apply_filters( 'aal_filter_custom_meta_keys', array() );
                return $_aCache;        
            }    
    
}