<?php
/**
 * Auto Amazon Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2023 Michael Uno
 */

/**
 * Provides outputs for Ajax unit loading using REST API.
 *
 * @since 5.4.0
 */
class AmazonAutoLinks_Unit_EventAjax_UnitLoading {

    public function __construct() {

        // If REST API v2 or later is not available, use admin-ajax.php
        // @maybe some users might want to use admin-ajax.php instead of REST API,
        if ( apply_filters( 'aal_filter_should_use_admin_ajax_for_unit_loading', $this->___shouldUseAdminAjax() ) ) {
            new AmazonAutoLinks_Unit_EventAjax_UnitLoading_AdminAjax; // [3.6.0]
            return;
        }
        new AmazonAutoLinks_Unit_EventAjax_UnitLoading_RESTAPI; // [5.4.0]

    }
        /**
         * @since  5.4.0
         * @return boolean
         */
        private function ___shouldUseAdminAjax() {
            if ( ! defined( 'REST_API_VERSION' ) ) {
                return true;
            }
            if ( version_compare( REST_API_VERSION, '2.0', '<' ) ) {
                return true;
            }
            return false;
        }

}