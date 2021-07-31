<?php
/**
 *
 */

/**
 * Class AmazonAutoLinks_Template_Common
 */
class AmazonAutoLinks_Template_Common_ResourceLoader extends AmazonAutoLinks_WPUtility {
    /**
     * Sets up properties and hooks.
     */
    public function __construct() {
        add_action( 'wp_enqueue_scripts', array( $this, 'replyToEnqueueScripts' ) );
        add_action( 'enqueue_embed_scripts', array( $this, 'replyToEnqueueScripts' ) );
    }
    public function replyToEnqueueScripts() {
        wp_enqueue_style( 'wp-pointer' );
        wp_enqueue_script( 'aal-pointer-tooltip' );
        wp_enqueue_script(
            'aal-product-tooltip',
            AmazonAutoLinks_Registry::getPluginURL( $this->isDebugMode()
                ? dirname( __FILE__ ) . '/js/product-tooltip.js'
                : dirname( __FILE__ ) . '/js/product-tooltip.min.js',
                true
            ),
            array( 'aal-pointer-tooltip' ),
            '1.0.0',
            true
        );
        wp_enqueue_script(
            'aal-image-preview',
            AmazonAutoLinks_Registry::getPluginURL( $this->isDebugMode()
                ? dirname( __FILE__ ) . '/js/product-image-preview.js'
                : dirname( __FILE__ ) . '/js/product-image-preview.min.js',
                true
            ),
            array( 'aal-pointer-tooltip' ),
            '1.0.0',
            true
        );
    }
}
new AmazonAutoLinks_Template_Common_ResourceLoader;