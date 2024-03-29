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
 * Provides utility methods that deal with strings.
 
 * @since       3       
 */
class AmazonAutoLinks_Utility_String extends AmazonAutoLinks_AdminPageFramework_WPUtility {

    /**
     * @param  string $sVersion
     * @return string
     * @since  4.7.5
     */
    static public function getVersionSanitized( $sVersion ) {
        preg_match( '/^([\da-z.-]+)$/', trim( $sVersion ), $_aMatches );
        return isset( $_aMatches[ 1 ] ) ? ( string ) $_aMatches[ 1 ] : '';
    }

    /**
     * Checks if a given string is JSON.
     * @param  string $sString
     * @return boolean
     * @since  3.9.0
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
     * @since       4.2.2       Fixed a typo in a method name
     * @return      string
     */
    static public function getTruncatedString( $sString, $iLength, $sSuffix='...' ) {
        
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
     *
     * @param  string       $sString
     * @param  integer      $iStart
     * @param  integer|null $iLength
     * @param  string|null  $sEncoding
     * @return false|string
     * @since  3            Moved from `AmazonAutoLinks_Utility`
     * @since  2.1.2
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

    /**
     * @param  string $sPath
     * @return string
     * @since  5.3.0
     */
    static public function getDoubleSlashesToSingle( $sPath ) {
        return ( string ) preg_replace('/\/\//', '\/', $sPath );
    }

    /**
     * @return string A regex pattern that matches a URL
     * @since  5.3.0
     */
    static public function getRegexPattern_URL( $sDomain='amazon' ) {
        $_sPatternHref  = "("; // first element open
            $_sPatternHref .= "<"; // 1 start of the tag
            $_sPatternHref .= "\s*"; // 2 zero or more whitespace
            $_sPatternHref .= "a"; // 3 the a of the tag itself
            $_sPatternHref .= "\s+"; // 4 one or more whitespace
            $_sPatternHref .= "[^>]*"; // 5 zero or more of any character that is _not_ the end of the tag
            $_sPatternHref .= "href"; // 6 the href bit of the tag
            $_sPatternHref .= "\s*"; // 7 zero or more whitespace
            $_sPatternHref .= "="; // 8 the = of the tag
            $_sPatternHref .= "\s*"; // 9 zero or more whitespace
            $_sPatternHref .= '["\']?'; // 10 none or one of " or ' opening quote
        $_sPatternHref .= ')'; // first element close
        $_sPatternHref .= "("; // second element open
            $_sPatternHref .= 'https?:\/\/(www\.)?' . $sDomain .  '\.[^"\' >]+';   // URL
        $_sPatternHref .= ")"; // second element close
        $_sPatternHref .= "("; // fourth element
            $_sPatternHref .= '["\' >]'; // 14 closing characters of the bit we want to capture
        $_sPatternHref .= ')'; // fourth element close

        $_sNeedle  = "/"; // regex start delimiter
        $_sNeedle .= $_sPatternHref;
        $_sNeedle .= "/"; // regex end delimiter
        $_sNeedle .= "i"; // Pattern Modifier - makes regex case insensative
        $_sNeedle .= "s"; // Pattern Modifier - makes a dot metacharater in the pattern
        // match all characters, including newlines
        $_sNeedle .= "U"; // Pattern Modifier - makes the regex ungready
        return $_sNeedle;
    }
         
}