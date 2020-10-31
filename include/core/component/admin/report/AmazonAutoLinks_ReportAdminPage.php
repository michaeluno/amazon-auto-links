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
 * Adds the `Report` page.
 * 
 * @since   4.4.0
 */
class AmazonAutoLinks_ReportAdminPage {

    public function __construct() {
        add_action( 'set_up_' .  'AmazonAutoLinks_AdminPage', array( $this, 'replyToSetUp' ) );
    }

    /**
     * Sets up admin pages.
     * @param   AmazonAutoLinks_AdminPageFramework $oFactory
     */
    public function replyToSetUp( $oFactory ) {
        new AmazonAutoLinks_ReportAdminPage_Report( $oFactory );
    }
   
}