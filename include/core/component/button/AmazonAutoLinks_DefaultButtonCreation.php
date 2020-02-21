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
 * Creates a default button if there is not.
 * 
 * @remark      Expected to be called when a plugin is activated.
 * @package     Amazon Auto Links
 * @since       3
 */
class AmazonAutoLinks_DefaultButtonCreation extends AmazonAutoLinks_ButtonUtility {

    /**
     * Triggers event actions.
     * @remark      `wp_count_posts()` returns an empty object when the post type is not created.
     * So make sure this class is called after the button post type registration is done.
     */
    public function __construct() {

        // Check the count of posts of the button post type.
        $_oPostCount = wp_count_posts(
            AmazonAutoLinks_Registry::$aPostTypes[ 'button' ]
        );

        // If a button exists, return
        if (
            ! is_object( $_oPostCount )
            || ! isset( $_oPostCount->publish )
            || $_oPostCount->publish > 0
        ) {
            return;
        }

        // Otherwise, create one.
        $_iPostID = $this->___createDefaultButton();

        // Update the button CSS option.
        if ( $_iPostID ) {
            update_option(
                AmazonAutoLinks_Registry::$aOptionKeys[ 'button_css' ],
                $this->getCSSRulesOfActiveButtons() // data
            );
        }

    }

        /**
         * @since       3
         * @since       3.6.2       Made it return the created post ID.
         * @return      integer     The created post ID. `0` for failing.
         */
        private function ___createDefaultButton() {
            
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
            return ( integer ) $_iPostID;

        }
}