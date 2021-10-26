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
 * Verifies given classes that can run tests.
 *  
 * @since       4.3.4
*/
class AmazonAutoLinks_Test_ClassLister {

    private $___sBaseDirPath;
    private $___aClassFiles;
    private $___aBaseClasses;

    /**
     * Sets up properties.
     *
     * @param string $sBaseDirPath
     * @param array  $aClassFiles
     * @param array  $aBaseClasses
     * @since 4.3.4
     */
    public function __construct( $sBaseDirPath, array $aClassFiles, array $aBaseClasses ) {
        
        $this->___sBaseDirPath = wp_normalize_path( $sBaseDirPath ); 
        $this->___aClassFiles  = $aClassFiles;
        $this->___aBaseClasses = $aBaseClasses;
        
    }

    /**
     * @return array    A list of valid classes.
     */
    public function get() {
        
        $_aClassFiles = array();
        foreach( $this->___aClassFiles as $_sKey => $_sFilePath ) {
            
            $_sFilePath  = wp_normalize_path( $_sFilePath );
            if ( ! $this->___canProcess( $_sFilePath ) ) {
                continue;
            }

            $_sCategory  = str_replace( $this->___sBaseDirPath, '', $_sFilePath );
            $_sCategory  = ltrim( $_sCategory, '/' );
            $_sCategory  = pathinfo( $_sCategory, PATHINFO_DIRNAME );
            // @deprecated 4.4.4 This might be causing a problem on Unix based systems.
            // $_sFilePath  = ltrim( $_sFilePath, '/' );
            if ( ! file_exists( $_sFilePath ) ) {
                continue;
            }
            
            $_aClassFiles[ $_sCategory ] = isset( $_aClassFiles[ $_sCategory ] ) ? $_aClassFiles[ $_sCategory ] : array();
            $_aClassFiles[ $_sCategory ][] = $_sFilePath;
            
        }
        return $_aClassFiles;
        
    }

        /**
         * @param string $sFilePath
         * @return boolean
         * @since  4.3.4
         * @see ReflectionClass;
         */
        private function ___canProcess( $sFilePath ) {

            // Check if the file is under the base dir
            if ( false === strpos( $sFilePath, $this->___sBaseDirPath ) ) {
                return false;
            }

            $_sPHPCode    = AmazonAutoLinks_Test_Utility::getPHPCode( $sFilePath );
            $_sClassName  = AmazonAutoLinks_Test_Utility::getDefinedClass( $_sPHPCode );
            try {
                $_oReflection = new ReflectionClass( $_sClassName );
            } catch ( Exception $oException ) {
                return false;
            }
            foreach( $this->___aBaseClasses as $_sBaseClass ) {
                if ( $_oReflection->isSubclassOf( $_sBaseClass ) ) {
                    return true;
                }
            }
            return false;

        }

}