<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 */

/**
 * Loads the component, Geo-targeting
 *
 * This enables the feature of Geo-targeting for links of Amazon stores.
 *
 * @since        4.6.0
 */
class AmazonAutoLinks_Geotargeting_Loader {

    /**
     * @var string
     */
    static public $sDirPath;

    public function __construct() {

        self::$sDirPath = dirname( __FILE__ );

        $this->___loadAdminPages();

        $_oOption = AmazonAutoLinks_Option::getInstance();
        if ( ! $_oOption->get( array( 'geotargeting', 'enable' ) ) ) {
            return;
        }

        // Resources
        new AmazonAutoLinks_Geotargeting_Resource;

        // Events
        // new AmazonAutoLinks_Geotargeting_EventAjax_GeolocationResolver; // @deprecated 4.6.0
        new AmazonAutoLinks_Geotargeting_EventQuery_SearchRedirect;

    }

    private function ___loadAdminPages() {
        if ( ! is_admin() ) {
            return;
        }
        new AmazonAutoLinks_Geotargeting_Setting;
    }

}