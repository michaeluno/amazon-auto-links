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
 * Resolves Client IP Geo-location
 * @since   4.6.0
 * @deprecated 4.6.0
 */
class AmazonAutoLinks_Geotargeting_EventAjax_GeolocationResolver extends AmazonAutoLinks_AjaxEvent_Base {

    /**
     * The part after `wp_ajax_` or `wp_ajax_nopriv_`.
     * @var string
     */
    protected $_sActionHookSuffix = 'aal_action_resolve_ip_geolocation';

    protected $_bLoggedIn = true;
    protected $_bGuest    = true;

    /**
     * @param  array            $aPost $_POST data
     * @return string|array
     * @throws Exception        Throws a string value of an error message.
     * @since  4.6.0
     */
    protected function _getResponse( array $aPost ) {

        $_oOption = AmazonAutoLinks_Option::getInstance();
        $_oUtil   = new AmaAmazonAutoLinks_Geotargeting_Utility;
        $_sIP     = $_oUtil->getClientIPAddress();
        if ( $_oUtil->isLocalhost( $_sIP ) && $_oOption->get( 'geotargeting', 'resolve_localhost' ) ) {
            $_sIP = $this->___getExternalIPAddress();
        }

        return array();

        // @see
        /**
         * API Providers
         * @see https://stackoverflow.com/questions/391979/how-to-get-clients-ip-address-using-javascript
         */
        // https://api.bigdatacloud.net/data/ip-geolocation?key=' + apiKey,
        // 10,000 requests per month
        // Requires registration to get your API key

        // https://www.cloudflare.com/cdn-cgi/trace
        // Returns plain text
        // Returns only IPv6 address if you have that

        // https://api.db-ip.com/v2/free/self
        // 1000 per day
        // Requires non-null Origin request header

        // https://json.geoiplookup.io
        // 10,000 requests per hour
        // Free plan only for non-commercial use
        // Returns only IPv6 address if you have that

        // http://www.geoplugin.net/json.gp
        // 120 requests per minute
        // No SSL (https) with the free plan

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


    }

    /**
     * @return string
     * @since  4.6.0
     */
    private function ___getExternalIPAddress() {
        // https://jsonip.com/
        // https://ip-api.io/api/json // this gives other information including country codes
        // https://api.ipify.org/?format=json
        $_sEndpoint     = 'https://api64.ipify.org/?format=json';
        $_oHTTP         = new AmazonAutoLinks_HTTPClient(
            $_sEndpoint,
            86400*7,
            array(),
            'api_geotargeting'
        );
        $_sResponseBody = $_oHTTP->getBody();
        $_aResponseBody = json_decode( $_sResponseBody, true );
        return isset( $_aResponseBody[ 'ip' ] ) ? $_aResponseBody[ 'ip' ] : '';
    }

}