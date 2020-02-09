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
class AmazonAutoLinks_CustomOEmbed_Loader {

    /**
     * @var string
     */
    static public $sDirPath;

    public function __construct() {

        self::$sDirPath = dirname( __FILE__ );

        $this->___loadAdminPages();

        $_oOption = AmazonAutoLinks_Option::getInstance();
        if ( ! $_oOption->get( array( 'custom_oembed', 'enabled' ) ) ) {
            return;
        }

        new AmazonAutoLinks_CustomOEmbed_Provider;
        new AmazonAutoLinks_CustomOEmbed_Endpoint;

        // The iframe is needed anyway for the post editor to preview outputs.
        new AmazonAutoLinks_CustomOEmbed_Content_Iframe;
        if ( ! $_oOption->get( array( 'custom_oembed', 'use_iframe' ) ) ) {
            new AmazonAutoLinks_CustomOEmbed_Content_NonIframe;
        }

        // For debugging
        // add_filter( 'the_content', array( $this, 'testMeta' ) );

    }

    private function ___loadAdminPages() {
        if ( ! is_admin() ) {
            return;
        }
        new AmazonAutoLinks_CustomOEmbed_Setting;
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