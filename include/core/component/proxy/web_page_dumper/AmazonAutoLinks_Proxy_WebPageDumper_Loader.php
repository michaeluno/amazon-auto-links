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
 * Loads the Web Page Dumper sub-component.
 *
 * @package      Auto Amazon Links
 * @since        4.5.0
 */
class AmazonAutoLinks_Proxy_WebPageDumper_Loader extends AmazonAutoLinks_PluginUtility {

    static public $sDirPath;

    /**
     * The required Web Page Dumper version.
     * @var string
     * @since 4.7.5
     */
    const REQUIRED_VERSION = '1.8.1';

    public function __construct() {

        self::$sDirPath  = dirname( __FILE__ );

        if ( is_admin() ) {
            new AmazonAutoLinks_Proxy_WebPageDumper_Admin;
        }

        $this->___loadEventsMust();

        if ( ! $this->___shouldProceed() ) {
            return;
        }

        $this->___loadEvents();

    }
        /**
         * @return boolean
         * @since  4.6.23
         */
        private function ___shouldProceed() {
            $_oToolOption = AmazonAutoLinks_ToolOption::getInstance();
            if ( ! trim( $_oToolOption->get( array( 'web_page_dumper', 'list' ), '' ) ) ) {
                return false;   // if the list is empty, even though the option is enabled, there is nothing the component can do
            }
            if ( $_oToolOption->get( array( 'web_page_dumper', 'enable' ), false ) ) {
                return true;
            }
            if ( $this->isDoingAjax() && $this->getElement( $_POST, array( 'enableWebPageDumper' ) ) ) {
                return true;
            }
            return false;
        }

        /**
         * Loads events that must be loaded regardless of whether the option is enabled or not.
         * @since 4.6.23
         */
        private function ___loadEventsMust() {
            new AmazonAutoLinks_Proxy_WebPageDumper_Event_Must_Filter_CategorySelectionPostSanitization;    // [4.6.23+]
            new AmazonAutoLinks_Proxy_WebPageDumper_Event_Must_Filter_CategorySelectionReloadMessage;       // [4.6.23+]
            new AmazonAutoLinks_Proxy_WebPageDumper_Event_Must_Action_CategorySelection;                    // [4.6.23+]
            new AmazonAutoLinks_Proxy_WebPageDumper_Event_Must_Action_CaptchaErrorNotice;                   // [4.7.1+]
            if ( is_admin() ) {
                new AmazonAutoLinks_Proxy_WebPageDumper_Event_Ajax_Enable;  // 4.7.3
            }
        }

        /**
         *
         */
        private function ___loadEvents() {
            new AmazonAutoLinks_Proxy_WebPageDumper_Event_Filter_AmazonCookies;
            new AmazonAutoLinks_Proxy_WebPageDumper_Event_Filter_HTTPResponse;
            new AmazonAutoLinks_Proxy_WebPageDumper_Event_Filter_WebPageDumperArguments;
            new AmazonAutoLinks_Proxy_WebPageDumper_Event_Filter_HTTPRequestInterval;
            new AmazonAutoLinks_Proxy_WebPageDumper_Event_Filter_HTTPRequestPreResponse;
            new AmazonAutoLinks_Proxy_WebPageDumper_Event_Filter_IsAllowedURL;
            new AmazonAutoLinks_Proxy_WebPageDumper_Event_Ajax_VersionChecksAdminNotices; // [4.7.5+]
        }

}