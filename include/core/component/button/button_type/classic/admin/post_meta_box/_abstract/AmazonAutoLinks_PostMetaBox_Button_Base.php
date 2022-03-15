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
 * Defines the meta box for the button post type.
 */
abstract class AmazonAutoLinks_PostMetaBox_Button_Base extends AmazonAutoLinks_Button_ButtonType_PostMetaBox_Base {

    /**
     * @callback add_action() admin_head
     */
    public function replyToPrintCustomStyleTag() {
        echo "<style type='text/css' id='amazon-auto-links-button-style'>" . PHP_EOL
                . '.amazon-auto-links-button {}' . PHP_EOL
            . "</style>";
    }

    /**
     *
     * @callback add_action() admin_enqueue_scripts
     */
    public function replyToSetScripts() {

        $this->enqueueScript(
            AmazonAutoLinks_Button_Classic_Loader::$sDirPath . '/asset/js/button-classic-preview-event.js',
            $this->oProp->aPostTypes,
            array(
                'handle_id'    => 'aal_button_script_preview_event',
                'dependencies' => array( 'jquery', 'apfRevealerFieldType' ),
                'in_footer'    => true,
            )
        );
        $this->enqueueScript(
            AmazonAutoLinks_Button_Classic_Loader::$sDirPath . '/asset/js/button-classic-preview-updater.js',
            $this->oProp->aPostTypes,
            array(
                'handle_id'    => 'aal_button_script_preview_updater',
                'dependencies' => array( 'jquery' ),
                'translation'  => array(
                    'post_id' => isset( $_GET[ 'post' ] )   // sanitization unnecessary as just checking
                        ? absint( $_GET[ 'post' ] )         // sanitization done
                        : '___button_id___',
                ),
                'in_footer'    => true,
            )
        );
    }

    /**
     * @since  5.2.0
     * @return boolean
     */
    protected function _shouldLoad() {

        if ( ! empty( $_GET[ 'button_type' ] ) ) {
            return false;
        }

        // Get the post ID.
        $_iPostID = AmazonAutoLinks_WPUtility::getCurrentPostID();

        // Maybe post-new.php
        if ( ! $_iPostID ) {
            return true;
        }

        $_sButtonType = get_post_meta( $_iPostID, '_button_type', true );
        return empty( $_sButtonType );

    }
        
}