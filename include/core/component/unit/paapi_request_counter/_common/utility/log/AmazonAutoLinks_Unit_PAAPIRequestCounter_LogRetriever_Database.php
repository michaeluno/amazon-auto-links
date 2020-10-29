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
        $this->aAllLog = $this->getRawFromDatabase();
    }

    /**
     * Saves the log into the database.
     * @since  4.4.0
     */
    public function save() {
        update_option( $this->___getOptionKey( $this->sLocale ), $this->aAllLog );
    }

    /**
     * Sets the log.
     * @param array $aAllLog
     * @since 4.4.0
     */
    public function set( array $aAllLog ) {
        $this->aAllLog = $aAllLog;
    }

    /**
     * @param integer $iStartTime
     * @param integer $iEndTime
     */
    public function truncate( $iStartTime, $iEndTime ) {
        $this->set( $this->get( $iStartTime, $iEndTime ) );
    }

    /**
     * @param  integer $iStartTime
     * @param  integer $iEndTime
     * @return array
     * @since  4.4.0
     */
    public function get( $iStartTime, $iEndTime ) {
        if ( empty( $this->aAllLog ) ) {
            return array();
        }
        if ( ! $iStartTime && $iEndTime >= time() ) {
            return $this->aAllLog;
        }
        return $this->___getRaw( $this->aAllLog, $iStartTime, $iEndTime );
    }
        /**
         * @param  array   $aStoredLog
         * @param  integer $iTimeFrom   The timestamp of the start of the range.
         * @param  integer $iTimeTo     The timestamp of the end of the range.
         * @return array
         * @sicne  4.4.0
         */
        private function ___getRaw( array $aStoredLog, $iTimeFrom, $iTimeTo ) {

            ksort( $aStoredLog );

            $_aCountLog  = array();
            $iTimeFrom   = $iTimeFrom ? $iTimeFrom : $this->___getFirstFoundItemTime( $aStoredLog );

            $this->_setVariablesOfTime(
                $_sStartYear, $_sEndYear,
                $_sStartMonth, $_sEndMonth,
                $_sStartDate, $_sEndDate,
                $_sStartHour, $_sEndHour,
                $iTimeFrom, $iTimeTo
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
            private function ___getFirstFoundItemTime( array $aAllLog ) {
                $_sYear  = $this->___getFirstFoundItem( $aAllLog );
                $_sMonth = $this->___getFirstFoundItem( $aAllLog[ $_sYear ] );
                $_sDate  = $this->___getFirstFoundItem( $aAllLog[ $_sYear ][ $_sMonth ] );
                $_sHour  = $this->___getFirstFoundItem( $aAllLog[ $_sYear ][ $_sMonth ][ $_sDate ] );
                return strtotime( "{$_sYear}-{$_sMonth}-{$_sDate} {$_sHour}:00" );
            }
                private function ___getFirstFoundItem( $aLog ) {
                    ksort( $aLog );
                    foreach( $aLog as $_sThis => $_aThisLog ) {
                        if ( ! empty( $_aThisLog ) ) {
                            return $_sThis;
                        }
                    }
                    return '';
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
                            $_aLogByDate[ $_sThisHour ] = $_iThisCount;
                        }
                        return $_aLogByDate;
                    }

    /**
     * @return array
     * @since  4.4.0
     */
    public function getRawFromDatabase() {
        return $this->___getRawFromDatabase( $this->sLocale );
    }
        /**
         * @remark This retrieves all the stored data in the options table record without any specific time range.
         * @param  string $sLocale
         * @return array
         * @since  4.4.0
         */
        private function ___getRawFromDatabase( $sLocale ) {
            return $this->getAsArray( get_option( $this->___getOptionKey( $sLocale ), array() ) );
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