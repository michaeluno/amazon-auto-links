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
 * A class that provides utility methods for the PA-API request counter sub unit component.
 * @since 4.4.0
 */
class AmazonAutoLinks_Unit_PAAPIRequestCounter_Utility extends AmazonAutoLinks_Unit_Utility {

    /**
     * @param  boolean $bGMTOffset Whether to adjust the time with GMT offset.
     * @return integer
     * @since  4.4.0
     */
    static public function getDefaultChartStartTime( $bGMTOffset=false ) {
        return ( ( integer ) strtotime('tomorrow midnight' ) )
            - ( 86400 * 7 )
            + ( $bGMTOffset ? self::getGMTOffset() : 0 );
    }

    /**
     * @param  boolean $bGMTOffset Whether to adjust the time with GMT offset.
     * @return integer
     * @since  4.4.0
     */
    static public function getDefaultChartEndTime( $bGMTOffset=false ) {
        return time() // now
            + ( $bGMTOffset ? self::getGMTOffset() : 0 );
    }

    /**
     * @param  array  $aList
     * @param  string $sDelimiter
     * @param  string $sEnclosure
     * @param  string $sEscapeChar
     * @see    https://stackoverflow.com/questions/13108157/php-array-to-csv/53882337#53882337
     * @return string
     * @since  4.4.0
     */
    static public function getCSV( array $aList, $sDelimiter=',', $sEnclosure='"', $sEscapeChar= "\\" ) {
        $_rFile = fopen('php://memory', 'r+' ); // r+
        foreach( $aList as $_aFields ) {
            fputcsv( $_rFile, $_aFields, $sDelimiter, $sEnclosure, $sEscapeChar );
        }
        rewind( $_rFile );
        $_bsCSV = stream_get_contents( $_rFile );
        return false === $_bsCSV
            ? ''
            : rtrim( $_bsCSV );
    }

    /**
     * @param  string $sDirPath
     * @param  null|callable $cCallable
     * @param  boolean $bRecursive
     * @return string[] Found directory paths.
     */
    static public function getDirectoriesFromFileSystem( $sDirPath, $cCallable=null, $bRecursive=true ) {
        $_aDirPaths = array();
        foreach ( new DirectoryIterator( $sDirPath ) as $_oFileInfo ) {
            if ( $_oFileInfo->isDot() ) {
                continue;
            }
            if ( ! $_oFileInfo->isDir() ) {
                continue;
            }
            if ( $bRecursive ) {
                $_aDirPaths = array_merge( $_aDirPaths, self::getDirectoriesFromFileSystem( $_oFileInfo->getRealPath(), $cCallable ) );
            }
            $_sPath = $_oFileInfo->getRealPath();
            $_sPath = is_callable( $cCallable ) ? call_user_func_array( $cCallable, array( $_sPath ) ) : $_sPath;
            if ( $_sPath ) {
                $_aDirPaths[] = $_sPath;
            }
        }
        return $_aDirPaths;
    }
    
    /**
     * @param  string   $sDirPath
     * @param  string[] $aExtensions
     * @param  callable $cCallable   The function to format found paths.
     * @param  boolean  $bRecursive
     * @return string[] The found file paths.
     * @since  4.4.0
     */
    static public function getFilesFromFileSystem( $sDirPath, $aExtensions=array( 'txt' ), $cCallable=null, $bRecursive=true ) {

        $_aFilePaths = array();
        foreach ( new DirectoryIterator( $sDirPath ) as $_oFileInfo ) {
            if ( $_oFileInfo->isDot() ) {
                continue;
            }
            if ( $_oFileInfo->isFile() ) {
                if ( ! in_array( $_oFileInfo->getExtension(), $aExtensions, true ) ) {
                    continue;
                }
                $_sPath = $_oFileInfo->getRealPath();
                $_sPath = is_callable( $cCallable ) ? call_user_func_array( $cCallable, array( $_sPath ) ) : $_sPath;
                if ( $_sPath ) {
                    $_aFilePaths[] = $_sPath;
                }
                continue;
            }
            if ( $_oFileInfo->isDir() && $bRecursive ) {
                $_aFilePaths = array_merge( $_aFilePaths, self::getFilesFromFileSystem( $_oFileInfo->getRealPath(), $aExtensions, $cCallable, $bRecursive ) );
            }
        }
        return $_aFilePaths;

    }

    /**
     * @since  4.4.0
     * @return string
     */
    static public function getDefaultLocale() {
        $_oOption = AmazonAutoLinks_Option::getInstance();
        return $_oOption->getMainLocale();
    }

    /**
     * @param  integer $iTimeFrom
     * @param  integer $iTimeTo
     * @return integer
     * @since  4.4.0
     * @see    https://stackoverflow.com/questions/2040560/finding-the-number-of-days-between-two-dates/2040589#2040589
     * @deprecated unused
     */
/*    static public function getDaysBetween( $iTimeFrom, $iTimeTo ) {
        if ( version_compare( PHP_VERSION, '5.3.0', '>=' ) ) {
            $_oFrom = new DateTime("@{$iTimeFrom}" );
            $_oTo   = new DateTime("@{$iTimeTo}" );
            return $_oTo->diff( $_oFrom )->format("%a" );
        }
        $_iDiff  = $iTimeTo - $iTimeFrom;
        $_iRound = round( $_iDiff / ( 60 * 60 * 24 ) );
        return ( integer ) $_iRound;
    }*/

}


