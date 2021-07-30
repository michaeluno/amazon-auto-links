<?php
/**
 * Amazon Auto Links
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 *
 */

/**
 * Provides locale information.
 *
 * @since       4.3.4
 */
class AmazonAutoLinks_Locale {

    /**
     * @var   array
     * @since 4.3.4
     */
    static public $aCaches = array();

    /**
     * @var   AmazonAutoLinks_Locale_Base
     * @since 4.3.4
     */
    public $oLocale;

    /**
     * Sets up properties and hooks.
     * @param string $sLocale
     * @since 4.3.4
     */
    public function __construct( $sLocale ) {
        $this->oLocale = $this->___getLocaleObject( $sLocale );
    }
        /**
         * @param string $sLocale
         * @return AmazonAutoLinks_Locale_Base
         * @since  4.3.4
         */
        private function ___getLocaleObject( $sLocale ) {
            $_sSlug      = strtoupper( $sLocale );
            $_sClassName = "AmazonAutoLinks_Locale_{$_sSlug}";
            if ( isset( self::$aCaches[ $_sClassName ] ) ) {
                return self::$aCaches[ $_sClassName ];
            }
            $_sClassName = class_exists( $_sClassName )
                ? $_sClassName
                : "AmazonAutoLinks_Locale_US";  // default
            $_oLocale = new $_sClassName;
            self::$aCaches[ $_sClassName ] = $_oLocale;
            return $_oLocale;
        }

    /**
     * @return AmazonAutoLinks_Locale_Base
     * @since  4.3.4
     */
    public function get() {
        return $this->oLocale;
    }

    /**
     * @return string The country name.
     * @since  4.5.0
     */
    public function getName() {
        return $this->oLocale->getName();
    }

    /**
     * @return string
     * @since  4.3.4
     */
    public function getDomain() {
        return $this->oLocale->getDomain();
    }

    /**
     * @return string
     * @since  4.3.4
     */
    public function getFlagImg() {
        return $this->oLocale->getFlagImg();
    }

    /**
     * @return string
     * @since  4.3.4
     */
    public function getNoImageURL() {
        return $this->oLocale->getNoImageURL();
    }

    /**
     * @return string
     * @since  4.3.4
     */
    public function getLocaleNumber() {
        return $this->oLocale->getLocaleNumber();
    }

    /**
     * @return string
     * @since  4.3.4
     */
    public function getAddToCartURL() {
        return $this->oLocale->getAddToCartURL();
    }

    /**
     * @return string
     * @since  4.3.4
     */
    public function getBlackCurtainURL() {
        return $this->oLocale->getBlackCurtainURL();
    }

    /**
     * @param  string $sPath
     * @return string
     * @since  4.3.4
     * @since  4.3.5  Added the `$sPath` parameter.
     */
    public function getAssociatesURL( $sPath='' ) {
        return $this->oLocale->getAssociatesURL( $sPath );
    }

    /**
     * @param  string $sPath
     * @return string
     * @since  4.3.4
     * @since  4.3.5  Added the `$sPath` parameter.
     */
    public function getBestSellersURL( $sPath='' ) {
        return $this->oLocale->getBestSellersURL( $sPath );
    }

    /**
     * @param  string $sPath
     * @return string
     * @since  4.3.4
     */
    public function getMarketPlaceURL( $sPath='' ) {
        return $this->oLocale->getMarketPlaceURL( $sPath );
    }

    /**
     * @param  string $sASIN
     * @param  string $sAssociateID
     * @param  string $sLanguage    A preferred language code.
     * @return string
     * @since  4.3.4
     */
    public function getCustomerReviewURL( $sASIN, $sAssociateID='', $sLanguage='' ) {
        return $this->oLocale->getCustomerReviewURL( $sASIN, $sAssociateID, $sLanguage );
    }

    /**
     * @param  string   $sASIN
     * @return string
     * @since  4.3.4
     */
    public function getProductRatingWidgetURL( $sASIN ) {
        return $this->oLocale->getProductRatingWidgetURL( $sASIN );
    }

    /**
     * @param  array $aPayload
     * @return string
     * @since  4.6.9
     */
    public function getAdWidgetAPIEndpoint( array $aPayload ) {
        return $this->oLocale->getAdWidgetAPIEndpoint( $aPayload );
    }

    /**
     * @param  string  $sLanguage The preferred language.
     * @param  boolean $bRenewCookies Whether to renew cookies.
     * @return array   An array for the `cookies` argument of `wp_remote_request()`.
     * @since  4.3.4
     * @since  4.5.0   Added the `$bRenewCookies` parameter.
     */
    public function getHTTPRequestCookies( $sLanguage='', $bRenewCookies=false ) {
        return $this->oLocale->getHTTPRequestCookies( $sLanguage, $bRenewCookies );
    }

    /**
     * @param  string
     * @return string
     * @since  4.3.4
     */
    public function getImpressionCounterScriptTag( $sAssociatesTag ) {
        return $this->oLocale->getImpressionCounterScriptTag( $sAssociatesTag );
    }

    /**
     * @param  string $sMethodName
     * @param  array  $aArguments
     * @return mixed|void
     * @since  4.3.4
     */
    public function __call( $sMethodName, array $aArguments ) {
        return call_user_func_array( array( $this->oLocale, $sMethodName ), $aArguments );
    }

}