<?php
/**
 * Auto Amazon Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 */

/**
 * Loads the component, HTTP Proxy.
 *
 * @since        4.2.0
 */
class AmazonAutoLinks_Proxy_Event_Filter_FetchProxyList extends AmazonAutoLinks_PluginUtility {

    public function __construct() {

        add_filter( 'aal_filter_imported_proxies', array( $this, 'replyToFetchProxies' ) );
        add_filter( 'aal_filter_excepted_http_request_types_for_requests', array( $this, 'replyToAddExceptedRequestType' ) );
        add_filter( 'aal_filter_disallowed_http_request_types_for_background_cache_renewal', array( $this, 'replyToAddExceptedRequestType' ) );

    }
        public function replyToAddExceptedRequestType( $aExceptedRequestTypes ) {
            $aExceptedRequestTypes[] = AmazonAutoLinks_Proxy_Loader::$sHTTPRequestType;
            return $aExceptedRequestTypes;
        }

    /**
     * @param array $aProxies
     * @return array
     */
    public function replyToFetchProxies( array $aProxies ) {

        $_aClassNames = array(
            'AmazonAutoLinks_Proxy_Fetch_http',
            'AmazonAutoLinks_Proxy_Fetch_https',
            'AmazonAutoLinks_Proxy_Fetch_socks4',
            'AmazonAutoLinks_Proxy_Fetch_socks5',
        );
        foreach( $_aClassNames as $_sClassName ) {
            $_oProxyList = new $_sClassName;
            $aProxies    = array_merge( $aProxies, $_oProxyList->get() );
        }
        $aProxies = array_unique( $aProxies );
        foreach( $aProxies as $_iIndex => $_sAddress ) {
            // Validation
            if ( ! filter_var( $_sAddress, FILTER_VALIDATE_URL ) ){
                unset( $aProxies[ $_iIndex ] );
            }
        }
        return $aProxies;

    }

}