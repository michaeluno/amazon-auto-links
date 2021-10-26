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
 * Checks a Captcha error in an existent HTTP response.
 *
 * This should be called only when the proxy option is enabled.
 * Say, the user got a Captcha error and then enabled the proxy option. In that case, the cache with a Captcha error will be
 * returned until the expiration time. So this class shortens the expiration time if the parsing HTTP response has a Captcha error.
 *
 * @package      Auto Amazon Links
 * @since        4.2.2
 */
class AmazonAutoLinks_Proxy_Event_Filter_CheckExistentCaptchaErrors extends AmazonAutoLinks_PluginUtility {

    public function __construct() {
        add_filter( 'aal_filter_http_response_cache', array( $this, 'replyToDetectCaptchaErrors' ), 100, 4 );
    }

    /**
     * @param array $aCache
     * @param integer $iCacheDuration
     * @param array $aArguments
     * @param string $sRequestType
     *
     * @return array
     */
    public function replyToDetectCaptchaErrors( $aCache, $iCacheDuration, $aArguments, $sRequestType ) {

        $_aExceptedRequestTypes = apply_filters( 'aal_filter_excepted_http_request_types_for_requests', array() );
        if ( in_array( $sRequestType, $_aExceptedRequestTypes ) ) {
            return $aCache;
        }

        $_sURL      = $this->getElement( $aCache, array( 'request_uri' ), '' );
        if ( ! preg_match( '/https?:\/\/(www\.)?amazon\.[^"\' >]+/', $_sURL ) ) {
            return $aCache;
        }

        $_osData    = $this->getElement( $aCache, array( 'data' ), '' );
        if ( is_wp_error( $_osData ) && $_osData->get_error_code() === 'BLOCKED_BY_CAPTCHA' ) {
            $aCache[ 'remained_time' ] = 0;
            return $aCache;
        }
        $_sBody     = $this->getElement( $_osData, array( 'body' ), '' );
        if ( $this->isBlockedByAmazonCaptcha( $_sBody, $_sURL ) ) {
            $aCache[ 'remained_time' ] = 0;
        }
        return $aCache;

    }

}