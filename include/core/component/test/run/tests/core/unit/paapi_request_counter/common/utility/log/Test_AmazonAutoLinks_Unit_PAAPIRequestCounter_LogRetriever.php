<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 */

/**
 *
 * @since       4.4.0
*/
class Test_AmazonAutoLinks_Unit_PAAPIRequestCounter_LogRetriever extends AmazonAutoLinks_UnitTest_Base {

    /**
     * @tags chart
     */
    public function test_getCountLog_US() {
        $this->_outputDetails( 'GMT Offset', $this->getGMTOffset() );
        $_oFileLog = new AmazonAutoLinks_Unit_PAAPIRequestCounter_LogRetriever( 'US' );
        $_oFileLog->getCountLog( $_aDates, $_aCounts, time() - ( 3 * 86400 ), time(), $this->getGMTOffset() );
        $this->_assertNotEmpty( $_aDates );
        $this->_assertNotEmpty( $_aCounts );
    }
    /**
     * @tags chart
     */
    public function test_getCountLog_IT() {
        $_oFileLog = new AmazonAutoLinks_Unit_PAAPIRequestCounter_LogRetriever( 'IT' );
        $_oFileLog->getCountLog( $_aDates, $_aCounts, time() - ( 7 * 86400 ), time(), $this->getGMTOffset() );
        $this->_assertNotEmpty( $_aDates );
        $this->_assertNotEmpty( $_aCounts );
    }
}