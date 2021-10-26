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
 * Logs when plugin cron is triggered.
 *
 * @since        4.3.0
 */
class AmazonAutoLinks_Log_Debug_Event_PluginCron extends AmazonAutoLinks_PluginUtility {

    /**
     * Performs necessary set-ups.
     */
    public function __construct() {
        add_action( 'aal_action_do_plugin_cron', array( $this, 'replyToLog' ) );
    }

    public function replyToLog( $aWPCronTasks ) {
        $_aData = array(
            'tasks'   => $aWPCronTasks,
        );
        do_action( 'aal_action_debug_log', 'PLUGIN_CRON', $this->getCurrentURL(), $_aData, current_filter() );
    }

}