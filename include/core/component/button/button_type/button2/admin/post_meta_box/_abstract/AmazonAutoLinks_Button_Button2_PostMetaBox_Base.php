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
 * A base class for post meta boxes that define buttons of the `button2` button type and of the `button` post type.
 * @since 5.2.0
 */
abstract class AmazonAutoLinks_Button_Button2_PostMetaBox_Base extends AmazonAutoLinks_Button_ButtonType_PostMetaBox_Base {

    /**
     * @var   string
     * @since 5.2.0
     */
    protected $_sButtonType = 'button2';

    /**
     * @callback add_action() admin_enqueue_scripts
     */
    public function replyToSetScripts() {
        $this->enqueueScript(
            AmazonAutoLinks_Button_Button2_Loader::$sDirPath . '/asset/js/button-preview-button2.js',
            $this->oProp->aPostTypes,
            array(
                'handle_id'    => 'aalButton2ButtonPreviewEventBinder',
                'dependencies' => array( 'jquery' ),
                'in_footer'    => true,
                'translation'  => array(
                    'postID'    => $this->oUtil->getHTTPQueryGET( array( 'post' ), '___button_id___' ),
                    'nonce'     => wp_create_nonce( 'aal_button_preview_nonce' ),
                    'debugMode' => $this->oUtil->isDebugMode(),
                ),
            )
        );
        $this->enqueueStyle(
            AmazonAutoLinks_Button_Button2_Loader::$sDirPath . "/asset/css/button-button2-preview.css",
            $this->oProp->aPostTypes
        );
    }

}