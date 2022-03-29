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
 * Adds an in-page tab to a setting page.
 * 
 * @since       4.2.0
 * @extends     AmazonAutoLinks_AdminPage_Tab_Base
 */
class AmazonAutoLinks_ToolAdminPage_Proxy_Tab extends AmazonAutoLinks_AdminPage_Tab_Base {

    /**
     * @return  array
     * @since   4.2.0
     */
    protected function _getArguments() {
        return array(
            'tab_slug'  => 'proxy',
            'title'     => __( 'HTTP Proxies', 'amazon-auto-links' ),
            'order'     => 30,
            'style'     => AmazonAutoLinks_Proxy_Loader::$sDirPath . '/asset/css/style.css',
        );
    }

    /**
     * Triggered when the tab is loaded.
     */
    public function replyToLoadTab( $oAdminPage ) {

        // Form sections
        new AmazonAutoLinks_ToolAdminPage_Proxy_Tab_Section( $oAdminPage, $this->sPageSlug );

        $this->___loadAjaxScript();

    }

        private function ___loadAjaxScript() {

            $_sScriptHandle = 'aal_proxy_loader';
            $_aScriptData   = array(
                'ajaxURL'               => admin_url( 'admin-ajax.php' ),
                'nonce'                 => wp_create_nonce( 'aal_nonce_ajax_' . $_sScriptHandle ),
                'action_hook_suffix'    => $_sScriptHandle, // WordPress action hook name which follows after `wp_ajax_`
                'spinnerURL'            => admin_url( 'images/loading.gif' ),
            );
            wp_enqueue_script( 'jquery' );
            wp_enqueue_script(
                $_sScriptHandle,    // handle
                $this->getSRCFromPath(
                    $this->isDebugMode()
                        ? AmazonAutoLinks_Proxy_Loader::$sDirPath . '/asset/js/proxy-loader.js'
                        : AmazonAutoLinks_Proxy_Loader::$sDirPath . '/asset/js/proxy-loader.min.js'
                ),
                array( 'jquery' ),
                true
            );
            wp_localize_script(
                $_sScriptHandle,
                'aalProxyLoader',        // variable name on JavaScript side
                $_aScriptData
            );
        }
        
}
