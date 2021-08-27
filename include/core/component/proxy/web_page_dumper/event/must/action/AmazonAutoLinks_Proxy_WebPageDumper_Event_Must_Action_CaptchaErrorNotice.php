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
 * Shows a notice to the user when there is a captcha error and encourage the user to enable Web Page Dumper.
 *
 * @since        4.7.1
 */
class AmazonAutoLinks_Proxy_WebPageDumper_Event_Must_Action_CaptchaErrorNotice extends AmazonAutoLinks_Utility {

    public $sNonceKey = 'aal-link-action-enable-web-page-dumper';

    /**
     * Sets up hooks.
     * @since 4.7.1
     */
    public function __construct() {
        add_action( 'load_' . 'AmazonAutoLinks_AdminPage', array( $this, 'replyToDo' ) );
        add_action( 'load_' . 'AmazonAutoLinks_ToolAdminPage', array( $this, 'replyToDo' ) );

        // At the moment, not showing messages in  the post listing pages as setSettingNotice() is not supported in these factory classes
        // add_action( 'load_' . 'AmazonAutoLinks_PostType_Unit', array( $this, 'replyToDo' ) );
        // add_action( 'load_' . 'AmazonAutoLinks_PostType_AutoInsert', array( $this, 'replyToDo' ) );
        // add_action( 'load_' . 'AmazonAutoLinks_PostType_Button', array( $this, 'replyToDo' ) );
    }

    /**
     * @since 4.7.1
     * @param AmazonAutoLinks_AdminPageFramework_Factory $oFactory
     */
    public function replyToDo( $oFactory ) {
        if ( ! is_admin() ) {
            return;
        }
        if ( isset( $_GET[ 'action' ], $_GET[ 'nonce' ] ) && 'enableWebPageDumper' === $_GET[ 'action' ] ) {
            $this->___handleEnableAction( $this->getHTTPQueryGET( 'nonce' ), $oFactory );
            return;
        }
        $_oToolOption = AmazonAutoLinks_ToolOption::getInstance();
        if ( $_oToolOption->get( array( 'web_page_dumper', 'enable' ), false ) ) {
            return;
        }
        if ( $this->___hasCaptchaErrors() ) {
            return;
        }
        // @todo add setting notice with an ajax script
        $_sURL     = add_query_arg(
            array(
                'action' => 'enableWebPageDumper',
                'nonce'  => wp_create_nonce( $this->sNonceKey ),
            )
        );
        $_sMessage = __( 'You have a captcha error. Consider enabling Web Page Dumper.', 'amazon-auto-links' )
             . "<a href='" . esc_url( $_sURL ) . "' style='text-decoration:none;margin-left:1em;' class='button-link'>"
                . "<button class='button button-secondary button-small'>"
                    . __( 'Enable', 'amazon-auto-links' )
                . "</button>"
            . "</a>";
        AmazonAutoLinks_Registry::setAdminNotice(
            $_sMessage,
            'error',
            'warning'
        );
    }
        /**
         * @return boolean
         * @since  4.7.1
         */
        private function ___hasCaptchaErrors() {
            $_sErrorLogPath = apply_filters( 'aal_filter_log_error_log_file_path', '' );
            if ( ! file_exists( $_sErrorLogPath ) ) {
                return false;
            }
            $_sErrorLog = file_get_contents( $_sErrorLogPath );
            return false !== strpos( $_sErrorLog, 'BLOCKED_BY_CAPTCHA' );
        }

        /**
         * @since 4.7.1
         * @param string $sNonce
         * @param AmazonAutoLinks_AdminPageFramework_Factory $oFactory
         */
        private function ___handleEnableAction( $sNonce, $oFactory ) {

            if ( ! wp_verify_nonce( $sNonce, $this->sNonceKey ) ) {
                return;
            }
            $_oToolOption = AmazonAutoLinks_ToolOption::getInstance();
            $_oToolOption->set( array( 'web_page_dumper', 'enable' ), true );
            $_oToolOption->save();

            $oFactory->setSettingNotice( __( 'The options have been updated.', 'amazon-auto-links' ), 'updated' );

            $_sURL = remove_query_arg( array( 'nonce', 'action' ) );
            exit( wp_safe_redirect( $_sURL ) );

        }

}