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
 * Loads the component, Custom oEmbed
 *
 * This enables the feature of oEmbed for links of Amazon stores.
 *
 * @package      Auto Amazon Links
 * @since        4.0.0
 */
class AmazonAutoLinks_CustomOEmbed_Provider {

    /**
     *
     * @see WP_oEmbed::__construct()
     */
    public function __construct() {

        // Case: replacing the provider patterns
         add_filter( 'oembed_providers', array( $this, 'replyToAddProviders' ) );
    }

    /**
     * @param $aProviders
     *
     * @return array
     * @callback filter oembed_providers
     * @see WP_oEmbed::__construct()
     */
    public function replyToAddProviders( $aProviders ) {

        $_sEndpointURL = add_query_arg(
            array(
                'oembed'   => 'amazon-auto-links',
                'endpoint' => true,
                'format'   => 'xml', // json or xml
            ),
            site_url()
        );
        $aProviders = array(
            "#https?://([a-z0-9-]+\.)?amazon\.[a-z]+(\.[a-z])?/.*#i" => array( $_sEndpointURL, true ),
        ) + $aProviders; // with array union, the element will be placed at the top of the array.
        $aProviders[ "#https?://([a-z0-9-]+\.)?amazon\.(com|com\.mx|com\.br|ca)/.*#i" ] = array( $_sEndpointURL, true );
        $aProviders[ "#https?://([a-z0-9-]+\.)?amazon\.(co\.uk|de|fr|it|es|in|nl|ru)/.*#i" ] = array( $_sEndpointURL, true );
        $aProviders[ "#https?://([a-z0-9-]+\.)?amazon\.(co\.jp|com\.au)/.*#i" ] = array( $_sEndpointURL, true );
        return $aProviders;

    }

}