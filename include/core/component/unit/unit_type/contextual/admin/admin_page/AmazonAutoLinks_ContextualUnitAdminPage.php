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
 * Adds admin pages for the contextual unit type.
 * 
 * @since 3.5.0
 */
final class AmazonAutoLinks_ContextualUnitAdminPage extends AmazonAutoLinks_SimpleWizardAdminPage {

    /**
     * Adds admin pages.
     * @since 5.0.0
     */
    protected function _addPages() {
        new AmazonAutoLinks_ContextualUnitAdminPage_ContextualUnit( $this );
    }
        
}