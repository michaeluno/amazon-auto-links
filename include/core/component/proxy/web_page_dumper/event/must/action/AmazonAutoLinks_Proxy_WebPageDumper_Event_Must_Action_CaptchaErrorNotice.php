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
class AmazonAutoLinks_Proxy_WebPageDumper_Event_Must_Action_CaptchaErrorNotice extends AmazonAutoLinks_Event_Action_AdminNotices_Base {

    public $sNonceKey = 'aal_action_web_page_dumper_enable';

    /**
     * @since 4.7.1
     * @param AmazonAutoLinks_AdminPageFramework_Factory $oFactory
     */
    public function replyToDo( $oFactory ) {
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
             . "<a href='" . esc_url( $_sURL ) . "' style='text-decoration:none;margin-left:1em;' class='button-link web-page-dumper-action' data-action='enable'>"
                . "<button class='button button-secondary button-small'>"
                    . __( 'Enable', 'amazon-auto-links' )
                . "</button>"
            . "</a>";
        AmazonAutoLinks_Registry::setAdminNotice(
            $_sMessage,
            'error',
            'warning'
        );
        $this->___enqueueScript();
    }
        /**
         * @since 4.7.3
         */
        private function ___enqueueScript() {
            $_sMin = $this->isDebugMode() ? '' : '.min';
            wp_enqueue_script(
                'web-page-dumper-enable-button',
                AmazonAutoLinks_Registry::getPluginURL( AmazonAutoLinks_Proxy_WebPageDumper_Loader::$sDirPath . "/asset/js/web-page-dumper-enable-button{$_sMin}.js", true ),
                array( 'jquery' ),
                AmazonAutoLinks_Registry::VERSION,
                true
            );
            $_aData = array(
                'ajaxURL'          => admin_url( 'admin-ajax.php' ),
                'actionHookSuffix' => $this->sNonceKey,
                'nonce'            => wp_create_nonce( $this->sNonceKey ),
                'spinnerURL'       => admin_url( 'images/loading.gif' ),
                'pluginName'       => AmazonAutoLinks_Registry::NAME,
                'debugMode'        => AmazonAutoLinks_Option::getInstance()->isDebug( 'js' ),
            );
            wp_localize_script( 'web-page-dumper-enable-button', 'aalWebPageDumperEnable', $_aData );
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