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
 * Removes the Gutenberg Kindle block.
 *
 *
 * @package      Amazon Auto Links
 * @since        4.0.0
 */
class AmazonAutoLinks_CustomOEmbed_Gutenberg {

    /**
     *
     * @see https://developer.wordpress.org/block-editor/developers/filters/block-filters/#using-a-blacklist
     */
    public function __construct() {


         add_action( 'enqueue_block_editor_assets', array( $this, 'replyToRemoveBlock' ) );

    }

    public function replyToRemoveBlock() {
        wp_enqueue_script(
            'my-plugin-blacklist-blocks',
            plugins_url( 'include/core/component/custom_oembed/asset/js/remove-kindle-block.js', AmazonAutoLinks_Registry::$sFilePath ),
            array( 'wp-blocks', 'wp-dom-ready', 'wp-edit-post' )
        );
    }

}