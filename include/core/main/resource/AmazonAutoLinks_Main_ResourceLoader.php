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

        $_sAssetDirectoryPath = dirname( dirname( __FILE__ ) );
        $_sMin = $this->isDebugMode() ? '' : '.min';
        wp_register_script(
            'aal-pointer-tooltip',
            AmazonAutoLinks_Registry::getPluginURL( $_sAssetDirectoryPath . "/asset/js/pointer-tooltip{$_sMin}.js", true ),
            array( 'jquery', 'wp-pointer', ),
            AmazonAutoLinks_Registry::VERSION,
            true
        );
        wp_register_script(
            'aal-iframe-height-adjuster',
            AmazonAutoLinks_Registry::getPluginURL( $_sAssetDirectoryPath . "/asset/js/iframe-height-adjuster{$_sMin}.js", true ),
            array(),
            AmazonAutoLinks_Registry::VERSION,
            true
        );
        wp_register_script(
            'aal-content-height-notifier',
            AmazonAutoLinks_Registry::getPluginURL( $_sAssetDirectoryPath . "/asset/js/wp-embed-template-lite{$_sMin}.js", true ),
            array(),
            AmazonAutoLinks_Registry::VERSION,
            true
        );
    }
}