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
 * Loads the component, HTTP Proxy.
 *
 * @package      Amazon Auto Links
 * @since        4.2.0
 */
class AmazonAutoLinks_Proxy_Event_Filter_SetProxy extends AmazonAutoLinks_Proxy_Utility {

    /**
     * @assmes  Already checked whether the proxy option is enabled or not.
     * @since   4.2.0
     */
    public function __construct() {

        add_action( 'aal_filter_http_request_arguments', array( $this, 'replyToGetProxySet' ), 10, 2 );

    }

        /**
         * @param  array $aArguments
         * @param  string $sRequestType
         * @return array
         */
        public function replyToGetProxySet( array $aArguments, $sRequestType ) {

            // HTTP Requests for PA-API and fetching proxy lists should not use proxies.
            $_aExceptedTypes  = apply_filters( 'aal_filter_excepted_http_request_types_for_requests', array( AmazonAutoLinks_Proxy_Loader::$sHTTPRequestType ) );
            if ( in_array( $sRequestType, $_aExceptedTypes ) ) {
                return $aArguments;
            }

            // The proxy list saved in the options and pick random one
            $_sProxyList     = AmazonAutoLinks_ToolOption::getInstance()->get( array( 'proxies', 'proxy_list' ), '' );
            $_aProxies       = ( array ) preg_split( "/\s+/", trim( ( string ) $_sProxyList ), 0, PREG_SPLIT_NO_EMPTY );

            /// For multiple attempts, avoid using the previous proxy.
            $_sPreviousProxy = ( string ) $this->getElement( $aArguments, array( 'proxy', 'raw' ), '' );
            $_iIndex         = array_search( $_sPreviousProxy, $_aProxies );
            if ( false !== $_iIndex ) {
                unset( $_aProxies[ $_iIndex ] );
            }

            /// Pick random one
            $_iRandomIndex   = empty( $_aProxies )
                ? 0
                : array_rand( $_aProxies, 1 );
            if ( empty( $_aProxies[ $_iRandomIndex ] ) ) {
                new AmazonAutoLinks_Error( 'PROXY_SET_FAILURE', 'The proxy option is enabled but there is no proxy available.', array( 'arguments' => $aArguments ), true );
                return $aArguments;
            }

            // Set the proxy
            $_sProxy               = $_aProxies[ $_iRandomIndex ];
            $aArguments[ 'proxy' ] = $this->getProxyArguments( $_sProxy );

            return $aArguments;

        }

}