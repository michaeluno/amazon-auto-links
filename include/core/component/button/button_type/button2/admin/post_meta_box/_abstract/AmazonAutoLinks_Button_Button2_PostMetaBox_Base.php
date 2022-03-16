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
     *
     * @callback add_action() admin_enqueue_scripts
     */
    public function replyToPrintCustomStyleTag() {
        // echo "<style type='text/css' id='amazon-auto-links-button-style'>" . PHP_EOL
        //         . '.amazon-auto-links-button {}' . PHP_EOL
        //     . "</style>";
    }

    /**
     *
     * @callback add_action() admin_head For unknown reasons, `wp_enqueue_scripts` does not work.
     */
    public function replyToSetScripts() {
    }

    /**
     * @since  5.2.0
     * @return boolean
     */
    protected function _shouldLoad() {
        return ( ! empty( $_GET[ 'button_type' ] ) && $this->_sButtonType === $_GET[ 'button_type' ] )
            || get_post_meta( AmazonAutoLinks_WPUtility::getCurrentPostID(), '_button_type', true ) === $this->_sButtonType;;
    }
        
}