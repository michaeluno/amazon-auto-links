<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2017 Michael Uno
 */

/**
 * Renews HTTP request caches in the background.
 *
 * @package      Amazon Auto Links
 * @since        3
 *
 */
class AmazonAutoLinks_Event_HTTPCacheRenewal extends AmazonAutoLinks_PluginUtility {
    
    public $sCacheRenewalActionName = 'aal_action_http_cache_renewal';

    /**
     * Sets up hooks.
     * @since       3
     */
    public function __construct() {

        add_filter(
            'aal_filter_http_response_cache',  // filter hook name
            array( $this, 'replyToModifyCacheRemainedTime' ), // callback
            10, // priority
            4 // number of parameters
        );

        add_action(
            $this->sCacheRenewalActionName, // action hook name
            array( $this, 'replyToRenewCache' ),
            10,
            4
        );

        $this->_construct();

    }

    /**
     * @since       3.5.0
     * @return      void
     */
    protected function _construct() {}

    /**
     * Checks whether the given type is accepted.
     * @since       3.5.0
     * @return      boolean
     */
    protected function _isType( $sType ) {
        return ! in_array(
            $sType,
            $this->getAsArray( apply_filters( 'aal_filter_excepted_http_request_types', array() ) )
        );
    }

    /**
     *
     * @callback        action      aal_action_http_cache_renewal
     */
    public function replyToRenewCache( $sURL, $iCacheDuration, $aArguments, $sType='wp_remote_get' ) {

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
     * @callback        filter      aal_filter_http_response_cache
     */
    public function replyToModifyCacheRemainedTime( $aCache, $iCacheDuration, $aHTTPArguments, $sType='wp_remote_get' ) {

        // API requests have request-specific parameters such as timestamp and signature,
        // so it needs a special handling and do not process it here.
        if ( ! $this->_isType( $sType ) ) {
            return $aCache;
        }

        // Check if it is expired.
        if ( 0 >= $aCache[ 'remained_time' ] ) {

            // It is expired. So schedule a task that renews the cache in the background.
            $_bScheduled = $this->scheduleSingleWPCronTask(
                $this->sCacheRenewalActionName,
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