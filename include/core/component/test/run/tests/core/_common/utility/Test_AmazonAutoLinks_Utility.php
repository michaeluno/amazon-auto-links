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
 * Tests the AmazonAutoLinks_Unit_Utility class methods.
 *
 * @package     Amazon Auto Links
 * @since       4.3.2
*/
class Test_AmazonAutoLinks_Utility extends AmazonAutoLinks_UnitTest_Base {

    /**
     * @var AmazonAutoLinks_Utility
     */
    public $oUtil;

    public function __construct() {
        $this->oUtil = new AmazonAutoLinks_Utility;
    }

    /**
     * @purpose The result of the second call must match the result of the first call.
     * @return bool
     */
    public function test_getPageLoadID() {
        $_sPageLoadID = $this->oUtil->getPageLoadID();
        return is_string( $_sPageLoadID ) && $_sPageLoadID = $this->oUtil->getPageLoadID();
    }

    /**
     * @return bool
     * @tags array
     */
    public function test_getTopmostItems() {
        $_aArray = array(
            'a', 'b', 'c', 'd', 'e', 'f', 'g'
        );
        return array( 'a', 'b', 'c', ) === $this->getTopmostItems( $_aArray, 3 );
    }
    /**
     * @return bool
     * @tags array
     */
    public function test_getTopmostItems2() {
        $_aArray = array(
            'a', 'b', 'c', 'd', 'e', 'f', 'g'
        );
        return empty( $this->getTopmostItems( $_aArray, 0 ) );
    }

    /**
     * @return bool
     * @throws Exception
     * @tags file system, path
     */
    public function test_isDirectoryEmpty() {
        if ( ! $this->isDirectoryEmpty( dirname( __FILE__ ) .'/_empty' ) ) {
            throw new Exception( 'the directory is empty but the method returned false.' );
        }
        if ( null !== $this->isDirectoryEmpty( dirname( __FILE__ ) .'/_non_existent_path' ) ) {
            throw new Exception( 'For non existent path, the method must return null.' );
        }
        return ! $this->isDirectoryEmpty( dirname( __FILE__ ) );
    }

    /**
     * @return bool
     * @throws Exception
     * @tags file system, directory
     */
    public function test_removeDirectoryRecursive() {

        $_sTestDirPath = untrailingslashit( sys_get_temp_dir() ) . '/_aaltest/inner';
        $_bCreated     = mkdir( $_sTestDirPath, 0777, true );
        if ( ! $_bCreated ) {
            throw new Exception( 'Failed to create a test directory' );
        }
        if ( ! file_exists( $_sTestDirPath ) ) {
            throw new Exception( 'The created directory does not exist.' );
        }
        $this->removeDirectoryRecursive( dirname( $_sTestDirPath ) );
        if ( file_exists( $_sTestDirPath ) ) {
            throw new Exception( 'Failed to delete the test directory.' );
        }
        return ! file_exists( dirname( $_sTestDirPath ) );

    }

    /**
     * @tags time
     * @return bool
     */
    public function test_isExpired() {
        $_iBefore = time() - 1000;
        return $this->isExpired( $_iBefore );
    }

    /**
     * @purpose The second call with the same path should return false.
     * @return bool
     * @tags file
     * @throws Exception
     */
    public function test_includeOnce() {

        $_sVar = $this->includeOnce( dirname( __FILE__ ) . '/include-once.php' );
        if ( 'foo' !== $_sVar ) {
            throw new Exception( 'The file is not included.' );
        }
        return false === $this->includeOnce( dirname( __FILE__ ) . '/include-once.php' );;

    }

    /**
     * @return bool
     */
    public function test_isEmpty() {
        return $this->isEmpty( array() );
    }

    /**
     * @return bool
     * @tags string
     */
    public function test_getEachDelimitedElementTrimmed() {
        $_s = $this->getEachDelimitedElementTrimmed( '   a , bcd ,  e,f, g h , ijk ', ',' );
        return 'a, bcd, e, f, g h, ijk' === $_s;
    }

    /**
     * @return boolean
     * @tags array
     */
    public function test_getStringIntoArray() {
        $_aResult = $this->getStringIntoArray(
            'a-1,b-2,c,d|e,f,g',
            "|", ',', '-'
        );
        $_aSample = array(
            array(
                array( 'a', '1' ),
                array( 'b', '2' ),
                array( 'c' ),
                array( 'd' ),
            ),
            array(
                array( 'e' ),
                array( 'f' ),
                array( 'g' ),
            ),
        );
        return $_aSample === $_aResult;
    }

    /**
     * @return boolean
     * @tags ini
     */
    public function test_getAllowedMaxExecutionTime() {
        return 1 === $this->getAllowedMaxExecutionTime( 30, 1 );
    }
    /**
     * @return boolean
     * @tags ini
     */
    public function test_getAllowedMaxExecutionTime2() {
        return ( integer ) ini_get( 'max_execution_time' ) === $this->getAllowedMaxExecutionTime( 30, 999999 );
    }
}