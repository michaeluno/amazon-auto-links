<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2017 Michael Uno
 */

/**
 * Renews Amazon Product Advertising API Request HTTP request caches in the background.
 *
 * @since        3.5.0
 */
class AmazonAutoLinks_Event_APIRequestCacheRenewal extends AmazonAutoLinks_Event_HTTPCacheRenewal {

    protected function _construct() {
        add_filter( 'aal_filter_excepted_http_request_types', array( $this, 'replyToAddExceptedRequestType' ) );
    }
        public function replyToAddExceptedRequestType( $aExceptedRequestTypes ) {
            $aExceptedRequestTypes[] = 'api';
            return $aExceptedRequestTypes;
        }

    /**
     * Checks whether the given type is accepted.
     * @since       3.5.0
     * @return      boolean
     */
    protected function _isType( $sType ) {
        return 'api' === $sType;
    }

    /**
     *
     * @callback        action      aal_action_http_cache_renewal
     */
    public function replyToRenewCache( $sURL, $iCacheDuration, $aHTTPArguments, $sType='wp_remote_get' ) {

        if ( ! $this->_isType( $sType ) ) {
            return;
        }

        // Perform API Request.
        $_aConstructorParameters = $aHTTPArguments[ 'constructor_parameters' ] + array(
            null, null, null, '', array()
        );
        $_oAPI = new AmazonAutoLinks_ProductAdvertisingAPI(
            $_aConstructorParameters[ 0 ],
            $_aConstructorParameters[ 1 ],
            $_aConstructorParameters[ 2 ],
            $_aConstructorParameters[ 3 ],
            $_aConstructorParameters[ 4 ]
        );
        $_oAPI->request(
            $aHTTPArguments[ 'api_parameters' ],
            $iCacheDuration,
            true // force caching
        );

    }

}