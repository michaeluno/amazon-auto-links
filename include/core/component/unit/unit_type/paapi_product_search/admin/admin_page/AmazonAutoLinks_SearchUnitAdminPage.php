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
 * Deals with the plugin admin pages.
 * 
 * @since 3
 */
final class AmazonAutoLinks_SearchUnitAdminPage extends AmazonAutoLinks_Unit_Admin_Page_UnitCreationWizard {

    /**
     * Whether PA-API access is required or not.
     * @since 5.0.0
     * @var   boolean
     */
    public $bRequirePAAPI = true;

    /**
     * Adds admin pages.
     * @since 5.0.0
     */
    protected function _addPages() {
        new AmazonAutoLinks_SearchUnitAdminPage_SearchUnit( $this );
    }

    /**
     * Page styling
     * @since 3
     */
    public function doPageSettings() {
        $this->setPageTitleVisibility( false ); // disable the page title of a specific page.
        $this->setPluginSettingsLinkLabel( '' ); // pass an empty string to disable it.
    }
        
}