<?php
/**
 * Auto Amazon Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 */

/**
 * Tests the `AmazonAutoLinks_VersatileFileManager_AmazonCookies` class.
 *
 * @since   4.3.5
 * @see     AmazonAutoLinks_VersatileFileManager
 * @tags    files
*/
class Test_AmazonAutoLinks_VersatileFileManager extends AmazonAutoLinks_UnitTest_Base {

    /**
     * @tags base
     */
    public function test_Class() {
        $_oFile       = new AmazonAutoLinks_VersatileFileManager( 'test', 100, 'AAL_TEST_' );
        $_iUnlockTime = $_oFile->getUnlockTime();
        $this->_outputDetails( 'Current Time:', $this->getSiteReadableDate( time(), 'Y/m/d/ H:i:s', true ) );
        $this->_outputDetails( 'Unlock Time:', $this->getSiteReadableDate( $_iUnlockTime, 'Y/m/d/ H:i:s', true ) ); //  . ' g:i a'
        $this->_assertNotEmpty( $_oFile->getUnlockTime() );
        sleep( 1 );
        $this->_assertEqual( $_iUnlockTime, $_oFile->getUnlockTime() );
        $_oFile->delete();
    }

}