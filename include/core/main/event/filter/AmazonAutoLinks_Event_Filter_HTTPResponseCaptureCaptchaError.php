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
 * Captures Captcha errors and change the HTTP response to an WP_Error object.
 *
 * @since 4.2.2
 */
class AmazonAutoLinks_Event_Filter_HTTPResponseCaptureCaptchaError extends AmazonAutoLinks_PluginUtility {

    /**
     * Sets up hooks.
     * @since 4.2.2
     * @since 4.3.4 Changed the filter hook from `aal_filter_http_request_response`.
     */
    public function __construct() {
        // @deprecated 4.3.4
//        add_filter(
//            'aal_filter_http_request_response',
//            array( $this, 'replyToCaptureCaptchaError' ),
//            1,  // must hook early as the error object must be returned when a captcha error is detected.
//            5
//        );
        add_filter(
            'aal_filter_http_request_result',   // not using `aal_filter_http_request_response` so that the raw response will be cached.
            array( $this, 'replyToCaptureCaptchaError' ),
            1,  // must hook early as the error object must be returned when a captcha error is detected.
            2
        );
        //
    }

    /**
     * @param array|WP_Error $aoResponse
     * @param string   $sURL
     * @since 4.3.4
     * @callback add_filter()    aal_filter_http_request_result
     * @return array|WP_Error
     */
    public function replyToCaptureCaptchaError( $aoResponse, $sURL ) {

        if ( is_wp_error( $aoResponse ) ) {
            return $aoResponse;
        }
        $_sBody = wp_remote_retrieve_body( $aoResponse );
        if ( $this->isBlockedByAmazonCaptcha( $_sBody, $sURL ) ) {
            return new WP_Error( 'BLOCKED_BY_CAPTCHA', 'Blocked by captcha.' );
        }
        return $aoResponse;

    }

    /**
     * Called when HTTP request cache is not an array.
     *
     * @param WP_Error|array $oaResult
     * @param string $sURL
     * @param array $aArguments
     * @param string $sRequestType
     * @param integer $iCacheDuration
     * @return  WP_Error|array
     * @since   4.2.2
     * @deprecated 4.3.4
     */
/*    public function replyToCaptureCaptchaError( $oaResult, $sURL, $aArguments, $sRequestType, $iCacheDuration ) {

        if ( is_wp_error( $oaResult ) ) {
            return $oaResult;
        }
        $_sBody = $this->getElement( $oaResult, array( 'body' ) );
        if ( $this->isBlockedByAmazonCaptcha( $_sBody, $sURL ) ) {
            return new WP_Error( 'BLOCKED_BY_CAPTCHA', 'Blocked by captcha.' );
        }
        return $oaResult;

    }*/

}