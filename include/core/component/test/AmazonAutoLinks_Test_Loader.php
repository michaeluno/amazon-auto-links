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
 * Loads the test component.
 *  
 * @package     Amazon Auto Links
 * @since       4.3.0
*/
class AmazonAutoLinks_Test_Loader extends AmazonAutoLinks_PluginUtility {

    /**
     * Stored the component directory path.
     *
     * Referred to enqueue resources.
     *
     * @var string
     * @since   4.3.0
     */
    static public $sDirPath = '';

    /**
     * AmazonAutoLinks_UnitLoader constructor.
     *
     */
    public function __construct() {

        self::$sDirPath = dirname( __FILE__ );

        if ( ! $this->___shouldProceed() ) {
            return;
        }

        $this->___loadAdminComponents();

        // Events
        new AmazonAutoLinks_Test_Event_Ajax_Tests;
        new AmazonAutoLinks_Test_Event_Ajax_Scratches;
        new AmazonAutoLinks_Test_Event_Ajax_Delete;

        new AmazonAutoLinks_Test_Event_Query_Cookie;    // [4.3.4]
        new AmazonAutoLinks_Test_Event_Query_Referer;   // [4.3.4]

    }
        /**
         * Loads admin components.
         */
        private function ___loadAdminComponents() {

            add_action( 'set_up_' .  'AmazonAutoLinks_AdminPage', array( $this, 'replyToSetUpAdminPage' ) );

        }
            /**
             * @param AmazonAutoLinks_AdminPageFramework $oFactory
             */
            public function replyToSetUpAdminPage( $oFactory ) {
                new AmazonAutoLinks_Test_AdminPage_Test( $oFactory );
            }

        /**
         * @return boolean
         * @since  4.3.5 It allows if the plugin debug mode is turned on.
         */
        private function ___shouldProceed() {
            if ( ! is_admin() ) {
                return false;
            }
            if ( AmazonAutoLinks_WPUtility::isDebugMode() ) {
                return true;
            }
            $_oOption = AmazonAutoLinks_Option::getInstance();
            if ( $_oOption->isDebug() ) {
                return true;
            }
            return false;
        }

}