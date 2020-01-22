<?php
/**
 * Amazon Auto Links
 *
 * http://en.michaeluno.jp/amazn-auto-links/
 * Copyright (c) 2013-2020 Michael Uno
 *
 */

/**
 * Diminishes HTTP request caches.
 *
 * Sometimes the sql size exceeds 1mb and some servers with a small value for the `max_allowed_packet` MySQL option
 * gets an error. As a result, the data gets not cached. TO avoid that the data should be diminished (compressed).
 *
 * This class helps to just remove unnecessary elements from retrieved HTML outputs.
 *
 * @since       3.7.5
 */
class AmazonAutoLinks_Unit_URL_Event_HTTPCacheDiminisher extends AmazonAutoLinks_Event___Filter_CustomerReviewCache {

    protected $_sFilterHookName = 'aal_filter_http_request_set_cache_url_unit_type';
    protected $_iPriority       = 10;
    protected $_iParameters     = 4;

    protected $_aTagsToRemove = array( 'title', 'link', 'meta', 'br', 'iframe', 'script', 'style' );
    protected $_aAttributesToRemove = array( 'onload', 'onclick', 'title', 'style', 'class', 'align', 'border', 'for', 'action', 'aria-label' );

}