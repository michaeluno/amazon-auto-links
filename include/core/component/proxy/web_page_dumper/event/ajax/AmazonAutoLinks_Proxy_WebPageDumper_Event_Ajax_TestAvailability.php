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
     * @param  array $aPost
     *
     * @return string|boolean
     * @throws Exception        Throws a string value of an error message.
     */
    protected function _getResponse( array $aPost ) {

        $_sURLWebPageDumper = $this->getElement( $aPost, array( 'url' ) );
        if ( ! filter_var( $_sURLWebPageDumper, FILTER_VALIDATE_URL ) ) {
            throw new Exception( 'The passed value is not an URL' );
        }

        $_oOption         = AmazonAutoLinks_Option::getInstance();
        $_oLocale         = new AmazonAutoLinks_Locale( $_oOption->get( array( 'unit_default', 'country' ), 'US' ) );
        $_sURLBestsellers = $_oLocale->getBestSellersURL();
        $_aArguments      = array(
            'timeout' => 30,
        );
        $_oHTTP           = new AmazonAutoLinks_Proxy_WebPageDumper_HTTPClient( $_sURLWebPageDumper, $_sURLBestsellers, 0, $_aArguments, 'web_page_dumper' );
        $_iStatusCode     = $_oHTTP->getStatusCode();
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