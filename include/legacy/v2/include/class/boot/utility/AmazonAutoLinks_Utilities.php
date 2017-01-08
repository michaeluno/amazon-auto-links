<?php
/**
 *    Provides utility methods.
 *
 * @package     Amazon Auto Links
 * @copyright   Copyright (c) 2013, Michael Uno
 * @authorurl    http://michaeluno.jp
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since        2.0.0
 * 
 */

class AmazonAutoLinks_Utilities {
    
    /**
     * Merges multiple multi-dimensional array recursively.
     * 
     * The advantage of using this method over the array unite operator or array_merge() is that it merges recursively and the null values of the preceding array will be overridden.
     * 
     * @since            2.0.0
     * @static
     * @access            public
     * @remark            The parameters are variadic and can add arrays as many as necessary.
     * @return            array            the united array.
     */
    static public function uniteArrays( $arrPrecedence, $arrDefault1 ) {
                
        $arrArgs = array_reverse( func_get_args() );
        $arrArray = array();
        foreach( $arrArgs as $arrArg ) 
            $arrArray = self::uniteArraysRecursive( $arrArg, $arrArray );
            
        return $arrArray;
        
    }
    /**
     * Merges two multi-dimensional arrays recursively.
     * 
     * The first parameter array takes its precedence. This is useful to merge default option values. 
     * An alternative to <em>array_replace_recursive()</em>; it is not supported PHP 5.2.x or below.
     * 
     * @since            2.0.0
     * @static
     * @access            public
     * @remark            null values will be overwritten.     
     * @param            array            $arrPrecedence            the array that overrides the same keys.
     * @param            array            $arrDefault                the array that is going to be overridden.
     * @return            array            the united array.
     */ 
    static public function uniteArraysRecursive( $arrPrecedence, $arrDefault ) {
                
        if ( is_null( $arrPrecedence ) ) $arrPrecedence = array();
        
        if ( ! is_array( $arrDefault ) || ! is_array( $arrPrecedence ) ) return $arrPrecedence;
            
        foreach( $arrDefault as $strKey => $v ) {
            
            // If the precedence does not have the key, assign the default's value.
            if ( ! array_key_exists( $strKey, $arrPrecedence ) || is_null( $arrPrecedence[ $strKey ] ) )
                $arrPrecedence[ $strKey ] = $v;
            else {
                
                // if the both are arrays, do the recursive process.
                if ( is_array( $arrPrecedence[ $strKey ] ) && is_array( $v ) ) 
                    $arrPrecedence[ $strKey ] = self::uniteArraysRecursive( $arrPrecedence[ $strKey ], $v );
            
            }
        }
        return $arrPrecedence;        
    }
    
    /**
     * Converts characters not supported to be used in the URL query key to underscore.
     * 
     * @see            http://stackoverflow.com/questions/68651/can-i-get-php-to-stop-replacing-characters-in-get-or-post-arrays
     */
    static public function sanitizeCharsForURLQueryKey( $str ) {

        $search = array( chr( 32 ), chr( 46 ), chr( 91 ) );
        for ( $i=128; $i <= 159; $i++ ) {
            array_push( $search, chr( $i ) );
        }
        return str_replace ( $search , '_', $str );
        
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
        
        $strToFix = ( string ) $strToFix;
        $arrElems = self::convertStringToArray( $strToFix, $strDelimiter );
        $arrNewElems = array();
        foreach ( $arrElems as $strElem ) 
            if ( ! is_array( $strElem ) || ! is_object( $strElem ) )
                $arrNewElems[] = trim( $strElem );
        
        if ( $fUnique )
            $arrNewElems = array_unique( $arrNewElems );
        
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
     * e.g. $arr = convertStringToArray( 'a-1,b-2,c,d|e,f,g', "|", ',', '-' );
     * 
     */
    static public function convertStringToArray() {
        
        $intArgs = func_num_args();
        $arrArgs = func_get_args();
        $strInput = $arrArgs[ 0 ];            
        $strDelimiter = $arrArgs[ 1 ];
        
        if ( ! is_string( $strDelimiter ) || $strDelimiter == '' ) return $strInput;
        if ( is_array( $strInput ) ) return $strInput;    // note that is_string( 1 ) yields false.
            
        $arrElems = preg_split( "/[{$strDelimiter}]\s*/", trim( $strInput ), 0, PREG_SPLIT_NO_EMPTY );
        if ( ! is_array( $arrElems ) ) return array();
        
        foreach( $arrElems as &$strElem ) {
            
            $arrParams = $arrArgs;
            $arrParams[0] = $strElem;
            unset( $arrParams[ 1 ] );    // remove the used delimiter.
            // now $strElem becomes an array.
            if ( count( $arrParams ) > 1 ) // if the delimiters are gone, 
                $strElem = call_user_func_array( 'AmazonAutoLinks_Utilities::convertStringToArray', $arrParams );
            
            // Added this because the function was not trimming the elements sometimes... not fully tested with multi-dimensional arrays. 
            if ( is_string( $strElem ) )
                $strElem = trim( $strElem );
            
        }

        return $arrElems;

    }        
    
    /**
     * Implodes the given (multi-dimensional) array.
     * 
     * @param            array            $arrInput                The subject array to be imploded.
     * @param            array            $arrGlues                An array numerically indexed with the values of glue. 
     * Each element should represent the glue of the dimension corresponding to the depth of the array.
     * e.g. array( ',', ':' ) will glue the elements of first dimension with comma and second dimension with colon.
     * @return            string
     */
    static public function implodeRecursive( $arrInput, $arrGlues ) {    
        
        $arrGlues_ = ( array ) $arrGlues;
        array_shift( $arrGlues_ );

        foreach( $arrInput as $k => &$vElem ) {
            
            if ( ! is_array( $vElem ) ) continue;
                
            $vElem = $this->ImplodeRecursive( $vElem, ( ( array ) $arrGlues_[0] ) );
        
        }
        
        return implode( $arrGlues[0], $arrInput );

    }    

    /**
     * Returns an XML object from the given XML string content.
     * 
     * Returns a tag-stripped string on error.
     */
    static public function getXMLObject( $sXML ) {
        
        $bDOMError = libxml_use_internal_errors( true );    // Disable DOM related errors to be displayed.
        $oXML = simplexml_load_string( $sXML );
        libxml_use_internal_errors( $bDOMError );    // Restore the error setting.
        if ( $oXML !== false )
            return $oXML;
            
        // Possibly it's an 'HTML output
        return strip_tags( $sXML );
        
        // return libxml_get_errors();
        
    }
    
    /**
     * Converts an XML document to json.
     * 
     */
    static public function convertXMLtoJSON( $osXML ) {
                
        if ( is_object( $osXML ) )
            return json_encode( $osXML );
                
        // Otherwise, it's a string.
        $bDOMError = libxml_use_internal_errors( true );    // Disable DOM related errors to be displayed.
        $oXML = simplexml_load_string( $osXML );
        libxml_use_internal_errors( $bDOMError );    // Restore the error setting.
        if ( $oXML !== false )
            return json_encode( $oXML );    // Process XML structure here
        
        return  json_encode( libxml_get_errors() );    // libxml_get_errors() returns an array
                    
    }
    
    /**
     * Converts an XML document to associative array.
     */
    static public function convertXMLtoArray( $osXML ) {
        
        $sJSON = self::convertXMLtoJSON( $osXML );
        return json_decode( $sJSON, true );
        
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
     * @see                http://stackoverflow.com/questions/2637945/getting-relative-path-from-absolute-path-in-php/2638272#2638272
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
    
    
    /**
     * Returns a truncated string.
     * @since       2.2.0
     * @return      string
     */
    static public function getTrancatedString( $sString, $iLength, $sSuffix='...' ) {        
        return ( self::getStringLength( $sString ) > $iLength )
            ? substr( $sString, 0, $iLength - self::getStringLength( $sSuffix ) ) . $sSuffix
            : $sString;
    }
    
    /**
     * Indicates whether the mb_strlen() exists or not.
     * @since   2.1.2
     */
    static private $_bFunctionExists_mb_strlen;
    
    /**
     * Returns the given string length.
     * @since           2.1.2
     */
    static public function getStringLength( $sString ) {
        
        self::$_bFunctionExists_mb_strlen = isset( self::$_bFunctionExists_mb_strlen )
            ? self::$_bFunctionExists_mb_strlen
            : function_exists( 'mb_strlen' );
        
        return self::$_bFunctionExists_mb_strlen
            ? mb_strlen( $sString )
            : strlen( $sString );        
        
    }
    
    /**
     * Indicates whether the mb_substr() exists or not.
     * @since   2.1.2
     */
    static private $_bFunctionExists_mb_substr;    
    
    /**
     * Returns the substring of the given subject string.
     * @since           2.1.2
     */
    static public function getSubstring( $sString, $iStart, $iLength=null, $sEncoding=null ) {

        self::$_bFunctionExists_mb_substr = isset( self::$_bFunctionExists_mb_substr )
            ? self::$_bFunctionExists_mb_substr
            : function_exists( 'mb_substr' ) && function_exists( 'mb_internal_encoding' );
        
        if ( ! self::$_bFunctionExists_mb_substr ) {
            return substr( $sString, $iStart, $iLength );
        }
        
        $sEncoding = isset( $sEncoding ) 
            ? $sEncoding 
            : mb_internal_encoding();
            
        return mb_substr( $sString, $iStart, $iLength, $sEncoding );
        
    }
    
}