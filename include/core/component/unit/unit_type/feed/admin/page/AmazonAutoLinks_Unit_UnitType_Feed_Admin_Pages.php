<?php
/**
 * Auto Amazon Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2022 Michael Uno
 */


/**
 * Adds a plugin admin page.
 * 
 * @since 4.0.0
 * @since 5.0.0 Renamed from `AmazonAutoLinks_FeedUnitAdminPage`.
 */
final class AmazonAutoLinks_Unit_UnitType_Feed_Admin_Pages extends AmazonAutoLinks_Unit_Admin_Page_UnitCreationWizard {

    /**
     * @remark Added for extended classes.
     * @since  4.0.0
     */
    protected function _addPages() {
        new AmazonAutoLinks_Unit_UnitType_Feed_Admin_Page_FeedUnit( $this );
    }

}