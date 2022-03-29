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
 */
class AmazonAutoLinks_Proxy_Event_Ajax_LoadProxyList extends AmazonAutoLinks_AjaxEvent_Base {

    protected $_sActionHookName = 'wp_ajax_aal_proxy_loader';

    /**
     * The nonce key passed to the `wp_create_nonce()`
     * @var string
     */
    protected $_sNonceKey = 'aal_nonce_ajax_aal_proxy_loader';

    /**
     * @param  array $aPost Passed POST data.
     * @return array
     * @since  4.6.18
     */
    protected function _getPostSanitized( array $aPost ) {
        return array();
    }

    /**
     * @return string|boolean
     * @throws Exception        Throws a string value of an error message.
     * @param  array $aPost     POST data. Unused at the moment.
     */
    protected function _getResponse( array $aPost ) {

        /**
         * Allows third parties to add own populated proxies
         */
        $_aProxies = apply_filters( 'aal_filter_imported_proxies', array() );
        $_aProxies = array_unique( $_aProxies );
        return trim( implode( PHP_EOL, $_aProxies ) );

    }



}