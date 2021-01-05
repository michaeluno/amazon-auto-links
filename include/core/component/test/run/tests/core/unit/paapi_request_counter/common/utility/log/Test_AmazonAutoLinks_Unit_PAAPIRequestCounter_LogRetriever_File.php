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
 *
 * @since       4.4.0
*/
class Test_AmazonAutoLinks_Unit_PAAPIRequestCounter_LogRetriever_File extends AmazonAutoLinks_UnitTest_Base {

    /**
     * @tags log
     */
    public function test_get() {
        $_oFileLog = new AmazonAutoLinks_Unit_PAAPIRequestCounter_LogRetriever_File( 'US' );
        $this->_assertNotEmpty( $_oFileLog->get( time() - ( 3 * 86400 ), time(), $_aFilePaths ) );
        $this->_assertNotEmpty( $_aFilePaths );
    }

    /**
     * @tags file, first
     * @throws ReflectionException
     */
    public function test____getFirstFoundItemTime() {
        $_oCounter  = new AmazonAutoLinks_VersatileFileManager_PAAPI_RequestCounter( 'US' );
        $_sDirPath  = $_oCounter->getDirectoryPath();
        $_oMock     = new AmazonAutoLinks_MockClass( 'AmazonAutoLinks_Unit_PAAPIRequestCounter_LogRetriever_File', array( 'US' ) );
        $_iTime     = $_oMock->call( '___getFirstFoundItemTime', array( $_sDirPath ) );
        $this->_assertNotEmpty( $_iTime );
        $this->_assertNotEmpty( date( 'Y-m-d H:00', $_iTime ) );

    }



}