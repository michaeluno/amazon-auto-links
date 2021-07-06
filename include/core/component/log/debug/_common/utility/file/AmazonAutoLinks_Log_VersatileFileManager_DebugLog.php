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
 * Handles debug log files.
 *
 * @since   4.4.0
 */
class AmazonAutoLinks_Log_VersatileFileManager_DebugLog extends AmazonAutoLinks_Log_VersatileFileManager_ErrorLog {

    protected $_sIdentifier     = 'DebugLog';
    protected $_sFileNamePrefix = 'DebugLog_';

}