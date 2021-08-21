<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 */

/**
 * Enables the Web Page Dumper option if the user checks the checkbox in the category selection screen.
 *
 * @since        4.6.23
 */
class AmazonAutoLinks_Proxy_WebPageDumper_Event_Must_Action_CategorySelection extends AmazonAutoLinks_Utility {

    /**
     * Sets up hooks.
     * @since 4.6.23
     */
    public function __construct() {
        add_action( 'aal_action_ajax_response_category_selection', array( $this, 'replyToDo' ) );
    }

    /**
     * @param  array $aPost
     * @since  4.6.23
     */
    public function replyToDo( $aPost ) {
        if ( ! ( boolean ) $this->getElement( $aPost, 'enableWebPageDumper' ) ) {
            return;
        }
AmazonAutoLinks_Debug::log( $_POST );
AmazonAutoLinks_Debug::log( $aPost );
        $this->___setWebPageDumperEnabled();
    }
        /**
         * @since 4.6.23
         */
        private function ___setWebPageDumperEnabled() {
            $_oToolOption = AmazonAutoLinks_ToolOption::getInstance();
            $_oToolOption->set( array( 'web_page_dumper', 'enable' ), true );
            $_oToolOption->save();
        }

}