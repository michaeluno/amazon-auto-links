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
 * Retrieves log items from the database.
 *
 * @since   4.4.0
 */
class AmazonAutoLinks_Unit_PAAPIRequestCounter_LogRetriever_Database extends AmazonAutoLinks_Unit_PAAPIRequestCounter_LogRetriever_Base {

    /**
     * @var array
     * @since  4.4.0
     */
    public $aAllLog = array();

    /**
     * Sets up properties and hooks.
     * @param string $sLocale
     * @since  4.4.0
     */
    public function __construct( $sLocale ) {
        parent::__construct( $sLocale );
        $this->set( $this->___getRawFromDatabase() );
    }

    /**
     * Saves the log into the database.
     * @since  4.4.0
     */
    public function save() {
        update_option( $this->___getOptionKey( $this->sLocale ), $this->aAllLog );
    }
        /**
         * @remark This retrieves all the stored data in the options table record without any specific time range.
         * @return array
         * @since  4.4.0
         */
        private function ___getRawFromDatabase() {
            return $this->getAsArray( get_option( $this->___getOptionKey( $this->sLocale ), array() ) );
        }

    /**
     * Sets the log.
     * @param array $aAllLog
     * @since 4.4.0
     */
    public function set( array $aAllLog ) {
        $this->aAllLog = $this->_getArraySortedByKeyRecursive( $aAllLog );
    }

    /**
     * @param integer $iStartTime
     * @param integer $iEndTime
     * @since 4.4.0
     */
    public function truncate( $iStartTime, $iEndTime ) {
        $this->set( $this->get( $iStartTime, $iEndTime ) );
    }

    /**
     * @since  4.4.0
     * @return boolean true if the count log data was deleted, false otherwise.
     */
    public function delete() {
        return delete_option( get_option( $this->___getOptionKey( $this->sLocale ) ) );
    }

    /**
     * @return array
     * @since  4.4.0
     */
    public function getAll() {
        return $this->aAllLog;
    }
    
    /**
     * @param  integer $iStartTime
     * @param  integer $iEndTime
     * @return array
     * @since  4.4.0
     */
    public function get( $iStartTime, $iEndTime ) {

        $_aAllLog = $this->getAll();
        
        ksort( $_aAllLog );

        $_iFirstItemTime = $this->_getFirstItemTime( $_aAllLog );

        // Update the start and end times.
        $_iNow           = time();
        $iStartTime      = $iStartTime < $_iFirstItemTime ? $_iFirstItemTime : $iStartTime;
        $iEndTime        = $iEndTime > $_iNow ? $_iNow : $iEndTime;

        // If empty, no need to parse log.
        if ( empty( $_aAllLog ) ) {
            return array();
        }
        // If the entire log is in the specified range, no need to parse.
        if ( $iStartTime <= $_iFirstItemTime && $iEndTime >= $_iNow ) {
            return $_aAllLog;
        }

        return $this->___getRaw( $_aAllLog, $iStartTime, $iEndTime );

    }
        /**
         * @param  array   $aStoredLog
         * @param  integer $iStartTime   The timestamp of the start of the range.
         * @param  integer $iEndTime     The timestamp of the end of the range.
         * @return array
         * @sicne  4.4.0
         */
        private function ___getRaw( array $aStoredLog, &$iStartTime, &$iEndTime ) {


            $_aCountLog      = array();


            $this->_setVariablesOfTime(
                $_sStartYear, $_sEndYear,
                $_sStartMonth, $_sEndMonth,
                $_sStartDate, $_sEndDate,
                $_sStartHour, $_sEndHour,
                $iStartTime, $iEndTime
            );

            foreach( $aStoredLog as $_iThisYear => $_aLogByYear ) { // the key gets converted to integer automatically in PHP
                $_sThisYear   = ( string ) $_iThisYear;
                if ( ! $this->_isInRange( ( integer ) $_sStartYear, ( integer ) $_sThisYear, ( integer ) $_sEndYear ) ) {
                    continue;
                }
                $_bFirstYear  = $_sStartYear === $_sThisYear;
                $_bLastYear   = $_sEndYear === $_sThisYear;
                $_aCountLog[ $_sThisYear ] = $this->___getLogByEachYear(
                    $_aLogByYear,
                    $_bFirstYear ? $_sStartMonth : null,
                    $_bLastYear ? $_sEndMonth : null,
                    $_bFirstYear ? $_sStartDate : null,
                    $_bLastYear ? $_sEndDate : null,
                    $_bFirstYear ? $_sStartHour : null,
                    $_bLastYear ? $_sEndHour : null,
                    $_sThisYear
                );
            }
            return $_aCountLog;

        }

            private function ___getLogByEachYear( array $aLogByYear, $sStartMonth, $sEndMonth, $sStartDate, $sEndDate, $sStartHour, $sEndHour, $sThisYear ) {
                $_sStartMonth = null === $sStartMonth ? '01' : $sStartMonth;
                $_sEndMonth   = null === $sEndMonth   ? '12' : $sEndMonth;
                ksort( $aLogByYear );
                $_aLogByYear  = array();
                foreach( $aLogByYear as $_isThisMonth => $_aLogByMonth ) {
                    $_sThisMonth = ( string ) $_isThisMonth;
                    if ( ! $this->_isInRange( ( integer ) $_sStartMonth, ( integer ) $_sThisMonth, ( integer ) $_sEndMonth ) ) {
                        continue;
                    }
                    $_bStartMonth = $sStartMonth === $_sThisMonth; // referring to the original parameter
                    $_bEndMonth   = $sEndMonth   === $_sThisMonth; // referring to the original parameter
                    $_aLogByYear[ $_sThisMonth ] = $this->___getLogByEachMonth(
                        $_aLogByMonth,
                        $_bStartMonth ? $sStartDate : null,
                        $_bEndMonth ? $sEndDate : null,
                        $_bStartMonth ? $sStartHour : null,
                        $_bEndMonth ? $sEndHour : null,
                        $sThisYear,
                        $_sThisMonth
                    );
                }
                return $_aLogByYear;
            }
                private function ___getLogByEachMonth( array $aLogByMonth, $sStartDate, $sEndDate, $sStartHour, $sEndHour, $sThisYear, $sThisMonth ) {
                    $_sStartDate  = null === $sStartDate ? '01' : $sStartDate;
                    $_sEndDate    = null === $sEndDate   ? $this->_getMaxDateOfThisMonth( strtotime( "{$sThisYear}-{$sThisMonth}-01" ) ) : $sEndDate;
                    ksort( $aLogByMonth );
                    $_aLogByMonth = array();
                    foreach( $aLogByMonth as $_isThisDate => $_aLogByDate ) {
                        $_sThisDate  = ( string ) $_isThisDate;
                        $_bStartDate = $sStartDate === $_sThisDate; // referring to the original parameter
                        $_bEndDate   = $sEndDate   === $_sThisDate; // referring to the original parameter
                        if ( ! $this->_isInRange( ( integer ) $_sStartDate, ( integer ) $_sThisDate, ( integer ) $_sEndDate ) ) {
                            continue;
                        }
                        $_aLogByMonth[ $_sThisDate ] = $this->___getLogByEachDate(
                            $_aLogByDate,
                            $_bStartDate ? $sStartHour : null,
                            $_bEndDate ? $sEndHour : null
                        );
                    }
                    return $_aLogByMonth;
                }
                    private function ___getLogByEachDate( array $aLogByDate, $sStartHour, $sEndHour ) {
                        $sStartHour   = null === $sStartHour ? '00' : $sStartHour;
                        $sEndHour     = null === $sEndHour   ? '23' : $sEndHour;
                        ksort( $aLogByDate );
                        $_aLogByDate  = array();
                        foreach( $aLogByDate as $_isThisHour => $_iThisCount ) {
                            $_sThisHour = ( string ) $_isThisHour;
                            if ( ! $this->_isInRange( ( integer ) $sStartHour, ( integer ) $_sThisHour, ( integer ) $sEndHour ) ) {
                                continue;
                            }
                            $_aLogByDate[ $_sThisHour ] = ( integer ) $_iThisCount;
                        }
                        return $_aLogByDate;
                    }

    /**
     * @param  string $sLocale
     * @since  4.4.0
     * @return string
     */
    private function ___getOptionKey( $sLocale ) {
        return isset( AmazonAutoLinks_Registry::$aOptionKeys[ 'paapi_request_counter' ][ $sLocale ] )
            ? AmazonAutoLinks_Registry::$aOptionKeys[ 'paapi_request_counter' ][ $sLocale ]
            : AmazonAutoLinks_Registry::$aOptionKeys[ 'paapi_request_counter' ][ 'US' ];
    }

}