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
 * Loads the unit option converter component.
 *
 * @since        3.8.0
 */
class AmazonAutoLinks_Loader_LinkConverter {

    public function __construct() {

        if ( is_admin() ) {
            $this->___loadAdminComponents();
        }

        if ( ! $this->___shouldProceed() ) {
            return;
        }

        new AmazonAutoLinks_LinkConverter_Output;

    }
        /**
         * @return boolean
         * @since  4.7.0
         */
        private function ___shouldProceed() {

            // Backward compatibility with below 4.7.0.
            $_oOption     = AmazonAutoLinks_Option::getInstance();
            $_aRawOptions = $_oOption->getRawOptions();
            if ( isset( $_aRawOptions[ 'convert_links' ] ) ) {
                return ( boolean ) $_oOption->get( 'convert_links', 'enabled' );
            }
            $_oToolOption = AmazonAutoLinks_ToolOption::getInstance();
            return ( boolean ) $_oToolOption->get( 'convert_links', 'enabled' );

        }

        private function ___loadAdminComponents() {
            add_action( 'load_' . AmazonAutoLinks_Registry::$aAdminPages[ 'tool' ], array( $this, 'replyToLoadPage' ) );
        }
            public function replyToLoadPage( $oAdminPage ) {
                new AmazonAutoLinks_LinkConverter_Setting_Tab( $oAdminPage, AmazonAutoLinks_Registry::$aAdminPages[ 'tool' ] );
            }

}