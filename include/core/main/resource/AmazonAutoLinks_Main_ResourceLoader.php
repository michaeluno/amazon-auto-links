<?php
/**
 * Auto Amazon Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 */

 
/**
 * Handles the resource file registration for the main component.
 *  
 * @since   4.7.0
 */
class AmazonAutoLinks_Main_ResourceLoader extends AmazonAutoLinks_PluginUtility {

    /**
     * Sets up properties and hooks.
     */
    public function __construct() {
        add_action( 'init', array( $this, 'replyToRegisterResources' ) );
    }

    /**
     *
     */
    public function replyToRegisterResources() {

        $_sFileURL = AmazonAutoLinks_Registry::getPluginURL(
                dirname( dirname( __FILE__ ) ) . (
                    $this->isDebugMode()
                        ? '/asset/js/pointer-tooltip.js'
                        : '/asset/js/pointer-tooltip.min.js'
                ),
            true
        );
        wp_register_script(
            'aal-pointer-tooltip',
            $_sFileURL,
            array( 'jquery', 'wp-pointer', ),
            AmazonAutoLinks_Registry::VERSION,
            true
        );

    }
}