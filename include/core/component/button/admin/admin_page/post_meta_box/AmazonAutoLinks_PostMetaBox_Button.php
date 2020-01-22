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
 * Defines the meta box for the button post type.
 */
abstract class AmazonAutoLinks_PostMetaBox_Button extends AmazonAutoLinks_AdminPageFramework_MetaBox {

    
    public function start() {
    
        // Register custom field types
        new AmazonAutoLinks_RevealerCustomFieldType(
            $this->oProp->sClassName
        );
        
        add_action(
            "set_up_" . $this->oProp->sClassName,
            array( $this, 'replyToInsertCustomStyleTag' )
        );

    }
        
    /**
     * Indicates whether the custom style tag was inserted in the head tag or not.
     */
    static $bCustomStyleLoaded = false;
    
    /**
     * 
     * @callback        action      set_up_{instantiated class name}
     */
    public function replyToInsertCustomStyleTag() {
                    
        if ( self::$bCustomStyleLoaded ) {
            return;
        }
        self::$bCustomStyleLoaded = true;
        
        add_action( 
            'admin_head',  
            array( $this, 'replyToPrintCustomStyleTag' )
        );
          
        add_action( 
            'admin_head', 
            array( $this, 'replyToSetScripts' )
        );
        
    }
        /**
         * 
         * @callback        action      admin_head
         */
        public function replyToPrintCustomStyleTag() {
            // echo "<style type='text/css'></style>";
            echo "<style type='text/css' id='amazon-auto-links-button-style'>" . PHP_EOL
                    . '.amazon-auto-links-button {}' . PHP_EOL
                . "</style>";                    
                
        }
        
        /**
         * 
         * @callback        action      admin_head      For unknown reasons, `wp_enqueue_scripts` does not work.
         */        
        public function replyToSetScripts() {

            $_sFileBaseName = defined( 'WP_DEBUG' ) && WP_DEBUG
                ? 'button-preview-event-binder.js'
                : 'button-preview-event-binder.min.js';
            $this->enqueueScript(
                AmazonAutoLinks_Registry::$sDirPath . '/asset/js/' . $_sFileBaseName,
                $this->oProp->aPostTypes,
                array(  
                    'handle_id'    => 'aal_button_script_event_binder',                
                    'dependencies' => array( 'jquery' )
                )
            );

            $_sFileBaseName = defined( 'WP_DEBUG' ) && WP_DEBUG
                ? 'button-preview-updator.js'
                : 'button-preview-updator.min.js';
            $this->enqueueScript(
                AmazonAutoLinks_Registry::$sDirPath . '/asset/js/' . $_sFileBaseName,
                $this->oProp->aPostTypes,
                array(  
                    'handle_id'    => 'aal_button_script_preview_updator',
                    'dependencies' => array( 'jquery' ),
                    'translation'  => array(
                        'post_id' => isset( $_GET[ 'post' ] )
                            ? $_GET[ 'post' ]
                            : '___button_id___',
                    ),
                )
            ); 
                        
        }        
    
    
}