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
 * Provides PA-API 5.0 locale information.
 * 
 * @since       3.9.0
 */
class AmazonAutoLinks_PAAPI50___Locales extends AmazonAutoLinks_PluginUtility {

    /**
     * @var array
     * @see https://webservices.amazon.com/paapi5/documentation/common-request-parameters.html#host-and-region
     * @remark  The CN locale is missing
     */
    public $aRegionNames = array(
        'AU'        => 'us-west-2',  // Australia    webservices.amazon.com.au
        'BR'        => 'us-east-1',  // Brazil    webservices.amazon.com.br
        'CA'        => 'us-east-1',  // Canada    webservices.amazon.ca
        'FR'        => 'eu-west-1',  // France    webservices.amazon.fr
        'DE'        => 'eu-west-1',  // Germany    webservices.amazon.de
        'IN'        => 'eu-west-1',  // India    webservices.amazon.in
        'IT'        => 'eu-west-1',  // Italy    webservices.amazon.it
        'JP'        => 'us-west-2',  // Japan    webservices.amazon.co.jp
        'MX'        => 'us-east-1',  // Mexico    webservices.amazon.com.mx
        'ES'        => 'eu-west-1',  // Spain    webservices.amazon.es
        'TR'        => 'eu-west-1',  // Turkey    webservices.amazon.com.tr
        'AE'        => 'eu-west-1',  // United Arab Emirates    webservices.amazon.ae
        'UK'        => 'eu-west-1',  // United Kingdom    webservices.amazon.co.uk
        'US'        => 'us-east-1',  // United States    webservices.amazon.com
        'CN'        => 'us-west-2',  // Not set by API
    );
    public $aHosts = array(
        'AU'        => 'webservices.amazon.com.au',     // Australia
        'BR'        => 'webservices.amazon.com.br',     // Brazil
        'CA'        => 'webservices.amazon.ca',         // Canada
        'FR'        => 'webservices.amazon.fr',         // France
        'DE'        => 'webservices.amazon.de',         // Germany
        'IN'        => 'webservices.amazon.in',         // India
        'IT'        => 'webservices.amazon.it',         // Italy
        'JP'        => 'webservices.amazon.co.jp',      // Japan
        'MX'        => 'webservices.amazon.com.mx',     // Mexico
        'ES'        => 'webservices.amazon.es',         // Spain
        'TR'        => 'webservices.amazon.com.tr',     // Turkey
        'AE'        => 'webservices.amazon.ae',         // United Arab Emirates
        'UK'        => 'webservices.amazon.co.uk',      // United Kingdom
        'US'        => 'webservices.amazon.com',        // United States
        'CN'        => 'webservices.amazon.cn',         // Not set by API
    );

    public $aMarketPlaces = array(
        'AU'        => 'www.amazon.com.au',
        'BR'        => 'www.amazon.com.br',
        'CA'        => 'www.amazon.ca',
        'FR'        => 'www.amazon.fr',
        'DE'        => 'www.amazon.de',
        'IN'        => 'www.amazon.in',
        'IT'        => 'www.amazon.it',
        'JP'        => 'www.amazon.co.jp',
        'MX'        => 'www.amazon.com.mx',
        'ES'        => 'www.amazon.es',
        'TR'        => 'www.amazon.com.tr',
        'AE'        => 'www.amazon.ae',
        'UK'        => 'www.amazon.co.uk',
        'US'        => 'www.amazon.com',
        'CN'        => 'www.amazon.cn',
    );
    static public $aDefaultLanguages = array(
        'AU'        => 'en_AU', // English - AUSTRALIA
        'BR'        => 'pt_BR', // Portuguese - BRAZIL
        'CA'        => 'en_CA', // English - CANADA
        'FR'        => 'fr_FR', // French - FRANCE
        'DE'        => 'de_DE', // German - GERMANY
        'IN'        => 'en_IN', // English - INDIA
        'IT'        => 'it_IT', // Italian - ITALY
        'JP'        => 'ja_JP', // Japanese - JAPAN
        'MX'        => 'es_MX', // Spanish - MEXICO
        'ES'        => 'es_ES', // Spanish - SPAIN
        'TR'        => 'tr_TR', // Turkish - TURKEY
        'AE'        => 'en_AE', // English - UNITED ARAB EMIRATES
        'UK'        => 'en_GB', // English - UNITED KINGDOM
        'US'        => 'en_US', // English - UNITED STATES
        'CN'        => 'zh_CN', // not set by API
    );
    static public $aDefaultCurrencies = array(
        'AU'        => 'AUD',   // Australian Dollar
        'BR'        => 'BRL',   // Brazilian Real
        'CA'        => 'CAD',   // Canadian Dollar
        'FR'        => 'EUR',   // Euro
        'DE'        => 'EUR',   // Euro
        'IN'        => 'INR',   // Indian Rupee
        'IT'        => 'EUR',   // Euro
        'JP'        => 'JPY',   // Japanese Yen
        'MX'        => 'MXN',   // Mexican Peso
        'ES'        => 'EUR',   // EUR
        'TR'        => 'TRY',   // Turkish Lira
        'AE'        => 'AED',   // Arab Emirates Dirham
        'UK'        => 'GBP',   // British Pound
        'US'        => 'USD',   // United States Dolla
        'CN'        => 'CNY',   // Chinese Yen
    );

    static public function getDefaultLanguageByLocale( $sLocale ) {
        if ( isset( self::$aDefaultLanguages[ $sLocale ] ) ) {
            return self::$aDefaultLanguages[ $sLocale ];
        }
        $_aLanguages = self::getLanguagesByLocale( $sLocale );
        foreach( $_aLanguages as $_sLanguageCode => $_sLabel ) {
            return $_sLanguageCode;
        }
        return '';
    }

    /**
     * Returns an array of the supported languages for the locale.
     *
     * @since   3.9.0   Made it compatible with PA-API 5
     * @see     https://webservices.amazon.com/paapi5/documentation/locale-reference.html
     */
    static public function getLanguagesByLocale( $sLocale ) {
        switch ( strtoupper( $sLocale ) ) {
            case 'AU':
                return array(
                    'en_AU' => __( 'English - AUSTRALIA', 'amazon-auto-links' ),
                );
            case 'BR':
                return array(
                    'pt_BR' => __( 'Portuguese - BRAZIL', 'amazon-auto-links' ),
                );
            case 'CA':
                return array(
                    'en_CA' => __( 'English - CANADA', 'amazon-auto-links' ),
                    // ---
                    'fr_CA' => __( 'French - CANADA', 'amazon-auto-links' ),
                );
            case 'FR':
                return array(
                    'fr_FR' => __( 'French - FRANCE', 'amazon-auto-links' ),
                );
            case 'DE':
                return array(
                    'de_DE' => __( 'German - GERMANY', 'amazon-auto-links' ),
                    // ---
                    'cs_CZ' => __( 'Czech - CZECHIA', 'amazon-auto-links' ),
                    'en_GB' => __( 'English - UNITED KINGDOM', 'amazon-auto-links' ),
                    'nl_NL' => __( 'Dutch - NETHERLANDS', 'amazon-auto-links' ),
                    'pl_PL' => __( 'Polish - POLAND', 'amazon-auto-links' ),
                    'tr_TR' => __( 'Turkish - TURKEY', 'amazon-auto-links' ),
                );
            case 'IN':
                return array(
                    'en_IN' => __( 'English - INDIA', 'amazon-auto-links' ),
                );
            case 'IT':
                return array(
                    'it_IT' => __( 'Italian - ITALY', 'amazon-auto-links' ),
                );
            case 'JP':
                return array(
                    'ja_JP' => __( 'Japanese - JAPAN', 'amazon-auto-links' ),
                    // ----
                    'en_US' => __( 'English - UNITED STATES', 'amazon-auto-links' ),
                    'zh_CN' => __( 'Chinese - CHINA', 'amazon-auto-links' ),
                );
            case 'MX':
                return array(
                    'es_MX' => __( 'Spanish - MEXICO', 'amazon-auto-links' ),
                );
            case 'ES':
                return array(
                    'es_ES' => __( 'Spanish - SPAIN', 'amazon-auto-links' ),
                );
            case 'TR':
                return array(
                    'tr_TR' => __( 'Turkish - TURKEY', 'amazon-auto-links' ),
                );
            case 'AE':
                return array(
                    'en_AE' => __( 'English - UNITED ARAB EMIRATES', 'amazon-auto-links' ),
                    // ---
                    'ar_AE' => __( 'Arabic - UNITED ARAB EMIRATES', 'amazon-auto-links' ),
                );
            case 'UK':
                return array(
                    'en_GB' => __( 'English - UNITED KINGDOM', 'amazon-auto-links' ),
                );
            case 'CN':  // not set by API
                return array(
                    'zh_CN' => __( 'Chinese - CHINA', 'amazon-auto-links' ),
                );
            default:
            case 'US':
                return array(
                    'en_US' => __( 'English - UNITED STATES', 'amazon-auto-links' ),
                    // ---
                    'de_DE' => __( 'German - GERMANY', 'amazon-auto-links' ),
                    'es_US' => __( 'Spanish - UNITED STATES', 'amazon-auto-links' ),
                    'ko_KR' => __( 'Korean - KOREA', 'amazon-auto-links' ),
                    'pt_BR' => __( 'Portuguese - BRAZIL', 'amazon-auto-links' ),
                    'zh_CN' => __( 'Chinese - CHINA', 'amazon-auto-links' ),
                    'zh_TW' => __( 'Chinese - TAIWAN', 'amazon-auto-links' ),
                );
        }
    }


    /**
     * @param $sLocale
     *
     * @return string
     * @since   3.10.0
     */
    static public function getDefaultCurrencyByLocale( $sLocale ) {

        if ( isset( self::$aDefaultCurrencies[ $sLocale ] ) ) {
            return self::$aDefaultCurrencies[ $sLocale ];
        }
        $_aCurrencies = self::getCurrenciesByLocale( $sLocale );
        foreach( $_aCurrencies as $_sCurrency => $_sLabel ) {
            return $_sCurrency;
        }
        return '';

    }

    /**
     * Returns an array of the supported currencies for the locale.
     *
     * @since   3.9.0   Made it compatible with PA-API 5
     * @see     https://webservices.amazon.com/paapi5/documentation/locale-reference.html
     */
    static public function getCurrenciesByLocale( $sLocale ) {
        switch ( strtoupper( $sLocale ) ) {
            case 'AU':
                return array(
                    'AUD' => __( 'Australian Dollar', 'amazon-auto-links' ),
                );
            case 'BR':
                return array(
                    'BRL' => __( 'Brazilian Real', 'amazon-auto-links' ),
                );
            case 'CA':
                return array(
                    'CAD' => __( 'Canadian Dollar', 'amazon-auto-links' ),
                );
            case 'FR':
                return array(
                    'EUR' => __( 'Euro', 'amazon-auto-links' ),
                );
            case 'DE':
                return array(
                    'EUR' => __( 'Euro', 'amazon-auto-links' ),
                );
            case 'IN':
                return array(
                    'INR' => __( 'Indian Rupee', 'amazon-auto-links' ),
                );
            case 'IT':
                return array(
                    'EUR' => __( 'Euro', 'amazon-auto-links' ),
                );
            case 'JP':
                return array(
                    'JPY' => __( 'Japanese Yen', 'amazon-auto-links' ),
                );
            case 'MX':
                return array(
                    'MXN' => __( 'Mexican Peso', 'amazon-auto-links' ),
                );
            case 'ES':
                return array(
                    'EUR' => __( 'Euro', 'amazon-auto-links' ),
                );
            case 'TR':
                return array(
                    'TRY' => __( 'Turkish Lira', 'amazon-auto-links' ),
                );
            case 'AE':
                return array(
                    'AED' => __( 'Arab Emirates Dirham', 'amazon-auto-links' ),
                );
            case 'UK':
                return array(
                    'GBP' => __( 'British Pound', 'amazon-auto-links' ),
                );
            case 'CN':
                return array(
                    'CNY' => __( 'Chinese Yuan Renminbi', 'amazon-auto-links' ),
                );
            default:
            case 'US':
                return array(
                    'USD' => __( 'United States Dollar', 'amazon-auto-links' ), // the default one is at the top
                    'AED' => __( 'United Arab Emirates Dirham', 'amazon-auto-links' ),
                    'AMD' => __( 'Armenian Dram', 'amazon-auto-links' ),
                    'ARS' => __( 'Argentine Peso', 'amazon-auto-links' ),
                    'AUD' => __( 'Australian Dollar', 'amazon-auto-links' ),
                    'AWG' => __( 'Aruban Florin', 'amazon-auto-links' ),
                    'AZN' => __( 'Azerbaijani Manat', 'amazon-auto-links' ),
                    'BGN' => __( 'Bulgarian Lev', 'amazon-auto-links' ),
                    'BND' => __( 'Bruneian Dollar', 'amazon-auto-links' ),
                    'BOB' => __( 'Bolivian Boliviano', 'amazon-auto-links' ),
                    'BRL' => __( 'Brazilian Real', 'amazon-auto-links' ),
                    'BSD' => __( 'Bahamian Dollar', 'amazon-auto-links' ),
                    'BZD' => __( 'Belize Dollar', 'amazon-auto-links' ),
                    'CAD' => __( 'Canadian Dollar', 'amazon-auto-links' ),
                    'CLP' => __( 'Chilean Peso', 'amazon-auto-links' ),
                    'CNY' => __( 'Chinese Yuan Renminbi', 'amazon-auto-links' ),
                    'COP' => __( 'Colombian Peso', 'amazon-auto-links' ),
                    'CRC' => __( 'Costa Rican Colon', 'amazon-auto-links' ),
                    'DOP' => __( 'Dominican Peso', 'amazon-auto-links' ),
                    'EGP' => __( 'Egyptian Pound', 'amazon-auto-links' ),
                    'EUR' => __( 'Euro', 'amazon-auto-links' ),
                    'GBP' => __( 'British Pound', 'amazon-auto-links' ),
                    'GHS' => __( 'Ghanaian Cedi', 'amazon-auto-links' ),
                    'GTQ' => __( 'Guatemalan Quetzal', 'amazon-auto-links' ),
                    'HKD' => __( 'Hong Kong Dollar', 'amazon-auto-links' ),
                    'HNL' => __( 'Honduran Lempira', 'amazon-auto-links' ),
                    'HUF' => __( 'Hungarian Forint', 'amazon-auto-links' ),
                    'IDR' => __( 'Indonesian Rupiah', 'amazon-auto-links' ),
                    'ILS' => __( 'Israeli Shekel', 'amazon-auto-links' ),
                    'INR' => __( 'Indian Rupee', 'amazon-auto-links' ),
                    'JMD' => __( 'Jamaican Dollar', 'amazon-auto-links' ),
                    'JPY' => __( 'Japanese Yen', 'amazon-auto-links' ),
                    'KES' => __( 'Kenyan Shilling', 'amazon-auto-links' ),
                    'KHR' => __( 'Cambodian Riel', 'amazon-auto-links' ),
                    'KRW' => __( 'South Korean Won', 'amazon-auto-links' ),
                    'KYD' => __( 'Caymanian Dollar', 'amazon-auto-links' ),
                    'KZT' => __( 'Kazakhstani Tenge', 'amazon-auto-links' ),
                    'LBP' => __( 'Lebanese Pound', 'amazon-auto-links' ),
                    'MAD' => __( 'Moroccan Dirham', 'amazon-auto-links' ),
                    'MNT' => __( 'Mongolian Tughrik', 'amazon-auto-links' ),
                    'MOP' => __( 'Macanese Pataca', 'amazon-auto-links' ),
                    'MUR' => __( 'Mauritian Rupee', 'amazon-auto-links' ),
                    'MXN' => __( 'Mexican Peso', 'amazon-auto-links' ),
                    'MYR' => __( 'Malaysian Ringgit', 'amazon-auto-links' ),
                    'NAD' => __( 'Namibian Dollar', 'amazon-auto-links' ),
                    'NGN' => __( 'Nigerian Naira', 'amazon-auto-links' ),
                    'NOK' => __( 'Norwegian Krone', 'amazon-auto-links' ),
                    'NZD' => __( 'New Zealand Dollar', 'amazon-auto-links' ),
                    'PAB' => __( 'Panamanian Balboa', 'amazon-auto-links' ),
                    'PEN' => __( 'Peruvian Sol', 'amazon-auto-links' ),
                    'PHP' => __( 'Philippine Peso', 'amazon-auto-links' ),
                    'PYG' => __( 'Paraguayan Guaraní', 'amazon-auto-links' ),
                    'QAR' => __( 'Qatari Riyal', 'amazon-auto-links' ),
                    'RUB' => __( 'Russian Ruble', 'amazon-auto-links' ),
                    'SAR' => __( 'Saudi Arabian Riyal', 'amazon-auto-links' ),
                    'SGD' => __( 'Singapore Dollar', 'amazon-auto-links' ),
                    'THB' => __( 'Thai Baht', 'amazon-auto-links' ),
                    'TRY' => __( 'Turkish Lira', 'amazon-auto-links' ),
                    'TTD' => __( 'Trinidadian Dollar', 'amazon-auto-links' ),
                    'TWD' => __( 'Taiwan New Dollar', 'amazon-auto-links' ),
                    'TZS' => __( 'Tanzanian Shilling', 'amazon-auto-links' ),
                    'UYU' => __( 'Uruguayan Peso', 'amazon-auto-links' ),
                    'VND' => __( 'Vietnamese Dong', 'amazon-auto-links' ),
                    'XCD' => __( 'Eastern Caribbean Dollar', 'amazon-auto-links' ),
                    'ZAR' => __( 'South African Rand', 'amazon-auto-links' ),
                );
        }
    }

    /**
     * @return array
     * @since   3.9.1
     */
    static public function getHostLabels() {
        $_oLocales = new AmazonAutoLinks_PAAPI50___Locales;
        $_aLabels  = array();
        foreach( $_oLocales->aHosts as $_sKey => $_sHost ) {
            $_aLabels[ $_sKey ] = $_sKey . ' - ' . $_sHost;
        }
        return $_aLabels;
    }

//    static public function getMarketPlaceLabels() {
//        $_oLocales = new AmazonAutoLinks_PAAPI50___Locales;
//        $_aLabels  = array();
//        foreach( $_oLocales->aMarketPlaces as $_sKey => $_sHost ) {
//            $_aLabels[ $_sKey ] = $_sKey . ' - ' . $_sHost;
//        }
//        return $_aLabels;
//    }

    /**
     * @param $sLocale
     *
     * @return string   THe market-place URL.
     * @since   3.9.1
     */
    static public function getMarketPlaceByLocale( $sLocale ) {
        $_oLocales = new AmazonAutoLinks_PAAPI50___Locales;
        $_sLocale  = strtoupper( $sLocale );
        $_sScheme  = 'https://';
        return isset( $_oLocales->aMarketPlaces[ $_sLocale ] )
            ? $_sScheme . $_oLocales->aMarketPlaces[ $_sLocale ]
            : $_sScheme . $_oLocales->aMarketPlaces[ 'US' ];    // default
    }

    /**
     * @return array
     * @since   3.9.1
     */
    static public function getCountryLabels() {
        return array(
            'CA' => 'CA - ' . __( 'Canada', 'amazon-auto-links' ),
            'CN' => 'CN - ' . __( 'China', 'amazon-auto-links' ),
            'FR' => 'FR - ' . __( 'France', 'amazon-auto-links' ),
            'DE' => 'DE - ' . __( 'Germany', 'amazon-auto-links' ),
            'IT' => 'IT - ' . __( 'Italy', 'amazon-auto-links' ),
            'JP' => 'JP - ' . __( 'Japan', 'amazon-auto-links' ),
            'UK' => 'UK - ' . __( 'United Kingdom', 'amazon-auto-links' ),
            'ES' => 'ES - ' . __( 'Spain', 'amazon-auto-links' ),
            'US' => 'US - ' . __( 'United States', 'amazon-auto-links' ),
            'IN' => 'IN - ' . __( 'India', 'amazon-auto-links' ),
            'BR' => 'BR - ' . __( 'Brazil', 'amazon-auto-links' ),
            'MX' => 'MX - ' . __( 'Mexico', 'amazon-auto-links' ),
            'AU' => 'AU - ' . __( 'Australia', 'amazon-auto-links' ), // 3.5.5+
            'TR' => 'TR - ' . __( 'Turkey', 'amazon-auto-links' ), // 3.9.1
            'AE' => 'AE - ' . __( 'United Arab Emirates', 'amazon-auto-links' ), // 3.9.1
        );
    }

}