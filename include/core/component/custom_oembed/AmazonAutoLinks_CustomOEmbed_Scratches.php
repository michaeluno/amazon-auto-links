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
 * Testing custom oEmbed outputs
 *
 * This enables the feature of oEmbed for links of Amazon stores.
 *
 * @remark       This is for debugging. Should be removed.
 * @package      Amazon Auto Links
 * @since        4.0.0
 * @deprecated
 */
class AmazonAutoLinks_CustomOEmbed_Scratches {

    /**
     *
     * @see WP_oEmbed::__construct()
     */
    public function __construct() {

//        "#https?://([a-z0-9-]+\.)?amazon\.(com|com\.mx|com\.br|ca)/.*#i" => array( "https://read.amazon.com/kp/api/oembed", true ),
//        "#https?://([a-z0-9-]+\.)?amazon\.(co\.uk|de|fr|it|es|in|nl|ru)/.*#i" => array( "https://read.amazon.co.uk/kp/api/oembed", true ),
//        "#https?://([a-z0-9-]+\.)?amazon\.(co\.jp|com\.au)/.*#i" => array( "https://read.amazon.com.au/kp/api/oembed", true ),

//         wp_oembed_add_provider(
//             "#https?://([a-z0-9-]+\.)?amazon\.(co\.jp|com\.au)/.*#i",
//             site_url() . '?oembed=amazon-auto-links',
//             true
//         );

        // Case: replacing the output
//        wp_embed_register_handler(
//            "amazon_auto_links_oembed",
//            "#https?://([a-z0-9-]+\.)?amazon\.(co\.jp|com\.au)/.*#i",
//            array( $this, "replyToSimulateWPPost" )
//        );

        // Non-existing url
//        wp_embed_register_handler(
//            "test_oembed",
//            "#https?://([a-z0-9-]+\.)?somewhere\.com/.*#i",
//            array( $this, "getOutputOfSomewhere" )
//        );

        // Case: replacing the provider patterns
         add_filter( 'oembed_providers', array( $this, 'replyToAddProviders' ) );


//         add_filter( 'pre_oembed_result', array( $this, 'replyToGetCustomOutput' ), 10, 3 );

//        add_filter( 'oembed_response_data', array( $this, 'debugOEmbedResponseData' ), 10, 4 );


    }

    /**
     * @param array   $data   The response data.
     * @param WP_Post $post   The post object.
     * @param int     $width  The requested width.
     * @param int     $height The calculated height.
     */
    public function debugOEmbedResponseData( $data, $post, $width, $height ) {
//AmazonAutoLinks_Debug::log( func_get_args() );
        return $data;
    }


    public function getOutputOfSomewhere( $aMatches, $aAttributes, $sURL, $aAttributesRaw ) {
        return $this->getDummyContent();
    }

    /**
     * @param $aProviders
     *
     * @return array
     * @callback filter oembed_providers
     * @see WP_oEmbed::__construct()
     */
    public function replyToAddProviders( $aProviders ) {

        // e.g. http://www.flickr.com/services/oembed/?format=json&url=http%3A//www.flickr.com/photos/bees/2341623661/

        // $_sURL = get_oembed_endpoint_url( get_permalink( 1 ) );
        $_sEndpointURL = add_query_arg(
            array(
                'oembed'   => 'amazon-auto-links',
                'endpoint' => true,
                // 'format'   => 'json', // or xml
                'format'   => 'xml', // or xml
            ),
            site_url()
        );
        $aProviders[ "#https?://([a-z0-9-]+\.)?amazon\.(com|com\.mx|com\.br|ca)/.*#i" ] = array( $_sEndpointURL, true );
        $aProviders[ "#https?://([a-z0-9-]+\.)?amazon\.(co\.uk|de|fr|it|es|in|nl|ru)/.*#i" ] = array( $_sEndpointURL, true );
        $aProviders[ "#https?://([a-z0-9-]+\.)?amazon\.(co\.jp|com\.au)/.*#i" ] = array( $_sEndpointURL, true );
        $aProviders[ "#https?://([a-z0-9-]+\.)?somewhere\.com/.*#i" ] = array( $_sEndpointURL, true );

        return $aProviders;
    }

    private function getDummyContent() {
        return "<pre>"
            . "<h3>Amazon Custom oEmbed Output</h3>"
            . "<p>This is a custom oEmbed output.</p>"
            . "</pre>";
    }

    /**
     * @param $snOutput
     * @param $sURL
     * @param $aArguments
     *
     * @return string
     * @callback  filter  pre_oembed_result
     */
    public function replyToGetCustomOutput( $snOutput, $sURL, $aArguments ) {

        if ( ! preg_match( "#https?://([a-z0-9-]+\.)?amazon\.(co\.jp|com\.au)/.*#i", $sURL ) ) {
            return $snOutput;
        }

        return get_post_embed_html( 600, 200, get_post( 1 ) );

        $_sIFrameURL = add_query_arg(
            array(
                "oembed" => 'amazon-auto-links',
            ),
            site_url()
        );
        $_aAttributes = array(
            'src' => esc_url( $_sIFrameURL ),
//            'width' => $aAttributes[ "width" ] . 'px',
            'width' => $aArguments[ "width" ] . 'px',
            'height' => 200,
            'frameborder'   => 0,
            'scrolling'     => 'no',
            'marginwidth'   => 0,
            'marginheight'  => 0,
        );
        return "<iframe " . AmazonAutoLinks_Utility::getAttributes( $_aAttributes ) . "></iframe>";
    }

    /**
     * @param array  $aMatches       The RegEx matches from the provided regex when calling
     *                               wp_embed_register_handler().
     * @param array  $aAttributes    Embed attributes.
     * @param string $sURL           The original URL that was matched by the regex.
     * @param array  $aAttributesRaw The original unmodified attributes.
     *
     * @return string
     * @callback    function    wp_embed_register_handler()
     */
    public function replyToGetCustomOEmbedOutput( $aMatches, $aAttributes, $sURL, $aAttributesRaw ) {
        $_sIFrameURL = add_query_arg(
            array(
                "oembed" => 'amazon-auto-links',
//                "show"   => $aMatches[ 1 ],
//                "format" => "frame",
//                "width"  => $aAttributes[ "width" ],
//                "height" => $aAttributes[ "height" ],
//                "mode"   => "render",
            ),
            site_url()
        );

        $_aAttributes = array(
            'src' => esc_url( $_sIFrameURL ),
//            'width' => $aAttributes[ "width" ] . 'px',
            'width' => $aAttributes[ "width" ] . 'px',
            'height' => 200,
            'frameborder'   => 0,
            'scrolling'     => 'no',
            'marginwidth'   => 0,
            'marginheight'  => 0,
        );
        return "<iframe " . AmazonAutoLinks_Utility::getAttributes( $_aAttributes ) . "></iframe>";
//        return sprintf(
//        '<iframe src="'. esc_url( $_sIFrameURL ) . '" width="%1$spx" height="%2$spx" frameborder="0" scrolling="no" marginwidth="0" marginheight="0"></iframe>',
//                esc_attr( $aAttributes[ "width" ] ),
//                esc_attr( $aAttributes[ "height" ] )
//        );
    }

    /**
     * @param $aMatches
     * @param $aAttributes
     * @param $sURL
     * @param $aAttributesRaw
     *
     * @return false|string
     * @callback function wp_embed_register_handler
     */
    public function replyToSimulateWPPost( $aMatches, $aAttributes, $sURL, $aAttributesRaw ) {
        return get_post_embed_html( 600, 200, get_post( 0 ) );
    }

}