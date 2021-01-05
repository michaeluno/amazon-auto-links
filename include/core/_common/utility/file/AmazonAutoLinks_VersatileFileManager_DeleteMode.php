<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 */

/**
 * The delete mode version of versatile file manager.
 *
 * @since   4.2.4
 */
class AmazonAutoLinks_VersatileFileManager_DeleteMode extends AmazonAutoLinks_VersatileFileManager {

    /**
     * Returns the file contents if it is not expired.
     * @since  4.3.4
     */
    public function get() {
        $_sFileContent = parent::get();
        if ( ! $this->isAlive() ) {
            add_action( 'shutdown', array( $this, 'replyToCleanFiles' ) );
        }
        return $_sFileContent;
    }

    /**
     * @return bool
     */
    public function isLocked() {
        if ( parent::isLocked() ) {
            return true;
        }
        add_action( 'shutdown', array( $this, 'replyToCleanFiles' ) );
        return false;
    }

    /**
     * Cleans up left files with the set timeout.
     *
     * This is to cover cases of more than one simultaneously spawned actions;
     * at the time that the one finishes immediately and after that the other starts, the other one thinks it is not locked
     * and does the same routine again, which is redundant and consumes the server resource.
     */
    public function replyToCleanFiles() {
        $_sPattern = $this->_sTempDirPath . DIRECTORY_SEPARATOR
            . $this->_sFileNamePrefix . "*.txt";
        foreach ( glob( $_sPattern ) as $_sFilePath ) {
            if ( time() - filectime( $_sFilePath ) > $this->_iTimeout ) {
                unlink( $_sFilePath );
            }
        }
    }

}