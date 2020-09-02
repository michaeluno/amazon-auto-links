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
 * Loads the debug component.
 *  
 * @package     Amazon Auto Links
 * @since       4.3.0
*/
class AmazonAutoLinks_Debug_Loader extends AmazonAutoLinks_PluginUtility {

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

        if ( ! $this->isDebugMode() ) {
            return;
        }

        new AmazonAutoLinks_Debug_Event_DebugLog;
        new AmazonAutoLinks_Debug_Event_HTTPRequest;

        $this->___loadAdminComponents();


    }
        /**
         * Loads admin components.
         */
        private function ___loadAdminComponents() {
            add_action( 'load_' .  AmazonAutoLinks_Registry::$aAdminPages[ 'tool' ], array( $this, 'replyToLoadToolPage' ) );
        }
            /**
             * @param AmazonAutoLinks_AdminPageFramework $oFactory
             */
            public function replyToLoadToolPage( $oFactory ) {

                new AmazonAutoLinks_ToolAdminPage_Tool_DebugLog( $oFactory, $oFactory->oProp->getCurrentPageSlug() );

            }

}