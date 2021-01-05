<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 */

/**
 * Adds the `Report` admin page.
 * 
 * @since   4.4.0
 */
class AmazonAutoLinks_ReportAdminPage {

    /**
     * Sets up hooks.
     */
    public function __construct() {
        add_action( 'set_up_' .  'AmazonAutoLinks_AdminPage', array( $this, 'replyToSetUp' ) );
    }

    /**
     * Sets up admin pages.
     * @param    AmazonAutoLinks_AdminPageFramework $oFactory
     * @callback add_action() set_up_{class name}
     */
    public function replyToSetUp( $oFactory ) {
        new AmazonAutoLinks_ReportAdminPage_Report( $oFactory );
    }
   
}