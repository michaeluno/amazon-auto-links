<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 */

/**
 * Loads the debug component.
 *  
 * @package     Amazon Auto Links
 * @since       4.3.0
*/
class AmazonAutoLinks_Log_Debug_Loader extends AmazonAutoLinks_PluginUtility {

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

        $_oOption = AmazonAutoLinks_Option::getInstance();
        if ( ! $_oOption->isDebug( 'log' ) ) {
            return;
        }

        new AmazonAutoLinks_Log_Debug_Event_DebugLog;
        new AmazonAutoLinks_Log_Debug_Event_HTTPRequest;
        new AmazonAutoLinks_Log_Debug_Event_PluginCron;

        $this->___loadAdminComponents();


    }
        /**
         * Loads admin components.
         */
        private function ___loadAdminComponents() {
            add_action( 'load_' .  AmazonAutoLinks_Registry::$aAdminPages[ 'report' ], array( $this, 'replyToLoadToolPage' ) );
        }
            /**
             * @param AmazonAutoLinks_AdminPageFramework $oFactory
             */
            public function replyToLoadToolPage( $oFactory ) {

                new AmazonAutoLinks_Log_Debug_AdminPage_Tab_DebugLog( $oFactory, $oFactory->oProp->getCurrentPageSlug() );

            }

}