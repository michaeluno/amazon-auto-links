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
    private $___sFileNamePrefix = '';

    private $___sIdentifier = '';

    private $___sTempDirPath = '';

    private $___iTimeout;

    public function __construct( $sIdentifier, $iTimeout=30, $sFileNamePrefix='AALTemp_' ) {
        $this->___sIdentifier       = $sIdentifier;
        $this->___sTempDirPath      = trailingslashit( sys_get_temp_dir() ) . AmazonAutoLinks_Registry::$sTempDirName . '/' . md5( site_url() );
        $this->___iTimeout          = $iTimeout;
        $this->___sFileNamePrefix   = $sFileNamePrefix;
    }

    /**
     * @return bool
     */
    public function isLocked() {
        if ( $this->exist() ) {
            return true;
        }
        $this->set();
        return false;
    }

    /**
     * As WP_Shadow is implemented, occasionally the action may run in multiple separate page loads at almost the exact same time, one in wp-cron.php and index.php.
     * If that happens, the action runs twice.
     * This method ensures it only runs once by checking a lock file.
     * Checks 30 seconds.
     * @since   3.7.7
     * @return boolean  True if the file is not timed out; otherwise, false.
     */
    public function exist() {

        $_sLockFilePath = $this->___getActionLockFilePath();
        if ( ! file_exists( $_sLockFilePath ) ) {
            return false;
        }
        $_iModifiedTime  = ( integer ) filemtime( $_sLockFilePath );
        if ( $_iModifiedTime + $this->___iTimeout > time() ) {
            // the file is not timed-out yet
            return true;
        }
        // lock file is timed-out
        return false;

    }

    public function set() {

        if ( ! is_dir( $this->___sTempDirPath ) ) {
            mkdir( $this->___sTempDirPath, 0777, true );
        }
        file_put_contents(
            $this->___getActionLockFilePath( ),
            microtime( true ),
            LOCK_EX
        );
        // Schedule cleaning up files
        add_action( 'shutdown', array( $this, 'replyToCleanFiles' ) );

    }
        /**
         * @remark Consider the file resides in the server's shared temporary directory.
         * @return string
         */
        private function ___getActionLockFilePath() {
            return $this->___sTempDirPath . '/'
                . $this->___sFileNamePrefix . md5(site_url() . $this->___sIdentifier )
                . '.txt';
        }
        /**
         * The created file will not be deleted because in the situation there are two simultaneously spawned actions,
         * at the time that the one finishes immediately and after that the other starts, the other one thinks it is not locked
         * and does the same routine again, which is redundant and consumes the server resource.
         * So here just cleans up left files with the set timeout.
         */
        public function replyToCleanFiles() {
            $_sPattern = $this->___sTempDirPath . DIRECTORY_SEPARATOR
                . $this->___sFileNamePrefix . "*.txt";
            foreach ( glob( $_sPattern ) as $_sFilePath ) {
                if ( time() - filectime( $_sFilePath ) > $this->___iTimeout ) {
                    unlink( $_sFilePath );
                }
            }
        }

}