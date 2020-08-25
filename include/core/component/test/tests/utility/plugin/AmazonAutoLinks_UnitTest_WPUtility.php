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
 * Utility tests.
 *  
 * @package     Amazon Auto Links
 * @since       4.3.0
*/
class AmazonAutoLinks_UnitTest_WPUtility extends AmazonAutoLinks_UnitTest_Base {

    /**
     * @purpose Checks if files exist;
     * @return bool
     */
    public function test_doFilesExist() {
        return AmazonAutoLinks_WPUtility::doFilesExist(
            array(
                __FILE__,
                AmazonAutoLinks_Test_Loader::$sDirPath,
            )
        );
    }

    /**
     * @purpose Checks if plugin files exist;
     * @return bool
     */
    public function test_doFilesExist2() {
        $_aClassFiles = array();
        include( AmazonAutoLinks_Registry::$sDirPath . '/include/class-list.php' );
        return AmazonAutoLinks_WPUtility::doFilesExist( $_aClassFiles );
    }

}