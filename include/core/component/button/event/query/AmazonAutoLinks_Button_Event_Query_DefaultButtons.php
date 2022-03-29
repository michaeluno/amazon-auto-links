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
 * Responds to the default button creation URL query.
 * @since 5.2.0
 */
class AmazonAutoLinks_Button_Event_Query_DefaultButtons {

    /**
     * Sets up properties and hooks.
     * @since 5.2.0
     */
    public function __construct() {
        if ( ! $this->___shouldProceed() ) {
            return;
        }
        $_oDefaultButtons = new AmazonAutoLinks_Button_DefaultButtons();
        $_oDefaultButtons->getButtonsCreated();

        exit( wp_safe_redirect( remove_query_arg( array( 'aal_action', 'nonce', 'post_status' ) ) ) );  // `post_status` is for a case from the Trash screen
    }

        /**
         * @since  5.2.0
         * @return false
         */
        private function ___shouldProceed() {
            if ( ! isset( $_GET[ 'aal_action' ], $_GET[ 'post_type' ], $_GET[ 'nonce' ] ) ) {
                return false;
            }
            if (
                'generate_default_buttons' !== $_GET[ 'aal_action' ]
                || AmazonAutoLinks_Registry::$aPostTypes[ 'button' ] !== $_GET[ 'post_type' ]
                || ! wp_verify_nonce( $_GET[ 'nonce' ], 'aal_admin' )
            ) {
                return false;
            }
            return true;
        }

}