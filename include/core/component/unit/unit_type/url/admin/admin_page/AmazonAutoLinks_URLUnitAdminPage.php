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
 * Deals with the plugin admin pages.
 * 
 * @since  3.2.0
 * @remark This class is not final as being extended by the feed unit type admin page class.
 * @see    AmazonAutoLinks_FeedUnitAdminPage
 */
class AmazonAutoLinks_URLUnitAdminPage extends AmazonAutoLinks_SimpleWizardAdminPage {

    /**
     * Adds admin pages.
     * @since  4.0.0
     */
    protected function _addPages() {
        new AmazonAutoLinks_URLUnitAdminPage_URLUnit( $this );
    }

    public function load() {
        AmazonAutoLinks_Unit_Admin_Utility::checkAPIKeys( $this );
    }

}