<?php
/**
 * Auto Amazon Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2022 Michael Uno
 */

/**
 * Outputs for plugin custom oEmbed iframe requests.
 *
 * This replaces the iframe outputs to direct plugin Amazon product outputs
 * for cases that some user might not like to use iframe.
 *
 * @since        4.0.0
 */
class AmazonAutoLinks_CustomOEmbed_Content_NonIframe {

    /**
     *
     * @see WP_oEmbed::__construct()
     */
    public function __construct() {

        wp_embed_register_handler(
            "amazon_auto_links_oembed",
            "#https?://([a-z0-9-]+\.)?amazon\.(com|com\.mx|com\.br|ca|co\.uk|de|fr|it|es|in|nl|ru|co\.jp|com\.au)/.*#i",
            array( $this, "replyToSimulateWPPost" )
        );

    }

    /**
     * @param    array  $aMatches
     * @param    array  $aAttributes
     * @param    string $sURL
     * @param    array  $aAttributesRaw
     * @return   false|string
     * @callback wp_embed_register_handler()
     */
    public function replyToSimulateWPPost( $aMatches, $aAttributes, $sURL, $aAttributesRaw ) {
        $_oOption      = AmazonAutoLinks_Option::getInstance();
        $_aArguments   = array(
            'uri'         => urldecode( $sURL ),
            'template_id' => $_oOption->get( array( 'custom_oembed', 'template_id' ) ),
        );
        return AmazonAutoLinks( $_aArguments, false );
    }

}