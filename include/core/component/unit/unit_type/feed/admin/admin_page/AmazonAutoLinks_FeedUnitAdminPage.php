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
 * Adds a plugin admin page.
 * 
 * @since       4.0.0
 */
final class AmazonAutoLinks_FeedUnitAdminPage extends AmazonAutoLinks_URLUnitAdminPage {

    /**
     * @remark      Added for extended classes.
     * @since       4.0.0
     */
    protected function _addPages() {
        new AmazonAutoLinks_FeedUnitAdminPage_FeedUnit( $this );
    }

    /**
     * @remark  Do not perform API key checks.
     */
    public function load() {}

}