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
 * @since  5.0.0
 */
class AmazonAutoLinks_Unit_UnitType_AdminPages_ad_widget_search extends AmazonAutoLinks_Unit_Admin_Page_UnitCreationWizard {

    /**
     * Whether PA-API access is required or not.
     * @since 5.0.0
     * @var   boolean
     */
    public $bRequirePAAPI = true;

    /**
     * @since  5.0.0
     */
    protected function _addPages() {
        new AmazonAutoLinks_Unit_UnitType_AdminPages_Page_ad_widget_search( $this );
    }

    /**
     * @since  5.0.0
     */
    public function load() {
        AmazonAutoLinks_Unit_Admin_Utility::checkAssociatesIDAndPAAPIKeys( $this );
    }

}