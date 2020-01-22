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
 * Renews HTTP request caches in the background.
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
        add_filter(
            'aal_filter_http_response_cache',  // filter hook name
            array( $this, 'replyToModifyCacheRemainedTime' ), // callback
            10, // priority
            4 // number of parameters
        );
    }

    /**
     * Checks whether the given type is accepted for performing HTTP requests.
     * @since       3.5.0
     * @return      boolean
     */
    protected function _isType( $sType ) {
        return ! in_array(
            $sType,
            $this->getAsArray( apply_filters( 'aal_filter_excepted_http_request_types_for_requests', array() ) )
        );
    }

    /**
     * Checks whether the given request type is accepted for caching.
     * @since       3.5.0
     * @return      boolean
     */
    protected function _isBackgroundCacheRenewalAllowed( $sType ) {
        return ! in_array(
            $sType,
            $this->getAsArray( apply_filters( 'aal_filter_disallowed_http_request_types_for_background_cache_renewal', array() ) )
        );
    }

    /**
     *
     */
    protected function _doAction( /* $sURL, $iCacheDuration, $aArguments, $sType='wp_remote_get' */ ) {

        $_aArguments    = func_get_args() + array( null, 8600, array(), 'wp_remote_get', );
        $sURL           = $_aArguments[ 0 ];
        $iCacheDuration = $_aArguments[ 1 ];
        $aArguments     = $_aArguments[ 2 ];
        $sType          = $_aArguments[ 3 ];

        if ( ! $this->_isType( $sType ) ) {
            return;
        }

        $_oHTTP = new AmazonAutoLinks_HTTPClient(
            $sURL,
            $iCacheDuration,
            $aArguments,
            $sType
        );
        $_oHTTP->deleteCache();
        $_oHTTP->get();

    }

    /**
     *
     * @callback        add_filter      aal_filter_http_response_cache
     */
    public function replyToModifyCacheRemainedTime( $aCache, $iCacheDuration, $aHTTPArguments, $sType='wp_remote_get' ) {

        /**
         * API requests have request-specific parameters such as timestamp and signature,
         * so it needs a special handling and do not process it here.
         */
        if ( ! $this->_isBackgroundCacheRenewalAllowed( $sType ) ) {
            return $aCache;
        }

        // Check if it is expired.
        if ( 0 >= $aCache[ 'remained_time' ] ) {

            // It is expired. So schedule a task that renews the cache in the background.
            $_bScheduled = $this->scheduleSingleWPCronTask(
                $this->_sActionHookName,    // aal_action_http_cache_renewal
                array(
                    $aCache[ 'request_uri' ],
                    $iCacheDuration,
                    $aHTTPArguments,
                    $sType
                )
            );
            if ( $_bScheduled ) {
                AmazonAutoLinks_Shadow::see();
            }

            // Tell the plugin it is not expired. 
            $aCache[ 'remained_time' ] = time();
            
        }

        return $aCache;
                
    }
        
}