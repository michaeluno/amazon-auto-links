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
 * Displays oEmbed information outputs.
 *
 * @package      Amazon Auto Links
 * @since        4.0.0
 */
class AmazonAutoLinks_CustomOEmbed_Endpoint {

    /**
     *
     */
    public function __construct() {

        if ( ! isset( $_GET[ 'oembed' ], $_GET[ 'endpoint' ] ) ) {
            return;
        }
        if ( $_GET[ 'oembed' ] !== 'amazon-auto-links' ) {
            return;
        }

        add_action( 'init', array( $this, 'replyToPrintOEmbedInformation' ) );

    }

    /**
     * $_GET contains the following keys:
     * ```
     *   [maxwidth] => (string) e.g. 600
     *   [maxheight] => (string) e.g. 960
     *   [url] => (string) https://... (urlencoded)
     *```
     */
    public function replyToPrintOEmbedInformation() {

        $_sIFrameURL = add_query_arg(
            array(
                'embed'  => 'amazon-auto-links',    // usually the value is 1 for normal oEmbed posts but here we use a custom value to differentiate the request to process own custom outputs
                // 'url'    => urlencode( $_GET[ 'url' ] ), // the key is reserved by the core for oEmbed discovery routine and when used, it causes recursive requests.
                'uri'    => urlencode( $_GET[ 'url' ] ),
            ),
            untrailingslashit( site_url() ) . '/amazon-auto-links/embed/'   // using a custom non-existent url so when the plugin is deactivated, the iframe displays the 404 embedded page.
        );
        $_aAttributes = array(
            'src'           => esc_url( $_sIFrameURL ),
            'sandbox'       => "allow-scripts",
            'security'      => "restricted",
            'width'         => 600,
            'height'        => 200,
            'frameborder'   => '0',
            'scrolling'     => 'no',
            'seamless'      => 'seamless',
            'marginwidth'   => '0',
            'marginheight'  => '0',
            'class'         => 'wp-embedded-content',
        );
        $_sIFrame = "<iframe " . AmazonAutoLinks_Utility::getAttributes( $_aAttributes ) . "></iframe>";
        $_aData = array(
            'version'       => '1.0',
            'type'          => 'rich',
            'width'         => 600,
            'height'        => 200,
            'title'         => 'Testing Amazon Custom oEmbed',
            'url'           => site_url(),
            'author_name'   => 'John Doe',
            'author_url'    => 'https://en.michaeluno.jp',
            'provider_name' => 'Amazon Auto Links',
            'provider_url'  => site_url(),
//            'html'  => "<figure>" . $_sIFrame . "</figure>",
            'html'  => $_sIFrame,
        );

        if ( 'xml' === $_GET[ 'format' ] ) {
            if ( ! headers_sent() ) {
                header( 'Content-Type', 'text/xml; charset=' . get_option( 'blog_charset' ) );
            }
            $_bsResult = _oembed_create_xml( $_aData );
        } else {
            $_bsResult = json_encode( $_aData );
        }
        echo trim( $_bsResult );
        exit;

    }

}