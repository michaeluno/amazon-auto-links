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
 * Provides utility methods.
 * @since       2
 * @since       3       Changed the name from `AmazonAutoLinks_Utilities`.
 */
class AmazonAutoLinks_Utility extends AmazonAutoLinks_Utility_FileSystem {

    /**
     * @param  string  $sURL
     * @return boolean
     * @since  4.7.6
     */
    static public function doesURLExist( $sURL ) {
        $file_headers = @get_headers( $sURL );
        if( ! $file_headers || $file_headers[ 0 ] == 'HTTP/1.1 404 Not Found' ) {
            return false;
        }
        return true;
    }

    /**
     * @param  string $sURL
     * @return string|false
     * @since  4.6.18
     */
    static public function getURLSanitized( $sURL ) {
        $sURL = strip_tags( $sURL );
        $sURL = stripslashes( $sURL );
        $sURL = trim( $sURL );
        return filter_var( $sURL, FILTER_VALIDATE_URL );
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
     * @since  4.3.4
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
     * @param  array   $aItems
     * @param  integer $iCount
     * @param  boolean $bPreserveKeys
     * @return array
     * @since  4.2.0
     */
    static public function getTopMostItems( array $aItems, $iCount, $bPreserveKeys=false ) {
        return array_slice( $aItems, 0, ( integer ) $iCount, $bPreserveKeys );
    }

    /**
     * @param  array   $aItems
     * @param  integer $iCount
     * @param  boolean $bPreserveKeys
     * @return array
     * @since  4.4.0
     */
    static public function getBottomMostItems( array $aItems, $iCount, $bPreserveKeys=false ) {
        $iCount = ( integer ) $iCount;
        return array_slice( $aItems, $iCount * -1, $iCount, $bPreserveKeys );
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
     * @deprecated  4.6.17          Use include_once() as it doesn't have any performance issue as of PHP 5.
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
     * @remark Used as a callback such as with array_filter().
     * @param  $mValue
     * @return boolean
     * @since  4.6.19
     */
    static public function isNotEmpty( $mValue ) {
        return ! empty( $mValue );
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