<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2020 Michael Uno
 */

/**
 * Loads the component, HTTP Proxy.
 *
 * @package      Amazon Auto Links
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

        $this->sPageSlug = AmazonAutoLinks_Registry::$aAdminPages[ 'tool' ];
        add_action( 'load_' . $this->sPageSlug, array( $this, 'replyToLoadPage' ) );

        new AmazonAutoLinks_Proxy_Event_Ajax_LoadProxyList;
        new AmazonAutoLinks_Proxy_Event_Filter_FetchProxyList;


        // Check if the proxy option is enabled
        $_aToolsOptions  = $this->getAsArray( get_option( AmazonAutoLinks_Registry::$aOptionKeys[ 'tools' ], array() ) );
        $_bEnabled       = $this->getElement( $_aToolsOptions, array( 'proxies', 'enable' ), false );
        if ( ! $_bEnabled ) {
            return;
        }

        new AmazonAutoLinks_Proxy_Event_Filter_SetProxy;
        new AmazonAutoLinks_Proxy_Event_Filter_MultipleAttempts;
        new AmazonAutoLinks_Proxy_Event_Action_UnusableProxy;
        new AmazonAutoLinks_Proxy_Event_Filter_PreventCaching;

    }


    /**
     *
     * @param           AmazonAutoLinks_AdminPageFramework
     * @callback        action      load_{page slug}
     */
    public function replyToLoadPage( $oFactory ) {

        // Register custom filed type.
        new AmazonAutoLinks_RevealerCustomFieldType( $oFactory->oProp->sClassName );

        // Tabs
        new AmazonAutoLinks_ToolAdminPage_Proxy_Tab(
            $oFactory,
            $this->sPageSlug
        );

        $this->___doPageSettings( $oFactory );

    }
        /**
         * Page styling
         * @since       4.2.0
         * @return      void
         */
        private function ___doPageSettings( $oFactory ) {

            $oFactory->setPageTitleVisibility( false ); // disable the page title of a specific page.
            $oFactory->setInPageTabTag( 'h2' );
            $oFactory->enqueueStyle( AmazonAutoLinks_Registry::getPluginURL( 'asset/css/admin.css' ) );

        }

}