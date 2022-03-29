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
 * Adds admin pages for the contextual unit type.
 * 
 * @since 3.5.0
 */
final class AmazonAutoLinks_ContextualUnitAdminPage extends AmazonAutoLinks_Unit_Admin_Page_UnitCreationWizard {

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
        new AmazonAutoLinks_ContextualUnitAdminPage_ContextualUnit( $this );
    }

    /**
     * @since  5.1.1
     */
    public function load() {
        AmazonAutoLinks_Unit_Admin_Utility::checkAssociatesIDAndPAAPIKeys( $this );
    }


}