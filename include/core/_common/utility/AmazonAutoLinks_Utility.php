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
 * Provides utility methods.
 * @since       2
 * @since       3       Changed the name from `AmazonAutoLinks_Utilities`.
 */
class AmazonAutoLinks_Utility extends AmazonAutoLinks_Utility_XML {

    /**
     * @return string
     * @since   4.3.0
     */
    static public function getPageLoadID() {
        static $_sPageLoadID;
        $_sPageLoadID = $_sPageLoadID ? $_sPageLoadID : uniqid();
        return $_sPageLoadID;
    }

    /**
     * Retrieves the topmost items in an array.
     *
     * Used to cut off too many items.
     *
     * @remark The array should be numerically indexed, not associative.
     * @param array $aItems
     * @param integer $iCount
     * @return array
     * @since   4.2.0
     */
    static public function getTopmostItems( array $aItems, $iCount ) {
        $iCount = ( integer ) $iCount;
        $aItems = array_reverse( $aItems );
        $aItems = array_slice( $aItems, $iCount * -1, $iCount, true );
        return array_reverse( $aItems );
    }

    /**
     * @remark  used upon plugin uninstall.
     * @param   string $sDirectoryPath
     * @return  bool|null
     * @since   3.7.10
     */
    static public function isDirectoryEmpty( $sDirectoryPath ) {
        if ( ! is_readable( $sDirectoryPath ) ) {
            return null;
        }
        return ( count( scandir( $sDirectoryPath ) ) == 2 );
    }

    /**
     * @remark  used upon plugin uninstall.
     * @param   $sDirectoryPath
     * @since   3.7.10
     */
    static public function removeDirectoryRecursive( $sDirectoryPath ) {

        if ( ! is_dir( $sDirectoryPath ) ) {
            return;
        }
        $_aItems = scandir( $sDirectoryPath );
        foreach( $_aItems as $_sItem ) {
            if ( $_sItem !== "." && $_sItem !== ".." ) {
                if ( is_dir($sDirectoryPath . "/" . $_sItem ) ) {
                    self::removeDirectoryRecursive($sDirectoryPath . "/" . $_sItem );
                    continue;
                }
                unlink($sDirectoryPath . "/" . $_sItem );
            }
        }
        rmdir( $sDirectoryPath );

    }

    /**
     * Checks whether the given array is sequential or associative.
     * @return      boolean
     * @param       array $aArray
     * @deprecated 4.3.2    The same is defined in the framework. No need to override.
     */
    /*static public function isAssociative( array $aArray ) {
        return array_keys( $aArray ) !== range( 0, count( $aArray ) - 1 );
    }*/
    
    /**
     *
     * e.g. ISO-8859-1, utf-8, Shift_JIS
     * 
     * @remark  The value set to the header charset should be case-insensitive.
     * @see     http://www.iana.org/assignments/character-sets/character-sets.xhtml
     * @param   array|string $asHeaderResponse
     * @return  string      The found character set.
     */
    public function getCharacterSetFromResponseHeader( $asHeaderResponse ) {
        
        $_sContentType = '';
        if ( is_string( $asHeaderResponse ) ) {
            $_sContentType = $asHeaderResponse;
        } 
        // It should be an array then.
        else if ( isset( $asHeaderResponse[ 'content-type' ] ) ) {
            $_sContentType = $asHeaderResponse[ 'content-type' ];
        } 
        else {
            foreach( $asHeaderResponse as $_iIndex => $_sHeaderElement ) {
                if ( ! is_scalar( $_sHeaderElement ) ) {    // 4.2.0 - with a proxy, there is a case that this element is an array
                    continue;
                }
                if ( false !== stripos( $_sHeaderElement, 'charset=' ) ) {
                    $_sContentType = $asHeaderResponse[ $_iIndex ];
                }
            }
        }
        
        preg_match(
            '/charset=(.+?)($|[;\s])/i',  // needle
            $_sContentType, // haystack
            $_aMatches
        );
        return isset( $_aMatches[ 1 ] )
            ? ( string ) $_aMatches[ 1 ]
            : '';
            
    }
    
    /**
     * 
     * @since       3
     * @deprecated  4.3.2   Unused
     * @param       array   $aSubject
     * @return      string
     */
/*    static public function getKeyOfLowestElement( array $aSubject ) {
        natsort( $aSubject );
        foreach( $aSubject as $_isKey => $_mValue ) {
            // return the key(index) of the first item.
            return $_isKey;
        }
    }*/
    
    /**
     * Checks if the current time is over the given time.
     * @since       3
     * @remark      Assumed that the given time is not have any local time offset.
     * @param       integer|double|string   $nsSetTime
     * @return      boolean
     */
    static public function isExpired( $nsSetTime ) {
        $_nSetTime = is_numeric( $nsSetTime )
            ? $nsSetTime
            : strtotime( $nsSetTime );
        return ( $_nSetTime <= time() );
    }    
    
    /**
     * Stores included file path using the `includeOnce()` method below.
     * @since       3
     */
    public static $_aLoadedFiles = array();
    /**
     * Includes the given file.
     * 
     * As it is said that include_once() is slow, let's check whether it is included by ourselves 
     * and use include().
     *
     * @param       string          $sFilePath
     * @return      false|mixed     false on failure. Otherwise, the return value of the included file.
     */
    static public function includeOnce( $sFilePath ) {

        if ( self::hasBeenCalled( __METHOD__ . '_' . $sFilePath ) ) {
            return false;
        }
        if ( ! file_exists( $sFilePath ) ) {
            return false;
        }
        return @include( $sFilePath );
        
    }

    /**
     * Checks if the given value is empty or not.
     *
     * @remark  This is useful when PHP throws an error ' Fatal error: Can't use method return value in write context.'.
     * @param   mixed $mValue
     * @return  bool
     * @since   3
     */
    static public function isEmpty( $mValue ) {
        return ( boolean ) empty( $mValue );
    }

    /**
     * @param   string  $sToFix
     * @param   string  $sDelimiter
     * @param   boolean $bReadable
     * @param   boolean $bUnique
     * @return  string
     * @deprecated 4.3.2    Use `getEachDelimitedElementTrimmed()`.
     */
    static public function trimDelimitedElements( $sToFix, $sDelimiter, $bReadable=true, $bUnique=true ) {
        return self::getEachDelimitedElementTrimmed( $sToFix, $sDelimiter, $bReadable, $bUnique );
    }

    /**
     * Trims each delimited element of the given string with the specified delimiter.
     *
     * ```
     * $str = getEachDelimitedElementTrimmed( '   a , bcd ,  e,f, g h , ijk ', ',' );
     * ```
     *
     * produces:
     * ```
     * 'a, bcd, e, f, g h, ijk'
     * ```
     * @remark  One left white space gets added in each element to be readable.
     * @remark  Supports only one dimensional array.
     * @param   string  $sToFix
     * @param   string  $sDelimiter
     * @param   boolean $bReadable
     * @param   boolean $bUnique
     * @return  string
     */
    static public function getEachDelimitedElementTrimmed( $sToFix, $sDelimiter, $bReadable=true, $bUnique=true ) {
        $sToFix        = ( string ) $sToFix;
        $_aElements    = self::getStringIntoArray( $sToFix, $sDelimiter );
        $_aNewElements = array();
        foreach ( $_aElements as $sElement ) {
            if ( ! is_array( $sElement ) || ! is_object( $sElement ) ) {
                $_aNewElements[] = trim( $sElement );
            }
        }
        if ( $bUnique ) {
            $_aNewElements = array_unique( $_aNewElements );
        }
        return $bReadable
            ? implode( $sDelimiter . ' ' , $_aNewElements )
            : implode( $sDelimiter, $_aNewElements );
    }
        
    /**
     * Converts the given string with delimiters to a multi-dimensional array.
     * 
     * Parameters: 
     * 1: haystack string
     * 2, 3, 4...: delimiter
     * 
     * Example:
     * ```
     * $_aArray = getStringIntoArray( 'a-1,b-2,c,d|e,f,g', "|", ',', '-' );
     * ```
     * @return array
     */
    static public function getStringIntoArray() {

        $_aArgs      = func_get_args();
        $_sInput     = $_aArgs[ 0 ];            
        $_sDelimiter = $_aArgs[ 1 ];
        
        if ( ! is_string( $_sDelimiter ) || $_sDelimiter == '' ) {
            return $_sInput;
        }
        if ( is_array( $_sInput ) ) {
            return $_sInput;    // note that is_string( 1 ) yields false.
        }
            
        $_aElements = preg_split( "/[{$_sDelimiter}]\s*/", trim( $_sInput ), 0, PREG_SPLIT_NO_EMPTY );
        if ( ! is_array( $_aElements ) ) {
            return array();
        }
        
        foreach( $_aElements as &$_sElement ) {
            
            $_aParams = $_aArgs;
            $_aParams[0] = $_sElement;
            unset( $_aParams[ 1 ] );    // remove the used delimiter.
            // now `$_sElement` becomes an array.
            // if the delimiters are gone, 
            if ( count( $_aParams ) > 1 ) {                
                $_sElement = call_user_func_array( 
                    array( __CLASS__, 'getStringIntoArray' ),
                    $_aParams 
                );
            }
            
            // Added this because the function was not trimming the elements sometimes... not fully tested with multi-dimensional arrays. 
            if ( is_string( $_sElement ) ) {
                $_sElement = trim( $_sElement );
            }
            
        }
        return $_aElements;

    }        
        /**
         * An alias of `getStringIntoArray()`.
         * @deprecated      3.4.9       Use `getStringIntoArray()`.
         */
        static public function convertStringToArray() {
            $_aParams = func_get_args();
            return call_user_func_array(
                array( __CLASS__, 'getStringIntoArray' ),
                $_aParams
            );
        }        
    
    /**
     * Implodes the given (multi-dimensional) array.
     * 
     * @param            array            $arrInput                The subject array to be imploded.
     * @param            array            $arrGlues                An array numerically indexed with the values of glue. 
     * Each element should represent the glue of the dimension corresponding to the depth of the array.
     * e.g. array( ',', ':' ) will glue the elements of first dimension with comma and second dimension with colon.
     * @return            string
     * @todo    deprecated this as it seems not used anywhere
     */
/*    static public function implodeRecursive( $arrInput, $arrGlues ) {
        
        $arrGlues_ = ( array ) $arrGlues;
        array_shift( $arrGlues_ );

        foreach( $arrInput as $k => &$vElem ) {
            
            if ( ! is_array( $vElem ) ) { 
                continue;
            }
                
            $vElem = self::ImplodeRecursive( $vElem, ( ( array ) $arrGlues_[0] ) );
        
        }
        
        return implode( $arrGlues[0], $arrInput );

    }*/


    /**
     * For form validation
     * @deprecated 4.3.2 The same as the framework one. No need to override.
     */
/*    static public function fixNumber( $numToFix, $numDefault, $numMin="", $numMax="" ) {
            
        if ( ! is_numeric( trim( $numToFix ) ) ) return $numDefault;
        if ( $numMin !== "" && $numToFix < $numMin ) return $numMin;
        if ( $numMax !== "" && $numToFix > $numMax ) return $numMax;
        return $numToFix;
        
    }*/
    
    /**
     * Calculates the relative path from the given path.
     * 
     * This function is used to generate a template path.
     *
     * @see               http://stackoverflow.com/questions/2637945/getting-relative-path-from-absolute-path-in-php/2638272#2638272
     * @param   string $from
     * @param   string $to
     * @return  string
     * @deprecated 4.3.2 Same as the framework one. No need to override.
     */
/*    static public function getRelativePath( $from, $to ) {
        
        // some compatibility fixes for Windows paths
        $from = is_dir($from) ? rtrim($from, '\/') . '/' : $from;
        $to   = is_dir($to)   ? rtrim($to, '\/') . '/'   : $to;
        $from = str_replace('\\', '/', $from);
        $to   = str_replace('\\', '/', $to);

        $from     = explode('/', $from);
        $to       = explode('/', $to);
        $relPath  = $to;

        foreach($from as $depth => $dir) {
            // find first non-matching dir
            if($dir === $to[$depth]) {
                // ignore this directory
                array_shift($relPath);
            } else {
                // get number of remaining dirs to $from
                $remaining = count($from) - $depth;
                if($remaining > 1) {
                    // add traversals up to first matching dir
                    $padLength = (count($relPath) + $remaining - 1) * -1;
                    $relPath = array_pad($relPath, $padLength, '..');
                    break;
                } else {
                    $relPath[0] = './' . $relPath[0];
                }
            }
        }
        return implode('/', $relPath);
        
    } */
    
    /**
     * Retrieves the server set allowed maximum PHP script execution time.
     * 
     * @since            2.0.4
     * @param   integer $iDefault
     * @param   integer $iMax
     * @return  integer
     */
    static public function getAllowedMaxExecutionTime( $iDefault=30, $iMax=120 ) {

        $_iSetTime = function_exists( 'ini_get' )
            ? ( integer ) ini_get( 'max_execution_time' )
            : ( integer ) $iDefault;
        $_iSetTime = 0 === $_iSetTime ? $iMax : $_iSetTime;
        return $_iSetTime > $iMax
            ? ( integer ) $iMax : ( integer ) $_iSetTime;
        
    }
    
}