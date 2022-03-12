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
 * @since 5.2.0
 */
class AmazonAutoLinks_Button_Button2_Loader extends AmazonAutoLinks_Button_ButtonType_Loader {

    /**
     * @since  5.2.0
     * @var    string
     * @remark This property needs to be declared in each extended class. Otherwise, the parent value gets applied.
     */
    static public $sDirPath;

    /**
     * @since 5.2.0
     */
    protected function _construct() {
        self::$sDirPath = dirname( __FILE__ );
    }

    /**
     * @since 5.2.0
     */
    protected function _loadPostMetaBoxes() {
        new AmazonAutoLinks_Button_Button2_PostMetaBox_Preview(
            null, // meta box ID - null to auto-generate
            __( 'Preview', 'amazon-auto-links' ),
            array( // post type slugs: post, page, etc.
                AmazonAutoLinks_Registry::$aPostTypes[ 'button' ]
            ),
            'side', // context (what kind of metabox this is)
            'high' // priority - 'high', 'sorted', 'core', 'default', 'low'
        );
    }

}