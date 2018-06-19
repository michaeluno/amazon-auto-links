<?php
/**
 * JS Files Minifier
 *
 * @author      Michael Uno <michael@michaeluno.jp>
 * @copyright   2015 (c) Michael Uno
 * @license     MIT    <http://opensource.org/licenses/MIT>
 */
if ( ! class_exists( 'PHP_Class_Files_Script_Generator_Base' ) ) {
    require( dirname( dirname( dirname( __FILE__ ) ) ) . '/php_class_files_script_generator/PHP_Class_Files_Script_Generator_Base.php' );
}

/**
 * Creates a minified version of JavaScript files from the given directory.
 * 
 * @version    1.0.0
 */
class JS_Files_Minifier extends PHP_Class_Files_Script_Generator_Base {
    
    static protected $_aStructure_Options = array(
        'header_class_name' => '',
        'header_class_path' => '',        
        'output_buffer'     => true,
        'write_to_file'     => true,        
        'character_encode'  => 'UTF-8',     
        'use_beautifier'    => true,        
        'header_type'       => 'DOCBLOCK',    
        'carriage_return'   => PHP_EOL,
        // Search options
        'search'    =>    array(
            'allowed_extensions' => array( 'js' ),    // e.g. array( 'js', )
            'exclude_extensions' => array( 'min.js' ),
            'exclude_dir_paths'  => array(),
            'exclude_dir_names'  => array(),
            'is_recursive'       => true,
        ),        
        
    );
        
    /**
     * Stores current iterated class name.
     * 
     * Currently only used in the loop of the JavaScript minifier.
     * 
     * @since       1.0.0
     */
    private $_sCurrentIterationClassName;
    
    /**
     * Stores the output file path.
     * @since       1.0.0
     */
    public $sOutputFilePath;    
    
    /**
     * Stores the header comment to insert at the top of the script.
     * @since       1.0.0
     */
    public $sHeaderComment;

    /**
     * Stores the scanned files.
     * @since       1.0.0
     */    
    public $aFiles = array();
    
    /**
     * 
     * @param string    $sSourceDirPath     The target directory path.
     * @param string    $sOutputFilePath    The destination file path.
     * @param array     $aOptions           The options array. It takes the following arguments.
     *  - 'header_class_name'   : string    the class name that provides the information for the heading comment of the result output of the minified script.
     *  - 'header_class_path'   : string    (optional) the path to the header class file.
     *  - 'output_buffer'       : boolean    whether or not output buffer should be printed.
     *  - 'header_type'         : string    whether or not to use the docBlock of the header class; otherwise, it will parse the constants of the class. 
     *  - 'search'              : array        the arguments for the directory search options.
     *     The accepted values are 'CONSTANTS' or 'DOCBLOCK'.
     * <h3>Example</h3>
     * <code>array(
     *        'header_class_name' => 'HeaderClassForMinifiedVerions',
     *        'file_pettern'      => '/.+\.(php|inc)/i',
     *        'output_buffer'     => false,
     *        'header_type'       => 'CONSTANTS',
     * 
     * )</code>
     * 
     * When false is passed to the 'use_docblock' argument, the constants of the header class must include 'Version', 'Name', 'Description', 'URI', 'Author', 'CopyRight', 'License'. 
     * <h3>Example</h3>
     * <code>class TaskScheduler_Registry_Base {
     *         const VERSION       = '1.0.0b08';
     *         const NAME          = 'Task Scheduler';
     *         const DESCRIPTION   = 'Provides an enhanced task management system for WordPress.';
     *         const URI           = 'http://en.michaeluno.jp/';
     *         const AUTHOR        = 'miunosoft (Michael Uno)';
     *         const AUTHOR_URI    = 'http://en.michaeluno.jp/';
     *         const COPYRIGHT     = 'Copyright (c) 2014, <Michael Uno>';
     *         const LICENSE       = 'GPL v2 or later';
     *         const CONTRIBUTORS  = '';
     * }</code>
     */
    public function __construct( $asScanDirPaths, $sOutputFilePath='', array $aOptions=array() ) {

        $this->_setUp();
    
        $this->sOutputFilePath       = $sOutputFilePath;
        $aOptions = $this->_getOptionsFormatted( $aOptions );

        $_aScanDirPaths              = ( array ) $asScanDirPaths;        
        if ( $aOptions['output_buffer'] ) {
            echo 'Searching files under the directory: ' . implode( ', ', $_aScanDirPaths ) . $aOptions['carriage_return'];
        }
        
        // Store the file paths into an array. 
        $_aFiles = $this->_getFileLists( $_aScanDirPaths, $aOptions[ 'search' ] );        
        if ( $aOptions[ 'output_buffer' ] ) {                
            echo sprintf( 'Found %1$s file(s)', count( $_aFiles ) ) . $aOptions[ 'carriage_return' ];
        }    
        foreach( $_aFiles as $_iIndex => $_sPath ) {
            echo ( $_iIndex + 1 ) . '. ' . $_sPath . $aOptions[ 'carriage_return' ];    
        }
        
        $_sHeaderComment = $this->_getHeaderComment( array(), $aOptions );
        
        $this->_minifyFiles( $_aFiles, $aOptions, $_sHeaderComment );

    }
        /**
         * Loads required libraries.
         */
        private function _setUp() {
            
            $_sPathJSMinPlus = dirname( __FILE__ ) . '/library/JSMinPlus.php';
            if ( ! file_exists( $_sPathJSMinPlus ) ) {
                exit( 'Error: The JSMinPlus library could not be located.' );
            }
            require( $_sPathJSMinPlus );            
            
        }
        /**
         * @return      array
         */
        private function _getOptionsFormatted( $aOptions ) {
            
            $aOptions                      = $aOptions + self::$_aStructure_Options;
            $aOptions[ 'search' ]          = $aOptions[ 'search' ] + self::$_aStructure_Options[ 'search' ];
            $aOptions[ 'output_buffer' ]   = $aOptions[ 'write_to_file' ] 
                ? $aOptions[ 'output_buffer' ] 
                : false;
            $aOptions[ 'carriage_return' ] = php_sapi_name() == 'cli' ? PHP_EOL : '<br />';            
            return $aOptions;
            
        }
        
        /**
         * @return      array
         */
        protected function _getFileLists( $asScanDirPaths, $aSearchOptions ) {
            $_aFiles = parent::_getFileLists( $asScanDirPaths, $aSearchOptions );
            foreach( $_aFiles as $_iIndex => $_sPath ) {
                if ( $this->_hasExcludeFileExtension( $_sPath, $aSearchOptions[ 'exclude_extensions' ] ) ) {
                    unset( $_aFiles[ $_iIndex ] );
                }
            }
            return array_values( $_aFiles );    // re-index
        }
            /**
             * @return      boolean
             */
            private function _hasExcludeFileExtension( $_sPath, $aExtensionsToExclude=array() ) {
                foreach( $aExtensionsToExclude as $_sExtension ) {
                    if ( $this->_hasSuffix( '.' . $_sExtension, $_sPath ) ) {
                        return true;
                    }
                }
                return false;
            }
                /**
                 * Checks if the given string has a certain suffix.
                 * 
                 * Used to check file base name etc.
                 * 
                 * @since   1.0.0
                 * @reurn   boolean
                 */
                private function _hasSuffix( $sNeedle, $sHaystack ) {
                    
                    $_iLength = strlen( $sNeedle );
                    if ( 0 === $_iLength ) {
                        return true;
                    }
                    return substr( $sHaystack, - $_iLength ) === $sNeedle;
                    
                }            
        
    /**
     * Minifies the given JavaScript files and save them.
     */
    private function _minifyFiles( array $aFiles, array $aOptions, $sHeaderComment )  {
        
        foreach( $aFiles as $_iIndex => $_sFilePath ) {
            
            $_sContent = JSMinPlus::minify( file_get_contents( $_sFilePath ) );
            
            if ( ! $_sContent ) {
                echo 'The content is empty: ' . $_sFilePath . $aOptions[ 'carriage_return' ];    
            }
            
            $_sNewFilePath = $this->_getFileExtensionReplaced( $_sFilePath );
            echo ( $_iIndex + 1 ) . '. Writing to: ' . $_sNewFilePath . $aOptions[ 'carriage_return' ];    
            $this->write(
                $_sNewFilePath,
                trim( $sHeaderComment ) . PHP_EOL 
                .  trim( $_sContent )
            );
            
        }
  
    }        
        /**
         * @return      string
         */
        private function _getFileExtensionReplaced( $sFilePath, $sNewExtension='min.js' ) {
            $_aPathInfo = pathinfo( $sFilePath );
            return $_aPathInfo[ 'dirname' ] . '/' . $_aPathInfo[ 'filename' ] . '.' . $sNewExtension;
        }    
   
     
    /**
     * Write the output to a file.
     */
    public function write( $sFilePath, $sData ) {

        if ( file_exists( $sFilePath ) ) {
            unlink( $sFilePath );
        }   
        file_put_contents( $sFilePath, $sData, FILE_APPEND | LOCK_EX );
        
    }
    
}