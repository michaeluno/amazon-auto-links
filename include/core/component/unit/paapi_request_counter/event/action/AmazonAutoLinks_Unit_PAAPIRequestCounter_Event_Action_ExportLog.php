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
 * Handles exporting PA-API request count logs.
 *
 * @since 4.4.0
 */
class AmazonAutoLinks_Unit_PAAPIRequestCounter_Event_Action_ExportLog extends AmazonAutoLinks_Unit_PAAPIRequestCounter_Utility {
    
    /**
     * Sets up hooks.
     */
    public function __construct() {
        $_sPageSlug = AmazonAutoLinks_Registry::$aAdminPages[ 'report' ];
        $_sTabSlug  = 'paapi_request_counts';
        add_action( "load_{$_sPageSlug}_{$_sTabSlug}", array( $this, 'replyToLoadTab' ) );
    }

    /**
     * @param    AmazonAutoLinks_AdminPageFramework $oFactory
     * @callback add_action() load_{page slug}_{tab slug}
     */
    public function replyToLoadTab( $oFactory ) {
        $_sPageSlug = $oFactory->oProp->getCurrentPageSlug();
        $_sTabSlug  = $oFactory->oProp->getCurrentTabSlug();
        add_filter( "export_{$_sPageSlug}_{$_sTabSlug}", array( $this, 'replyToFilterExportData' ), 10, 0 );
        add_filter( "export_name_{$_sPageSlug}_{$_sTabSlug}", array( $this, 'replyToGetExportFileName' ), 10, 0 );
        add_filter( "export_format_{$_sPageSlug}_{$_sTabSlug}", array( $this, 'replyToGetFormat' ), 10, 0 );
    }

    /**
     * @return string
     * @since  4.4.0
     * @remark An extended class also uses this.
     */
    public function replyToGetFormat() {
        return 'text';
    }
    /**
     * @return string
     * @since  4.4.0
     */
    public function replyToFilterExportData() {

        $_sOptionKey  = AmazonAutoLinks_Registry::$aOptionKeys[ 'main' ];
        $_sLocale     = $this->getElement( $_POST, array( $_sOptionKey, '_graph', '_locale' ), 'US' );
        $_sExportType = $this->getElement( $_POST, array( $_sOptionKey, '_porting', '_export_type' ) );
        if ( 'all' === $_sExportType ) {
            return $this->___getCountLogAsCSVArray( 0, PHP_INT_MAX, $_sLocale );
        }

        $_sStartDate  = $this->getElement( $_POST, array( $_sOptionKey, '_graph', '_date_range', 'from' ) );
        $_sStartDate  = str_replace( '/', '-', $_sStartDate ) . ' 00:00';
        $_sEndDate    = $this->getElement( $_POST, array( $_sOptionKey, '_graph', '_date_range', 'to' ) );
        $_sEndDate    = str_replace( '/', '-', $_sEndDate ) . ' 00:00';
        $_iStartTime  = strtotime( $_sStartDate ) - $this->getGMTOffset();
        $_iEndTime    = strtotime( $_sEndDate ) - $this->getGMTOffset();
        return $this->___getCountLogAsCSVArray( $_iStartTime, $_iEndTime, $_sLocale );

    }
        private function ___getCountLogAsCSVArray( $iStartTime, $iEndTime, $sLocale ) {
            $_oLog = new AmazonAutoLinks_Unit_PAAPIRequestCounter_LogRetriever( $sLocale );
            return $_oLog->getAsCSV( $iStartTime, $iEndTime );
        }

    public function replyToGetExportFileName() {

        $_sOptionKey = AmazonAutoLinks_Registry::$aOptionKeys[ 'main' ];
        $_sLocale    = $this->getElement( $_POST, array( $_sOptionKey, '_graph', '_locale' ), 'US' );
        $_sExportType = $this->getElement( $_POST, array( $_sOptionKey, '_porting', '_export_type' ) );

        if ( 'all' === $_sExportType ) {
            $_sToday = date( 'Ymd', time() + $this->getGMTOffset() );
            return "PAAPIRequestCounts_{$_sLocale}-All-{$_sToday}.csv";
        }
        $_sEndDate   = $this->getElement( $_POST, array( $_sOptionKey, '_graph', '_date_range', 'to' ) );
        $_sEndDate   = str_replace( '/', '', $_sEndDate );
        $_sStartDate = $this->getElement( $_POST, array( $_sOptionKey, '_graph', '_date_range', 'from' ) );
        $_sStartDate = str_replace( '/', '', $_sStartDate );
        return "PAAPIRequestCounts_{$_sLocale}-{$_sStartDate}-{$_sEndDate}.csv";

    }

}