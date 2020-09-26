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
 * Finds classes from given directories.
 *  
 * @package     Amazon Auto Links
 * @since       4.3.0
*/
class AmazonAutoLinks_Test_ClassFinder extends AmazonAutoLinks_AdminPageFramework_RegisterClasses {

    private $___aFilePaths = array();
    private $___sScanDirPath = '';
    private $___aBaseClasses = array();
    static private $___aCaches = array();

    /**
     * Performs necessary set-ups.
     *
     * @param string $sScanDirPath
     * @param array $aBaseClasses
     * @param array $aOptions
     */
    public function __construct( $sScanDirPath, array $aBaseClasses, array $aOptions=array() ) {
        $this->___sScanDirPath = $sScanDirPath;
        $this->___aBaseClasses = $aBaseClasses;
        $_aOptions = $aOptions + self::$_aStructure_Options;
        $this->___aFilePaths = $this->___getTestFiles( $_aOptions );
    }

        private function ___getTestFiles( array $aOptions ) {

            if ( isset( self::$___aCaches[ $this->___sScanDirPath ] ) ) {
                return self::$___aCaches[ $this->___sScanDirPath ];
            }

            $_aFiles = $this->getFilePaths( $this->___sScanDirPath, $aOptions );
            $_aKept  = array();
            foreach( $_aFiles as $_sFilePath ) {
                $_sPHPCode   = AmazonAutoLinks_Test_Utility::getPHPCode( $_sFilePath );
//                $_sClassName = AmazonAutoLinks_Test_Utility::getDefinedClass( $_sPHPCode );
                $_sBaseClass = $this->___getParentClass( $_sPHPCode );
                if ( ! in_array( $_sBaseClass, $this->___aBaseClasses, true ) ) {
                    continue;
                }
                $_sDirTag    = str_replace( $this->___sScanDirPath, '', $_sFilePath );
                $_sDirTag    = str_replace( '\\', '/', $_sDirTag );
                $_sDirTag    = ltrim( $_sDirTag, '/' );
                $_sDirTag    = pathinfo( $_sDirTag, PATHINFO_DIRNAME );
                $_sFilePath  = str_replace( '\\', '/', $_sFilePath );
                $_sFilePath  = ltrim( $_sFilePath, '/' );

                $_aKept[ $_sDirTag ] = isset( $_aKept[ $_sDirTag ] ) && is_array( $_aKept[ $_sDirTag ] )
                    ? $_aKept[ $_sDirTag ]
                    : array();
                $_aKept[ $_sDirTag ][] = $_sFilePath;

                // @todo There will be a case that a test class extending another test class which extends a required class,
                // meaning an ancestor is a required one, not the parent.
//                $_oParse     = new $_sClassName;
//                if ( ! $_oParse instanceof AmazonAutoLinks_UnitTest_Base ) {
//                    continue;
//                }
//                $_aKept[] = $_sFilePath;
            }
            self::$___aCaches[ $this->___sScanDirPath ] = $_aKept;
            return $_aKept;
        }

        /**
         * Returns the parent class
         */
        private function ___getParentClass( $sPHPCode ) {
            if ( ! preg_match( '/class\s+(.+?)\s+extends\s+(.+?)\s+{/i', $sPHPCode, $aMatch ) ) {
                return null;
            }
            return $aMatch[ 2 ];
        }

    /**
     * @return array
     */
    public function getFiles() {
        return $this->___aFilePaths;
    }



        /**
         * Overriding a parent method to fix a bug.
         * @param $sPathPatten
         * @param int $nFlags
         * @param array $aExcludeDirs
         * @param array $aExcludeDirNames
         *
         * @return array|false
         */
        protected function doRecursiveGlob($sPathPatten, $nFlags = 0, array $aExcludeDirs = array(), array $aExcludeDirNames = array()) {
            $_aFiles = glob($sPathPatten, $nFlags);
            $_aFiles = is_array($_aFiles) ? $_aFiles : array();
            $_aDirs = glob(dirname($sPathPatten) . DIRECTORY_SEPARATOR . '*', GLOB_ONLYDIR | GLOB_NOSORT);
            $_aDirs = is_array($_aDirs) ? $_aDirs : array();
            foreach ($_aDirs as $_sDirPath) {
                if (in_array($_sDirPath, $aExcludeDirs)) {
                    continue;
                }
                // Fixed a bug PATHINFO_DIRNAME to PATHINFO_BASENAME.
                if (in_array(pathinfo($_sDirPath, PATHINFO_BASENAME), $aExcludeDirNames)) {
                    continue;
                }
                $_aFiles = array_merge($_aFiles, $this->doRecursiveGlob($_sDirPath . DIRECTORY_SEPARATOR . basename($sPathPatten), $nFlags, $aExcludeDirs));
            }
            return $_aFiles;
        }
}