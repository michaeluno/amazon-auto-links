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
    static public function getAbsolutePathFromRelative( $sRelativePath ) {
        
        // removes the heading ./ or .\ 
        $sRelativePath  = preg_replace( "/^\.[\/\\\]/", '', $sRelativePath, 1 );    
        
        // removes the leading slash and backslashes.
        $sRelativePath  = ltrim( $sRelativePath,'/\\'); 

        // APSPATH has a trailing slash.
        return ABSPATH . $sRelativePath;    
    }
    
   
    /**
     * Returns the file path by checking if the given path is a file.
     * 
     * If fails, it attempts to check with the relative path to ABSPATH.
     * 
     * This is necessary when some users build the WordPress site locally and immigrate to the production site.
     * In that case, the stored absolute path won't work so it needs to be converted to the one that works in the new environment.
     * 
     * @since            3
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

    /**
     * Calculates the URL from the given path.
     * 
     * @static
     * @access           public
     * @return           string            The source url
     * @since            2.0.1
     * @since            2.0.3.1           Prevented "/./" to be inserted in the url.
     * @since            3.8.0              Not to escape the url.
     * @todo            Can be deprecated as this is the same as the framework method.
     * @deprecated  4.0.0       Use the framework one
     */
    /*static public function getSRCFromPath( $sFilePath ) {
                        
        $_oWPStyles     = new WP_Styles();    // It doesn't matter whether the file is a style or not. Just use the built-in WordPress class to calculate the SRC URL.
        $_sRelativePath = AmazonAutoLinks_Utility::getRelativePath( ABSPATH, $sFilePath );       
        $_sRelativePath = preg_replace( "/^\.[\/\\\]/", '', $_sRelativePath, 1 ); // removes the heading ./ or .\ 
        $sHref          = trailingslashit( $_oWPStyles->base_url ) . $_sRelativePath;
        return $sHref;

    }*/
    
}