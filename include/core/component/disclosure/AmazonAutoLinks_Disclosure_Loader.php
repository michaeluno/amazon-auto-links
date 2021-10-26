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
 * Loads the component, Affiliate Disclosure.
 *
 * This enables the ability to display affiliate disclosure.
 *
 * @since        4.7.0
 */
class AmazonAutoLinks_Disclosure_Loader {

    /**
     * @var string The https:// is needed as WordPress automatically prepend http:// or https://.
     * @since 4.7.0
     */
    static public $sDisclosureGUID = 'https://aal-affiliate-disclosure-page';

    /**
     * @var string
     */
    static public $sDirPath;

    public function __construct() {

        self::$sDirPath = dirname( __FILE__ );

        $this->___loadAdminPages();

        // Shortcode
        new AmazonAutoLinks_Disclosure_Shortcode;
        // Events
        new AmazonAutoLinks_Disclosure_Event_Action_DefaultDisclosurePage;
        new AmazonAutoLinks_Disclosure_Event_Filter_DisclaimerTooltip;
    }

    private function ___loadAdminPages() {
        if ( ! is_admin() ) {
            return;
        }
        new AmazonAutoLinks_Disclosure_Setting;
    }

}