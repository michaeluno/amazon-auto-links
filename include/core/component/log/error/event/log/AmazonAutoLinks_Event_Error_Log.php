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
class AmazonAutoLinks_Event_Error_Log extends AmazonAutoLinks_PluginUtility {

    /**
     * Stores error logs;
     * @var array
     */
    protected $_aErrorLog = array();

    /**
     * Override in an extended class.
     * @var string
     */
    protected $_sOptionKey = '';

    /**
     * The action hook name.
     * @var string
     */
    protected $_sActionName = 'aal_action_error';

    /**
     * Sets how long the log entries are.
     * @var integer
     */
    protected $_iLogLength = 300;

    public function __construct() {

        // Singleton
        if ( $this->hasBeenCalled( get_class( $this ) . '::' . __METHOD__ ) ) {
            return;
        }

        $this->_sOptionKey = $this->_getOptionKey();

        add_action( $this->_sActionName, array( $this, 'replyToLogErrors' ), 10, 4 );

    }

    /**
     * @return string   The name of the option record that stores the log.
     */
    protected function _getOptionKey() {
        return AmazonAutoLinks_Registry::$aOptionKeys[ 'error_log' ];
    }

    /**
     * Called when an error is detected and AmazonAutoLinks_Error is instantiated.
     *
     * @return      void
     * @since       4.2.0
     * @since       4.3.0       Added the `$sCurrentHook` parameter.
     * @callback    action      aal_action_error
     */
    public function replyToLogErrors( $isCode, $sErrorMessage, $aData, $sCurrentHook ) {

        if ( ! $this->_sOptionKey ) {
            AmazonAutoLinks_Debug::log( 'The option key is not set.', WP_CONTENT_DIR . '/aal_errors.log' );
            return;
        }

        $sErrorMessage = trim( $sErrorMessage );
        $sErrorMessage = $sErrorMessage
            ? $sErrorMessage
            : '(no error message)';
        $_sCode        = trim( $isCode );
        $sErrorMessage = $_sCode
            ? $_sCode . ': ' . $sErrorMessage
            : $sErrorMessage;
        $this->___setErrorLogItem( $sErrorMessage, $aData, $sCurrentHook );

    }
        /**
         * @param string the error message
         * @param array $aExtra
         * @since   4.0.0
         * @since   4.3.0   Changed the scope to `private` from `public`.
         * @since   4.3.0   Added the `$sCurrentHook` parameter.
         */
        private function ___setErrorLogItem( $sMessage, array $aExtra=array(), $sCurrentHook='' ) {

            // For the first time of calling this method in a page
            if ( empty( $this->_aErrorLog ) ) {
                add_action( 'shutdown', array( $this, 'replyToUpdateErrorLog' ) );
            }
            $_iMicroTime = ( integer ) ( microtime( true ) * 1000 ); // as the float part will be omitted when assigned as a key, multiple by 1000
            $this->_aErrorLog[ $_iMicroTime ] = array(
                // required keys
                'time'           => time(),
                'message'        => $sMessage,
                'current_url'    => $this->getCurrentURL(),
                'page_load_id'   => $this->getPageLoadID(),
                'current_hook'   => $sCurrentHook,
            ) + $aExtra;

        }

    /**
     * Updates the error log.
     * @callback    action      shutdown
     */
    public function replyToUpdateErrorLog() {

        if ( empty( $this->_aErrorLog ) ) {
            return;
        }
        $_aErrorLog  = $this->getAsArray( get_option( $this->_sOptionKey, array() ) );
        $_aErrorLog  = $_aErrorLog + $this->_aErrorLog;

        // Keep up to latest 300 items
        $_aErrorLog = array_slice( $_aErrorLog, $this->_iLogLength * -1, $this->_iLogLength, true );
        update_option( $this->_sOptionKey, $_aErrorLog );

    }

}