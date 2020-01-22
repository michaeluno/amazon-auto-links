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
 * Provides utility methods that uses WordPerss built-in functions.
 *
 * @since       1
 * @since       3       Made it extend `AmazonAutoLinks_AdminPageFramework_Debug`.
 */
class AmazonAutoLinks_Debug extends AmazonAutoLinks_AdminPageFramework_Debug {

    /**
     * Prints out the given array contents
     * 
     * If a file path is given, it saves the output in the file.
     * 
     * @remark      An alias of the dumpArray() method.
     * @since       3.2.0
     */
    static public function dump( $asArray, $sFilePath=null ) {
        // @deprecated 3.10.0 Give more freedom to the user
//        if ( ! self::isDebugModeEnabled() ) {
//            return;
//        }
        parent::dump( $asArray, $sFilePath );
    }        
    
    /**
     * Retrieves the output of the given array contents.
     * 
     * If a file pass is given, it saves the output in the file.
     * 
     * @remark      An alias of getArray() method.
     * @since       3.2.0
     */
    static public function get( $asArray, $sFilePath=null, $bEscape=true ) {
        // @deprecated 3.10.0 Give more freedom to the user
//        if ( ! self::isDebugModeEnabled() ) {
//            return null;
//        }
        return parent::get( $asArray, $sFilePath, $bEscape );        
    }
    
    /**
     * Logs the given variable output to a file.
     * 
     * @param       mixed       $mValue         The value to log.  
     * @param       string      $sFilePath      The log file path.
     * @return      void
     **/
    static public function log( $mValue, $sFilePath=null ) {
        // @deprecated 3.10.0 Give more freedom to the user
//        if ( ! self::isDebugModeEnabled() ) {
//            return;
//        }
        self::$iLegibleStringCharacterLimit = PHP_INT_MAX;
        self::$iLegibleArrayDepthLimit = PHP_INT_MAX;
        parent::log( $mValue, $sFilePath );
    }
}