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
 * The delete mode version of versatile file manager.
 *
 * @since   4.2.4
 */
class AmazonAutoLinks_VersatileFileManager_DeleteMode extends AmazonAutoLinks_VersatileFileManager {

    /**
     * @return bool
     */
    public function isLocked() {
        $_bLocked = parent::isLocked();
        if ( $_bLocked ) {
            return true;
        }
        add_action( 'shutdown', array( $this, 'replyToCleanFiles' ) );
        return false;
    }

        /**
         * The created file will not be deleted because in the situation there are two simultaneously spawned actions,
         * at the time that the one finishes immediately and after that the other starts, the other one thinks it is not locked
         * and does the same routine again, which is redundant and consumes the server resource.
         * So here just cleans up left files with the set timeout.
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