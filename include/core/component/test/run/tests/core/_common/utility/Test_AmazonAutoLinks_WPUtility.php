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
 * Tests the AmazonAutoLinks_WPUtility class.
 *  
 * @since       4.3.0
 * @see         AmazonAutoLinks_WPUtility
*/
class Test_AmazonAutoLinks_WPUtility extends AmazonAutoLinks_UnitTest_Base {

    /**
     * @tags post
     */
    public function test_getPostByGUID() {
        $_aPost = AmazonAutoLinks_WPUtility::getPostByGUID( 'https://aal-affiliate-disclosure-page', 'ID' );
        $this->_outputDetails( gettype( $_aPost ) );
        $this->_assertTrue( is_array( $_aPost ) );
        $this->_assertNotEmpty( $_aPost );
        $this->_assertTrue( isset( $_aPost[ 'ID' ] ) );
        $_aPost = AmazonAutoLinks_WPUtility::getPostByGUID( 'https://aal-affiliate-disclosure-page', 'ID', 'ARRAY_A' );
        $this->_outputDetails( gettype( $_aPost ) );
        $this->_assertTrue( is_array( $_aPost ) );
        $this->_assertNotEmpty( $_aPost );
        $this->_assertTrue( isset( $_aPost[ 'ID' ] ) );
        $_oPost = AmazonAutoLinks_WPUtility::getPostByGUID( 'https://aal-affiliate-disclosure-page', 'ID', OBJECT );
        $this->_outputDetails( gettype( $_oPost ) );
        $this->_assertTrue( is_object( $_oPost ) );
        $this->_assertNotEmpty( $_oPost->ID );
    }


    /**
     * @throws Exception
     * @tags timezone, gmt
     */
    public function test_getGMTOffsetString() {

        $_sOffset = AmazonAutoLinks_WPUtility::getGMTOffsetString();
        $this->_assertTrue( is_string( $_sOffset ) );
        $this->_assertNotEmpty( $_sOffset );

    }

    /**
     * @throws Exception
     * @tags timezone, gmt
     */
    public function test_getGMTOffset() {

        $_iOffset = AmazonAutoLinks_WPUtility::getGMTOffset();
        $this->_assertTrue( is_integer( $_iOffset ) );
        $this->_assertNotEmpty( $_iOffset );

    }

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
        $_sClassMapPath = AmazonAutoLinks_Registry::$sDirPath . '/include/core/class-map.php';
        $this->_assertTrue( file_exists( $_sClassMapPath ) );
        $_aClassFiles = include( $_sClassMapPath );
        return AmazonAutoLinks_WPUtility::doFilesExist( $_aClassFiles );
    }


}