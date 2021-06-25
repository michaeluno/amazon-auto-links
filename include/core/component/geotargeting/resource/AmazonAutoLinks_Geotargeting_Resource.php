<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 */

/**
 * Loads resources for the Geo-targeting component.
 *
 * @since        4.6.0
 */
class AmazonAutoLinks_Geotargeting_Resource extends AmaAmazonAutoLinks_Geotargeting_Utility {

    /**
     * Sets up properties and hooks.
     */
    public function __construct() {
        add_action( 'wp_enqueue_scripts', array( $this, 'replyToLoadResources' ) );
        add_action( 'enqueue_embed_scripts', array( $this, 'replyToLoadResources' ) );
    }

    public function replyToLoadResources() {

        $_oOption       = AmazonAutoLinks_Option::getInstance();
        $_aGeotargeting = $_oOption->get( 'geotargeting' );

        wp_enqueue_script( 'jquery' );

        $_sActionHookSuffix = 'aal_action_resolve_ip_geolocation';
        $_aScriptData       = array(

            // for Ajax requests
            'ajaxURL'          => admin_url( 'admin-ajax.php' ),
            'actionHookSuffix' => $_sActionHookSuffix,
            'nonce'            => wp_create_nonce( $_sActionHookSuffix ),
            'spinnerURL'       => admin_url( 'images/loading.gif' ),
            'pluginName'       => AmazonAutoLinks_Registry::NAME,
            'debugMode'        => $_oOption->isDebug() || $this->isDebugMode(),

            // 'clientIPAddress'  => $this->getClientIPAddress(), // @deprecated
            'defaultLocale'    => $_oOption->getMainLocale(),
            'availableLocales' => $this->___getAvailableLocales( $_oOption ),
            'queryKey'         => $_oOption->get( 'query', 'cloak' ),
            'apiProviders'     => $this->___getAPIProviders( $_oOption ),
        ) + $_aGeotargeting;

        $_sPath = defined( 'WP_DEBUG' ) && WP_DEBUG
            ? AmazonAutoLinks_Geotargeting_Loader::$sDirPath . '/asset/js/geo-resolver.js'
            : AmazonAutoLinks_Geotargeting_Loader::$sDirPath . '/asset/js/geo-resolver.min.js';
        $_sURL  = AmazonAutoLinks_Utility::getSRCFromPath( $_sPath );
        $_sScriptHandle = 'aal-geo-resolver';
        wp_enqueue_script( $_sScriptHandle, $_sURL, array( 'jquery' ), false, true );
        wp_localize_script(
            $_sScriptHandle,
            'aalGeoResolver',        // variable name on JavaScript side
            $_aScriptData
        );
    }
        /**
         * @param  AmazonAutoLinks_Option $oOption
         * @return array
         * #since  4.6.0
         */
        private function ___getAPIProviders( $oOption ) {
            $_aUsingProviders   = array();
            $_aAPIProviders = array(
                // Returns plain text
                // Returns only IPv6 address if you have that
                'cloudflare' => array(
                    'endpoint' => 'https://www.cloudflare.com/cdn-cgi/trace',
                    'countryCodeKey' => 'loc',
                ),
                // 1000 per day
                // Requires non-null Origin request header
                'db-ip.com' => array(
                    'endpoint' => 'https://api.db-ip.com/v2/free/self',
                    'countryCodeKey' => 'countryCode',
                ),
                // 10,000 requests per hour
                // Free plan only for non-commercial use
                // Returns only IPv6 address if you have that
                'geoiplookup.io' => array(
                    'endpoint' => 'https://json.geoiplookup.io',
                    'countryCodeKey' => 'country_code',
                ),
                // 120 requests per minute
                // No SSL (https) with the free plan
                // Blocked by Ublock Origin
                'geoplugin.net' => array(
                    'endpoint' => 'http://www.geoplugin.net/json.gp',
                    'countryCodeKey' => 'geoplugin_countryCode',
                ),

                // https://api.hackertarget.com/geoip/?q=116.12.250.1
                // 100 requests per day
                // Requires IP address parameter
                // Returns plain text

                // https://ipapi.co/json/
                // 1,000 requests per day
                // Requires SSL (https)
                // Requires non-null Origin request header
                // Returns only IPv6 address if you have that

                // http://ip-api.com/json
                // 150 requests per minute
                // No SSL (https) with the free plan

                // https://api.ipdata.co?api-key=
                // 1,500 requests per day
                // Requires registration to get your API key
                // Requires SSL (https)

                // https://ipfind.co/me?auth=' + apiKey
                // 300 requests per day
                // Requires registration to get your API key

                // https://api.ipgeolocation.io/ipgeo?apiKey=
                // 50,000 requests per month
                // Requires registration to get your API key
                // Returns only IPv6 address if you have that
            );

            $_aEnabledProviders = $this->getAsArray( $oOption->get( 'geotargeting', 'api_providers' ) );
            foreach( $_aEnabledProviders as $_sProviderKey => $_bEnabled ) {
                if ( ! $_bEnabled ) {
                    continue;
                }
                if ( isset( $_aAPIProviders[ $_sProviderKey ] ) ) {
                    $_aUsingProviders[ $_sProviderKey ] = $_aAPIProviders[ $_sProviderKey ];
                }
            }
            return $_aUsingProviders;

        }

        /**
         * @param  AmazonAutoLinks_Option $oOption
         * @return array    Key value pairs of locale code and associate tag.
         * @since  4.6.0
         */
        private function ___getAvailableLocales( $oOption ) {

            $_aLocales = array();
            foreach( $this->getAsArray( $oOption->get( array( 'associates' ) ) ) as $_sLocale => $_aLocale ) {

                $_sAssociateIDByLocale = $this->getElement( $_aLocale, array( 'associate_id' ) );
                if ( ! $_sAssociateIDByLocale ) {
                    continue;
                }
                $_oLocale = new AmazonAutoLinks_Locale( $_sLocale );

                $_aLocales[ $_sLocale ] = array(
                    'locale'      => $_sLocale,
                    'associateID' => $_sAssociateIDByLocale,
                    'searchURL'   => $_oLocale->getMarketPlaceURL( 's?tag=' . $_sAssociateIDByLocale . '&k=' ),
                    'domain'      => $_oLocale->getDomain(),
                );

                // Alias
                if ( 'UK' === $_sLocale ) {
                    $_aLocales[ 'GB' ] = $_aLocales[ $_sLocale ];
                }

            }
            return $_aLocales;

        }

}