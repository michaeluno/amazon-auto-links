<?php
/**
 * Auto Amazon Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2022 Michael Uno
 */

/**
 * Manages creation and deletion of temporary files.
 *
 * Used to create lock files for event actions.
 *
 * @since 3.7.7
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
     * @since 4.3.4  Stores the file path.
     */
    protected $_sFilePath = '';

    /**
     * Tells whether the directory is accessible or not.
     * @var   boolean
     * @since 4.4.0
     */
    protected $_bWritable = false;

    /**
     * Sets up properties and create a temp directory.
     *
     * @param string  $sIdentifier      A string to identify the lock file.
     * @param integer $iTimeout         Timeout in seconds.
     * @param string  $sFileNamePrefix  A prefix to prepend to the file name.
     */
    public function __construct( $sIdentifier, $iTimeout=30, $sFileNamePrefix='AALTemp_' ) {
        $this->_sIdentifier        = $sIdentifier;
        $this->_sTempDirPath       = $this->getDirectoryPath();
        $this->_bWritable          = AmazonAutoLinks_Utility::getDirectoryCreatedRecursive( $this->_sTempDirPath, $this->_getBaseTemporaryDirectoryPath() );
        $this->_iTimeout           = $iTimeout;
        $this->_sFileNamePrefix    = $sFileNamePrefix;
        $this->_sFilePath          = $this->_getFilePath();
    }

    /**
     * @remark Consider the file resides in the server's shared temporary directory.
     * @return string
     */
    protected function _getFilePath() {
        return $this->_sTempDirPath . '/'
            . $this->_sFileNamePrefix . md5(site_url() . $this->_sIdentifier )
            . '.txt';
    }

    /**
     * @since  5.2.2  Added for debugging.
     * @return string
     */
    public function getFilePath() {
        return $this->_sFilePath;
    }

    /**
     * @since  4.3.5
     * @since  4.4.0  Changed the visibility from protected to public.
     * @return string
     */
    public function getDirectoryPath() {
        return $this->_getBaseTemporaryDirectoryPath() . '/ephemeral';
    }

    /**
     * @remark This is needed to apply proper CHMOD to directories between the base directory and the subject directory.
     * @remark Override this in an extended class.
     * @since  4.3.8
     * @since  5.2.8    Added the `aal_filter_temporary_directory_path` filter hook.
     * @return string
     */
    protected function _getBaseTemporaryDirectoryPath() {
        return untrailingslashit( apply_filters( 'aal_filter_temporary_directory_path', AmazonAutoLinks_Registry::getPluginSiteTempDirPath(), $this ) );
    }

    /**
     * Checks whether it is possible to access the directory and write files.
     * @param  string  $sPath   A file or directory path to check. If empty, the directory path set in the constructor will be applied.
     * @return boolean
     * @since  4.4.0
     */
    public function canWrite( $sPath='' ) {
        return $sPath
            ? is_writable( $sPath )
            : $this->_bWritable;
    }

    /**
     * Returns the file contents if it is not expired.
     * @since  4.3.4
     * @return false|string false on failure. Otherwise, the file content.
     */
    public function get() {
        return $this->isAlive()
            ? @file_get_contents( $this->_sFilePath )
            : '';
    }

    /**
     * @param  string $sText
     * @return false|integer The number of bytes that were written to the file, or false on failure.
     * @since  4.3.4
     */
    public function set( $sText ) {

        $_bWritable = $this->canWrite();

        // Check if the directory exists.
        $_sDirPath = dirname( $this->_sFilePath );
        if ( ! AmazonAutoLinks_Utility::doesDirectoryExist( $_sDirPath, false ) ) {
            $_bWritable = AmazonAutoLinks_Utility::getDirectoryCreatedRecursive( $_sDirPath, $this->_getBaseTemporaryDirectoryPath() );
        }

        // Without this check, file_put_contents() throws a warning.
        if ( ! $_bWritable ) {
            return false;
        }

        return file_put_contents( $this->_sFilePath, $sText, LOCK_EX );

    }

    /**
     * @return boolean
     * @since  4.3.5
     */
    public function delete() {
        if ( ! $this->_bWritable ) {
            return false;
        }
        if ( ! file_exists( $this->_sFilePath ) ) {
            return false;
        }
        return unlink( $this->_sFilePath );
    }

    /**
     * @param  integer  $iTime The time when the lock is unlocked. Default the current time.
     * @return boolean  true if locked; otherwise, false.
     * @since  4.3.5
     */
    public function lock( $iTime=0 ) {
        $_inTime = $iTime ? $iTime - $this->_iTimeout : null;
        return $this->___touch( $_inTime );
    }

    /**
     * @remark When called, if not locked, it automatically locks.
     * @return boolean
     */
    public function isLocked() {
        if ( $this->isAlive() ) {
            return true;
        }
        // At this point, the file does not exist or timed out
        $this->lock();
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
        $_iModifiedTime = $this->getModificationTime();
        if ( $_iModifiedTime + $this->_iTimeout >= time() ) {
            // the file is not timed-out yet
            return true; // alive
        }
        // lock file is timed-out
        return false;   // not alive

    }

    /**
     * @since  4.3.5
     * @return integer The timestamp of the modification time of the lock file.
     */
    public function getModificationTime() {
        clearstatcache( true, $this->_sFilePath );  // file functions such as filemtime() cache results.
        return ( integer ) filemtime( $this->_sFilePath );
    }

    /**
     * @since  4.3.5
     * @since  5.2.2   Changed the behavior not to create a lock file if not exist.
     * @return integer The timestamp of the unlock time. If the lock file does not exist, 0 is returned.
     */
    public function getUnlockTime() {
        if ( ! file_exists( $this->_sFilePath ) ) {
            // $this->lock(); @deprecated 5.2.2 If the file does not exist, returns 0. If the file needs to be locked, just call lock() in prior.
            return 0;
        }
        return $this->getModificationTime() + $this->_iTimeout;
    }


    /**
     * Touches a lock file.
     *
     * If a file does not exist, it will be crated.
     *
     * @since   3.7.7
     * @since   4.2.3  Changed the scope to private form public.
     * @since   4.2.3  Changed the ame from `set()` to `___set()`.
     * @since   4.3.4  Renamed from `___set()`.
     * @since   4.3.5  Added the `$inTime` and `$inAccessTime` parameters.
     * @param   null|integer $inTime       A timestamp of the time to set for the modification time.
     * @param   null|integer $inAccessTime A timestamp of the time to set for the last access time.
     * @return  boolean      true if touched or created. Otherwise, false.
     */
    private function ___touch( $inTime=null, $inAccessTime=null ) {

        if ( file_exists( $this->_sFilePath ) ) {
            // Update the modification time - passing null or 0 value erases the timestamp set to the file. So dropping null but 0 is accepted for the user to intentionally erase the value.
            $_aParams = array_filter( array( $this->_sFilePath, ( string ) $inTime, ( string ) $inAccessTime ), 'strlen' );
            return call_user_func_array( 'touch', $_aParams );
        }

        // At this point, the file does not exist so create it.
        return ( boolean ) $this->set( $this->_getDefaultContent() );

    }

    /**
     * @remark Override this method in an extended class.
     * @return string The default text to store in the file.
     * @since  4.3.5
     */
    protected function _getDefaultContent() {
        return ( string ) microtime( true );
    }

}