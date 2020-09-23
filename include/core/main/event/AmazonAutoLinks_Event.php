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
 * Plugin event handler.
 * 
 * @package      Amazon Auto Links
 * @since        2.0.0
 * @action       aal_action_simplepie_renew_cache
 * @action       aal_action_unit_prefetch
 * @action       aal_action_event_convert_template_options    
 * @filter       aal_filter_store_redirect_url - [2.0.5+] receives the redirecting url of the Amazon store
 */
class AmazonAutoLinks_Event {

    /**
     * Triggers event actions.
     */
    public function __construct() {
        add_action( 'aal_action_loaded_plugin', array( $this, 'replyToLoadEvents' ) );
    }

    /**
     * @return      void
     * @since       3.3.0
     */
    public function replyToLoadEvents() {

        do_action( 'aal_action_events' );

        $this->___handleWPCronEvents();
        $this->___handleQueryURL();
        $this->___handleActions();
        $this->___handleFilters();

        // This must be called at last as hooks especially for WP Cron must be set up.
        $this->___handleBackgroundRoutines();

    }

        /**
         * @since       3.5.0
         * @return      void
         */
        private function ___handleWPCronEvents() {

            new AmazonAutoLinks_Event___Action_HTTPCacheRenewal;
            new AmazonAutoLinks_Event___Action_SimplePie_CacheRenewal;
            new AmazonAutoLinks_Event___Action_DeleteExpiredCaches;

        }

        /**
         *
         * @since       3.1.0
         * @return      void
         */
        private function ___handleQueryURL() {

            $_oOption     = AmazonAutoLinks_Option::getInstance();
            $_sQueryKey   = $_oOption->get( 'query', 'cloak' );
            if ( ! isset( $_GET[ $_sQueryKey ] ) ) {
                return;
            }

            new AmazonAutoLinks_Event___Query_Feed( $_sQueryKey );
            new AmazonAutoLinks_Event___Query_Redirect( $_sQueryKey );

        }

        /**
         * @since       4.0.0
         */
        private function ___handleActions() {
            new AmazonAutoLinks_Event_Error_Log_HTTPRequestCache;
            new AmazonAutoLinks_Event_Error_Log;  // 4.2.0
        }

        private function ___handleFilters() {
            new AmazonAutoLinks_Event_HTTPClientArguments;
            new AmazonAutoLinks_Event_Filter_HTTPRequestCache;  // 4.2.0
            new AmazonAutoLinks_Event_Filter_HTTPResponseCaptureCaptchaError; // 4.2.2
        }

        /**
         * Handling background routines should be done after all hooks and their callbacks are set up.
         *
         * As it reads and trigger WP Cron tasks, the callback functions associated with registered action names must be set up.
         *
         * @callback    add_action  aal_action_loaded_plugin
         * @since       3.5.0
         * @return      void
         */
        private function ___handleBackgroundRoutines() {

            // This must be called after the above action hooks.
            $_oOption               = AmazonAutoLinks_Option::getInstance();
            $_bIsIntenseCachingMode = 'intense' === $_oOption->get( 'cache', 'caching_mode' );

            // Force executing actions.
            $_aHooksNormal  = array(
                'aal_action_unit_prefetch',
                'aal_action_api_get_products_info',         // 3.7.7
                'aal_action_api_get_customer_review2',
                'aal_action_http_cache_renewal',
                'aal_action_check_tasks',                   // 4.3.0
                'aal_action_proxy_update',
            );
            $_aHooksIntense = array_merge(
                $_aHooksNormal,
                array(
                    'aal_action_simplepie_renew_cache',
                    'aal_action_delete_expired_caches',     // 3.4.0
                )
            );
            new AmazonAutoLinks_Shadow( $_bIsIntenseCachingMode ? $_aHooksIntense : $_aHooksNormal );

            // @deprecated 4.3.0
            // If it is a background page load, exit() is performed via the constructor of AmazonAutoLinks_Shadow.
    //                if ( ! $_bIsIntenseCachingMode && AmazonAutoLinks_Shadow::isBackground() ) {
    //                    exit;
    //                }

        }


}
