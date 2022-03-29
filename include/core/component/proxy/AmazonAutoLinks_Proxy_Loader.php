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
 * Loads the component, HTTP Proxy.
 *
 * @since        4.2.0
 *
 * @todo        Remove caches of failed response with proxies.
 */
class AmazonAutoLinks_Proxy_Loader extends AmazonAutoLinks_PluginUtility {

    static public $sDirPath;

    static public $sHTTPRequestType = 'proxy_list';

    public $sPageSlug = ''; // set in the constructor

    public function __construct() {

        self::$sDirPath  = dirname( __FILE__ );

        new AmazonAutoLinks_Proxy_WebPageDumper_Loader;

        $this->sPageSlug = AmazonAutoLinks_Registry::$aAdminPages[ 'tool' ];
        add_action( 'load_' . $this->sPageSlug, array( $this, 'replyToLoadAdminPage' ) );

        new AmazonAutoLinks_Proxy_Event_Ajax_LoadProxyList;
        new AmazonAutoLinks_Proxy_Event_Filter_FetchProxyList;


        // Check if the proxy option is enabled
        $_oToolOption    = AmazonAutoLinks_ToolOption::getInstance();
        if ( ! $_oToolOption->get( array( 'proxies', 'enable' ), false ) ) {
            return;
        }

        new AmazonAutoLinks_Proxy_Event_Filter_SetProxy;
        new AmazonAutoLinks_Proxy_Event_Filter_MultipleAttempts;
        new AmazonAutoLinks_Proxy_Event_Action_UnusableProxy;
        new AmazonAutoLinks_Proxy_Event_Filter_PreventCaching;
        new AmazonAutoLinks_Proxy_Event_Filter_CheckExistentCaptchaErrors; // 4.2.2

        if ( ! $_oToolOption->get( array( 'proxies', 'automatic_updates' ), false ) ) {
            return;
        }
        new AmazonAutoLinks_Proxy_Event_WPCronAction_ProxyUpdate;

    }


    /**
     *
     * @param           AmazonAutoLinks_AdminPageFramework
     * @callback        action      load_{page slug}
     */
    public function replyToLoadAdminPage( $oFactory ) {

        // Register custom filed type.
        new AmazonAutoLinks_RevealerCustomFieldType( $oFactory->oProp->sClassName );

        // Tabs
        new AmazonAutoLinks_ToolAdminPage_Proxy_Tab(
            $oFactory,
            $this->sPageSlug
        );

        $this->_doPageSettings( $oFactory );

    }
        /**
         * Page styling
         * @since  4.2.0
         * @since  4.5.0   Change the visibility scope to protected from private.
         * @param  AmazonAutoLinks_AdminPageFramework $oFactory
         * @return void
         */
        protected function _doPageSettings( $oFactory ) {
            $oFactory->setPageTitleVisibility( false ); // disable the page title of a specific page.
            $oFactory->setInPageTabTag( 'h2' );
        }

}