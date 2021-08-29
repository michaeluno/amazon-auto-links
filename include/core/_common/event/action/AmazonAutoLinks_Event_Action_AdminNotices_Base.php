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
 * Shows notices to the user.
 *
 * @since        4.7.3
 */
abstract class AmazonAutoLinks_Event_Action_AdminNotices_Base extends AmazonAutoLinks_WPUtility {

    /**
     * Sets up hooks.
     * @since 4.7.3
     */
    public function __construct() {
        if ( ! is_admin() ) {
            return;
        }
        add_action( 'load_' . 'AmazonAutoLinks_AdminPage', array( $this, 'replyToDo' ) );
        add_action( 'load_' . 'AmazonAutoLinks_ToolAdminPage', array( $this, 'replyToDo' ) );

        // At the moment, when JavaScript is disabled, in these pages, the setting notice cannot be displayed.
        add_action( 'load_' . 'AmazonAutoLinks_PostType_Unit', array( $this, 'replyToDo' ) );
        add_action( 'load_' . 'AmazonAutoLinks_PostType_AutoInsert', array( $this, 'replyToDo' ) );
        add_action( 'load_' . 'AmazonAutoLinks_PostType_Button', array( $this, 'replyToDo' ) );
    }

    /**
     * @since 4.7.3
     * @param AmazonAutoLinks_AdminPageFramework_Factory $oFactory
     */
    public function replyToDo( $oFactory ) {}

}