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
 * Retrieves Amazon site cookies.
 *
 * @since        4.5.0
 */
class AmazonAutoLinks_Proxy_WebPageDumper_Event_Filter_AmazonCookies extends AmazonAutoLinks_Proxy_WebPageDumper_Utility {

    /**
     * Sets up hooks.
     */
    public function __construct() {

        add_filter( 'aal_filter_custom_amazon_cookies', array( $this, 'replyToGetAmazonCookies' ), 10, 3 );

    }

    /**
     * @param  array $aCookies
     * @param  AmazonAutoLinks_Locale_Base $oLocale
     * @param  string $sLanguage
     * @return array
     * @since  4.5.0
     */
    public function replyToGetAmazonCookies( $aCookies, $oLocale, $sLanguage ) {

        $_sURLWebPageDumper = $this->getWebPageDumperURL();
        if ( ! $_sURLWebPageDumper ) {
            return $aCookies;
        }

        $_aArguments        = array(
            'renew_cache' => true,
            'timeout' => 30, // seconds. Set a longer one as Web Page Dumper servers are often sleeping.
        );
        $_oCookieGetter     = new AmazonAutoLinks_Locale_AmazonCookies( $oLocale, $sLanguage );
        $_oHTTP             = new AmazonAutoLinks_Proxy_WebPageDumper_HTTPClient( $_sURLWebPageDumper, $oLocale->getBestSellersURL(), 86400, $_aArguments, $_oCookieGetter->sRequestType );
        return $this->getCookiesMerged( $_oHTTP->getCookies(), $aCookies );

    }

}