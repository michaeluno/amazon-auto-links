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
 * Loads the PA-API request counter sub-component.
 *  
 * @since       4.4.0
*/
class AmazonAutoLinks_Unit_PAAPIRequestCounter_Loader extends AmazonAutoLinks_PluginUtility {

    /**
     * @var   string
     * @since 4.4.0
     */
    static public $sDirPath;

    /**
     * @since 4.4.0
     */
    public function __construct() {

        self::$sDirPath = dirname( __FILE__ );

        if ( is_admin() ) {
            $this->___loadAdminComponents();
        }

        if ( ! $this->___shouldProceed() ) {
            return;
        }
        add_action( 'aal_action_events', array( $this, 'replyToLoadEvents' ) );

    }

    /**
     * @remark The admin component should be loaded. Otherwise, the option to enable it won't appear.
     * @return boolean
     * @since  4.4.0
     */
    private function ___shouldProceed() {
        $_oOption = AmazonAutoLinks_Option::getInstance();
        return ( boolean ) $_oOption->get( 'paapi_request_counts', 'enable' );
    }

    /**
     * Loads admin components.
     * @since 4.4.0
     */
    private function ___loadAdminComponents() {
        add_action( 'load_' .  AmazonAutoLinks_Registry::$aAdminPages[ 'report' ], array( $this, 'replyToLoadToolPage' ) );
    }
            /**
             * Adds tabs.
             * @param AmazonAutoLinks_AdminPageFramework $oFactory
             */
            public function replyToLoadToolPage( $oFactory ) {

                new AmazonAutoLinks_Unit_PAAPIRequestCounter_AdminPage_Tab_RequestCount( $oFactory, $oFactory->oProp->getCurrentPageSlug() );

            }
    
    /**
     * @callback add_action() aal_action_events
     * @since    4.4.0
     */
    public function replyToLoadEvents() {
        new AmazonAutoLinks_Unit_PAAPIRequestCounter_Event_Filter_Counter;
        new AmazonAutoLinks_Unit_PAAPIRequestCounter_Event_Ajax_LocaleChange;
        new AmazonAutoLinks_Unit_PAAPIRequestCounter_Event_Action_SaveLog;
        new AmazonAutoLinks_Unit_PAAPIRequestCounter_Event_Action_CleanLog;
        new AmazonAutoLinks_Unit_PAAPIRequestCounter_Event_Action_ExportLog;
        new AmazonAutoLinks_Unit_PAAPIRequestCounter_Event_Action_ImportLog;
    }

}