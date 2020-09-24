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
class AmazonAutoLinks_Proxy_Event_Filter_MultipleAttempts extends AmazonAutoLinks_PluginUtility {

    /**
     * @assmes  Already checked whether the proxy option is enabled or not.
     * @since   4.2.0
     */
    public function __construct() {

        add_filter( 'aal_filter_http_request_response', array( $this, 'replyToAttemptHTTPRequestsMultipleTimes' ), 10, 5 );

    }

    /**
     * @param WP_Error|array $osResponse
     * @param string $sURL
     * @param array $aArguments
     * @param string $sRequestType
     * @param integer $iCacheDuration
     *
     * @return  array|WP_Error
     * @since   4.2.0
     */
    public function replyToAttemptHTTPRequestsMultipleTimes( $osResponse, $sURL, array $aArguments, $sRequestType, $iCacheDuration ) {

        if ( empty( $aArguments[ 'proxy' ] ) ) {
            return $osResponse;
        }

        // At this point, the request is with a proxy

        if ( ! is_wp_error( $osResponse ) ) {
            return $osResponse;
        }

        // At this point, the response is an error
        /// Store the error object
        $_oError = $osResponse;

        // Let the unusable one to be saved.
        do_action( 'aal_action_detected_unusable_proxy', $aArguments );

        // if there is a previous attempt, check the count and if it exceeds a certain number, bail
        $_iAttempts = ( integer ) $aArguments[ 'attempts' ];
        if ( 1 <= $_iAttempts ) {
            new AmazonAutoLinks_Error( 'PROXY_FAILURE', 'HTTP requests failed with proxies.', array( 'arguments' => $aArguments ), true );
            return $osResponse;
        }
        $aArguments[ 'attempts' ] = $_iAttempts + 1;

        // At this point, the request returned an error

        $_oHTTP = new AmazonAutoLinks_HTTPClient(
            $sURL,
            $iCacheDuration,
            array( 'raw' => true ) + $aArguments,   // setting `raw` because the response format must be an array or an instance of WP_Error
            $sRequestType
        );
        $_osResponse = $_oHTTP->get();
        return is_wp_error( $_osResponse )
            ? $_oError  // the error of the previous attempt
            : $_osResponse;

    }

}