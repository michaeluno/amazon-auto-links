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

        // Form Sections
        new AmazonAutoLinks_Proxy_WebPageDumper_Admin_Section( $oFactory, $this->sPageSlug );

        $this->___loadAjaxScript();

    }


        private function ___loadAjaxScript() {

            $_sScriptHandle = 'aal_web_page_dumper_test_availability';
            $_aScriptData   = array(
                'ajaxURL'               => admin_url( 'admin-ajax.php' ),
                'nonce'                 => wp_create_nonce( 'aal_nonce_ajax_' . $_sScriptHandle ),
                'actionHookSuffix'      => $_sScriptHandle, // WordPress action hook name which follows after `wp_ajax_`
                'spinnerURL'            => admin_url( 'images/loading.gif' ),
                'label'                 => array(
                    'enterURL'    => __( 'Please enter a URL.', 'amazon-auto-links' ),
                    'testing'     => __( 'Testing...', 'amazon-auto-links' ),
                    'alradyAdded' => __( 'Already added.', 'amazon-auto-links' ),
                ),
            );
            wp_enqueue_script( 'jquery' );
            wp_enqueue_script(
                $_sScriptHandle,    // handle
                $this->getSRCFromPath(
                    $this->isDebugMode()
                        ? AmazonAutoLinks_Proxy_WebPageDumper_Loader::$sDirPath . '/asset/js/web-page-dumper-tester.js'
                        : AmazonAutoLinks_Proxy_WebPageDumper_Loader::$sDirPath . '/asset/js/web-page-dumper-tester.min.js'
                ),
                array( 'jquery' ),
                true
            );
            wp_localize_script(
                $_sScriptHandle,
                'aalWebPageDumperTester',        // variable name on JavaScript side
                $_aScriptData
            );
        }

}