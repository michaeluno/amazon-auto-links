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

    public function __construct() {
        add_filter(
            'aal_filter_http_request_response',
            array( $this, 'replyToCaptureCaptchaError' ),
            1,  // must hook early as the error object must be returned when a captcha error is detected.
            5
        );
    }

    /**
     * Called when HTTP request cache is not an array.
     *
     * @param WP_Error|array $oaResult
     * @param string $sURL
     * @param array $aArguments
     * @param string $sRequestType
     * @param integer $iCacheDuration
     *
     * @return  WP_Error|array
     * @since   4.2.2
     */
    public function replyToCaptureCaptchaError( $oaResult, $sURL, $aArguments, $sRequestType, $iCacheDuration ) {

        if ( is_wp_error( $oaResult ) ) {
            return $oaResult;
        }
        $_sBody = $this->getElement( $oaResult, array( 'body' ) );
        if ( $this->isBlockedByAmazonCaptcha( $_sBody, $sURL ) ) {
            return new WP_Error( 'BLOCKED_BY_CAPTCHA', 'Blocked by captcha.' );
        }
        return $oaResult;

    }

}