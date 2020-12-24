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
 * Handles renewing Amazon site cookies.
 *
 * @since        4.5.0
 */
class AmazonAutoLinks_Main_Event_Action_AmazonCookiesRenewal extends AmazonAutoLinks_PluginUtility {

    /**
     * Sets up hooks.
     */
    public function __construct() {
        add_action( 'aal_action_renew_amazon_cookies', array( $this, 'replyToRenewAmazonCookies' ), 10, 2 );
    }

    /**
     * @param  string $sLocale
     * @param  string $sLanguage
     * @since  4.0.0
     */
    public function replyToRenewAmazonCookies( $sLocale, $sLanguage ) {
        $_oLocaleSelector = new AmazonAutoLinks_Locale( $sLocale );
        $_oLocale         = $_oLocaleSelector->get();
        $_oCookieGetter   = new AmazonAutoLinks_Locale_AmazonCookies( $_oLocale, $sLanguage );
        $_oCookieGetter->get();
    }

}