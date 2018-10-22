<?php
/**
 * Amazon Auto Links
 *
 * http://en.michaeluno.jp/amazn-auto-links/
 * Copyright (c) 2013-2018 Michael Uno
 *
 */

/**
 * Renews HTTP request caches of pages set to URL unit type units.
 *
 * @since       3.7.5
 */
class AmazonAutoLinks_Unit_URL_Event_RenewCacheAction extends AmazonAutoLinks_PluginUtility {

    public function __construct() {
        add_action( 'aal_action_renew_unit_caches', array( $this, 'replyToDoAction' ) );
    }

    public function replyToDoAction( array $aPostIDs ) {
        foreach( $aPostIDs as $_iPostID ) {
            $_sUnitType = get_post_meta( $_iPostID, 'unit_type', true );
            if ( 'url' !== $_sUnitType ) {
                continue;
            }
            $this->___renewHTTPRequestCache( $_iPostID );
        }
    }
        private function ___renewHTTPRequestCache( $iPostID ) {

            $_aURLs          = get_post_meta( $iPostID, 'urls', true );
            $_iCacheDuration = get_post_meta( $iPostID, 'cache_duration', true );
            update_post_meta( $iPostID, '_error', null );

            // Just delete caches. Renewing will be done when performing the API request.
            $_oHTTP = new AmazonAutoLinks_HTTPClient(
                $_aURLs,
                $_iCacheDuration,
                array(),    // http arguments
                'url_unit_type'
            );
            $_oHTTP->deleteCache();

        }
}