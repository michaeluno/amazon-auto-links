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
 * Checks the version of Web Page Dumper stored in the `list` tool option.
 * @since 4.7.5
 */
class AmazonAutoLinks_Proxy_WebPageDumper_Event_Ajax_VersionChecksAdminNotices extends AmazonAutoLinks_Proxy_WebPageDumper_Event_Ajax_VersionChecks {

    /**
     * @var   string
     * @since 4.7.5
     */
    protected $_sActionHookSuffix = 'aal_action_web_page_dumper_check_versions_admin_notice';

    /**
     * @since 4.7.5
     */
    protected function _construct() {
        add_action( 'load_' . 'AmazonAutoLinks_AdminPage', array( $this, 'replyToLoadPluginAdminPages' ) );
        add_action( 'load_' . 'AmazonAutoLinks_ToolAdminPage', array( $this, 'replyToLoadPluginAdminPages' ) );
        add_action( 'load_' . 'AmazonAutoLinks_PostType_Unit', array( $this, 'replyToLoadPluginAdminPages' ) );
        add_action( 'load_' . 'AmazonAutoLinks_PostType_AutoInsert', array( $this, 'replyToLoadPluginAdminPages' ) );
        add_action( 'load_' . 'AmazonAutoLinks_PostType_Button', array( $this, 'replyToLoadPluginAdminPages' ) );
    }
        public function replyToLoadPluginAdminPages( $oFactory ) {
            /**
             * In the HTTP Proxies tab, AmazonAutoLinks_Proxy_WebPageDumper_Event_Ajax_VersionChecks takes care of it.
             * @see AmazonAutoLinks_Proxy_WebPageDumper_Event_Ajax_VersionChecks
             */
            if ( method_exists( $oFactory->oProp, 'getCurrentTabSlug' ) && 'proxy' === $oFactory->oProp->getCurrentTabSlug() ) {
                return;
            }

            $_sRequired   = AmazonAutoLinks_Proxy_WebPageDumper_Loader::REQUIRED_VERSION;
            $_oToolOption = AmazonAutoLinks_ToolOption::getInstance();
            $_aList       = explode( PHP_EOL, $_oToolOption->get( array( 'web_page_dumper', 'list' ), '' ) );
            $_aVersions   = $this->getAsArray( $_oToolOption->get( 'web_page_dumper', 'versions' ) );
            $_aToNotify   = array();
            $_aToCheck    = array();
            $_iNow        = time();
            foreach( $_aList as $_sURL ) {
                $_aVersion     = $this->getElementAsArray( $_aVersions, array( $_sURL ) );
                $_sVersion     = $this->getElement( $_aVersion, 'version' );
                $_iLastChecked = ( integer ) $this->getElement( $_aVersion, 'checked' );
                if ( $_iLastChecked && version_compare( $_sRequired, $_sVersion, '>' ) ) {
                    $_aToNotify[ $_sURL ] = $_sVersion;
                }
                if ( $_iNow < $_iLastChecked + 86400 ) {
                    continue;
                }
                $_aToCheck[] = $_sURL;
            }
            if ( ! empty( $_aToNotify ) ) {
                $this->___setAdminNotice( $_aToNotify );
            }
            if ( empty( $_aToCheck ) ) {
                return;
            }
            $this->___enqueueScripts( array(
                'nonce'                 => wp_create_nonce( $this->_sActionHookSuffix ),
                'actionHookSuffix'      => $this->_sActionHookSuffix, // WordPress action hook name which follows after `wp_ajax_`
                'urls'                  => $_aToCheck,
            ) + $this->getScriptDataBase() );
        }

            /**
             * @param array $aNotify
             * @since 4.7.5
             */
            private function ___setAdminNotice( array $aNotify ) {
                $_sMessage = "<span>"
                          /* translators: 1: A proper noun (Web Page Dumper) */
                        . sprintf( __( 'The following %1$s instances are outdated and will not run properly. Please update them.', 'amazon-auto-links' ), 'Web Page Dumper' )
                        . ' ' . sprintf(
                            __( 'See <a href="%1$s">how</a>.', 'amazon-auto-links' ),
                            add_query_arg(
                                array(
                                   'page' => AmazonAutoLinks_Registry::$aAdminPages[ 'tool' ],
                                   'tab'  => 'web_page_dumper_help'
                                ) + $this->getHTTPQueryGET(),
                                admin_url( 'edit.php' )
                            ) . '#updating-web-page-dumper'
                        )
                        . ' ' . sprintf(
                            __( 'Required version: <code>%1$s</code> or above.', 'amazon-auto-links' ),
                            AmazonAutoLinks_Proxy_WebPageDumper_Loader::REQUIRED_VERSION
                        )
                    . "</span>";
                $_sList = '';
                foreach( $aNotify as $_sURL => $_sVersion ) {
                    $_sList .= "<li>"
                            . $_sURL . ' ' . __( 'Version', 'amazon-auto-links' ) . ': ' .  $_sVersion
                        . "</li>";
                }
                $_sList = "<ul>{$_sList}</ul>";
                AmazonAutoLinks_Registry::setAdminNotice( $_sMessage, 'error', 'warning', $_sList );
            }

            /**
             * @param  array $aScriptData
             * @since  4.7.5
             */
            private function ___enqueueScripts( array $aScriptData ) {
                $_sScriptHandle = 'aal_web_page_dumper_check_versions_admin_notice';
                wp_enqueue_script(
                    $_sScriptHandle,    // handle
                    $this->getSRCFromPath(
                        $this->isDebugMode()
                            ? AmazonAutoLinks_Proxy_WebPageDumper_Loader::$sDirPath . '/asset/js/web-page-dumper-check-versions-notification.js'
                            : AmazonAutoLinks_Proxy_WebPageDumper_Loader::$sDirPath . '/asset/js/web-page-dumper-check-versions-notification.min.js'
                    ),
                    array( 'jquery' ),
                    true
                );
                wp_localize_script( $_sScriptHandle, 'aalWPDVersionCheckNotice', $aScriptData );
            }

}