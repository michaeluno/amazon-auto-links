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
 * Logs errors of Product Advertising API responses.
 *
 * @since        4.0.0
 */
class AmazonAutoLinks_Event_ErrorLog extends AmazonAutoLinks_PluginUtility {

    /**
     * Stores error logs;
     * @var array
     */
    static public $aErrorLog = array();


    /**
     * @param string the error message
     * @param array $aExtra
     * @since   4.0.0
     */
    static public function setErrorLogItem( $sMessage, array $aExtra=array() ) {

        // For the first time of calling this method in a page
        if ( empty( self::$aErrorLog ) ) {
            add_action( 'shutdown', array( __CLASS__, 'replyToUpdateErrorLog' ) );
        }
        $_iMicrotime = ( integer ) ( microtime( true ) * 1000 ); // as the float part will be omitted when assigned as a key, multiple by 1000
        self::$aErrorLog[ $_iMicrotime ] = array(
            // required keys
            'time'           => time(),
            'message'        => $sMessage,
            'current_url'    => self::getCurrentURL(),
        ) + $aExtra;

    }

    /**
     * Updates the error log.
     * @callback    action      shutdown
     */
    static public function replyToUpdateErrorLog() {
        if ( empty( self::$aErrorLog ) ) {
            return;
        }
        $_sOptionKey = AmazonAutoLinks_Registry::$aOptionKeys[ 'error_log' ];
        $_aErrorLog  = self::getAsArray( get_option( $_sOptionKey, array() ) );
        $_aErrorLog  = $_aErrorLog + self::$aErrorLog;

        // Keep up to latest 300 items
        $_aErrorLog = array_slice( $_aErrorLog, -300, 300, true );
        update_option( $_sOptionKey, $_aErrorLog );

    }

}