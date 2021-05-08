<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 */

/**
 * Handles JSON feed output.
 *
 * @package     Amazon Auto Links
 * @since       3.5.0
 */
class AmazonAutoLinks_Event___Feed_RSS2 extends AmazonAutoLinks_PluginUtility {

    /**
     * Sets up hooks.
     */
    public function __construct() {
        add_action( 'init', array( $this, 'replyToLoadRSS2Feed' ), 999 );
    }

    /**
     *
     * @since       3.1.0
     */
    public function replyToLoadRSS2Feed() {

        $_aArguments = $_GET;
        $_aArguments[ 'template_path' ]        = wp_normalize_path( AmazonAutoLinks_Registry::$sDirPath . '/template/rss2/template.php' );
        $_aArguments[ 'credit_link' ]          = false;
        $_aArguments[ '_no_outer_container' ]  = true;
        $_aArguments[ 'show_errors' ]          = 0;
        $_aArguments[ 'load_with_javascript' ] = false; // 3.6.0+

        add_filter( 'aal_filter_unit_format', array( $this, 'replyToGetUnitFormat' ) );
        header( 'Content-Type: ' . feed_content_type( 'rss-http' ) . '; charset=' . get_option( 'blog_charset' ), true );
        AmazonAutoLinks( $_aArguments, true );
        exit;
    }

    /**
     * Forces the unit format to only have `%products%` to avoid RSS validation errors.
     * @param  string $sUnitFormat
     * @return string
     * @since  4.5.8
     */
    public function replyToGetUnitFormat( $sUnitFormat ) {
        return '%products%';
    }

}