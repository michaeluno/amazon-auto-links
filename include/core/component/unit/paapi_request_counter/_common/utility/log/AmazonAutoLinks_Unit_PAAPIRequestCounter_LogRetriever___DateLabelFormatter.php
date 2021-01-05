<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 */

/**
 * Formats date labels for the chart.
 *
 * @since   4.4.0
 * @deprecated The formatting could be done in js side.
 */
class AmazonAutoLinks_Unit_PAAPIRequestCounter_LogRetriever___DateLabelFormatter {

    private $___sParsedYear;
    private $___sParsedMonth;
    private $___iGMTOffset;

    /**
     * Sets up properties and hooks.
     *
     * @param integer $iGMTOffset
     */
    public function __construct( $iGMTOffset=0 ) {
        $this->___iGMTOffset = $iGMTOffset;
    }

    /**
     * @param  string $sLabel
     * @param  string $sYear
     * @param  string $sMonth
     * @param  string $sDate
     *
     * @return string
     * @since  4.4.0
     */
    public function replyToFormatDateLabel( $sLabel, $sYear, $sMonth, $sDate ) {
        $_iTime      = strtotime( "{$sYear}-{$sMonth}-{$sDate} 00:00" ) + $this->___iGMTOffset;
        $_sYear      = $this->___getYearLabel( date( 'Y', $_iTime ) );
        $_sMonth     = $this->___getMonthLabel( date( 'm', $_iTime ) );
        return trim( "{$_sYear} {$_sMonth} {$sDate}" );
    }
        private function ___getYearLabel( $sYear ) {
            if ( $this->___sParsedYear === $sYear ) {
                // return '';
            }
            $this->___sParsedYear = $sYear; // update
            return $sYear;
        }
        private function ___getMonthLabel( $sMonth ) {
            if ( $this->___sParsedMonth === $sMonth ) {
                // return '';
            }
            $_sMonthName = $this->___getMonthName( $sMonth );
            $this->___sParsedMonth = $sMonth; // update - storing the original passed value, not the one formatted.
            return $_sMonthName;
        }

        /**
         * @param  string $sMonthNumber Zero-padded two digits number representing a month.
         * @return string
         * @since  4.4.0
         */
        private function ___getMonthName( $sMonthNumber ) {
            $_oDate      = DateTime::createFromFormat('!m', $sMonthNumber );
            return $_oDate->format('F' );
        }

}