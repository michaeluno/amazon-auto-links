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
 * Adds the `Help` page.
 * 
 * @since 3
 */
class AmazonAutoLinks_HelpAdminPage  {

    public function __construct() {
        add_action( 'set_up_' .  'AmazonAutoLinks_AdminPage', array( $this, 'replyToSetUp' ) );
    }

    /**
     * Sets up admin pages.
     * @param AmazonAutoLinks_AdminPageFramework $oFactory
     */
    public function replyToSetUp( $oFactory ) {
        new AmazonAutoLinks_HelpAdminPage_Help( $oFactory );
    }
   
}