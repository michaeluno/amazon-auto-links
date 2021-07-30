<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 */

/**
 * Handles JSON feed output.
 *
 * @package     Amazon Auto Links
 * @since       3.5.0
 * @since       4.6.0   Extends `AmazonAutoLinks_Event___Feed_Base`.
 */
class AmazonAutoLinks_Event___Feed_RSS2 extends AmazonAutoLinks_Event___Feed_Base {

    /**
     * @var string The feed type such as rss2 or json.
     * @since 4.6.4
     */
    public $sType = 'rss2';

    /**
     * @since 4.6.0
     */
    protected function _load() {

        add_filter( 'aal_filter_unit_show_error_mode', '__return_zero' );

        $_aArguments = $_GET;
        $_aArguments[ 'template_path' ]        = wp_normalize_path( AmazonAutoLinks_Registry::$sDirPath . '/template/rss2/template.php' );
        $_aArguments[ 'credit_link' ]          = false;
        $_aArguments[ '_no_outer_container' ]  = true;
        $_aArguments[ 'show_errors' ]          = 0;
        $_aArguments[ 'load_with_javascript' ] = false; // 3.6.0+

        header( 'Content-Type: ' . feed_content_type( 'rss-http' ) . '; charset=' . get_option( 'blog_charset' ), true );
        AmazonAutoLinks( $_aArguments, true );

    }

}