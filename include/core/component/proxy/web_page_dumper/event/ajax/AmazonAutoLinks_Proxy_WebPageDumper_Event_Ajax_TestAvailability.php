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
 * Checks the availability of a given URL of Web Page Dumper.
 *
 * @package      Amazon Auto Links
 * @since        4.5.0
 */
class AmazonAutoLinks_Proxy_WebPageDumper_Event_Ajax_TestAvailability extends AmazonAutoLinks_AjaxEvent_Base {

    protected $_sActionHookName = 'wp_ajax_aal_web_page_dumper_test_availability';

    /**
     * The nonce key passed to the `wp_create_nonce()`
     * @var string
     */
    protected $_sNonceKey = 'aal_nonce_ajax_aal_web_page_dumper_test_availability';

    /**
     * Whether to be accessible for non-logged-in users (guests).
     * @var boolean
     */
    protected $_bGuest    = false;

    protected function _construct() {
        add_action( 'aal_action_admin_load_tab_web_page_dumper', array( $this, 'replyToEnqueueScripts' ) );
    }
        /**
         * @since    4.5.0
         * @since    4.7.5        Moved from `AmazonAutoLinks_Proxy_WebPageDumper_Admin`.
         * @callback add_action() aal_action_admin_load_tab_web_page_dumper
         */
        public function replyToEnqueueScripts() {
            $_sScriptHandle = 'aal_web_page_dumper_test_availability';
            $_aScriptData   = array(
                'nonce'                 => wp_create_nonce( 'aal_nonce_ajax_' . $_sScriptHandle ),
                'actionHookSuffix'      => $_sScriptHandle, // WordPress action hook name which follows after `wp_ajax_`
                'label'                 => array(
                    'enterURL'    => __( 'Please enter a URL.', 'amazon-auto-links' ),
                    'testing'     => __( 'Testing...', 'amazon-auto-links' ),
                    'alradyAdded' => __( 'Already added.', 'amazon-auto-links' ),
                ),
            ) + $this->getScriptDataBase();
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

    /**
     * @param  array $aPost Passed POST data.
     * @return array
     * @since  4.6.18
     */
    protected function _getPostSanitized( array $aPost ) {
        return array(
            'url' => $this->getURLSanitized( $this->getElement( $aPost, array( 'url' ) ) ),
        );
    }

    /**
     * @return string|boolean
     * @throws Exception        Throws a string value of an error message.
     * @param  array $aPost     Contains the sanitized `url` element.
     */
    protected function _getResponse( array $aPost ) {

        $_sURLWebPageDumper = $this->getElement( $aPost, array( 'url' ) );
        if ( ! filter_var( $_sURLWebPageDumper, FILTER_VALIDATE_URL ) ) {
            throw new Exception( 'The passed value is not an URL' );
        }

        $_aArguments       = array(
            'timeout' => 30,
        );

        $_oHTTP            = new AmazonAutoLinks_HTTPClient( untrailingslashit( $_sURLWebPageDumper ) . '/version', 0, $_aArguments );
        $_sVersion         = trim( $_oHTTP->getBody() );
        $_sRequiredVersion = AmazonAutoLinks_Proxy_WebPageDumper_Loader::REQUIRED_VERSION;
        if ( version_compare( $_sVersion, $_sRequiredVersion, '<' ) ) {
            throw new Exception(
                sprintf(
                    __( 'Please update the Web Page Dumper application. Your version: <code>%1$s</code>. Expected version: <code>%2$s</code> or above.', 'amazon-auto-links' ),
                    $_sVersion,
                    $_sRequiredVersion
                )
            );
        }

        $_oOption          = AmazonAutoLinks_Option::getInstance();
        $_oLocale          = new AmazonAutoLinks_Locale( $_oOption->get( array( 'unit_default', 'country' ), 'US' ) );
        $_sURLBestsellers  = $_oLocale->getBestSellersURL();
        $_oHTTP            = new AmazonAutoLinks_Proxy_WebPageDumper_HTTPClient( $_sURLWebPageDumper, $_sURLBestsellers, 0, $_aArguments, 'web_page_dumper' );
        $_iStatusCode      = $_oHTTP->getStatusCode();
        if ( ! $this->hasPrefix( '2', $_iStatusCode ) ) {
            $_sMessage = $_iStatusCode
                ? $_iStatusCode . ': ' . $_oHTTP->getStatusMessage() . ' ' . $_sURLBestsellers
                : $_oHTTP->getStatusMessage();
            throw new Exception( $_sMessage, $_iStatusCode );
        }
        if ( $this->isBlockedByAmazonCaptcha( $_oHTTP->getBody(), $_sURLBestsellers ) ) {
            throw new Exception( __( 'Blocked by Captcha.', 'amazon-auto-links' ) );
        }
        return __( 'OK', 'amazon-auto-links' );

    }

}