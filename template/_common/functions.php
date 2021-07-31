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
    }
    public function replyToEnqueueScripts() {
        wp_enqueue_style( 'wp-pointer' );
        wp_enqueue_script( 'aal-pointer-tooltip' );
        wp_enqueue_script(
            'amazon-auto-links-product-tooltip',
            AmazonAutoLinks_Registry::getPluginURL( $this->isDebugMode()
                ? dirname( __FILE__ ) . '/js/product-tooltip.js'
                : dirname( __FILE__ ) . '/js/product-tooltip.min.js',
                true
            ),
            array( 'aal-pointer-tooltip' ),
            '1.0.0',
            true
        );
    }
}
new AmazonAutoLinks_Template_Common_ResourceLoader;