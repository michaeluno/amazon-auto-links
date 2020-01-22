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
 * Provides utility methods that deal with strings.
 
 * @since       3       
 */
class AmazonAutoLinks_Utility_String extends AmazonAutoLinks_AdminPageFramework_WPUtility {

    /**
     * Checks if a given string is JSON.
     * @param $sString
     * @return bool
     * @since   3.9.0
     */
    static public function isJSON( $sString ) {
       json_decode( $sString );
       return ( json_last_error() == JSON_ERROR_NONE );
    }

    /**
     * Converts characters not supported to be used in the URL query key to underscore.
     * 
     * @see         http://stackoverflow.com/questions/68651/can-i-get-php-to-stop-replacing-characters-in-get-or-post-arrays
     * @return      string      The sanitized string.
     */
    static public function sanitizeCharsForURLQueryKey( $sString ) {

        $_aSearch = array( chr( 32 ), chr( 46 ), chr( 91 ) );
        for ( $_i=128; $_i <= 159; $_i++ ) {
            array_push( $_aSearch, chr( $_i ) );
        }
        return str_replace ( $_aSearch , '_', $sString );
        
    }    
    
    /**
     * Returns a truncated string.
     * @since       2.2.0
     * @since       3           Moved from `AmazonAutoLinks_Utility`
     * @return      string
     */
    static public function getTrancatedString( $sString, $iLength, $sSuffix='...' ) {
        
        return ( self::getStringLength( $sString ) > $iLength )
            ? self::getSubstring( 
                    $sString, 
                    0, 
                    $iLength - self::getStringLength( $sSuffix )
                ) . $sSuffix
                // ? substr( $sString, 0, $iLength - self::getStringLength( $sSuffix ) ) . $sSuffix
            : $sString;
            
    }
    
    /**
     * Indicates whether the mb_strlen() exists or not.
     * @since       2.1.2
     * @since       3           Moved from `AmazonAutoLinks_Utility`
     */
    static private $_bFunctionExists_mb_strlen;
    
    /**
     * Returns the given string length.
     * @since       2.1.2
     * @since       3           Moved from `AmazonAutoLinks_Utility`
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
     * @since       2.1.2
     * @since       3           Moved from `AmazonAutoLinks_Utility`
     */
    static private $_bFunctionExists_mb_substr;    
    
    /**
     * Returns the substring of the given subject string.
     * @since       2.1.2
     * @since       3           Moved from `AmazonAutoLinks_Utility`
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
            
        return mb_substr( 
            $sString, 
            $iStart, 
            $iLength, 
            $sEncoding 
        );
        
    }
         
}