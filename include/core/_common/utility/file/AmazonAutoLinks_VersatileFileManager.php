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

    /**
     * @var   string
     * @sicne 4.3.4  Stores the file path.
     */
    protected $_sFilePath = '';

    /**
     * Sets up properties and create a temp directory.
     *
     * @param string  $sIdentifier      A string to identify the lock file.
     * @param integer $iTimeout         Timeout in seconds.
     * @param string  $sFileNamePrefix  A prefix to prepend to the file name.
     */
    public function __construct( $sIdentifier, $iTimeout=30, $sFileNamePrefix='AALTemp_' ) {
        $this->_sIdentifier       = $sIdentifier;
        $this->_sTempDirPath      = AmazonAutoLinks_Registry::getPluginSiteTempDirPath() . '/versatile';
        $this->_iTimeout          = $iTimeout;
        $this->_sFileNamePrefix   = $sFileNamePrefix;
        $this->_sFilePath         = $this->___getFilePath();
        if ( ! is_dir( $this->_sTempDirPath ) ) {
            mkdir( $this->_sTempDirPath, 0777, true );
        }
    }
        /**
         * @remark Consider the file resides in the server's shared temporary directory.
         * @return string
         */
        private function ___getFilePath() {
            return $this->_sTempDirPath . '/'
                . $this->_sFileNamePrefix . md5(site_url() . $this->_sIdentifier )
                . '.txt';
        }

    /**
     * Returns the file contents if it is not expired.
     * @since  4.3.4
     */
    public function get() {
        return $this->isAlive()
            ? file_get_contents( $this->_sFilePath )
            : '';
    }

    /**
     * @param  string $sText
     * @return false|int
     * @since  4.3.4
     */
    public function set( $sText ) {
        return file_put_contents( $this->_sFilePath, $sText, LOCK_EX );
    }

    /**
     * @return boolean
     */
    public function isLocked() {
        if ( $this->isAlive() ) {
            return true;
        }
        // At this point, the file does not exist or timed out
        $this->___touch();
        return false;
    }

    /**
     * Checks if the lock file is expired or not.
     *
     * As WP_Shadow is implemented, occasionally the action may run in multiple separate page loads at almost the exact same time, one in wp-cron.php and index.php.
     * If that happens, the action runs twice.
     *
     * This method ensures that it only runs once by checking a lock file.
     *
     * @since  3.7.7
     * @since  4.2.3    Changed the name from `exist()` to `___isAlive()`
     * @since  4.3.4    Changed the scope to public and renamed from `___isAlive()`.
     * @return boolean  `true` if the file is not timed out; otherwise, `false`.
     */
    public function isAlive() {

        if ( ! file_exists( $this->_sFilePath ) ) {
            return false; // not alive (not created yet)
        }
        $_iModifiedTime  = ( integer ) filemtime( $this->_sFilePath );
        if ( $_iModifiedTime + $this->_iTimeout > time() ) {
            // the file is not timed-out yet
            return true; // alive
        }
        // lock file is timed-out
        return false;   // not alive

    }

    /**
     * Touches a lock file.
     *
     * If a file does not exist, it will be crated.
     *
     * @since   3.7.7
     * @since   4.2.3   Changed the scope to private form public.
     * @since   4.2.3   Changed the ame from `set()` to `___set()`.
     * @since   4.3.4   Renamed from `___set()`.
     */
    private function ___touch() {

        if ( file_exists( $this->_sFilePath ) ) {
            // Update the modification time
            touch( $this->_sFilePath );
            return;
        }

        // At this point, the file does not exist so creat it.
        $this->set( microtime( true ) );

    }

}