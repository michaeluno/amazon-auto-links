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
 * Renews Amazon Product Advertising API Request HTTP request caches in the background.
 *
 * @since        3.5.0
 */
class AmazonAutoLinks_Event___Action_APIRequestCacheRenewal extends AmazonAutoLinks_Event___Action_HTTPCacheRenewal {

    private $___aAPIRequestTypes = array(
        'api',
        'api_test',
        'api50_test',
    );

    protected function _construct() {
        add_filter( 'aal_filter_excepted_http_request_types_for_requests', array( $this, 'replyToAddExceptedRequestType' ) );
        add_filter( 'aal_filter_disallowed_http_request_types_for_background_cache_renewal', array( $this, 'replyToAddExceptedRequestType' ) );
        parent::_construct();
    }
        public function replyToAddExceptedRequestType( $aExceptedRequestTypes ) {
            return array_merge( $this->___aAPIRequestTypes, $aExceptedRequestTypes );
        }

    /**
     * Checks whether the given type is accepted.
     * @since       3.5.0
     * @return      boolean
     */
    protected function _isType( $sType ) {
        return in_array( $sType, $this->___aAPIRequestTypes );
    }

    /**
     * Checks whether the given request type is accepted for caching.
     * @since       3.5.0
     * @return      boolean
     */
    protected function _isBackgroundCacheRenewalAllowed( $sType ) {
        return $this->_isType( $sType );
    }

    /**
     *
     * @callback        action      aal_action_http_cache_renewal
     */
    protected function _doAction( /* $sURL, $iCacheDuration, $aHTTPArguments, $sType='wp_remote_get' */ ) {

        $_aArguments    = func_get_args() + array( null, null, array(), 'wp_remote_get' );
        $sURL           = $_aArguments[ 0 ];
        $iCacheDuration = $_aArguments[ 1 ];
        $aHTTPArguments = $_aArguments[ 2 ];
        $sType          = $_aArguments[ 3 ];


        if ( ! $this->_isType( $sType ) ) {
            return;
        }

        $_aConstructorParameters = $aHTTPArguments[ 'constructor_parameters' ] + array(
            null, null, null, '', array(), 'api'
        );

        // For compatible with v3.8.x or below
        if ( ! isset( $aHTTPArguments[ 'body' ] ) ) {
            $_oAPI = new AmazonAutoLinks_ProductAdvertisingAPI(
                $_aConstructorParameters[ 0 ],
                $_aConstructorParameters[ 1 ],
                $_aConstructorParameters[ 2 ],
                $_aConstructorParameters[ 3 ],
                $_aConstructorParameters[ 4 ],
                $_aConstructorParameters[ 5 ]
            );
            $_oAPI->request( $aHTTPArguments[ 'api_parameters' ], $iCacheDuration, true ); // force caching
        }
        // When the `body` argument is set, it means the POSt method and is a PA-API 5 request.
        $_oPAAPI5 = new AmazonAutoLinks_PAAPI50(
            $_aConstructorParameters[ 0 ],  // $sLocale
            $_aConstructorParameters[ 1 ],  // $sPublicKey
            $_aConstructorParameters[ 2 ],  // $sSecretKey
            $_aConstructorParameters[ 3 ],  // $sAssociateID
            $_aConstructorParameters[ 4 ],  // $aHTTPArguments
            $_aConstructorParameters[ 5 ]   // $sRequestType
        );
        $_aResponse = $_oPAAPI5->request( $aHTTPArguments[ 'api_parameters' ], $iCacheDuration, true ); // force caching

    }

}