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
        $sStartYear  = date( 'Y', $iStartTime );
        $sStartMonth = date( 'm', $iStartTime );
        $sStartDate  = date( 'd', $iStartTime );
        $sStartHour  = date( 'H', $iStartTime );
        $sEndYear    = date( 'Y', $iEndTime );
        $sEndMonth   = date( 'm', $iEndTime );
        $sEndDate    = date( 'd', $iEndTime );
        $sEndHour    = date( 'H', $iEndTime );
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

}