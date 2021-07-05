<?php
/**
 * Amazon Auto Links
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 *
 */

/**
 * Provides locale information for PA-API 5.
 *
 * @since       4.3.4
 */
class AmazonAutoLinks_PAAPI50_Locale {

    /**
     * @var array
     */
    static public $aCaches = array();

    /**
     * @var AmazonAutoLinks_PAAPI50_Locale_Base
     */
    public $oAPILocale;

    /**
     * Sets up properties and hooks.
     * @param string $sLocale
     * @since 4.3.4
     */
    public function __construct( $sLocale ) {
        $this->oAPILocale = $this->___getLocaleObject( $sLocale );
    }
        /**
         * @param string $sLocale
         * @return AmazonAutoLinks_Locale_Base
         */
        private function ___getLocaleObject( $sLocale ) {
            $_sSlug      = strtoupper( $sLocale );
            $_sClassName = "AmazonAutoLinks_PAAPI50_Locale_{$_sSlug}";
            if ( isset( self::$aCaches[ $_sClassName ] ) ) {
                return self::$aCaches[ $_sClassName ];
            }
            $_sClassName = class_exists( $_sClassName )
                ? $_sClassName
                : "AmazonAutoLinks_PAAPI50_Locale_US";  // default
            $_oAPILocale = new $_sClassName;
            self::$aCaches[ $_sClassName ] = $_oAPILocale;
            return $_oAPILocale;
        }

    /**
     * @since 4.3.4
     * @return string
     */
    public function getHost() {
        return $this->oAPILocale->getHost();
    }

    /**
     * @return string
     */
    public function getServerRegion() {
        return $this->oAPILocale->getServerRegion();
    }

    /**
     * @since 4.3.4
     * @return array
     */
    public function getLanguages() {
        return $this->oAPILocale->getLanguages();
    }

    /**
     * @since 4.3.4
     * @return array
     */
    public function getCurrencies() {
        return $this->oAPILocale->getCurrencies();
    }

    /**
     * @return string
     */
    public function getDefaultLanguage() {
        return $this->oAPILocale->getDefaultLanguage();
    }

    /**
     * @return string
     */
    public function getDefaultCurrency() {
        return $this->oAPILocale->getDefaultCurrency();
    }

    /**
     * @return string The host name without http(s):// prefixed.
     * @remark Not a URL but a host.
     */
    public function getMarketPlaceHost() {
        return $this->oAPILocale->getMarketPlaceHost();
    }

    /**
     * @return array
     */
    public function getSearchIndex() {
        return $this->oAPILocale->getSearchIndex();
    }

    /**
     * @return string
     * @since  4.5.0
     */
    public function getLicenseAgreementURL() {
        return $this->oAPILocale->sLicenseURL;
    }

    /**
     * @return string
     * @since  4.5.0
     */
    public function getDisclaimer() {
        return $this->oAPILocale->sDisclaimer;
    }

    /**
     * @param  string $sMethodName
     * @param  array $aArguments
     * @return mixed|void
     */
    public function __call( $sMethodName, array $aArguments ) {
        return call_user_func_array( array( $this->oAPILocale, $sMethodName ), $aArguments );
    }

}