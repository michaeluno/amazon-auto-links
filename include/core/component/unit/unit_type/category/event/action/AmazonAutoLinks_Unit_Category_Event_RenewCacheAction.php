<?php
/**
 * Amazon Auto Links
 *
 * http://en.michaeluno.jp/amazn-auto-links/
 * Copyright (c) 2013-2020 Michael Uno
 *
 */

/**
 * Renews HTTP request caches of feeds set to Category unit type units.
 *
 * @since       3.7.6
 */
class AmazonAutoLinks_Unit_Category_Event_RenewCacheAction extends AmazonAutoLinks_PluginUtility {

    public function __construct() {
        add_action( 'aal_action_renew_unit_caches', array( $this, 'replyToDoAction' ) );
    }

    public function replyToDoAction( array $aPostIDs ) {
        foreach( $aPostIDs as $_iPostID ) {
            $_sUnitType = get_post_meta( $_iPostID, 'unit_type', true );
            if ( 'category' !== $_sUnitType ) { // 3.8.1 Changed the value was `url` although the subject unit type is `category`.
                continue;
            }
            $this->___deleteHTTPRequestCache( $_iPostID );
        }
    }
        private function ___deleteHTTPRequestCache( $iPostID ) {

            $_aURLs          = $this->___getSubjectURLs( $iPostID );
            $_iCacheDuration = get_post_meta( $iPostID, 'cache_duration', true );
            delete_post_meta( $iPostID, '_error' );

            // Just delete caches. Renewing will be done when a prefetch is called.
            $_oHTTP = new AmazonAutoLinks_HTTPClient(
                $_aURLs,
                $_iCacheDuration,
                array(),    // http arguments
                'category_unit_type'       // 3.8.1 Changed the value from `url_unit_type` as it appears to be a mistake.
            );
            $_oHTTP->deleteCache();

        }
            /**
             * @param   $iPostID
             * @since   3.8.1
             * @return  array
             */
            private function ___getSubjectURLs( $iPostID ) {
                $_aPageURLs          = $this->getAsArray( get_post_meta( $iPostID, 'categories', true ) );
                $_aPageURLs          = wp_list_pluck( $_aPageURLs, 'page_url' );
                $_aExcludingPageURLs = $this->getAsArray( get_post_meta( $iPostID, 'categories_exclude', true ) );
                $_aExcludingPageURLs = wp_list_pluck( $_aExcludingPageURLs, 'page_url' );
                $_aMerged            = array_merge( $_aPageURLs, $_aExcludingPageURLs );
                return array_unique( $_aMerged );
            }
}