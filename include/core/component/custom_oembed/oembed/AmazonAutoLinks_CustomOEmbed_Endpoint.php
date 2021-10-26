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
 * Displays oEmbed information outputs.
 *
 * @since        4.0.0
 */
class AmazonAutoLinks_CustomOEmbed_Endpoint {

    /**
     *
     */
    public function __construct() {

        $_aGET = $_GET; // sanitization unnecessary
        if ( ! isset( $_aGET[ 'oembed' ], $_aGET[ 'endpoint' ] ) ) {
            return;
        }
        if ( $_aGET[ 'oembed' ] !== 'amazon-auto-links' ) {
            return;
        }

        add_action( 'init', array( $this, 'replyToPrintOEmbedInformation' ) );

    }

    /**
     * GET contains the following keys:
     * ```
     *   [maxwidth] => (string) e.g. 600
     *   [maxheight] => (string) e.g. 960
     *   [url] => (string) https://... (urlencoded)
     *```
     */
    public function replyToPrintOEmbedInformation() {

        $_oOption           = AmazonAutoLinks_Option::getInstance();
        $_sNonce            = wp_create_nonce( 'aal_custom_oembed' );
        $_sProviderSiteURL  = $_oOption->get( array( 'custom_oembed', 'external_provider' ), '' );
        $_sProviderEndpoint = filter_var( $_sProviderSiteURL, FILTER_VALIDATE_URL )
            ? untrailingslashit( $_sProviderSiteURL ) . '/amazon-auto-links/embed/'
            : untrailingslashit( site_url() ) . '/amazon-auto-links/embed/'; // using a custom non-existent url so when the plugin is deactivated, the iframe displays the 404 embedded page.
        $_sIFrameURL        = add_query_arg(
            array(
                'embed'  => 'amazon-auto-links',    // usually the value is 1 for normal oEmbed posts but here we use a custom value to differentiate the request to process own custom outputs
                // 'url' => ... the key is reserved by the core for oEmbed discovery routine and when used, it causes recursive requests.
                'uri'    => urlencode( AmazonAutoLinks_PluginUtility::getURLSanitized( $_GET[ 'url' ] ) ),  // sanitization done
            ),
            $_sProviderEndpoint . "#secret={$_sNonce}" // referred by wp-embed.js and wp-embed-template-lite.js
        );
        $_aAttributes = array(
            'src'           => esc_url( $_sIFrameURL ),
            'width'         => 600,
            'height'        => 200,
            'frameborder'   => '0',
            'scrolling'     => 'no',
            'marginwidth'   => '0',
            'marginheight'  => '0',
            'class'         => 'wp-embedded-content aal-embed',
            'data-secret'   => $_sNonce,       // without this wp-embed.js adds the `src` attribute and it causes to load the frame source again which results in calling the unit output function twice in a very short period of time which causes duplicate API requests.
        );
        $_sIFrame = "<iframe " . AmazonAutoLinks_Utility::getAttributes( $_aAttributes ) . "></iframe>";
        $_aData = array(
            'version'       => '1.0',
            'type'          => 'rich',
            'width'         => 600,
            'height'        => 200,
            'title'         => __( 'Amazon Product', 'amazon-auto-links' ),
            'url'           => site_url(),
            'author_name'   => AmazonAutoLinks_Registry::AUTHOR,
            'author_url'    => AmazonAutoLinks_Registry::PLUGIN_URI,
            'provider_name' => AmazonAutoLinks_Registry::NAME,
            'provider_url'  => AmazonAutoLinks_Registry::AUTHOR_URI,
            'html'          => $_sIFrame,
        );

        if ( 'xml' === $_GET[ 'format' ] ) {    // sanitization unnecessary as just checking
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