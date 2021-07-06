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
 * Provides PA-API 5.0 locale information.
 * 
 * @since       3.9.0
 */
class AmazonAutoLinks_PAAPI50___Locales extends AmazonAutoLinks_PluginUtility {

    /**
     * Returns supported locale codes.
     * @remark No `CN`.
     * @return array
     * @since  4.3.4
     */
    static public function getLocales() {
        return array(
            'CA', 'FR', 'DE', 'IT', 'JP', 'UK',
            'ES', 'US', 'IN', 'BR', 'MX', 'AU', 'TR',
            'AE', 'SG', 'NL', 'SA', 'SE', 'PL',
        );
    }

    /**
     * @param  string $sLocale The locale slug.
     * @return boolean
     * @since  4.5.0
     */
    static public function exists( $sLocale ) {
        return in_array( strtoupper( $sLocale ), self::getLocales(), true );
    }

    static private $___aLocaleObjects;
    /**
     * Returns supported locale objects.
     * @return AmazonAutoLinks_PAAPI50_Locale_Base[]
     * @since 4.3.4
     */
    static public function getLocaleObjects() {
        if ( isset( self::$___aLocaleObjects ) ) {
            return self::$___aLocaleObjects;
        }
        $_aLocales = array();
        foreach( self::getLocales() as $_sLocale ) {
            $_sClassName = "AmazonAutoLinks_PAAPI50_Locale_{$_sLocale}";
            $_aLocales[ $_sLocale ] = new $_sClassName;
        }
        self::$___aLocaleObjects = $_aLocales;
        return $_aLocales;
    }

    /**
     * @param string $sLocale
     * @return string
     */
    static public function getDefaultLanguageByLocale( $sLocale ) {
        $_oLocale = new AmazonAutoLinks_PAAPI50_Locale( $sLocale );
        return $_oLocale->getDefaultLanguage();
    }

    /**
     * Returns an array of the supported languages for the locale.
     *
     * @since   3.9.0   Made it compatible with PA-API 5
     * @see     https://webservices.amazon.com/paapi5/documentation/locale-reference.html
     * @param   string $sLocale
     * @return  array
     */
    static public function getLanguagesByLocale( $sLocale ) {
        $_oLocale = new AmazonAutoLinks_PAAPI50_Locale( $sLocale );
        return $_oLocale->getLanguages();
    }


    /**
     * @param $sLocale
     * @return string
     * @since 3.10.0
     */
    static public function getDefaultCurrencyByLocale( $sLocale ) {
        $_oLocale = new AmazonAutoLinks_PAAPI50_Locale( $sLocale );
        return $_oLocale->getDefaultCurrency();
    }

    /**
     * Returns an array of the supported currencies for the locale.
     *
     * @since   3.9.0   Made it compatible with PA-API 5
     * @see     https://webservices.amazon.com/paapi5/documentation/locale-reference.html
     * @param   string  $sLocale
     * @return  array
     */
    static public function getCurrenciesByLocale( $sLocale ) {
        $_oLocale = new AmazonAutoLinks_PAAPI50_Locale( $sLocale );
        return $_oLocale->getCurrencies();
    }

    /**
     * @return array
     * @since   3.9.1
     */
    static public function getHostLabels() {
        $_aLabels  = array();
        foreach( self::getLocaleObjects() as $_sLocale => $_oPAAPILocale ) {
            $_aLabels[ $_sLocale ] = $_oPAAPILocale->getHostLabel();
        }
        return $_aLabels;
    }

}