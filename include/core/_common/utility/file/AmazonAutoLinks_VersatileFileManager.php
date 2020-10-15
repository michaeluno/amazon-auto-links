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
 * Manages creation and deletion of temporary files.
 *
 * Used to create lock files for event actions.
 *
 * @since   3.7.7
 */
class AmazonAutoLinks_VersatileFileManager {

    /**
     * @var string
     */
    protected $_sFileNamePrefix = '';

    protected $_sIdentifier = '';

    protected $_sTempDirPath = '';

    protected $_iTimeout;

    public function __construct( $sIdentifier, $iTimeout=30, $sFileNamePrefix='AALTemp_' ) {
        $this->_sIdentifier       = $sIdentifier;
        $this->_sTempDirPath      = AmazonAutoLinks_Registry::getPluginSiteTempDirPath();
        $this->_iTimeout          = $iTimeout;
        $this->_sFileNamePrefix   = $sFileNamePrefix;
    }

    /**
     * @return bool
     */
    public function isLocked() {
        if ( $this->___isAlive() ) {
            return true;
        }
        // At this point, the file does not exist or timed out
        $this->___set();
        return false;
    }

    /**
     * As WP_Shadow is implemented, occasionally the action may run in multiple separate page loads at almost the exact same time, one in wp-cron.php and index.php.
     * If that happens, the action runs twice.
     * This method ensures it only runs once by checking a lock file.
     * Checks 30 seconds.
     * @since   3.7.7
     * @since   4.2.3       Changed the name from `exist()` to `___isAlive()`
     * @return boolean  True if the file is not timed out; otherwise, false.
     */
    private function ___isAlive() {

        $_sLockFilePath = $this->___getActionLockFilePath();
        if ( ! file_exists( $_sLockFilePath ) ) {
            return false; // not alive (not created yet)
        }
        $_iModifiedTime  = ( integer ) filemtime( $_sLockFilePath );
        if ( $_iModifiedTime + $this->_iTimeout > time() ) {
            // the file is not timed-out yet
            return true; // alive
        }
        // lock file is timed-out
        return false;   // not alive

    }

    /**
     * Sets the lock file.
     * @since   3.7.7
     * @since   4.2.3   Changed the scope to private form public.
     * @since   4.2.3   Changed the ame from `set()` to `___set()`.
     */
    private function ___set() {

        if ( ! is_dir( $this->_sTempDirPath ) ) {
            mkdir( $this->_sTempDirPath, 0777, true );
        }
        $_sLockFilePath = $this->___getActionLockFilePath();
        if ( file_exists( $_sLockFilePath ) ) {
            // Update the modification time
            touch( $_sLockFilePath );
            return;
        }

        // At this point, the file does not exist so creat it.
        file_put_contents( $_sLockFilePath, microtime( true ), LOCK_EX );

    }
        /**
         * @remark Consider the file resides in the server's shared temporary directory.
         * @return string
         */
        private function ___getActionLockFilePath() {
            return $this->_sTempDirPath . '/'
                . $this->_sFileNamePrefix . md5(site_url() . $this->_sIdentifier )
                . '.txt';
        }

}