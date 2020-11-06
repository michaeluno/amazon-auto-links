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
 * Logs debug outputs.
 *
 * @since        4.3.0
 */
class AmazonAutoLinks_Log_Debug_Event_DebugLog extends AmazonAutoLinks_Event_Error_Log {

    /**
     * The action hook name.
     * @var string
     */
    protected $_sActionName = 'aal_action_debug_log';

    /**
     * @return string   The name of the option record that stores the log.
     * @deprecated 4.4.0 No longer uses the options table but a file.
     */
    protected function _getOptionKey() {
        return AmazonAutoLinks_Registry::$aOptionKeys[ 'debug_log' ];
    }

    /**
     * @return AmazonAutoLinks_Log_VersatileFileManager_ErrorLog
     */
    protected function _getFileHandlerObject() {
        return new AmazonAutoLinks_Log_VersatileFileManager_DebugLog;
    }

}