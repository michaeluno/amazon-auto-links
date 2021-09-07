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
 * Checks the version of Web Page Dumper stored in the `list` tool option.
 * @since        4.7.5
 */
class AmazonAutoLinks_Proxy_WebPageDumper_Event_Ajax_VersionChecks extends AmazonAutoLinks_AjaxEvent_Base {

    /**
     * The action hook name suffix.
     *
     * The action hook names will be:
     *  - for guests:  `wp_ajax_nopriv_{...}`
     *  - for logged-in users: `wp_ajax_{...}`
     *
     * The part `{...}` is where the suffix resides.
     *
     * @var   string
     * @since 4.7.5
     */
    protected $_sActionHookSuffix = 'aal_action_web_page_dumper_check_versions';

    /**
     * Whether to be accessible for non-logged-in users (guests).
     * @var   boolean
     * @since 4.7.5
     */
    protected $_bGuest    = false;

    /**
     * @since 4.7.5
     */
    protected function _construct() {
        add_action( 'aal_action_admin_load_tab_web_page_dumper', array( $this, 'replyToEnqueueScripts' ) );
    }
        /**
         * @since    4.7.5
         * @callback add_action() aal_action_admin_load_tab_web_page_dumper
         */
        public function replyToEnqueueScripts() {
            $_sScriptHandle = 'aal_web_page_dumper_check_versions';
            $_aScriptData   = array(
                'nonce'                 => wp_create_nonce( $this->_sActionHookSuffix ),
                'actionHookSuffix'      => $this->_sActionHookSuffix, // WordPress action hook name which follows after `wp_ajax_`
            ) + $this->getScriptDataBase();
            wp_enqueue_script( 'jquery' );
            wp_enqueue_script(
                $_sScriptHandle,    // handle
                $this->getSRCFromPath(
                    $this->isDebugMode()
                        ? AmazonAutoLinks_Proxy_WebPageDumper_Loader::$sDirPath . '/asset/js/web-page-dumper-check-versions.js'
                        : AmazonAutoLinks_Proxy_WebPageDumper_Loader::$sDirPath . '/asset/js/web-page-dumper-check-versions.min.js'
                ),
                array( 'jquery' ),
                true
            );
            wp_localize_script(
                $_sScriptHandle,
                'aalWebPageDumperVersionChecker',        // variable name on JavaScript side
                $_aScriptData
            );
        }


    /**
     * @param  array $aPost Passed POST data.
     * @return array
     * @since  4.7.5
     */
    protected function _getPostSanitized( array $aPost ) {
        return array(
            'versions' => $this->getArrayMappedRecursive( 'sanitize_text_field', $this->getElementAsArray( $aPost, array( 'versions' ) ) ),
        );
    }

    /**
     * @return string
     * @throws Exception        Throws a string value of an error message.
     * @param  array $aPost     Contains the sanitized `url` element.
     * @since  4.7.5
     */
    protected function _getResponse( array $aPost ) {
        $_sRequired       = AmazonAutoLinks_Proxy_WebPageDumper_Loader::REQUIRED_VERSION;
        $_aVersions       = $this->getElementAsArray( $aPost, array( 'versions' ) );

        $_oToolOption     = AmazonAutoLinks_ToolOption::getInstance();
        $_aSavedVersions  = $_oToolOption->get( 'web_page_dumper', 'versions' );

        $_aVersionsToSave = array();
        $_aInsufficient   = array();
        foreach( $_aVersions as $_sURL => $_sVersion ) {

            $_sVersion = trim( $_sVersion );
            $_aVersionsToSave[ $_sURL ] = $this->___getVersionToSave( $_sVersion, $_sURL, $_aSavedVersions );
            $_sVersion = $this->getElement( $_aVersionsToSave[ $_sURL ], 'version' );

            if ( version_compare( $_sVersion, $_sRequired, '>=' ) ) {
                continue;
            }
            $_aInsufficient[ $_sURL ] = $_sVersion ? $_sVersion : __( 'n/a', 'amazon-auto-links' );

        }

        // Update options
        $_oToolOption->set( array( 'web_page_dumper', 'versions' ), $_aVersionsToSave );
        $_oToolOption->save();

        // Return insufficient items to display
        if ( ! empty( $_aInsufficient ) ) {
            throw new Exception( AmazonAutoLinks_Proxy_WebPageDumper_Utility::getWebPageDumperVersionTable( $_aInsufficient ) );
        }
        return '';
    }


        /**
         * @param  string $sVersion
         * @param  string $sURL
         * @param  array  $aSavedVersions
         * @return array
         * @since  4.7.5
         */
        private function ___getVersionToSave( $sVersion, $sURL, $aSavedVersions ) {
            if ( $sVersion && is_scalar( $sVersion ) ) {
                return array(
                    'checked' => time(),
                    'version' => $sVersion,
                );
            }
            $_aSavedVersion = $this->getElementAsArray( $aSavedVersions, array( $sURL ) );
            return $_aSavedVersion + array(
                'checked' => time(),
                'version' => null,
            );
        }

}