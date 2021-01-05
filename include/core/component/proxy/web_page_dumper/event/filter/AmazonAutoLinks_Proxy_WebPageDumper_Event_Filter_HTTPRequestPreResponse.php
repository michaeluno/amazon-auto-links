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
 * Modifies HTTP request arguments.
 *
 * @since        4.5.0
 */
class AmazonAutoLinks_Proxy_WebPageDumper_Event_Filter_HTTPRequestPreResponse extends AmazonAutoLinks_Proxy_WebPageDumper_Utility {

    /**
     * Sets up hooks.
     * @since 4.5.0
     */
    public function __construct() {
        add_filter( 'aal_filter_http_request_pre_response', array( $this, 'replyToGetPreHTTPResponse' ), 10, 5 );
    }

    /**
     * @param  WP_Error|array $aoResponse
     * @param  string  $sRequestURL
     * @param  integer $iCacheDuration
     * @param  array   $aArguments
     * @param  string  $sRequestType
     * @return WP_Error|array
     * @since  4.5.0
     */
    public function replyToGetPreHTTPResponse( $aoResponse, $sRequestURL, $iCacheDuration, $aArguments, $sRequestType ) {

        $_aExceptedTypes = $this->getAsArray( apply_filters( 'aal_filter_excepted_http_request_types_for_requests', array( 'api', 'api_test', 'api50_test', 'test', 'web_page_dumper' ) ) );
        if ( in_array( $sRequestType, $_aExceptedTypes, true ) ) {
            return $aoResponse;
        }

        if ( ! $this->___shouldUseWebPageDumper( $sRequestURL, $aArguments ) ) {
            return $aoResponse;
        }

        $aArguments = array(
            'interval'    => 0,
            'timeout'     => 30, // seconds
        ) + $aArguments;
        $_oHTTP = new AmazonAutoLinks_Proxy_WebPageDumper_HTTPClient( $this->getWebPageDumperURL(), $sRequestURL, $iCacheDuration, $aArguments, $sRequestType );
        return $_oHTTP->getRawResponse();

    }
        /**
         * @param  string $sRequestURL
         * @param  array  $aArguments
         * @return boolean
         */
        private function ___shouldUseWebPageDumper( $sRequestURL, $aArguments ) {
            if ( $this->getElement( $aArguments, array( 'doing_web_page_dumper' ) ) ) {
                return false;
            }
            return $this->isUserRatingURL( $sRequestURL );
        }

}