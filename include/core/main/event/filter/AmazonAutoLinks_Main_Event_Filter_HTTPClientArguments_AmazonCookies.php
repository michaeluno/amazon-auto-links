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
 * Automatically sets cookies if the request URL is of Amazon.
 * @since 4.3.4
 */
class AmazonAutoLinks_Main_Event_Filter_HTTPClientArguments_AmazonCookies extends AmazonAutoLinks_PluginUtility {

    /**
     * @var string
     * @see AmazonAutoLinks_Locale_AmazonCookies
     */
    private $___sCookieRequestType = 'amazon_cookie';

    /**
     * Sets up hooks.
     */
    public function __construct() {
        add_filter( 'aal_filter_http_request_arguments', array( $this, 'replyToFilterArguments' ), 10, 3 );
        add_filter( 'aal_filter_http_request_response', array( $this, 'replyToSaveCookies' ), 10, 2 ); // maximum of 5 parameters
    }

    /**
     * @param  array|WP_Error $aoResponse
     * @param  string         $sURL
     * @return array|WP_Error
     * @since  4.3.4
     */
    public function replyToSaveCookies( $aoResponse, $sURL ) {

        if ( ! $this->isAmazonURL( $sURL ) ) {
            return $aoResponse;
        }
        $_aResponseCookies = $this->getRequestCookiesFromResponse( $aoResponse );
        if ( empty( $_aResponseCookies ) ) {
            return $aoResponse;
        }

        $_oOption           = AmazonAutoLinks_Option::getInstance();
        $_sDefaultLocale    = ( string ) $_oOption->get( array( 'unit_default', 'country' ), 'US' );
        $_sLocale           = AmazonAutoLinks_Locales::getLocaleFromURL( $sURL, $_sDefaultLocale );
        $_oVersatileCookies = new AmazonAutoLinks_VersatileFileManager_AmazonCookies( $_sLocale );
        $_oVersatileCookies->setCache( $_aResponseCookies );
        return $aoResponse;

    }

    /**
     * @param  array $aArguments
     * @param  string $sRequestType
     * @param  array|string $asURLs
     * @return array
     * @since  4.3.4
     */
    public function replyToFilterArguments( $aArguments, $sRequestType, $asURLs ) {

        // Do nothing for API requests.
        $_aExceptedTypes   = $this->getAsArray( apply_filters( 'aal_filter_excepted_http_request_types_for_requests', array() ) );
        $_aExceptedTypes[] = $this->___sCookieRequestType;
        if ( in_array( $sRequestType, $_aExceptedTypes, true ) ) {
            return $aArguments;
        }

        $_aURLs          = $this->getAsArray( $asURLs );
        $_aLocales       = $this->___getLocalesFromURLs( $_aURLs );
        if ( empty( $_aLocales ) ) {
            return $aArguments;
        }

        $_aPassedCookies = $this->getElementAsArray( $aArguments, 'cookies' );
        $_sLanguage      = $this->getElement( $aArguments, 'amazon_language' );
        $_aSavedCookies  = $this->___getSavedCookies( $_aLocales, $_sLanguage );
        $aArguments[ 'cookies' ] = $this->getCookiesMerged( $_aSavedCookies, $_aPassedCookies );
        return $aArguments;

    }
        /**
         * @param  array $aURLs
         * @return array
         * @since  4.3.4
         */
        private function ___getLocalesFromURLs( array $aURLs ) {
            $_oOption        = AmazonAutoLinks_Option::getInstance();
            $_sDefaultLocale = ( string ) $_oOption->get( array( 'unit_default', 'country' ), 'US' );
            $_aLocales       = array();
            foreach( $aURLs as $_sURL ) {
                if ( ! $this->isAmazonURL( $_sURL ) ) {
                    continue;
                }
                $_sLocale = AmazonAutoLinks_Locales::getLocaleFromURL( $_sURL, $_sDefaultLocale );
                if ( in_array( $_sLocale, $_aLocales, true ) ) {
                    continue;
                }
                $_aLocales[] = $_sLocale;
            }
            return $_aLocales;
        }
        /**
         * @param  array  $aLocales
         * @param  string $sLanguage
         * @return array
         * @since  4.3.4
         */
        private function ___getSavedCookies( array $aLocales, $sLanguage ) {
            $_aCookies = array();
            foreach( $aLocales as $_sLocale ) {
                $_oVersatileCookies = new AmazonAutoLinks_VersatileFileManager_AmazonCookies( $_sLocale );
                $_aSavedCookies     = $_oVersatileCookies->get();
                if ( empty( $_aSavedCookies ) ) {
                    $_oLocale       = new AmazonAutoLinks_Locale( $_sLocale );
                    $_aSavedCookies = $_oLocale->getHTTPRequestCookies( $sLanguage );
                }
                $_aCookies          = array_merge( $_aSavedCookies, $_aCookies );
            }
            return $_aCookies;
        }

}