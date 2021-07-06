<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 */

/**
 * Renews HTTP request caches in the background.
 *
 * ### The workflow
 * 1. An HTTP request is made.
 * 2. The `aal_filter_http_response_cache` filter hook callbacks are called.
 * 3. In the callback, if the cache is expired, schedule a renew background routine with the `aal_action_http_cache_renewal` action hook.
 * And flag the cache as not expired to be loaded in front-end normally.
 * 4. In the callback of the `aal_action_http_cache_renewal` action hook, perform the HTTP request by removing the existing cache.
 *
 * @package      Amazon Auto Links
 * @since        3
 *
 */
class AmazonAutoLinks_Event___Action_HTTPCacheRenewal extends AmazonAutoLinks_Event___Action_Base {

    protected $_sActionHookName     = 'aal_action_http_cache_renewal';
    protected $_iCallbackParameters = 4;

    /**
     * Sets up hooks.
     * @since       3.5.0
     */
    protected function _construct() {
        add_filter( 'aal_filter_http_response_cache', array( $this, 'replyToModifyCacheRemainedTime' ), 10, 4 );
    }

    /**
     * Checks whether the given type is accepted for performing HTTP requests in the background.
     * @since       3.5.0
     * @param       string  $sType
     * @return      boolean
     */
    protected function _isType( $sType ) {
        return ! in_array( $sType, $this->getAsArray( apply_filters( 'aal_filter_excepted_http_request_types_for_requests', array() ) ) );
    }

    /**
     * Checks whether the given request type is accepted for background caching.
     * @since  3.5.0
     * @param  string  $sType
     * @return boolean
     */
    protected function _isBackgroundCacheRenewalAllowed( $sType ) {
        return ! in_array( $sType, $this->getAsArray( apply_filters( 'aal_filter_disallowed_http_request_types_for_background_cache_renewal', array() ) ), true );
    }

    /**
     * @return boolean
     * @since  4.3.4
     */
    protected function _shouldProceed() {
        if ( ! $this->_isType( func_get_arg( 3 ) ) ) {
            return false;
        }
        return true;
    }

    /**
     * @callback add_action() aal_action_http_cache_renewal
     */
    protected function _doAction( /* $sURL, $iCacheDuration, $aArguments, $sType='wp_remote_get' */ ) {

        $_aArguments    = func_get_args() + array( null, 8600, array(), 'wp_remote_get', );
        $sURL           = $_aArguments[ 0 ];
        $iCacheDuration = $_aArguments[ 1 ];
        $aArguments     = $_aArguments[ 2 ];
        $sType          = $_aArguments[ 3 ];
        $_oHTTP         = new AmazonAutoLinks_HTTPClient( $sURL, $iCacheDuration, $aArguments, $sType );
        $_oHTTP->deleteCache();
        $_oHTTP->get();

    }

    /**
     *
     * @param    array          $aCache
     * @param    integer        $iCacheDuration
     * @param    array          $aHTTPArguments
     * @param    string         $sType
     * @return   array
     * @callback add_filter()   aal_filter_http_response_cache
     */
    public function replyToModifyCacheRemainedTime( $aCache, $iCacheDuration, $aHTTPArguments, $sType='wp_remote_get' ) {

        /**
         * API requests have request-specific parameters such as timestamp and signature,
         * so it needs a special handling and do not process it here.
         */
        if ( ! $this->_isBackgroundCacheRenewalAllowed( $sType ) ) {
            return $aCache;
        }

        // Is it expired?
        if ( 0 < $aCache[ 'remained_time' ] ) {
            return $aCache;
        }

        // At this point, it is expired. So schedule a task that renews the cache in the background.
        $_aArguments = array(
            $aCache[ 'request_uri' ],
            $iCacheDuration,
            $aHTTPArguments,
            $sType
        );
        $_bScheduled = $this->scheduleTask( $this->_sActionHookName, $_aArguments );
        if ( ! $_bScheduled ) {
            new AmazonAutoLinks_Error( 'CACHE_RENEWAL_EVENT', 'Failed to schedule a background cache renewal event. Action: ' . $this->_sActionHookName, $_aArguments, true );
        }

        // Tell the plugin it is not expired.
        $aCache[ 'remained_time' ] = time();
        return $aCache;
                
    }
        
}