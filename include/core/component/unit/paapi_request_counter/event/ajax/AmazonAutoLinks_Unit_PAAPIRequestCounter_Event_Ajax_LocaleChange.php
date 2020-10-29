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
        $_iStartTime   = $this->___getDateRangeUnixTimeStamp( $this->getElement( $aPost, array( 'startTime' ) ) );
        $_iEndTime     = $this->___getDateRangeUnixTimeStamp( $this->getElement( $aPost, array( 'endTime' ) ) );
        $_oLogData     = new AmazonAutoLinks_Unit_PAAPIRequestCounter_LogRetriever( $_sLocale );
        $_oLogData->setVariablesForChart( $_aDates, $_aCounts, $_iStartTime, $_iEndTime, $this->oUtil->getGMTOffset() );
        return array(
            'date'  => $_aDates,
            'count' => $_aCounts,
            'total' => number_format( array_sum( $_aCounts ) ),
        );

    }
        /**
         * @param  string|integer $isDate
         * @since  4.4.0
         * @return integer  Unix time stamp, not GMT compliant.
         */
        private function ___getDateRangeUnixTimeStamp( $isDate ) {
            $_iDateTimeStamp = is_numeric( $isDate) ? ( integer ) $isDate : strtotime( $isDate );
            return $_iDateTimeStamp + $this->oUtil->getGMTOffset();
        }

}