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
     * @since  4.4.0
     */
    public function setVariablesForChart( &$aDates, &$aCounts, $iStartTime, $iEndTime, $iGMTOffset=0, $bFillEmpty=true ) {

        $aDates         = $this->getAsArray( $aDates );
        $aCounts        = $this->getAsArray( $aCounts );
        $_aRawLog       = $this->___getRaw( $iStartTime - 86400, $iEndTime + 86400 ); // to cover a GMT offset, expand the range by one day each.
        $_iStartTime    = $iStartTime + $iGMTOffset;
        $_iEndTime      = $iEndTime + $iGMTOffset;
        $this->_setVariablesOfTime(
            $_sStartYear, $_sEndYear,
            $_sStartMonth, $_sEndMonth,
            $_sStartDate, $_sEndDate,
            $_sStartHour, $_sEndHour,
            $_iStartTime, $_iEndTime
        );
        $this->___setChartVariables( $aDates, $aCounts, $_aRawLog, $_sStartYear, $_sEndYear, $_sStartMonth, $_sEndMonth, $_sStartDate, $_sEndDate, $_sStartHour, $_sEndHour, null, $bFillEmpty );

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
         * @param callable $cFormatLabel
         * @param boolean  $bFillEmpty
         * @since 4.4.0
         */
        private function ___setChartVariables( array &$aDates, array &$aCounts, array $aRawLog, $sStartYear, $sEndYear, $sStartMonth, $sEndMonth, $sStartDate, $sEndDate, $sStartHour, $sEndHour, $cFormatLabel, $bFillEmpty ) {
            $aRawLog = $bFillEmpty ? $this->___getYearsFilled( $aRawLog, ( integer ) $sStartYear, ( integer ) $sEndYear ) : $aRawLog;
            foreach( $aRawLog as $_iThisYear => $_aLogByYear ) {    // be careful non-zero-padded numeric key gets automatically converted to an integer in PHP
                $_sThisYear   = ( string ) $_iThisYear;
                if ( ! $this->_isInRange( ( integer ) $sStartYear, ( integer ) $_sThisYear, ( integer ) $sEndYear ) ) {
                    continue;
                }
                $_bFirstYear  = $sStartYear === $_sThisYear;
                $_bLastYear   = $sEndYear === $_sThisYear;
                $this->___setChartVariablesByYear(
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
                    $cFormatLabel,
                    $bFillEmpty
                );
            }
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
                
        private function ___setChartVariablesByYear( array &$aDates, array &$aCounts, array $aLogByYear, $sStartMonth, $sEndMonth, $sStartDate, $sEndDate, $sStartHour, $sEndHour, $sThisYear, $cFormatLabel, $bFillEmpty ) {

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
                $this->___setChartVariablesByMonth(
                    $aDates,
                    $aCounts,
                    $_aLogByMonth,
                    $_bStartMonth ? $sStartDate : null,
                    $_bEndMonth ? $sEndDate : null,
                    $_bStartMonth ? $sStartHour : null,
                    $_bEndMonth ? $sEndHour : null,
                    $sThisYear,
                    $_sThisMonth,
                    $cFormatLabel,
                    $bFillEmpty
                );
            }
        }
            private function ___getYearFilled( array $aLogByYear, $iStartMonth, $iEndMonth ) {
                return $this->___getLogElementFilled( $aLogByYear, $iStartMonth, $iEndMonth, array() );
            }

            private function ___setChartVariablesByMonth( array &$aDates, array &$aCounts, array $aLogByMonth, $sStartDate, $sEndDate, $sStartHour, $sEndHour, $sThisYear, $sThisMonth, $cFormatLabel, $bFillEmpty ) {

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
                    $_aLogByMonth[ $_sThisDate ] = $this->___setChartVariablesByDate(
                        $aDates,
                        $aCounts,
                        $_aLogByDate,
                        $_bStartDate ? $sStartHour : null,
                        $_bEndDate   ? $sEndHour   : null,
                        $sThisYear,
                        $sThisMonth,
                        $_sThisDate,
                        $cFormatLabel,
                        $bFillEmpty
                    );
                }

            }
                private function ___getMonthFilled( array $aLogByMonth, $iStartDate, $iEndDate ) {
                    return $this->___getLogElementFilled( $aLogByMonth, $iStartDate, $iEndDate, array() );
                }
                    
                private function ___setChartVariablesByDate( array &$aDates, array &$aCounts, array $aLogByDate, $sStartHour, $sEndHour, $sThisYear, $sThisMonth, $sThisDate, $cFormatLabel, $bFillEmpty ) {
                    $sStartHour   = null === $sStartHour ? '00' : $sStartHour;
                    $sEndHour     = null === $sEndHour   ? '23' : $sEndHour;
                    $aLogByDate   = $bFillEmpty ? $this->___getDayFilled( $aLogByDate, ( integer ) $sStartHour, ( integer ) $sEndHour ) : $aLogByDate;
                    $_iCountOfDay = 0;
                    foreach( $aLogByDate as $_isThisHour => $_iThisCount ) { // be careful. Non-zero-padded numeric keys gets automatically converted to an integer in PHP.
                        $_sThisHour   = ( string ) $_isThisHour;
                        if ( ! $this->_isInRange( ( integer ) $sStartHour, ( integer ) $_sThisHour, ( integer ) $sEndHour ) ) {
                            continue;
                        }
                        $_iCountOfDay = $_iCountOfDay + ( ( integer ) $_iThisCount );
                    }
                    $_sLabel   = "$sThisYear-$sThisMonth-$sThisDate";
                    $aDates[]  = is_callable( $cFormatLabel )
                        ? call_user_func_array( $cFormatLabel, array( $_sLabel, $sThisYear, $sThisMonth, $sThisDate ) )
                        : $_sLabel;
                    $aCounts[] = $_iCountOfDay;
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
     * @param  integer $iStartTime
     * @param  integer $iEndTime
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

}