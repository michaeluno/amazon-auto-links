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
 * Logs when an http requests are made.
 *
 * @since        4.3.0
 */
class AmazonAutoLinks_Log_Debug_Event_HTTPRequest extends AmazonAutoLinks_PluginUtility {

    /**
     * Performs necessary set-ups.
     */
    public function __construct() {
        add_action( 'aal_action_http_remote_get', array( $this, 'replyToLog' ), 10, 3 );
    }

    public function replyToLog( $sURL, $aArguments, $sRequestType ) {
        $_sStackTrace = in_array( $sRequestType, array( 'api' ), true )
            ? AmazonAutoLinks_Debug::getStackTrace()
            : '';
        do_action( 'aal_action_debug_log', 'HTTP_REQUEST::' . $sRequestType, $sURL, $aArguments, current_filter(), $_sStackTrace );
    }

}