<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 */

/**
 * Loads the Web Page Dumper sub-component.
 *
 * @package      Amazon Auto Links
 * @since        4.5.0
 */
class AmazonAutoLinks_Proxy_WebPageDumper_Loader extends AmazonAutoLinks_PluginUtility {

    static public $sDirPath;

    public function __construct() {

        self::$sDirPath  = dirname( __FILE__ );

        if ( is_admin() ) {
            new AmazonAutoLinks_Proxy_WebPageDumper_Admin;
        }

        $_oToolOption = AmazonAutoLinks_ToolOption::getInstance();
        if ( ! $_oToolOption->get( array( 'web_page_dumper', 'enable' ), false ) ) {
            return;
        }

        $this->___loadEvents();

    }

        private function ___loadEvents() {
            new AmazonAutoLinks_Proxy_WebPageDumper_Event_Filter_AmazonCookies;
            new AmazonAutoLinks_Proxy_WebPageDumper_Event_Filter_HTTPResponse;
            new AmazonAutoLinks_Proxy_WebPageDumper_Event_Filter_WebPageDumperArguments;
            new AmazonAutoLinks_Proxy_WebPageDumper_Event_Filter_HTTPRequestInterval;
            new AmazonAutoLinks_Proxy_WebPageDumper_Event_Filter_HTTPRequestPreResponse;
        }


}