<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 */

/**
 * Handles counting PA-API requests.
 *
 * @since   4.4.0
 */
class AmazonAutoLinks_Unit_PAAPIRequestCounter_LogRetriever_File extends AmazonAutoLinks_Unit_PAAPIRequestCounter_LogRetriever_Base {

    /**
     * @since  4.4.0
     * @return boolean true if the count log data was deleted, false otherwise.
     */
    public function delete() {
        $_oCounter = new AmazonAutoLinks_VersatileFileManager_PAAPI_RequestCounter( $this->sLocale );
        return $this->emptyDirectory( $_oCounter->getDirectoryPath() );
    }


    /**
     * @param  integer $iStartTime  If 0, starts from the first found item.
     * @param  integer $iEndTime    If larger than the current time, the current time will be applied.
     * @param  array   $aFilePaths  Stores the parsed file paths. Used to move log data into the database.
     * @return array
     * @since  4.4.0
     */
    public function get( $iStartTime, $iEndTime, &$aFilePaths=array() ) {
        $aFilePaths        = $this->getAsArray( $aFilePaths );
        $_oCounter         = new AmazonAutoLinks_VersatileFileManager_PAAPI_RequestCounter( $this->sLocale );
        $_sDirPath         = $_oCounter->getDirectoryPath();
        $_bIsDir           = is_dir( $_sDirPath );
        $_iFirstItemTime   = $_bIsDir ? $this->___getFirstFoundItemTime( $_sDirPath ) : 0;
        $_iNow             = time();
        $iStartTime        = $iStartTime < $_iFirstItemTime ? $_iFirstItemTime : $iStartTime;
        $iEndTime          = $iEndTime <= $_iNow ? $iEndTime : $_iNow;
        if ( ! $_bIsDir ) {
            return array();
        }
        return $this->___getRaw( $_sDirPath, $iStartTime, $iEndTime, $aFilePaths );
    }

    /**
     * @param  string  $sDirPath    The subject directory.
     * @param  integer $iStartTime  The timestamp of the start of the range, not GMT compliant.
     * @param  integer $iEndTime    The timestamp of the end of the range, not GMT compliant.
     * @param  array  &$aFilePaths
     * @return array   Returns the raw log data of the given time range.
     * @remark Directory structure: .../{locale}/{year}/{month}/{day} and getDirectoryPath() gives till .../{locale}
     * @since  4.4.0
     */
    private function ___getRaw( $sDirPath, $iStartTime, $iEndTime, array &$aFilePaths ) {

        $_aCountLog        = array();
        $this->_setVariablesOfTime(
            $_sStartYear, $_sEndYear,
            $_sStartMonth, $_sEndMonth,
            $_sStartDate, $_sEndDate,
            $_sStartHour, $_sEndHour,
            $iStartTime, $iEndTime
        );
        $_aYearDirPaths    = $this->___getYearDirectoryPaths( $_sStartYear, $_sEndYear, $sDirPath );
        $_sLastYearDirPath = end($_aYearDirPaths );
        $_sFirstYearPath   = reset( $_aYearDirPaths ); // Rewind the pointer. can be null if not found
        if ( ! $_sFirstYearPath ) {
            return $_aCountLog;
        }

        foreach( $_aYearDirPaths as $_sYearDirectoryPath ) {
            $_sThisYear  = basename( $_sYearDirectoryPath );
            $_bFirstYear = $_sFirstYearPath === $_sYearDirectoryPath;
            $_bLastYear  = $_sLastYearDirPath === $_sYearDirectoryPath;
            $_aCountLog[ $_sThisYear ] = $this->___getLogByEachYear(
                $_sYearDirectoryPath,
                $_bFirstYear ? $_sStartMonth : null,
                $_bLastYear ? $_sEndMonth : null,
                $_bFirstYear ? $_sStartDate : null,
                $_bLastYear ? $_sEndDate : null,
                $_bFirstYear ? $_sStartHour : null,
                $_bLastYear ? $_sEndHour : null,
                $_sThisYear,
                $aFilePaths
            );
        }
        return $_aCountLog;
        
    }
        /**
         * @param  string  $sDirPath
         * @return integer
         * @remark Uses `scandir()` to parse directory items alphabetically.
         */
        private function ___getFirstFoundItemTime( $sDirPath ) {

            $_sYear  = $this->___getFirstFoundDirName( $sDirPath );
            if ( ! $_sYear ) {
                return 0;
            }

            // At this point a year is found. But the year directory might be empty.
            $_aIgnoreNames = array();
            while ( ! strlen( $_sMonth = $this->___getFirstFoundDirName( "{$sDirPath}/{$_sYear}" ) )  ) { // the year directory is empty
                $_aIgnoreNames[] = $_sYear;
                $_sYear = $this->___getFirstFoundDirName( $sDirPath, $_aIgnoreNames ); // check another year as the year directory is empty
                if ( ! strlen( $_sYear ) ) {
                    return 0;   // not found
                }
            }

            // At this point a month is found. But the month directory might be empty.
            $_aIgnoreNames = array();
            while ( ! strlen( $_sDate = $this->___getFirstFoundDirName( "{$sDirPath}/{$_sYear}/{$_sMonth}" ) ) ) {
                $_aIgnoreNames[] = $_sMonth;
                $_sMonth = $this->___getFirstFoundDirName( "{$sDirPath}/{$_sYear}", $_aIgnoreNames ); // check another year as the year directory is empty
                if ( ! strlen( $_sMonth ) ) {
                    return 0;   // not found
                }
            }

            // At this point, a date is found. But the date directory might be empty.
            $_aIgnoreNames = array();
            while ( ! strlen( $_sFileName = $this->___getFirstFoundFileName( "{$sDirPath}/{$_sYear}/{$_sMonth}/{$_sDate}" ) ) ) {
                $_aIgnoreNames[] = $_sDate;
                $_sDate = $this->___getFirstFoundDirName( "{$sDirPath}/{$_sYear}/{$_sMonth}", $_aIgnoreNames ); // check another month as the month directory is empty
                if ( ! strlen( $_sDate ) ) {
                    return 0;   // not found
                }
            }

            // At this point, the first log item is found.
            $_sFilePath = "{$sDirPath}/{$_sYear}/{$_sMonth}/{$_sDate}/{$_sFileName}";
            $_sHour = pathinfo( $_sFilePath, PATHINFO_FILENAME );
            return strtotime( "{$_sYear}-{$_sMonth}-{$_sDate} {$_sHour}:00" );

        }
            private function ___getFirstFoundFileName( $sDirPath, array $aIgnoreNames=array() ) {
                return $this->___getMostEndItemInFileSystem( $sDirPath, $aIgnoreNames, false );
            }
            private function ___getFirstFoundDirName( $sDirPath, array $aIgnoreNames=array() ) {
                return $this->___getMostEndItemInFileSystem( $sDirPath, $aIgnoreNames, true );
            }

            /**
             * Finds the most end item either of the first or the last in a given directory.
             *
             * @param  string  $sDirPath
             * @param  array   $aIgnoreNames
             * @param  boolean $bDir            If true, a found directory base name will be returned. Otherwise, a found file base name will be returned.
             * @param  boolean $bFindFirst      If true, tries to find the first item. Otherwise, last.
             * @return mixed|string
             * @since  4.4.0
             */
            private function ___getMostEndItemInFileSystem( $sDirPath, array $aIgnoreNames, $bDir, $bFindFirst=true ) {
                $_aIgnoreNames = array_merge( $aIgnoreNames, array( '.', '..' ) );
                $_aItems = scandir( $sDirPath );
                $_aItems = $bFindFirst ? $_aItems : array_reverse( $_aItems );
                foreach( $_aItems as $_sFileOrDir )  {
                    if ( in_array( $_sFileOrDir, $_aIgnoreNames, true ) ) {
                        continue;
                    }
                    // Only need the first item.
                    if ( $bDir && is_dir( "$sDirPath/$_sFileOrDir" ) ) {
                        return $_sFileOrDir;
                    }
                    if ( ! $bDir && ! is_dir( "$sDirPath/$_sFileOrDir" )  ) {
                        return $_sFileOrDir;
                    }
                }
                return '';
            }

        private function ___getLogByEachYear( $sYearDirectoryPath, $sStartMonth, $sEndMonth, $sStartDate, $sEndDate, $sStartHour, $sEndHour, $sThisYear, array &$aFilePaths=array() ) {
            $_sStartMonth     = null === $sStartMonth ? '01' : $sStartMonth;
            $_sEndMonth       = null === $sEndMonth   ? '12' : $sEndMonth;
            $_aCountLogByYear = array();
            $_aMonthDirPaths  = $this->getDirectoriesFromFileSystem( $sYearDirectoryPath, 'wp_normalize_path', false );
            foreach( $_aMonthDirPaths as $_sThisMonthDirectoryPath ) {
                $_sThisMonth = basename( $_sThisMonthDirectoryPath );
                if ( ! $this->_isInRange( $_sStartMonth, $_sThisMonth, $_sEndMonth ) ) {
                    continue;
                }
                $_bStartMonth = $sStartMonth === $_sThisMonth; // referring to the original parameter
                $_bEndMonth   = $sEndMonth   === $_sThisMonth; // referring to the original parameter
                $_aCountLogByYear[ $_sThisMonth ] = $this->___getLogByEachMonth(
                    $_sThisMonthDirectoryPath,
                    $_bStartMonth ? $sStartDate : null,
                    $_bEndMonth ? $sEndDate : null,
                    $_bStartMonth ? $sStartHour : null,
                    $_bEndMonth ? $sEndHour : null,
                    $sThisYear,
                    $_sThisMonth,
                    $aFilePaths
                );
            }
            return $_aCountLogByYear;
        }
            private function ___getLogByEachMonth( $sMonthDirectoryPath, $sStartDate, $sEndDate, $sStartHour, $sEndHour, $sThisYear, $sThisMonth, array &$aFilePaths ) {
                $_sStartDate       = null === $sStartDate ? '01' : $sStartDate;
                $_sEndDate         = null === $sEndDate   ? $this->_getMaxDateOfThisMonth( strtotime( "{$sThisYear}-{$sThisMonth}-01" ) ) : $sEndDate;
                $_aCountLogByMonth = array();
                $_aDateDirPaths    = $this->getDirectoriesFromFileSystem( $sMonthDirectoryPath, 'wp_normalize_path', false );
                foreach( $_aDateDirPaths as $_sDateDirectoryPath ) {
                    $_sDate = basename( $_sDateDirectoryPath );
                    $_bStartDate = $sStartDate === $_sDate; // referring to the original parameter
                    $_bEndDate   = $sEndDate   === $_sDate; // referring to the original parameter
                    if ( ! $this->_isInRange( ( integer ) $_sStartDate, ( integer ) $_sDate, ( integer ) $_sEndDate ) ) {
                        continue;
                    }
                    $_aCountLogByMonth[ $_sDate ] = $this->___getLogByEachDate(
                        $_sDateDirectoryPath,
                        $_bStartDate ? $sStartHour : null,
                        $_bEndDate ? $sEndHour : null,
                        $aFilePaths
                    );
                }
                return $_aCountLogByMonth;
            }
                private function ___getLogByEachDate( $sDateDirectoryPath, $sStartHour=null, $sEndHour=null, array &$aFilePaths=array() ) {
                    $sStartHour  = null === $sStartHour ? '00' : $sStartHour;
                    $sEndHour    = null === $sEndHour   ? '23' : $sEndHour;
                    $_aLogByDate = array();
                    $_aFilePaths = $this->getFilesFromFileSystem( $sDateDirectoryPath, array( 'txt' ), 'wp_normalize_path' );
                    foreach( $_aFilePaths as $_sFilePath ) {
                        $_sHour = pathinfo( $_sFilePath, PATHINFO_FILENAME );
                        if ( ! $this->_isInRange( ( integer ) $sStartHour, ( integer ) $_sHour, ( integer ) $sEndHour ) ) {
                            continue;
                        }
                        $_aLogByDate[ $_sHour ] = ( integer ) file_get_contents( $_sFilePath );
                        $aFilePaths[] = $_sFilePath;
                    }
                    return $_aLogByDate;
                }

        /**
         * @param  string   $sStartYear
         * @param  string   $sEndYear
         * @param  string   $sBaseDirPath
         * @return string[] Found year directories in a time range that actually exist.
         * @since  4.4.0
         */
        private function ___getYearDirectoryPaths( $sStartYear, $sEndYear, $sBaseDirPath ) {
            $_aDirPaths  = array();
            $_iStartYear = ( ( integer ) $sStartYear ) - 1;
            $_iEndYear   = ( integer ) $sEndYear;
            while ( $_iStartYear <= $_iEndYear ) {
                $_iStartYear++;
                $_sDirPath = $sBaseDirPath . '/' . $_iStartYear;
                if ( is_dir( $_sDirPath ) ) {
                    $_aDirPaths[] = $_sDirPath;
                }
            }
            return $_aDirPaths;
        }

}