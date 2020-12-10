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
    protected $_aLog = array();

    /**
     * Override in an extended class.
     * @var string
     * @deprecated 4.4.0
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
    protected $_iLogLength = 100;

    public function __construct() {

        // Singleton
        if ( $this->hasBeenCalled( get_class( $this ) . '::' . __METHOD__ ) ) {
            return;
        }

        // $this->_sOptionKey = $this->_getOptionKey();

        add_action( $this->_sActionName, array( $this, 'replyToLogErrors' ), 10, 5 );

    }

    /**
     * @return string   The name of the option record that stores the log.
     * @deprecated 4.4.0
     */
    protected function _getOptionKey() {
        return AmazonAutoLinks_Registry::$aOptionKeys[ 'error_log' ];
    }

    /**
     * Called when an error is detected and AmazonAutoLinks_Error is instantiated.
     *
     * @param    integer|string $isCode
     * @param    string         $sErrorMessage
     * @param    array          $aData
     * @param    string         $sCurrentHook
     * @param    boolean|string $bsStackTrace
     * @since    4.3.0          Added the `$sCurrentHook` parameter.
     * @callback add_action()   aal_action_error
     * @since    4.2.0
     */
    public function replyToLogErrors( $isCode, $sErrorMessage, $aData, $sCurrentHook, $bsStackTrace='' ) {

        // @deprecated 4.4.0 No longer uses the options table but a file.
        /*if ( ! $this->_sOptionKey ) {
            AmazonAutoLinks_Debug::log(
                'The option key is not set.' . PHP_EOL . AmazonAutoLinks_Debug::getStackTrace(),
                WP_CONTENT_DIR . '/aal_errors.log'
            );
            return;
        }*/

        $sErrorMessage = trim( $sErrorMessage );
        $sErrorMessage = $sErrorMessage
            ? $sErrorMessage
            : '(no error message)';
        $_sCode        = trim( $isCode );
        $sErrorMessage = $_sCode
            ? $_sCode . ': ' . $sErrorMessage
            : $sErrorMessage;
        $_sStackTrace = $bsStackTrace === true
            ? AmazonAutoLinks_Debug::getStackTrace( 2 )
            : $bsStackTrace;
        $this->___setErrorLogItem( $sErrorMessage, $aData, $sCurrentHook, $_sStackTrace );

    }

        /**
         * @param string $sMessage
         * @param array  $aExtra
         * @param string $sCurrentHook
         * @param string $sStackTrace
         * @since 4.0.0
         * @since 4.3.0  Changed the scope to `private` from `public`.
         * @since 4.3.0  Added the `$sCurrentHook` parameter.
         */
        private function ___setErrorLogItem( $sMessage, array $aExtra=array(), $sCurrentHook='', $sStackTrace='' ) {

            // For the first time of calling this method in a page
            if ( empty( $this->_aLog ) ) {
                add_action( 'shutdown', array( $this, 'replyToUpdateErrorLog' ) );
            }
            $_iMicroTime = microtime( true );
            $_iIndex     = ( integer ) ( $_iMicroTime * 1000 ); // as the float part will be omitted when assigned as a key, multiple by 1000
            $this->_aLog[ $_iIndex ] = array(
                // required keys
                'time'           => $_iMicroTime,
                'message'        => $sMessage,
                'current_url'    => $this->getCurrentURL(),
                'page_load_id'   => $this->getPageLoadID(),
                'current_hook'   => $sCurrentHook,
                'stack_trace'    => $sStackTrace,
            ) + $aExtra;

        }

    /**
     * Updates the error log.
     * @callback    action      shutdown
     */
    public function replyToUpdateErrorLog() {

        if ( empty( $this->_aLog ) ) {
            return;
        }
        // @deprecated 4.4.0
        // $this->_saveInOptionsTable( $this->_sOptionKey );
        $this->___saveInFile();
    }
        private function ___saveInFile() {
            $_oFile = $this->_getFileHandlerObject();
            $_aLog  = $_oFile->get();
            $_aLog  = $_aLog + $this->_aLog;
            $_aLog  = array_slice( $_aLog, $this->_iLogLength * -1, $this->_iLogLength, true );
            $_oFile->setLog( $_aLog );
        }

        /**
         * @return AmazonAutoLinks_Log_VersatileFileManager_ErrorLog
         */
        protected function _getFileHandlerObject() {
            return new AmazonAutoLinks_Log_VersatileFileManager_ErrorLog;
        }
        /**
         * Stores the log in the options table.
         * @param string $sOptionKey
         * @deprecated 4.4.0
         */
        /*protected function _saveInOptionsTable( $sOptionKey ) {
            $_aLog  = $this->getAsArray( get_option( $sOptionKey, array() ) );
            $_aLog  = $_aLog + $this->_aLog;

            // Keep up to latest 300 items
            $_aLog = array_slice( $_aLog, $this->_iLogLength * -1, $this->_iLogLength, true );
            update_option( $sOptionKey, $_aLog );
        }*/

}