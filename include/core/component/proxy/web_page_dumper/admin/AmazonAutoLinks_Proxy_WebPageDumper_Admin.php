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
 * Loads the admin component of the Proxy/WebPageDumper component.
 * 
 * @since       4.5.0
 */
class AmazonAutoLinks_Proxy_WebPageDumper_Admin extends AmazonAutoLinks_PluginUtility {

    /**
     * @var string
     */
    public $sPageSlug;


    /**
     * Sets up properties and hooks.
     */
    public function __construct() {

        $this->sPageSlug = AmazonAutoLinks_Registry::$aAdminPages[ 'tool' ];
        add_action( 'load_' . $this->sPageSlug, array( $this, 'replyToLoadAdminPage' ) );
        add_action( 'load_' . $this->sPageSlug . '_proxy', array( $this, 'replyToLoadTab' ) );

    }

    /**
     *
     * @param           AmazonAutoLinks_AdminPageFramework
     * @callback        add_action()      load_{page slug}
     */
    public function replyToLoadAdminPage( $oFactory ) {

        $oFactory->enqueueStyle( AmazonAutoLinks_Proxy_WebPageDumper_Loader::$sDirPath . '/asset/css/admin-web-page-dumper.css' , $this->sPageSlug );

        // Tabs
        new AmazonAutoLinks_Proxy_WebPageDumper_Admin_Tab_Help( $oFactory, $this->sPageSlug );

    }

    public function replyToLoadTab( $oFactory ) {

        // this must be called regardless of the option is enabled or not so that when the option is not checked, the ajax test can run.
        new AmazonAutoLinks_Proxy_WebPageDumper_Event_Ajax_TestAvailability;
        if ( AmazonAutoLinks_ToolOption::getInstance()->get( array( 'web_page_dumper', 'enable' ), false ) ) {
            new AmazonAutoLinks_Proxy_WebPageDumper_Event_Ajax_VersionChecks; // [4.7.5+]
        }
        // Form Sections
        new AmazonAutoLinks_Proxy_WebPageDumper_Admin_Section( $oFactory, $this->sPageSlug );

        do_action( 'aal_action_admin_load_tab_web_page_dumper' );

    }

}