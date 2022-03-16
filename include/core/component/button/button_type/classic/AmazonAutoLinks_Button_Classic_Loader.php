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
class AmazonAutoLinks_Button_Classic_Loader extends AmazonAutoLinks_Button_ButtonType_Loader {

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

        new AmazonAutoLinks_Button_Classic_Event_ButtonOutput;
        new AmazonAutoLinks_Button_Classic_Event_Query_ButtonPreview;

    }

    /**
     * @since  5.2.0
     * @param  array $aMetaKeys
     * @return array
     */
    protected function _getProtectedMetaKeys( $aMetaKeys ) {

        $_aClassNames = array(
            'AmazonAutoLinks_FormFields_Button_Preview',
            'AmazonAutoLinks_FormFields_Button_Selector',
            'AmazonAutoLinks_FormFields_Button_Box',
            'AmazonAutoLinks_FormFields_Button_Hover',
            'AmazonAutoLinks_FormFields_Button_Text',
            'AmazonAutoLinks_FormFields_Button_Background',
            'AmazonAutoLinks_FormFields_Button_Border',
            'AmazonAutoLinks_FormFields_Button_CSS',
        );
        foreach( $_aClassNames as $_sClassName ) {
            $_oFields = new $_sClassName();  // not passing a factory object as it's hard and not necessary only to get field IDs.
            $aMetaKeys = array_merge( $aMetaKeys, $_oFields->getFieldIDs() );
        }
        return $aMetaKeys;

    }

    protected function _loadPostMetaBoxes() {
        new AmazonAutoLinks_PostMetaBox_Button_Preview(
            null, // meta box ID - null to auto-generate
            __( 'Button Preview', 'amazon-auto-links' ),
            array( // post type slugs: post, page, etc.
                AmazonAutoLinks_Registry::$aPostTypes[ 'button' ]
            ),
            'side', // context (what kind of metabox this is)
            'high' // priority - 'high', 'sorted', 'core', 'default', 'low'
        );
        new AmazonAutoLinks_PostMetaBox_Button_CSS(
            null, // meta box ID - null to auto-generate
            __( 'CSS', 'amazon-auto-links' ),
            array( // post type slugs: post, page, etc.
                AmazonAutoLinks_Registry::$aPostTypes[ 'button' ]
            ),
            'side', // context (what kind of metabox this is)
            'high' // priority - 'high', 'sorted', 'core', 'default', 'low'
        );
        new AmazonAutoLinks_PostMetaBox_Button_Text(
            null, // meta box ID - null to auto-generate
            __( 'Text', 'amazon-auto-links' ),
            array( // post type slugs: post, page, etc.
                AmazonAutoLinks_Registry::$aPostTypes[ 'button' ]
            ),
            'normal', // context (what kind of metabox this is)
            'default' // priority
        );
        new AmazonAutoLinks_PostMetaBox_Button_Box(
            null, // meta box ID - null to auto-generate
            __( 'Box', 'amazon-auto-links' ),
            array( // post type slugs: post, page, etc.
                AmazonAutoLinks_Registry::$aPostTypes[ 'button' ]
            ),
            'normal', // context (what kind of metabox this is)
            'default' // priority
        );
        new AmazonAutoLinks_PostMetaBox_Button_Border(
            null, // meta box ID - null to auto-generate
            __( 'Border', 'amazon-auto-links' ),
            array( // post type slugs: post, page, etc.
                AmazonAutoLinks_Registry::$aPostTypes[ 'button' ]
            ),
            'normal', // context (what kind of metabox this is)
            'default' // priority
        );
        new AmazonAutoLinks_PostMetaBox_Button_Background(
            null, // meta box ID - null to auto-generate
            __( 'Background', 'amazon-auto-links' ),
            array( // post type slugs: post, page, etc.
                AmazonAutoLinks_Registry::$aPostTypes[ 'button' ]
            ),
            'normal', // context (what kind of metabox this is)
            'default' // priority
        );
        new AmazonAutoLinks_PostMetaBox_Button_Hover(
            null, // meta box ID - null to auto-generate
            __( 'Hover', 'amazon-auto-links' ),
            array( // post type slugs: post, page, etc.
                AmazonAutoLinks_Registry::$aPostTypes[ 'button' ]
            ),
            'normal', // context (what kind of metabox this is)
            'default' // priority
        );
    }

}