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
 * Modifies HTTP request intervals.
 *
 * @since        4.5.0
 */
class AmazonAutoLinks_Proxy_WebPageDumper_Event_Filter_HTTPRequestInterval extends AmazonAutoLinks_Proxy_WebPageDumper_Utility {

    /**
     * Sets up hooks.
     * @since 4.5.0
     */
    public function __construct() {
        add_filter( 'aal_filter_http_request_interval_customer_reviews', array( $this, 'replyToGetHTTPRequestIntervals' ) );
    }

    /**
     * @param  integer $iInterval An interval in seconds.
     * @return integer
     * @since  4.5.0
     */
    public function replyToGetHTTPRequestIntervals( $iInterval ) {
        return 1;
    }

}