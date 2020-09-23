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
        $_aData = isset( $aArguments[ 'body' ] )
            ? array( 'body' => $aArguments[ 'body' ] )
            : array();
        $_sStackTrace = in_array( $sRequestType, array( 'api' ), true )
            ? AmazonAutoLinks_Debug::getStackTrace()
            : '';
        do_action( 'aal_action_debug_log', 'HTTP_REQUEST::' . $sRequestType, $sURL, $_aData, current_filter(), $_sStackTrace );
    }

}