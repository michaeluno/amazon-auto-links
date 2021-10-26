<?php
/**
 * Auto Amazon Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 */

/**
 * Overrides the `show errors` unit option.
 * @since   4.6.11
 */
class AmazonAutoLinks_Unit_Event_Filter_ShowErrorMode extends AmazonAutoLinks_PluginUtility {

    /**
     * @since  4.6.11
     */
    public function __construct() {
        add_filter( 'aal_filter_unit_show_error_mode', array( $this, 'replyToGetShowErrorMode' ), 10, 2 );
    }

    /**
     * @param  integer $iErrorMode
     * @param  array   $aUnitOptions
     * @since  4.6.11
     * @return integer
     *  - 0: Do not show errors.
     *  - 1: Show errors.
     *  - 2: Show as an HTML comment.
     */
    public function replyToGetShowErrorMode( $iErrorMode, $aUnitOptions ) {
        static $_bUserCanEditUnits;
        if ( isset( $_bUserCanEditUnits ) ) {
            return $_bUserCanEditUnits ? 1 : ( integer ) $iErrorMode;
        }
        $_bUserCanEditUnits = $this->___canUserEditUnits();
        return $_bUserCanEditUnits ? 1 : ( integer ) $iErrorMode;
    }
        /**
         * @return boolean
         * @since  4.6.11
         */
        private function ___canUserEditUnits() {
            if ( ! is_user_logged_in() ) {
                return false;
            }
            $_oOption       = AmazonAutoLinks_Option::getInstance();
            $_sRoleEditUnit = $_oOption->get( array( 'capabilities', 'create_units' ), 'edit_pages' );
            return current_user_can( $_sRoleEditUnit );
        }

}