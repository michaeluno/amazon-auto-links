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
 * Checks HTTP response errors and if an error is detected, use Web Page Dumper to fetch web contents.
 *
 * @since        4.5.0
 */
class AmazonAutoLinks_Proxy_WebPageDumper_Event_Filter_HTTPResponse extends AmazonAutoLinks_Proxy_WebPageDumper_Utility {

    /**
     * Sets up hooks.
     * @since 4.5.0
     */
    public function __construct() {
        add_filter(
            'aal_filter_http_request_response',
            array( $this, 'replyToCheckErrors' ),
            9,  // earlier then those try to capture errors
            5
        );
    }
    /**
     * Checks HTTP response errors and if an error is detected, use Web Page Dumper to fetch web contents.
     * @param  WP_Error|array  $aoResponse
     * @param  string          $sURL
     * @param  array           $aArguments
     * @param  string          $sRequestType
     * @param  integer         $iCacheDuration
     * @return WP_Error|array
     * @since  4.5.0
     */
    public function replyToCheckErrors( $aoResponse, $sURL, $aArguments, $sRequestType, $iCacheDuration ) {

        $_aError = $this->getHTTPResponseError( $aoResponse, $sURL );
        if ( empty( $_aError ) ) {
            return $aoResponse;
        }

        $_aExceptedTypes   = $this->getAsArray( apply_filters( 'aal_filter_excepted_http_request_types_for_requests', array( 'api', 'api_test', 'api50_test', 'test' ) ) );
        if ( in_array( $sRequestType, $_aExceptedTypes, true ) ) {
            return $aoResponse;
        }
        if ( $this->getElement( $aArguments, array( 'doing_web_page_dumper' ) ) ) {
            return $aoResponse;
        }
        $_sURLWebPageDumper = $this->getWebPageDumperURL();
        $aArguments         = array(
            'renew_cache'           => true,
            'timeout'               => 60, // seconds. Set a longer one as Web Page Dumper servers are often sleeping.
        ) + $aArguments;
        $_oHTTP = new AmazonAutoLinks_Proxy_WebPageDumper_HTTPClient( $_sURLWebPageDumper, $sURL, $iCacheDuration, $aArguments, $sRequestType );
        return $_oHTTP->getResponse();

    }
}