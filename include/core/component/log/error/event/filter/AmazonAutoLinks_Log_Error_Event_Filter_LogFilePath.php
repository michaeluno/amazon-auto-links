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
 * Allows other components to retrieve the log file path through a filter hook.
 *
 * @since        4.7.1
 */
class AmazonAutoLinks_Log_Error_Event_Filter_LogFilePath extends AmazonAutoLinks_PluginUtility {

    /**
     * @var string
     * @since 4.7.1
     */
    static public $sLogFilePath;

    /**
     * Sets up properties and hooks.
     */
    public function __construct() {
        add_filter( 'aal_filter_log_error_log_file_path', array( $this, 'replyToGetLogFilePath' ) );
    }

    public function replyToGetLogFilePath( $sPath ) {
        if ( isset( self::$sLogFilePath ) ) {
            return self::$sLogFilePath;
        }
        $_oFileManager =  new AmazonAutoLinks_Log_VersatileFileManager_ErrorLog;
        self::$sLogFilePath = $_oFileManager->getFilePath();
        return self::$sLogFilePath;
    }

}