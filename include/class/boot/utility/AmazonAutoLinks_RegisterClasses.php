<?php
/**
 * Registers PHP classes to be auto-loaded.
 * 
 * @package     Amazon Auto Links
 * @copyright   Copyright (c) 2013, Michael Uno
 * @authorurl    http://michaeluno.jp
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since        1.0.0
 */

final class AmazonAutoLinks_RegisterClasses {
    
    function __construct( $strClassDirPath, & $arrClassPaths=array() ) {
        
        $this->arrClassPaths = $arrClassPaths;    // the link to the array storing registered classes outside this object.
        $this->strClassDirPath = trailingslashit( $strClassDirPath );    
        $this->arrClassFileNames = array_map( array( $this, 'getBaseName' ), glob( $this->strClassDirPath . '*.php' ) );
        $this->setUpClassArray();
                
    }
    public function getBaseName( $strPath ) {
        return basename( $strPath );
    }    
    
    /**
     * Sets up the array consisting of class paths with the key of file base name.
     * 
     * This array is referred by the auto-loader callback and pass the stored file path. 
     * So the plugin can be extended by modifying this path locations so that the plugin loads the modified classes
     * instead of the built-in ones.
     * 
     * An example of the structure of $this->arrClassPath 
     * 
        Array (
            [AmazonAutoLinks_APIRequestTransient.php] => ...\wp36x\wp-content\plugins\amazon-auto-links/class/AmazonAutoLinks_APIRequestTransient.php
            [AmazonAutoLinks_AdminPage.php] => ...\wp36x\wp-content\plugins\amazon-auto-links/class/AmazonAutoLinks_AdminPage.php
            [AmazonAutoLinks_AdminPage_.php] => ...\wp36x\wp-content\plugins\amazon-auto-links/class/AmazonAutoLinks_AdminPage_.php
            ...
            ...
        )
     * 
     */
    function setUpClassArray() {
                
        foreach( $this->arrClassFileNames as $strClassFileName ) {
            
            // if it's set, do not register ( add it to the array ).
            if ( isset( $this->arrClassPaths[ $strClassFileName ] ) ) continue;
            
            $this->arrClassPaths[ $strClassFileName ] = $this->strClassDirPath . $strClassFileName;    
        
        }
// if ( class_exists('AmazonAutoLinks_Debug') )
    // AmazonAutoLinks_Debug::logArray( $this->arrClassPaths );

    }
    
    /**
     * Performs registration of the callback.
     * 
     * This registers the method to be triggered when an unknown class is instantiated. 
     * 
     * @remark            The front-end method. 
     */
    public function registerClasses() {
        
        spl_autoload_register( array( $this, 'callBackFromAutoLoader' ) );
        
    }
    
    public function callBackFromAutoLoader( $strClassName ) {
        
        $strBaseName = $strClassName . '.php';
        
        if ( ! in_array( $strBaseName, $this->arrClassFileNames ) ) return;
        
        if ( file_exists( $this->arrClassPaths[ $strBaseName ] ) ) 
            include_once( $this->arrClassPaths[ $strBaseName ] );
        
    }
    
}