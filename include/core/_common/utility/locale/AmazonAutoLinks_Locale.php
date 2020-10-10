<?php
/**
 * Amazon Auto Links
 *
 * http://en.michaeluno.jp/amazn-auto-links/
 * Copyright (c) 2013-2020 Michael Uno
 *
 */

/**
 * Provides locale information.
 *
 * @since       4.3.4
 */
class AmazonAutoLinks_Locale {

    /**
     * @var array
     */
    static public $aCaches = array();

    /**
     * @var AmazonAutoLinks_Locale_Base
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
     * @return string
     */
    public function getDomain() {
        return $this->oLocale->getDomain();
    }

    /**
     * @return string
     */
    public function getFlagImg() {
        return $this->oLocale->getFlagImg();
    }

    /**
     * @return string
     */
    public function getNoImageURL() {
        return $this->oLocale->getNoImageURL();
    }

    /**
     * @return string
     */
    public function getLocaleNumber() {
        return $this->oLocale->getLocaleNumber();
    }

    /**
     * @return string
     */
    public function getAddToCartURL() {
        return $this->oLocale->getAddToCartURL();
    }

    /**
     * @return string
     */
    public function getBlackCurtainURL() {
        return $this->oLocale->getBlackCurtainURL();
    }

    /**
     * @return string
     */
    public function getAssociatesURL() {
        return $this->oLocale->getAssociatesURL();
    }

    /**
     * @return string
     */
    public function getBestSellersURL() {
        return $this->oLocale->getBestSellersURL();
    }

    /**
     * @param  string $sPath
     * @return string
     */
    public function getMarketPlaceURL( $sPath='' ) {
        return $this->oLocale->getMarketPlaceURL( $sPath );
    }

    /**
     * @param  string   $sASIN
     * @return string
     */
    public function getProductRatingWidgetURL( $sASIN ) {
        return $this->oLocale->getProductRatingWidgetURL( $sASIN );
    }

    /**
     * @param  string
     * @return string
     */
    public function getImpressionCounterScriptTag( $sAssociatesTag ) {
        return $this->oLocale->getImpressionCounterScriptTag( $sAssociatesTag );
    }

    /**
     * @param  string $sMethodName
     * @param  array $aArguments
     * @return mixed|void
     */
    public function __call( $sMethodName, array $aArguments ) {
        return call_user_func_array( array( $this->oLocale, $sMethodName ), $aArguments );
    }

}