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
 * Adds an in-page tab of the PA-API Requests to a setting page.
 * 
 * @since       4.4.0
 */
class AmazonAutoLinks_Unit_PAAPIRequestCounter_AdminPage_Tab_RequestCount extends AmazonAutoLinks_AdminPage_Tab_Base {

    /**
     * @var AmazonAutoLinks_Unit_PAAPIRequestCounter_Utility
     */
    public $oUtil;

    /**
     * @var array
     */
    private $___aLog;

    /**
     * @param AmazonAutoLinks_AdminPageFramework $oFactory
     */
    protected function _construct( $oFactory ) {
        $this->oUtil = new AmazonAutoLinks_Unit_PAAPIRequestCounter_Utility;
    }

    /**
     * @return  array
     * @since   3.9.0
     */
    protected function _getArguments() {
        return array(
            'tab_slug'  => 'paapi_request_counts',
            'title'     => __( 'PA-API Request Counter', 'amazon-auto-links' ),
            'order'     => 5,
            'style'     => array(
                AmazonAutoLinks_Main_Loader::$sDirPath . '/asset/css/admin.css',
            ),
        );
    }

    /**
     * Adds form sections.
     *
     * Triggered when the tab is loaded.
     * @param AmazonAutoLinks_AdminPageFramework $oAdminPage
     * @since 4.4.0
     */
    protected function _loadTab( $oAdminPage ) {

        new AmazonAutoLinks_Unit_PAAPIRequestCounter_AdminPage_Section_RequestCount( $oAdminPage, $this->sPageSlug, array( 'tab_slug' => $this->sTabSlug, ) );
        new AmazonAutoLinks_Unit_PAAPIRequestCounter_AdminPage_Section_Chart( $oAdminPage, $this->sPageSlug, array( 'tab_slug' => $this->sTabSlug, ) );
        new AmazonAutoLinks_Unit_PAAPIRequestCounter_AdminPage_Section_Porting( $oAdminPage, $this->sPageSlug, array( 'tab_slug' => $this->sTabSlug, ) );
        $this->___enqueueResources( $oAdminPage );

    }

        /**
         * @param AmazonAutoLinks_AdminPageFramework $oAdminPage
         * @since   4.4.0
         */
        private function ___enqueueResources( $oAdminPage ) {

            if ( $this->isDoingAjax() ) {
                return;
            }

            $_sDefaultLocale = AmazonAutoLinks_Unit_PAAPIRequestCounter_Utility::getDefaultLocale();
            $_oLogData       = new AmazonAutoLinks_Unit_PAAPIRequestCounter_LogRetriever( $_sDefaultLocale );
            $this->___aLog   = $_oLogData->getCountLog( $_aLogX, $_aLogY, $this->oUtil->getDefaultChartStartTime(), $this->oUtil->getDefaultChartEndTime(), $this->getGMTOffset() );

            $this->___registerMomentJS();

            wp_enqueue_script( 'jquery', 'moment' );
            $oAdminPage->enqueueScript(
                AmazonAutoLinks_Unit_PAAPIRequestCounter_Loader::$sDirPath . '/asset/js/chart-js/Chart.min.js',
                $this->sPageSlug,
                $this->sTabSlug,
                array(
                    'handle_id'     => 'chart.js',
                    'dependencies'  => array( 'jquery', 'moment' ),
                    'in_footer'     => true,
                )
            );
            $oAdminPage->enqueueScript(
                $oAdminPage->oUtil->isDebugMode()
                    ? AmazonAutoLinks_Main_Loader::$sDirPath . '/asset/js/utility.js'
                    : AmazonAutoLinks_Main_Loader::$sDirPath . '/asset/js/utility.min.js',
                $this->sPageSlug,
                $this->sTabSlug,
                array(
                    'handle_id'     => 'aalUtility',
                    'dependencies'  => array( 'jquery', ),
                    'in_footer'     => true,
                )
            );
            $oAdminPage->enqueueScript(
                $oAdminPage->oUtil->isDebugMode()
                    ? AmazonAutoLinks_Unit_PAAPIRequestCounter_Loader::$sDirPath . '/asset/js/chart-loader.js'
                    : AmazonAutoLinks_Unit_PAAPIRequestCounter_Loader::$sDirPath . '/asset/js/chart-loader.min.js',
                $this->sPageSlug,
                $this->sTabSlug,
                array(
                    'handle_id'     => 'aalChartJSLoader',
                    'dependencies'  => array( 'jquery', 'moment', 'chart.js', 'aalUtility' ),
                    'translation'   => array(
                        'debugMode' => $oAdminPage->oUtil->isDebugMode(),
                        'ajaxURL'   => admin_url( 'admin-ajax.php' ),
                        'actionHookSuffix'  => 'aal_action_ajax_paapi_request_count_locale_change',
                        'nonce'             => wp_create_nonce( 'aal_action_ajax_paapi_request_count_locale_change' ),
                        'spinnerURL'        => admin_url( 'images/loading.gif' ),
                        'labels'    => array(
                            // 'Last30Days' => __( 'Last 30 Days', 'amazon-auto-links' ),
                            // 'Last7Days' => __( 'Last 7 Days', 'amazon-auto-links' ),
                            'total'  => __( 'total', 'amazon-auto-links' ),
                            'dates'  => __( 'Dates', 'amazon-auto-links' ),
                            'count'  => __( 'Count', 'amazon-auto-links' ),
                            'counts' => __( 'Counts', 'amazon-auto-links' ),
                        ),
                        'GMTOffset' => str_replace( ':', '', $this->getGMTOffsetString() ),
                        'logX'      => $_aLogX,
                        'logY'      => $_aLogY,
                        'total'     => number_format( array_sum( $_aLogY ) ),
                        'chartID'   => 'PAAPIRequestCountChart',
                    ),
                    'in_footer'     => true,
                )
            );

        }
            /**
             * Registers Moment.js for WordPress version that does not support it.
             * Below WordPress 5.0.0 (might not be accurate), moment.js is not included.
             */
            private function ___registerMomentJS() {
                if ( wp_script_is( 'moment', 'registered' ) ) {
                     return;
                }
                $_sFilePath = $this->isDebugMode()
                    ? AmazonAutoLinks_Unit_PAAPIRequestCounter_Loader::$sDirPath . '/asset/js/moment/moment.js'
                    : AmazonAutoLinks_Unit_PAAPIRequestCounter_Loader::$sDirPath . '/asset/js/moment/moment.min.js';
                wp_register_script( 'moment', $this->getResolvedSRC( $_sFilePath ) );
            }

    /**
     * @param AmazonAutoLinks_AdminPageFramework $oFactory
     */
    protected function _doTab( $oFactory ) {
        $_oOption = AmazonAutoLinks_Option::getInstance();
        if ( ! ( $oFactory->oUtil->isDebugMode() || $_oOption->isDebug() ) ) {
            return;
        }

        $_sDefaultLocale   = AmazonAutoLinks_Unit_PAAPIRequestCounter_Utility::getDefaultLocale();

        echo "<h3>Debug</h3>";
        echo "<h4>Start and End Times</h4>";
        $_iStartTime    = $this->oUtil->getDefaultChartStartTime();
        $_iStartTimeGMT = $this->oUtil->getDefaultChartStartTime( true );
        $_iEndTime      = $this->oUtil->getDefaultChartEndTime();
        $_iEndTimeGMT   = $this->oUtil->getDefaultChartEndTime( true );
        $_iNow          = time();
        $_iNowGMT       = time() + $this->oUtil->getGMTOffset();
        echo "<div>";
        AmazonAutoLinks_Debug::dump( array(
            'timestamp' => array(
                'start'      => $_iStartTime,
                'now'        => $_iNow,
                'end'        => $_iEndTime,
            ),
            'time' => array(
                'start'           => date( 'Y-m-d H:i:s', $_iStartTime ),
                'now'             => date( 'Y-m-d H:i:s', $_iNow ),
                'end'             => date( 'Y-m-d H:i:s', $_iEndTime ),
            ),
            'timestamp gmt' => array(
                'start'  => $_iStartTimeGMT,
                'now'    => $_iNowGMT,
                'end'    => $_iEndTimeGMT,
            ),
            'time gmt' => array(
                'start'       => date( 'Y-m-d H:i:s', $_iStartTimeGMT ),
                'now'         => date( 'Y-m-d H:i:s', $_iNowGMT ),
                'end'         => date( 'Y-m-d H:i:s', $_iEndTimeGMT ),
            ),
            'label gmt' => array(
                'start' => date( 'Y/m/d', $_iStartTimeGMT ),
                'now'   => date( 'Y/m/d', $_iNowGMT ),
                'end'   => date( 'Y/m/d', $_iEndTimeGMT ),
            ),
        ) );
        echo "</div>";

        echo "<h4>Log</h4>";
        echo "<div>";
        AmazonAutoLinks_Debug::dump( $this->___aLog );
        echo "</div>";

        echo "<h4>Database Log</h4>";
        $_oDatabaseLog     = new AmazonAutoLinks_Unit_PAAPIRequestCounter_LogRetriever_Database( $_sDefaultLocale );
        $_aLogFromDatabase = $_oDatabaseLog->get( 0, time() );
        echo "<div>";
        AmazonAutoLinks_Debug::dump( $_aLogFromDatabase );
        echo "</div>";

        echo "<h4>File Log</h4>";
        $_oFileLog = new AmazonAutoLinks_Unit_PAAPIRequestCounter_LogRetriever_File( $_sDefaultLocale );
        $_aLogFromFile = $_oFileLog->get( 0, time() );
        echo "<div>";
        AmazonAutoLinks_Debug::dump( $_aLogFromFile );
        echo "</div>";

    }

}