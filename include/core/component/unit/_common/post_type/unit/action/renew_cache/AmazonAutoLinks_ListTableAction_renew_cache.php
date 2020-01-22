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
 * Provides methods to renew unit caches.
 * 
 * @package     Amazon Auto Links
 * @since       3.5.0
 */
class AmazonAutoLinks_ListTableAction_renew_cache extends AmazonAutoLinks_PluginUtility {

    /**
     * Performs he action.
     * @since       3.5.0
     */
    public function __construct( array $aPostIDs, $oFactory ) {

        /**
         * Allow unit components to do own tasks upon this action
         * For example, URL units need to renew associated HTTP request caches.
         * @since   3.7.5
         * @since   3.7.6   Moved this before the below `___getCacheRenewed()` method to let callbacks perform necessary routines
         */
        do_action( 'aal_action_renew_unit_caches', $aPostIDs );

        foreach( $aPostIDs as $_iPostID ) {
            $this->___getCacheRenewed( $_iPostID );
        }

    }
        /**
         * @param integer   $iPostID        The subject unit ID.
         */
        private function ___getCacheRenewed( $iPostID ) {
            AmazonAutoLinks_Event_Scheduler::prefetch(
                array(
                    'id'                    => $iPostID,
                    '_force_cache_renewal'  => true,
                )
            );
        }

}