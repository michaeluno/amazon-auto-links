<?php
/**
 * Amazon Auto Links
 * 
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2016 Michael Uno
 * 
 */

/**
 * Creates a default button if there is not.
 * 
 * @remark      Expected to be called when a plugin is activated.
 * @package     Amazon Auto Links
 * @since       3
 */
class AmazonAutoLinks_DefaultButtonCreation extends AmazonAutoLinks_PluginUtility {

    /**
     * Triggers event actions.
     */
    public function __construct() {

        // @todo check the count of posts of the button post type.
        $_oPostCount = wp_count_posts(
            AmazonAutoLinks_Registry::$aPostTypes[ 'button' ]
        );
        
        if ( ! is_object( $_oPostCount ) ) {
            return;
        }
        
        // If a button exists, return
        if ( 
            isset( $_oPostCount->publish )
            && $_oPostCount->publish <= 0 
        ) {
            
            // Otherwise, create one.
            $this->_createDefaultButton();            
            
        }
        
        // Update the button CSS option.
        update_option(
            AmazonAutoLinks_Registry::$aOptionKeys[ 'button_css' ],
            AmazonAutoLinks_PluginUtility::getCSSRulesOfActiveButtons() // data
        );
        
    }
        /**
         * 
         */
        private function _createDefaultButton() {
            
            $_iPostID = $this->createPost( 
                AmazonAutoLinks_Registry::$aPostTypes[ 'button' ], 
                array( // post columns
                    'post_title' => __( 'Default', 'amazon-auto-links' ),
                ), 
                array( // meta
                    'button_label' => __( 'Buy Now', 'amazon-auto-links' ),
                )
            );
            if( $_iPostID ) {
                update_post_meta(
                    $_iPostID, // post id
                    'button_css', // meta key
                    AmazonAutoLinks_ButtonResourceLoader::getDefaultButtonCSS( $_iPostID ) // value
                );                  
            }
            
        }
}