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
     * @param Exception $oException
     * @param integer $iSkip    The number of skipping records. This is used when the caller does not want to include the self function/method.
     *
     * @return  string
     * @since   4.3.0
     * @see https://stackoverflow.com/questions/1949345/how-can-i-get-the-full-string-of-php-s-gettraceasstring/6076667#6076667
     * @todo Use the framework one.
     */
    static public function getStackTrace( Exception $oException, $iSkip=0 ) {

        $_sTrace     = "";
        $_iCount     = 0;
        foreach ( $oException->getTrace() as $_iIndex => $_aFrame ) {

            if ( $iSkip > $_iIndex ) {
                continue;
            }
            $_aFrame     = $_aFrame + array(
                'file'  => null, 'line' => null, 'function' => null,
                'class' => null, 'args' => array(),
            );
            $_sArguments = self::___getArgumentsOfEachStackTrace( $_aFrame[ 'args' ] );
            $_sTrace    .= sprintf(
                "#%s %s(%s): %s(%s)\n",
                $_iCount,
                $_aFrame[ 'file' ],
                $_aFrame[ 'line' ],
                isset( $_aFrame[ 'class' ] ) ? $_aFrame[ 'class' ] . '->' . $_aFrame[ 'function' ] : $_aFrame[ 'function' ],
                $_sArguments
            );
            $_iCount++;

        }
        return $_sTrace;

    }
        /**
         * @param array $aTraceArguments
         * @return string
         * @since   4.3.0
         */
        static private function ___getArgumentsOfEachStackTrace( array $aTraceArguments ) {

            $_aArguments = array();
            foreach ( $aTraceArguments as $_mArgument) {
                if ( is_string( $_mArgument ) ) {
                     $_aArguments[] = "'" . $_mArgument . "'";
                     continue;
                }
                if ( is_array( $_mArgument ) ) {
                    $_sOutput = '';
                    $_iTotal  = count( $_mArgument );
                    foreach( $_mArgument as $_sKey => $_mValue ) {
                        $_mValue   = is_scalar( $_mValue )
                            ? $_mValue
                            : '(' . gettype( $_mValue ) . ')';
                        $_sOutput .= $_sKey . ': ' . $_mValue . ',';
                        if ( $_iTotal > 10 ) {
                            $_sOutput  = rtrim( $_sOutput, ','  ) . '...';
                            break;
                        }
                    }
                    $_sOutput = rtrim( $_sOutput, ',' );
                    $_aArguments[] = "Array({$_sOutput})";
                    continue;
                }
                if ( is_null( $_mArgument ) ) {
                     $_aArguments[] = 'NULL';
                     continue;
                }
                if ( is_bool( $_mArgument ) ) {
                     $_aArguments[] = ( $_mArgument ) ? "true" : "false";
                     continue;
                }
                if ( is_object( $_mArgument ) ) {
                     $_aArguments[] = 'Object(' . get_class( $_mArgument ) . ')';
                     continue;
                }
                if ( is_resource( $_mArgument ) ) {
                    $_aArguments[] = get_resource_type( $_mArgument );
                    continue;
                }
                $_aArguments[] = $_mArgument;
            }
            return join(", ",  $_aArguments );
        }

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
     * @remark  The array should be numerically indexed, not associative.
     * @param array $aItems
     * @param $iCount
     *
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
                if (is_dir($sDirectoryPath . "/" . $_sItem ) ) {
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
     */
    static public function isAssociative( array $aArray ) {
        return array_keys( $aArray ) !== range( 0, count( $aArray ) - 1 );
    }    
    
    /**
     * 
     * @return      string      The found character set.
     * e.g. ISO-8859-1, utf-8, Shift_JIS
     * 
     * @remark  The value set to the header charset should be case-insensitive.
     * @see     http://www.iana.org/assignments/character-sets/character-sets.xhtml
     */
    static public function getCharacterSetFromResponseHeader( $asHeaderResponse ) {
        
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
        
        $_bFound = preg_match(
            '/charset=(.+?)($|[;\s])/i',  // needle
            $_sContentType, // haystack
            $_aMatches
        );
        return isset( $_aMatches[ 1 ] )
            ? $_aMatches[ 1 ]
            : '';
            
    }
    
    /**
     * 
     * @since       3
     * @return      string
     */
    static public function getKeyOfLowestElement( array $aSubject ) {
        natsort( $aSubject );
        foreach( $aSubject as $_isKey => $_mValue ) {
            // return the key(index) of the first item.
            return $_isKey;
        }
    }
    
    /**
     * Checks if the current time is over the given time.
     * @since       3
     * @remark      Assumed that the given time is not have any local time offset.
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
     * @return      boolean     true on success; false on failure.
     */
    static public function includeOnce( $sFilePath ) {
            
        if ( in_array( $sFilePath, self::$_aLoadedFiles ) ) {
            return false;
        }
        self::$_aLoadedFiles[ $sFilePath ] = $sFilePath;
        if ( ! file_exists( $sFilePath ) ) {
            return false;
        }
        return @include( $sFilePath );                        
        
    }    
    
    /**
     * Checks if the given value is empty or not.
     * 
     * @remark      This is useful when PHP throws an error ' Fatal error: Can't use method return value in write context.'.
     * @since       3
     * @retuen      boolean
     */
    static public function isEmpty( $mValue ) {
        return ( boolean ) empty( $mValue );
    }
            
    /**
     * Trims each delimited element of the given string with the specified delimiter. 
     * 
     * $str = trimDlimitedElements( '   a , bcd ,  e,f, g h , ijk ', ',' );
     * 
     * produces:
     * 
     * 'a, bcd, e, f, g h, ijk'
     * 
     * @remark            One left white space gets added in each element to be readable.
     * @remark            Supports only one dimensional array.
     */
    static public function trimDelimitedElements( $strToFix, $strDelimiter, $fReadable=true, $fUnique=true ) {
        
        $strToFix    = ( string ) $strToFix;
        $arrElems    = self::getStringIntoArray( $strToFix, $strDelimiter );
        $arrNewElems = array();
        foreach ( $arrElems as $strElem ) {
            if ( ! is_array( $strElem ) || ! is_object( $strElem ) ) {
                $arrNewElems[] = trim( $strElem );
            }
        }
        
        if ( $fUnique ) {
            $arrNewElems = array_unique( $arrNewElems );
        }
        
        return $fReadable
            ? implode( $strDelimiter . ' ' , $arrNewElems )
            : implode( $strDelimiter, $arrNewElems );
                
    }    
        
    /**
     * Converts the given string with delimiters to a multi-dimensional array.
     * 
     * Parameters: 
     * 1: haystack string
     * 2, 3, 4...: delimiter
     * e.g. $arr = getStringIntoArray( 'a-1,b-2,c,d|e,f,g', "|", ',', '-' );
     * 
     */
    static public function getStringIntoArray() {
        
        $intArgs      = func_num_args();
        $arrArgs      = func_get_args();
        $strInput     = $arrArgs[ 0 ];            
        $strDelimiter = $arrArgs[ 1 ];
        
        if ( ! is_string( $strDelimiter ) || $strDelimiter == '' ) {
            return $strInput;
        }
        if ( is_array( $strInput ) ) {
            return $strInput;    // note that is_string( 1 ) yields false.
        }
            
        $arrElems = preg_split( "/[{$strDelimiter}]\s*/", trim( $strInput ), 0, PREG_SPLIT_NO_EMPTY );
        if ( ! is_array( $arrElems ) ) {
            return array();
        }
        
        foreach( $arrElems as &$strElem ) {
            
            $arrParams = $arrArgs;
            $arrParams[0] = $strElem;
            unset( $arrParams[ 1 ] );    // remove the used delimiter.
            // now `$strElem` becomes an array.
            // if the delimiters are gone, 
            if ( count( $arrParams ) > 1 ) {                
                $strElem = call_user_func_array( 
                    array( __CLASS__, 'getStringIntoArray' ),
                    $arrParams 
                );
            }
            
            // Added this because the function was not trimming the elements sometimes... not fully tested with multi-dimensional arrays. 
            if ( is_string( $strElem ) ) {
                $strElem = trim( $strElem );
            }
            
        }
        return $arrElems;

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
    static public function implodeRecursive( $arrInput, $arrGlues ) {    
        
        $arrGlues_ = ( array ) $arrGlues;
        array_shift( $arrGlues_ );

        foreach( $arrInput as $k => &$vElem ) {
            
            if ( ! is_array( $vElem ) ) { 
                continue;
            }
                
            $vElem = self::ImplodeRecursive( $vElem, ( ( array ) $arrGlues_[0] ) );
        
        }
        
        return implode( $arrGlues[0], $arrInput );

    }    


    /**
     * For form validation
     */
    static public function fixNumber( $numToFix, $numDefault, $numMin="", $numMax="" ) {
            
        if ( ! is_numeric( trim( $numToFix ) ) ) return $numDefault;
        if ( $numMin !== "" && $numToFix < $numMin ) return $numMin;
        if ( $numMax !== "" && $numToFix > $numMax ) return $numMax;
        return $numToFix;
        
    }    
    
    /**
     * Calculates the relative path from the given path.
     * 
     * This function is used to generate a template path.
     * 
     * @author            Gordon
     * @see               http://stackoverflow.com/questions/2637945/getting-relative-path-from-absolute-path-in-php/2638272#2638272
     */
    static public function getRelativePath( $from, $to ) {
        
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
        
    }    
    
    /**
     * Retrieves the server set allowed maximum PHP script execution time.
     * 
     * @since            2.0.4
     */
    static public function getAllowedMaxExecutionTime( $iDefault=30, $iMax=120 ) {
        
        $iSetTime = function_exists( 'ini_get' ) && ini_get( 'max_execution_time' ) 
            ? ( int ) ini_get( 'max_execution_time' ) 
            : $iDefault;
        
        return $iSetTime > $iMax
            ? $iMax
            : $iSetTime;
        
    }
    
}