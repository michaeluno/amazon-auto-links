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
 * Loads the component, Custom oEmbed
 *
 * This enables the feature of oEmbed for links of Amazon stores.
 *
 * @package      Amazon Auto Links
 * @since        4.0.0
 */
class AmazonAutoLinks__CustomOEmbed_Loader {
        
    public function __construct() {

        $_oOption = AmazonAutoLinks_Option::getInstance();
//        if ( ! $_oOption->get( array( 'custom_oembed' ) ) ) {
//            return;
//        }

//        new AmazonAutoLinks_CustomOEmbed_Gutenberg;
        new AmazonAutoLinks_CustomOEmbed_Provider;
        new AmazonAutoLinks_CustomOEmbed_Endpoint;
        new AmazonAutoLinks_CustomOEmbed_iFrame;


        // For debugging
        add_filter( 'the_content', array( $this, 'testMeta' ) );
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

    public function testMeta( $sContent ) {
        $_oPost     = get_post();
        $_aMetaKeys = get_post_custom_keys( $_oPost->ID );
        $_aMetaKeys = is_array( $_aMetaKeys ) ? $_aMetaKeys : array();
        $_aMeta = array();
        foreach( $_aMetaKeys as $_sMetaKey ) {
            $_aMeta[ $_sMetaKey ] = get_post_meta( $_oPost->ID, $_sMetaKey, true );
        }
        return $sContent
            . '<h3>Meta Values</h3>'
            . AmazonAutoLinks_Debug::get( $_aMeta );
    }


}