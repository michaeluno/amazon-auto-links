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
 * Tests the AmazonAutoLinks_WPUtility class.
 *  
 * @package     Amazon Auto Links
 * @since       4.3.0
 * @see         AmazonAutoLinks_WPUtility
*/
class Test_AmazonAutoLinks_WPUtility extends AmazonAutoLinks_UnitTest_Base {

    /**
     * @sine 4.3.4
     * @purpose Tests scheduling and unscheduling WP Cron items.
     * @tags cron
     */
    public function test_scheduleSingleWPCronTask(){

        $_sTestActionName = 'test_' . uniqid();
        $_aArguments      = array( 'foo', 'bar' );
        $_iTimeStamp      = time() + 10;
        $this->_assertFalse( wp_next_scheduled( $_sTestActionName, $_aArguments ), 'The action with a generated unique name should not be ever registered.' );

        $_bScheduled      = AmazonAutoLinks_WPUtility::scheduleSingleWPCronTask( $_sTestActionName, $_aArguments, $_iTimeStamp );
        $this->_assertTrue( $_bScheduled, 'Now it should be scheduled.' );

        $_bUnscheduled    = wp_unschedule_event( $_iTimeStamp, $_sTestActionName, $_aArguments );
        $this->_assertTrue( $_bUnscheduled, 'Now it should be unscheduled.' );
        $this->_assertFalse( wp_next_scheduled( $_sTestActionName, $_aArguments ), 'It is now unscheduled.' );

    }


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
        $_aClassFiles = include( AmazonAutoLinks_Registry::$sDirPath . '/include/class-map.php' );
        return AmazonAutoLinks_WPUtility::doFilesExist( $_aClassFiles );
    }


}