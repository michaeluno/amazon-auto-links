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
 *
 * @since       4.4.0
*/
class Test_AmazonAutoLinks_Unit_PAAPIRequestCounter_Utility extends AmazonAutoLinks_UnitTest_Base {

    /**
     * @var AmazonAutoLinks_Unit_PAAPIRequestCounter_Utility
     */
    public $oUtil;

    /**
     * Sets up properties and hooks.
     */
    public function __construct() {
        $this->oUtil = new AmazonAutoLinks_Unit_PAAPIRequestCounter_Utility;
    }

    /**
     * @purpose Tests the getFilesFromFileSystem() method.
     * @tags files, php
     */
    public function test_getFilesFromFileSystem() {
        $_aFiles = $this->oUtil->getFilesFromFileSystem( AmazonAutoLinks_Unit_PAAPIRequestCounter_Loader::$sDirPath, array( 'php' ) );
        $this->_assertNotEmpty( $_aFiles );
    }
    /**
     * @purpose Tests the getFilesFromFileSystem() method.
     * @tags files, counter
     */
    public function test_getFilesFromFileSystem_02() {
        $_sLocale   = $this->oUtil->getDefaultLocale();
        $_sLocale   = 'US';
        $_oCounter  = new AmazonAutoLinks_VersatileFileManager_PAAPI_RequestCounter( $_sLocale );
        $_aFiles    = $this->oUtil->getFilesFromFileSystem( $_oCounter->getDirectoryPath(), array( 'txt' ), 'wp_normalize_path' );
        $this->_assertNotEmpty( $_aFiles );
    }
}