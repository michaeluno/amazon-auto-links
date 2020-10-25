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

    static private $___aObjectCache = array();
    /**
     * @param string|array $asName  If array, it represents a multi-dimensional keys.
     * @param mixed        $mValue
     * @since 4.3.6
     */
    static public function setObjectCache( $asName, $mValue ) {
        self::setMultiDimensionalArray( self::$___aObjectCache, self::getAsArray( $asName ), $mValue );
    }

    /**
     * @param array|string $asName
     * @since 4.3.6
     */
    static public function unsetObjectCache( $asName ) {
        self::unsetDimensionalArrayElement( self::$___aObjectCache, self::getAsArray( $asName ) );
    }

    /**
     * Caches values in the class property.
     *
     * @remark The stored data will be gone after the page load.
     * @param  array|string $asName The key of the object cache array. If an array is given, it represents the multi-dimensional keys.
     * @param  mixed $mDefault
     * @return mixed
     * @since  4.3.6
     */
    static public function getObjectCache( $asName, $mDefault=null ) {
        return self::getArrayValueByArrayKeys( self::$___aObjectCache, self::getAsArray( $asName ), $mDefault );
    }
    
    /**
     * @param  string $sURL
     * @return string
     * @since  4.3.4
     */
    static public function getSubDomain( $sURL ) {
        return self::getSubDomainFromHostName( parse_url( $sURL, PHP_URL_HOST ) );
    }

    /**
     * @param  string $sHost
     * @sicne  4.3.4
     * @return string
     */
    static public function getSubDomainFromHostName( $sHost ) {
        return preg_replace("/^.*?\.(\w+(\.\w+)+)$/i",'$1', $sHost );
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
     * Checks if the current time is over the given time.
     * @since       3
     * @remark      Assumed that the given time is not have any local time offset.
     * @param       integer|double|string   $nsSetTime
     * @return      boolean
     */
    static public function isExpired( $nsSetTime ) {
        $_nSetTime = is_numeric( $nsSetTime ) ? $nsSetTime : strtotime( $nsSetTime );
        return ( $_nSetTime <= time() );
    }    

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
     * Retrieves the server set allowed maximum PHP script execution time by applying a maximum value.
     * 
     * @since   2.0.4
     * @param   integer $iDefault
     * @param   integer $iMax
     * @return  integer
     */
    static public function getAllowedMaxExecutionTime( $iDefault=30, $iMax=120 ) {

        $_iSetTime = self::getMaxExecutionTime( $iDefault );
        $iMax      = ( integer ) $iMax;
        $_iSetTime = 0 === $_iSetTime ? $iMax : $_iSetTime;
        return $_iSetTime > $iMax ? $iMax : $_iSetTime;
        
    }

    /**
     * Retrieves the server set maximum PHP script execution time.
     * @param  integer $iDefault    The default seconds.
     * @return integer
     * @since  4.3.6
     */
    static public function getMaxExecutionTime( $iDefault=30 ) {
        return function_exists( 'ini_get' )
            ? ( integer ) ini_get( 'max_execution_time' )
            : ( integer ) $iDefault;
    }
    
}