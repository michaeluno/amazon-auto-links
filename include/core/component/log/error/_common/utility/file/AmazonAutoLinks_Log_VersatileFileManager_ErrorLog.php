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
 * Handles error log files.
 *
 * @since   4.4.0
 */
class AmazonAutoLinks_Log_VersatileFileManager_ErrorLog extends AmazonAutoLinks_VersatileFileManager {

    protected $_sIdentifier     = 'ErrorLog';
    protected $_iTimeOut        = 2592000;      // 86400 * 30  (30 days)
    protected $_sFileNamePrefix = 'ErrorLog_';  // unused

    /**
     * @since 4.4.0
     */
    public function __construct() {
        parent::__construct( $this->_sIdentifier, $this->_iTimeOut, $this->_sFileNamePrefix );
    }

    /**
     * @since  4.4.0
     * @return string
     */
    public function getDirectoryPath() {
        return $this->_getBaseTemporaryDirectoryPath() . '/log';
    }

    /**
     * @return string
     * @since  4.4.0
     */
    protected function _getFilePath() {
        return $this->_sTempDirPath . '/' . $this->_sIdentifier . '.log';
    }

    /**
     * @return string  The file path generated based on the given/current time.
     * @since  4.4.0
     */
    public function getFilePath() {
        return $this->_sFilePath;
    }

    /**
     * @return array
     * @since  4.4.0
     */
    public function get() {
        $_bsContent = parent::get();
        if ( false === $_bsContent ) { // the file is locked by the system
            // @todo implement a fallback
            return array();
        }
        $_mValue = maybe_unserialize( $_bsContent );
        return is_array( $_mValue ) ? $_mValue : array();
    }

    /**
     * @param array $aLog
     * @return false|int
     */
    public function setLog( array $aLog ) {
        return parent::set( serialize( $aLog ) );
    }

}