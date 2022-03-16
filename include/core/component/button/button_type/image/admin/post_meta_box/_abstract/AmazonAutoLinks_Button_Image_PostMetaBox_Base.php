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
 * A base class for post meta boxes that define buttons of the `image` button type and of the `button` post type.
 * @since 5.2.0
 */
abstract class AmazonAutoLinks_Button_Image_PostMetaBox_Base extends AmazonAutoLinks_Button_ButtonType_PostMetaBox_Base {

    /**
     * @var   string
     * @since 5.2.0
     */
    protected $_sButtonType = 'image';

    /**
     *
     * @callback add_action() admin_head
     */
    public function replyToPrintCustomStyleTag() {
        // echo "<style type='text/css' id='amazon-auto-links-button-style'>" . PHP_EOL
        //         . '.amazon-auto-links-button {}' . PHP_EOL
        //     . "</style>";
    }

    /**
     *
     * @callback add_action() admin_enqueue_scripts
     */
    public function replyToSetScripts() {
        $this->enqueueScript(
            AmazonAutoLinks_Button_Image_Loader::$sDirPath . '/asset/js/button-image-preview.js',
            $this->oProp->aPostTypes,
            array(
                'handle_id'    => 'aalImageButtonPreviewEventBinder',
                'dependencies' => array( 'jquery' ),
                'in_footer'    => true,
                'translation'  => array(
                    'postID' => $this->oUtil->getHTTPQueryGET( array( 'post' ), '___button_id___' ),
                ),
            )
        );
        $this->enqueueStyle(
            AmazonAutoLinks_Button_Image_Loader::$sDirPath . "/asset/css/button-image-preview.css",
            $this->oProp->aPostTypes
        );
    }

    /**
     * @since  5.2.0
     * @return boolean
     */
    protected function _shouldLoad() {
        if ( ! empty( $_GET[ 'button_type' ] ) && $this->_sButtonType === $_GET[ 'button_type' ] ) {
            return true;
        }
        if ( ! empty( $_REQUEST[ '_button_type' ] ) && $this->_sButtonType === $_REQUEST[ '_button_type' ] ) {
            return true;
        }
        return get_post_meta( AmazonAutoLinks_WPUtility::getCurrentPostID(), '_button_type', true ) === $this->_sButtonType;;
    }
        
}