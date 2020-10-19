<?php
/**
 * Amazon Auto Links
 *
 * http://en.michaeluno.jp/amazn-auto-links/
 * Copyright (c) 2013-2020 Michael Uno
 *
 */

/**
 * Provides locales information.
 *
 * @since       4.3.4
 */
class AmazonAutoLinks_Locales {

    /**
     * Returns supported locale codes.
     * @return array
     */
    static public function getLocales() {
        return array(
            'CA', 'CN', 'FR', 'DE', 'IT', 'JP', 'UK',
            'ES', 'US', 'IN', 'BR', 'MX', 'AU', 'TR',
            'AE', 'SG', 'NL', 'SA',
        );
    }

    static private $___aLocaleObjects;
    /**
     * Returns supported locale objects.
     * @return AmazonAutoLinks_Locale_Base[]
     */
    static public function getLocaleObjects() {
        if ( isset( self::$___aLocaleObjects ) ) {
            return self::$___aLocaleObjects;
        }
        $_aLocales = array();
        foreach( self::getLocales() as $_sLocale ) {
            $_sClassName = "AmazonAutoLinks_Locale_{$_sLocale}";
            $_aLocales[ $_sLocale ] = new $_sClassName;
        }
        self::$___aLocaleObjects = $_aLocales;
        return $_aLocales;
    }

    static private $___aLabelCaches;
    /**
     * @return array
     * @remrak For form fields.
     */
    static public function getLabels() {
        if ( isset( self::$___aLabelCaches ) ) {
            return self::$___aLabelCaches;
        }
        $_aLabels = array();
        foreach( self::getLocaleObjects() as $_sLocale => $_oLocale ) {
            $_aLabels[ $_sLocale ] = $_oLocale->getLabel();
        }
        self::$___aLabelCaches = $_aLabels;
        return $_aLabels;
    }

    static private $___aDomains;

    /**
     * @return array
     */
    static public function getDomains() {
        if ( isset( self::$___aDomains ) ) {
            return self::$___aDomains;
        }
        $_aDomains = array();
        foreach( self::getLocaleObjects() as $_sLocale => $_oLocale ) {
            $_aDomains[ $_sLocale ] = $_oLocale->getDomain();
        }
        self::$___aDomains = $_aDomains;
        return $_aDomains;
    }

    /**
     * @return array
     * @since  4.3.5
     */
    static public function getSubDomains() {
        return array_map( 'AmazonAutoLinks_Utility::getSubDomainFromHostName', self::getDomains() );
    }

    /**
     * @param   string  $sDomain    The host.
     * @return  string  The locale code.
     * @since   4.3.4   Moved from `AmazonAutoLinks_Property`.
     */
    static public function getLocaleByDomain( $sDomain ) {
        $sDomain = untrailingslashit( $sDomain );
        $sDomain = str_replace( 'www.', '', $sDomain );
        foreach( self::getDomains() as $_sLocale => $_sStoreDomain ) {
            $_sStoreDomain = str_replace( 'www.', '', $_sStoreDomain );
            if ( $_sStoreDomain === $sDomain ) {
                return $_sLocale;
            }
        }
        return 'US';    // not found: default
    }

    /**
     * @param  string $sURL
     * @param  string $sDefaultLocale The default locale.
     * @return string
     * @remark Moved from `AmazonAutoLinks_UnitOutput_embed`.
     * @since  4.3.4
     */
    static public function getLocaleFromURL( $sURL, $sDefaultLocale='US' ) {
        $_sSubDomain = AmazonAutoLinks_Utility::getSubDomain( $sURL );
        $_sLocale = array_search( $_sSubDomain, self::getSubDomains() );
        return false === $_sLocale ? $sDefaultLocale : $_sLocale;
    }

}