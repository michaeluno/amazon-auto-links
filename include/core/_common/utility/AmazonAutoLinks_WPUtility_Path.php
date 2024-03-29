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
 * Provides methods that uses WordPress built-in functions.
 * @since      3       
 */
class AmazonAutoLinks_WPUtility_Path extends AmazonAutoLinks_Utility {

    /**
     * Calculates the absolute path from the given relative path to the WordPress installed directory.
     * 
     * @since       3
     * @return      string
     */
    static public function getAbsolutePathFromRelative( $sRelativePath, $sBasePath='' ) {

        $_sBasePath     = $sBasePath ? $sBasePath : ABSPATH;

        // removes the heading ./ or .\ 
        $sRelativePath  = preg_replace( "/^\.[\/\\\]/", '', $sRelativePath, 1 );    
        
        // removes the leading slash and backslashes.
        $sRelativePath  = ltrim( $sRelativePath,'/\\'); 

        return trailingslashit( $_sBasePath ) . $sRelativePath;
    }
    
   
    /**
     * Returns the file path by checking if the given path is a file.
     * 
     * If fails, it attempts to check with the relative path to ABSPATH.
     * 
     * This is necessary when some users build the WordPress site locally and immigrate to the production site.
     * In that case, the stored absolute path won't work so it needs to be converted to the one that works in the new environment.
     * 
     * @since      3
     * @deprecated 4.7.0 Unused
     */
    public static function getReadableFilePath( $sFilePath, $sRelativePathToABSPATH='' ) {
        
        if ( @file_exists( $sFilePath ) ) {
            return $sFilePath;
        }
        
        if ( ! $sRelativePathToABSPATH ) {
            return false;
        }
        
        // try with the relative path.
        $_sAbsolutePath = realpath( trailingslashit( ABSPATH ) . $sRelativePathToABSPATH );
        if ( ! $_sAbsolutePath ) {
            return false;
        }
        
        if ( @file_exists( $_sAbsolutePath ) ) {
            return $_sAbsolutePath;
        }
        
        return false;        
        
    }
    
}