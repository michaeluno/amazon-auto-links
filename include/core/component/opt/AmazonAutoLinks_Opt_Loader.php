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
 * Loads the component, Opt
 *
 * @since        4.7.0
 */
class AmazonAutoLinks_Opt_Loader {

    /**
     * @var   string
     * @since 4.7.0
     */
    static public $sDirPath;

    /**
     * @since 4.7.0
     */
    public function __construct() {

        self::$sDirPath = dirname( __FILE__ );

        $this->___loadAdminPages();

        new AmazonAutoLinks_Opt_Out_Loader;
        new AmazonAutoLinks_Opt_In_Loader;

        // Events
        add_action( 'aal_action_events', array( $this, 'replyToLoadEvents' ) );
        new AmazonAutoLinks_Opt_Event_Filter_SettingTabURL;

    }

    public function replyToLoadEvents() {
        // Ajax
        new AmazonAutoLinks_Opt_Event_Action_AdminFooter;   // [4.7.3]
        new AmazonAutoLinks_Opt_EventAjax_RatingPrompt;     // [4.6.6]
    }

    /**
     * @since 4.7.0
     */
    private function ___loadAdminPages() {
        if ( ! is_admin() ) {
            return;
        }
        new AmazonAutoLinks_Opt_Setting;
    }

}