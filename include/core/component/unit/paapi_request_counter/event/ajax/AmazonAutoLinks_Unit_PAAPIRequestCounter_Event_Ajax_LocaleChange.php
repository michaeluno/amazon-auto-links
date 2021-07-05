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
 * Updates the chart when the user selects the locale.
 * @since   4.4.0
 *
 */
class AmazonAutoLinks_Unit_PAAPIRequestCounter_Event_Ajax_LocaleChange extends AmazonAutoLinks_AjaxEvent_Base {

    protected $_sActionHookSuffix = 'aal_action_ajax_paapi_request_count_locale_change';
    protected $_bLoggedIn         = true;
    protected $_bGuest            = false;

    /**
     * @var AmazonAutoLinks_Unit_PAAPIRequestCounter_Utility
     * @sine 4.4.0
     */
    public $oUtil;

    protected function _construct() {
        $this->oUtil = new AmazonAutoLinks_Unit_PAAPIRequestCounter_Utility;
    }

    /**
     * @param  array $aPost
     * @return array
     * @throws Exception        Throws a string value of an error message.
     * @since  4.4.0
     */
    protected function _getResponse( array $aPost ) {

        $_sLocale      = $this->getElement( $aPost, array( 'locale' ), 'US' );
        $_iStartTime   = $this->___getDateRangeStartTimeStamp( $this->getElement( $aPost, array( 'startTime' ) ) );
        $_iEndTime     = $this->___getDateRangeEndTimeStamp( $this->getElement( $aPost, array( 'endTime' ) ) );
        $_oLogData     = new AmazonAutoLinks_Unit_PAAPIRequestCounter_LogRetriever( $_sLocale );
        $_oLogData->getCountLog( $_aDates, $_aCounts, $_iStartTime, $_iEndTime, $this->oUtil->getGMTOffset(), true, false );
        return array(
            'date'  => $_aDates,
            'count' => $_aCounts,
            'total' => number_format( array_sum( $_aCounts ) ),
        );

    }

        /**
         * @remark The time format is passed as {year}/{month}/{date} but there is a possibility that this can be interpreted by PHP as {year}/{date}/{month}. To prevent that replace slash with a hyphen and add an hour and a minute.
         * @param  string|integer $isDate
         * @since  4.4.0
         * @return integer  Unix time stamp, not GMT compliant.
         */
        private function ___getDateRangeStartTimeStamp( $isDate ) {
            $_iDateTimeStamp = is_numeric( $isDate)
                ? ( integer ) $isDate
                : strtotime( str_replace( '/', '-', $isDate ) . ' 00:00' );
            return $_iDateTimeStamp + $this->getGMTOffset();
        }
        /**
         * @remark The time format is passed as {year}/{month}/{date} but there is a possibility that this can be interpreted by PHP as {year}/{date}/{month}. To prevent that replace slash with a hyphen and add an hour and a minute.
         * @param  string|integer $isDate
         * @since  4.4.0
         * @return integer  Unix time stamp, not GMT compliant.
         */
        private function ___getDateRangeEndTimeStamp( $isDate ) {
            $_iDateTimeStamp = is_numeric( $isDate)
                ? ( integer ) $isDate
                : strtotime( str_replace( '/', '-', $isDate ) . ' 23:59' );
            return $_iDateTimeStamp + $this->getGMTOffset();
        }
}