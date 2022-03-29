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
 * Retrieves PA-API request count log data.
 *
 * @since   4.4.0
 */
class AmazonAutoLinks_Unit_PAAPIRequestCounter_LogRetriever extends AmazonAutoLinks_Unit_PAAPIRequestCounter_LogRetriever_Base {

    /**
     * @param  array  &$aDates     Site GMT-compliant dates.
     * @param  array  &$aCounts    Request counts.
     * @param  integer $iStartTime The timestamp of the start time of the time range, not GMT compliant.
     * @param  integer $iEndTime   The timestamp of the end time of the time range, not GMT compliant.
     * @param  integer $iGMTOffset GMT offset seconds, not hours.
     * @param  boolean $bFillEmpty Whether to apply 0 to all non existent records.
     * @param  boolean $bTrim      Whether to trim unrecorded items at the beginning and the ending.
     * @since  4.4.0
     * @return array   A log by hour. For charts, use the updated $aDates and $aCounts values.
     */
    public function getCountLog( &$aDates, &$aCounts, $iStartTime, $iEndTime, $iGMTOffset=0, $bFillEmpty=true, $bTrim=false ) {

        $aDates          = $this->getAsArray( $aDates );
        $aCounts         = $this->getAsArray( $aCounts );
        $_iNow           = time();
        $_iStartTime     = absint( ( integer ) $iStartTime - 86400 );
        $_iEndTime       = $iEndTime > $_iNow ? $_iNow : ( integer ) $iEndTime;
        $_aRawLog        = $this->___getRaw( $_iStartTime, $_iEndTime + 86400 ); // to cover a GMT offset, expand the range by one day each.
        if ( $bTrim ) {
            $_iStartTime = $this->_getFirstItemTime( $_aRawLog );
            $_iEndTime  = $this->_getLastItemTime( $_aRawLog );
        }

        $_iStartTime     = $_iStartTime + $iGMTOffset;
        $_iEndTime       = $_iEndTime + $iGMTOffset;
        $this->_setVariablesOfTime(
            $_sStartYear, $_sEndYear,
            $_sStartMonth, $_sEndMonth,
            $_sStartDate, $_sEndDate,
            $_sStartHour, $_sEndHour,
            $_iStartTime, $_iEndTime
        );
        return $this->___getLog( $aDates, $aCounts, $_aRawLog, $_sStartYear, $_sEndYear, $_sStartMonth, $_sEndMonth, $_sStartDate, $_sEndDate, $_sStartHour, $_sEndHour, $bFillEmpty );

    }

        /**
         * @param array   &$aDates
         * @param array   &$aCounts
         * @param array   &$aRawLog
         * @param string   $sStartYear
         * @param string   $sEndYear
         * @param string   $sStartMonth
         * @param string   $sEndMonth
         * @param string   $sStartDate
         * @param string   $sEndDate
         * @param string   $sStartHour
         * @param string   $sEndHour
         * @param boolean  $bFillEmpty
         * @since 4.4.0
         * @return array
         */
        private function ___getLog( array &$aDates, array &$aCounts, array $aRawLog, $sStartYear, $sEndYear, $sStartMonth, $sEndMonth, $sStartDate, $sEndDate, $sStartHour, $sEndHour, $bFillEmpty ) {
            $_aLogByHour = array();
            $aRawLog     = $bFillEmpty ? $this->___getYearsFilled( $aRawLog, ( integer ) $sStartYear, ( integer ) $sEndYear ) : $aRawLog;
            foreach( $aRawLog as $_iThisYear => $_aLogByYear ) {    // be careful non-zero-padded numeric key gets automatically converted to an integer in PHP
                $_sThisYear   = ( string ) $_iThisYear;
                if ( ! $this->_isInRange( ( integer ) $sStartYear, ( integer ) $_sThisYear, ( integer ) $sEndYear ) ) {
                    continue;
                }
                $_bFirstYear  = $sStartYear === $_sThisYear;
                $_bLastYear   = $sEndYear === $_sThisYear;
                $_aLogByHour  = $_aLogByHour + $this->___getLogByYear(
                    $aDates,
                    $aCounts,
                    $_aLogByYear,
                    $_bFirstYear ? $sStartMonth : null,
                    $_bLastYear ? $sEndMonth : null,
                    $_bFirstYear ? $sStartDate : null,
                    $_bLastYear ? $sEndDate : null,
                    $_bFirstYear ? $sStartHour : null,
                    $_bLastYear ? $sEndHour : null,
                    $_sThisYear,
                    $bFillEmpty
                );
            }
            return $_aLogByHour;
        }
            private function ___getYearsFilled( array $aLog, $iStartYear, $iEndYear ) {
                $_iThisYear = $iStartYear;
                $_bModified = false;
                while( $_iThisYear <= $iEndYear ) {
                    if ( ! isset( $aLog[ $_iThisYear ] ) ) {
                        $aLog[ $_iThisYear ] = array();
                        $_bModified = true;
                    }
                    $_iThisYear++;
                }
                if ( $_bModified ) {
                    ksort( $aLog );
                }
                return $aLog;
            }
                
        private function ___getLogByYear( array &$aDates, array &$aCounts, array $aLogByYear, $sStartMonth, $sEndMonth, $sStartDate, $sEndDate, $sStartHour, $sEndHour, $sThisYear, $bFillEmpty ) {

            $_aLogByHour  = array();
            $_sStartMonth = null === $sStartMonth ? '01' : $sStartMonth;
            $_sEndMonth   = null === $sEndMonth   ? '12' : $sEndMonth;
            $aLogByYear   = $bFillEmpty ? $this->___getYearFilled( $aLogByYear, ( integer ) $_sStartMonth, ( integer ) $_sEndMonth ) : $aLogByYear;
            foreach( $aLogByYear as $_isThisMonth => $_aLogByMonth ) { // be careful. Non-zero-padded numeric keys gets automatically converted to an integer in PHP.
                $_sThisMonth = ( string ) $_isThisMonth;
                if ( ! $this->_isInRange( ( integer ) $_sStartMonth, ( integer ) $_sThisMonth, ( integer ) $_sEndMonth ) ) {
                    continue;
                }
                $_bStartMonth = $sStartMonth === $_sThisMonth; // referring to the original parameter
                $_bEndMonth   = $sEndMonth   === $_sThisMonth; // referring to the original parameter
                $_aLogByHour  = $_aLogByHour + $this->___getLogByMonth(
                    $aDates,
                    $aCounts,
                    $_aLogByMonth,
                    $_bStartMonth ? $sStartDate : null,
                    $_bEndMonth ? $sEndDate : null,
                    $_bStartMonth ? $sStartHour : null,
                    $_bEndMonth ? $sEndHour : null,
                    $sThisYear,
                    $_sThisMonth,
                    $bFillEmpty
                );
            }
            return $_aLogByHour;
            
        }
            private function ___getYearFilled( array $aLogByYear, $iStartMonth, $iEndMonth ) {
                return $this->___getLogElementFilled( $aLogByYear, $iStartMonth, $iEndMonth, array() );
            }

            private function ___getLogByMonth( array &$aDates, array &$aCounts, array $aLogByMonth, $sStartDate, $sEndDate, $sStartHour, $sEndHour, $sThisYear, $sThisMonth, $bFillEmpty ) {
                $_aLogByHour  = array();
                $_sStartDate  = null === $sStartDate ? '01' : $sStartDate;
                $_sEndDate    = null === $sEndDate   ? $this->_getMaxDateOfThisMonth( strtotime( "{$sThisYear}-{$sThisMonth}-01" ) ) : $sEndDate;
                $aLogByMonth  = $bFillEmpty ? $this->___getMonthFilled( $aLogByMonth, ( integer ) $_sStartDate, ( integer ) $_sEndDate ) : $aLogByMonth;
                foreach( $aLogByMonth as $_isThisDate => $_aLogByDate ) {   // be careful. Non-zero-padded numeric keys gets automatically converted to an integer in PHP.
                    $_sThisDate  = ( string ) $_isThisDate;
                    $_bStartDate = $sStartDate === $_sThisDate; // referring to the original parameter
                    $_bEndDate   = $sEndDate   === $_sThisDate; // referring to the original parameter
                    if ( ! $this->_isInRange( ( integer ) $_sStartDate, ( integer ) $_sThisDate, ( integer ) $_sEndDate ) ) {
                        continue;
                    }
                    $_aLogByHour = $_aLogByHour + $this->___getLogByDate(
                        $aDates,
                        $aCounts,
                        $_aLogByDate,
                        $_bStartDate ? $sStartHour : null,
                        $_bEndDate   ? $sEndHour   : null,
                        $sThisYear,
                        $sThisMonth,
                        $_sThisDate,
                        $bFillEmpty
                    );
                }
                return $_aLogByHour;
            }
                private function ___getMonthFilled( array $aLogByMonth, $iStartDate, $iEndDate ) {
                    return $this->___getLogElementFilled( $aLogByMonth, $iStartDate, $iEndDate, array() );
                }
                    
                private function ___getLogByDate( array &$aDates, array &$aCounts, array $aLogByDate, $sStartHour, $sEndHour, $sThisYear, $sThisMonth, $sThisDate, $bFillEmpty ) {
                    $_aLogByHour  = array();
                    $sStartHour   = null === $sStartHour ? '00' : $sStartHour;
                    $sEndHour     = null === $sEndHour   ? '23' : $sEndHour;
                    $aLogByDate   = $bFillEmpty ? $this->___getDayFilled( $aLogByDate, ( integer ) $sStartHour, ( integer ) $sEndHour ) : $aLogByDate;
                    $_iCountOfDay = 0;
                    foreach( $aLogByDate as $_isThisHour => $_iThisCount ) { // be careful. Non-zero-padded numeric keys gets automatically converted to an integer in PHP.
                        $_sThisHour   = ( string ) $_isThisHour;
                        if ( ! $this->_isInRange( ( integer ) $sStartHour, ( integer ) $_sThisHour, ( integer ) $sEndHour ) ) {
                            continue;
                        }
                        $_iThisCount  = ( integer ) $_iThisCount;
                        $_iCountOfDay = $_iCountOfDay + ( $_iThisCount );
                        $_sHourLabel  = "{$sThisYear}-{$sThisMonth}-{$sThisDate} {$_sThisHour}:00";
                        $_aLogByHour[ $_sHourLabel ] = $_iThisCount;
                    }
                    $_sLabel   = "$sThisYear-$sThisMonth-$sThisDate";
                    $aDates[]  = apply_filters( 'aal_filter_paapi_request_counter_chart_date_label', $_sLabel, $sThisYear, $sThisMonth, $sThisDate );
                    $aCounts[] = $_iCountOfDay;
                    return $_aLogByHour;
                }
                    private function ___getDayFilled( array $aLogByDate, $iStartHour, $iEndHour ) {
                        return $this->___getLogElementFilled( $aLogByDate, $iStartHour, $iEndHour, 0 );
                    }

    /**
     * @param  array   $aLogElement
     * @param  integer $iStart
     * @param  integer $iEnd
     * @param  mixed   $mValue   The filling value.
     * @return array
     */
    private function ___getLogElementFilled( array $aLogElement, $iStart, $iEnd, $mValue ) {
        $_iThis     = $iStart;
        $_bModified = false;
        while( $_iThis <= $iEnd ) {
            $_sThis = sprintf( '%02d', $_iThis );
            if ( ! ( isset( $aLogElement[ $_iThis ] ) || isset( $aLogElement[ $_sThis ] ) ) ) {
                $aLogElement[ $_sThis ] = $mValue;
                $_bModified = true;
            }
            $_iThis++;
        }
        if ( $_bModified ) {
            ksort( $aLogElement );
        }
        return $aLogElement;
    }

    /**
     * The export log format is CSV with columns of Time and Count.
     *
     * ```
     * Time, Count
     * 2020-10-28 05:00, 5
     * 2020-10-28 06:00, 1
     * 2020-10-28 07:00, 10
     * ```
     * @param  integer $iStartTime
     * @param  integer $iEndTime
     * @return string
     * @since  4.4.0
     */
    public function getAsCSV( $iStartTime, $iEndTime ) {
        $_aLogAsCSV = $this->getCountLog( $_aDates, $_aCounts, $iStartTime, $iEndTime, 0, true, true );
        $_aList     = array( array( 'Time', 'Count', 'Locale' ) ); // saving the locale in the data. Although the locale is embedded in the file name, the user might change the file name.
        foreach( $_aLogAsCSV as $_sTime => $_iCount ) {
            $_aList[] = array( $_sTime, $_iCount, $this->sLocale );
        }
        return $this->getCSV( $_aList );
    }

    /**
     * @param  integer &$iStartTime  Will be updated to the oldest log time.
     * @param  integer &$iEndTime    Will be updated to the newest log time.
     * @return array   Non-formatted log data.
     * @since  4.4.0
     */
    private function ___getRaw( $iStartTime, $iEndTime ) {
        $_oDatabaseLogData = new AmazonAutoLinks_Unit_PAAPIRequestCounter_LogRetriever_Database( $this->sLocale );
        $_aLogFromDatabase = $_oDatabaseLogData->get( $iStartTime, $iEndTime );
        $_oFileLogData     = new AmazonAutoLinks_Unit_PAAPIRequestCounter_LogRetriever_File( $this->sLocale );
        $_aLogFromFiles    = $_oFileLogData->get( $iStartTime, $iEndTime );
        return $this->uniteArrays( $_aLogFromDatabase, $_aLogFromFiles );
    }

    /**
     * @since 4.4.0
     */
    public function delete() {
        $_oDatabaseLogData = new AmazonAutoLinks_Unit_PAAPIRequestCounter_LogRetriever_Database( $this->sLocale );
        $_oDatabaseLogData->delete();
        $_oFileLogData     = new AmazonAutoLinks_Unit_PAAPIRequestCounter_LogRetriever_File( $this->sLocale );
        $_oFileLogData->delete();
    }

}