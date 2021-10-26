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
 * A base class to create simple wizard form pages for unit creation.
 * 
 * @since 5
 */
abstract class AmazonAutoLinks_Unit_Admin_Page_UnitCreationWizard extends AmazonAutoLinks_SimpleWizardAdminPage {

    /**
     * Whether PA-API access is required or not.
     * @since 5.0.0
     * @var   boolean
     */
    public $bRequirePAAPI = false;

    /**
     * @since 5.0.0
     */
    public function load() {
        if ( $this->bRequirePAAPI ) {
            AmazonAutoLinks_Unit_Admin_Utility::checkAPIKeys( $this );
        }
    }

}