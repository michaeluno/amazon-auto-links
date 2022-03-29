<?php
/**
 * Auto Amazon Links
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2022 Michael Uno
 *
 */

/**
 * Renews HTTP request caches set to the feed unit type units.
 *
 * @since       4.6.1
 */
class AmazonAutoLinks_Unit_Feed_Event_RenewCacheAction extends AmazonAutoLinks_PluginUtility {

    public function __construct() {
        add_action( 'aal_action_renew_unit_caches', array( $this, 'replyToDoAction' ) );
    }

    public function replyToDoAction( array $aPostIDs ) {
        foreach( $aPostIDs as $_iPostID ) {
            $_sUnitType = $this->getPostMeta( $_iPostID, 'unit_type' );
            if ( 'feed' !== $_sUnitType ) {
                continue;
            }
            $this->___deleteHTTPRequestCache( $_iPostID );
        }
    }
        private function ___deleteHTTPRequestCache( $iPostID ) {

            $_aFeedURLs      = $this->getAsArray( $this->getPostMeta( $iPostID, 'feed_urls', array() ) );
            $_iCacheDuration = ( integer ) $this->getPostMeta( $iPostID, 'cache_duration' );
            delete_post_meta( $iPostID, '_error' );

            // Just delete caches. Renewing will be done when a prefetch is called.
            $_oHTTP = new AmazonAutoLinks_HTTPClient_Multiple(
                $_aFeedURLs,
                $_iCacheDuration,
                array(),    // http arguments
                'feed_unit_type'
            );
            $_oHTTP->deleteCaches();

        }

}