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
 * Deals with the plugin admin pages.
 * 
 * @since  3.2.0
 * @since  5.0.0 Re-added the final scope as the feed unit type no longer extends this class.
 */
final class AmazonAutoLinks_URLUnitAdminPage extends AmazonAutoLinks_Unit_Admin_Page_UnitCreationWizard {

    /**
     * Whether PA-API access is required or not.
     * @since 5.0.0
     * @var   boolean
     */
    public $bRequirePAAPI = true;

    /**
     * Adds admin pages.
     * @since  4.0.0
     */
    protected function _addPages() {
        new AmazonAutoLinks_URLUnitAdminPage_URLUnit( $this );
    }

    /**
     * @since  5.1.1
     */
    public function load() {
        AmazonAutoLinks_Unit_Admin_Utility::checkAssociatesIDAndPAAPIKeys( $this );
    }

}