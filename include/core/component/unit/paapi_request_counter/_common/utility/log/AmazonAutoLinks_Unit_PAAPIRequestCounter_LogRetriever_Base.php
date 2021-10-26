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
 * A base class of log retriever.
 *
 * @since   4.4.0
 */
abstract class AmazonAutoLinks_Unit_PAAPIRequestCounter_LogRetriever_Base extends AmazonAutoLinks_Unit_PAAPIRequestCounter_Utility {

    /**
     * @var string
     * @since 4.4.0
     */
    public $sLocale;

    /**
     * Sets up properties and hooks.
     * @param string $sLocale
     * @since 4.4.0
     */
    public function __construct( $sLocale ) {
        $this->sLocale = $sLocale;
    }

    /**
     * @param integer &$sStartYear
     * @param integer &$sEndYear
     * @param string  &$sStartMonth
     * @param string  &$sEndMonth
     * @param string  &$sStartDate
     * @param string  &$sEndDate
     * @param string  &$sStartHour
     * @param string  &$sEndHour
     * @param integer  $iStartTime
     * @param integer  $iEndTime
     * @since 4.4.0
     */
    protected function _setVariablesOfTime( &$sStartYear, &$sEndYear, &$sStartMonth, &$sEndMonth, &$sStartDate, &$sEndDate, &$sStartHour, &$sEndHour, $iStartTime, $iEndTime ) {
        $sStartYear  = date( 'Y', ( integer ) $iStartTime );
        $sStartMonth = date( 'm', ( integer ) $iStartTime );
        $sStartDate  = date( 'd', ( integer ) $iStartTime );
        $sStartHour  = date( 'H', ( integer ) $iStartTime );
        $sEndYear    = date( 'Y', ( integer ) $iEndTime );
        $sEndMonth   = date( 'm', ( integer ) $iEndTime );
        $sEndDate    = date( 'd', ( integer ) $iEndTime );
        $sEndHour    = date( 'H', ( integer ) $iEndTime );
    }

    /**
     * @param  string $sStart
     * @param  string $sSubject
     * @param  string $sEnd
     * @return boolean
     * @since  4.4.0
     */
    protected function _isInRange( $sStart, $sSubject, $sEnd ) {
        return $sStart <= $sSubject && $sSubject <= $sEnd;
    }

    /**
     * @param  integer $isTime  Either a string of a time representation or a Unix timestamp.
     * @return integer
     * @since  4.4.0
     */
    protected function _getMaxDateOfThisMonth( $isTime ) {
        $_iTime = is_string( $isTime ) ? strtotime( $isTime ) : ( integer ) $isTime;
        return ( integer ) date( "t", $_iTime );
    }

    /**
     * @param  array   $aLog
     * @return integer
     * @since  4.4.0
     */
    protected function _getFirstItemTime( array $aLog ) {
        return $this->___getEdgeLogItemTime( $aLog, true );
    }

    /**
     * @param  array   $aLog
     * @return integer
     * @since  4.4.0
     */
    protected function _getLastItemTime( array $aLog ) {
        return $this->___getEdgeLogItemTime( $aLog, false );
    }
        /**
         * Retrieves the first/last log item time.
         * @param  array   $aLog
         * @param  boolean $bFirst  If true, retrieves the first item time, otherwise, the last.
         * @return integer
         * @since  4.4.0
         */
        private function ___getEdgeLogItemTime( array $aLog, $bFirst=true ) {

            if ( empty( $aLog ) ) {
                return 0;
            }

            $_sYear = $this->___getEdgeItem( $aLog, array(), $bFirst );
            if ( ! $_sYear ) {
                return 0;
            }
            $_aIgnoreItems = array();
            while( ! ( $_sMonth = $this->___getEdgeItem( $this->getElementAsArray( $aLog, array( $_sYear ) ), array(), $bFirst ) ) ) {
                $_aIgnoreItems[] = $_sYear;
                $_sThisYear = $this->___getEdgeItem( $aLog, $_aIgnoreItems, $bFirst );
                if ( $_sThisYear === $_sYear ) { // Matching the previous value means it failed
                    return 0;
                }
                $_sYear = $_sThisYear;
            }

            $_aIgnoreItems = array();
            while( ! ( $_sDate = $this->___getEdgeItem( $this->getElementAsArray( $aLog, array( $_sYear, $_sMonth ) ), array(), $bFirst ) ) ) {
                $_aIgnoreItems[] = $_sMonth;
                $_sThisMonth = $this->___getEdgeItem( $this->getElementAsArray( $aLog, array( $_sYear ) ), $_aIgnoreItems, $bFirst );
                if ( $_sThisMonth === $_sMonth ) { // Matching the previous value means it failed
                    return 0;
                }
                $_sMonth = $_sThisMonth;
            }

            $_aIgnoreItems = array();
            while( ! ( $_sHour = $this->___getEdgeItem( $this->getElementAsArray( $aLog, array( $_sYear, $_sMonth, $_sDate ) ), array(), $bFirst ) ) ) {
                $_aIgnoreItems[] = $_sDate;
                $_sThisDate = $this->___getEdgeItem( $this->getElementAsArray( $aLog, array( $_sYear, $_sMonth ) ), $_aIgnoreItems, $bFirst );
                if ( $_sThisDate === $_sDate ) {
                    return 0;
                }
                $_sDate = $_sThisDate;
            }

            return ( integer ) strtotime( "{$_sYear}-{$_sMonth}-{$_sDate} {$_sHour}:00" );

        }
            /**
             * Retrieves an item placed on the edge (either first or last).
             * @param  array   $aLog
             * @param  array   $aIgnoreItems
             * @param  boolean $bFirst          If true, the first item, otherwise, the last.
             * @return string
             * @since  4.4.0
             */
            private function ___getEdgeItem( $aLog, array $aIgnoreItems=array(), $bFirst=true ) {
                if ( $bFirst ) {
                    ksort( $aLog );
                } else {
                    krsort( $aLog );
                }
                foreach( $aLog as $_sThis => $_aThisLog ) {
                    if ( in_array( $_sThis, $aIgnoreItems, true ) ) {
                        continue;
                    }
                    if ( ! empty( $_aThisLog ) ) {
                        return ( string ) $_sThis;
                    }
                }
                return '';
            }

    /**
     * Applies ksort() to the given array recursively.
     * This is used to sort log data as their order gets messed when merging with file and database logs.
     * @param  mixed $mValue
     * @return array
     * @since  4.4.0
     */
    protected function _getArraySortedByKeyRecursive( $mValue ) {
        if ( ! is_array( $mValue ) ) {
            return $mValue;
        }
        ksort( $mValue );
        return array_map( array( $this, '_getArraySortedByKeyRecursive' ), $mValue );
    }

}