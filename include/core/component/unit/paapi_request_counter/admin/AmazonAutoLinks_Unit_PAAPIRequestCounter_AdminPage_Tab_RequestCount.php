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
     * @var integer
     */
    private $___iDefaultStartTime;
    /**
     * @var integer
     */
    private $___iDefaultEndTime;

    /**
     * @param AmazonAutoLinks_AdminPageFramework $oFactory
     */
    protected function _construct( $oFactory ) {
        $this->oUtil = new AmazonAutoLinks_Unit_PAAPIRequestCounter_Utility;
        $_iNow       = time();
        $this->___iDefaultStartTime = $_iNow - ( 86400 * 6 );
        $this->___iDefaultEndTime   = $_iNow;
    }

    /**
     * @return  array
     * @since   3.9.0
     */
    protected function _getArguments() {
        return array(
            'tab_slug'  => 'paapi_request_counts',
            'title'     => __( 'PA-API Request Counts', 'amazon-auto-links' ),
            'order'     => 5,
            'style'     => array(
                AmazonAutoLinks_Registry::$sDirPath . '/asset/css/admin.css',
            ),
            'script'    => array(
                AmazonAutoLinks_Unit_PAAPIRequestCounter_Loader::$sDirPath . '/asset/js/chart-js/Chart.bundle.min.js',
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
            $_oLogData->setVariablesForChart( $_aLogX, $_aLogY, $this->___iDefaultStartTime, $this->___iDefaultEndTime, $this->getGMTOffset() );

            wp_enqueue_script( 'jquery' );
            $oAdminPage->enqueueScript(
                $oAdminPage->oUtil->isDebugMode()
                    ? AmazonAutoLinks_Unit_PAAPIRequestCounter_Loader::$sDirPath . '/asset/js/chart-loader.js'
                    : AmazonAutoLinks_Unit_PAAPIRequestCounter_Loader::$sDirPath . '/asset/js/chart-loader.min',
                $this->sPageSlug,
                $this->sTabSlug,
                array(
                    'handle_id'     => 'aalChartJSLoader',
                    'dependencies'  => array( 'jquery', ),
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
                            'counts' => __( 'Counts', 'amazon-auto-links' ),
                        ),
                        'logX'      => $_aLogX,
                        'logY'      => $_aLogY,
                        'total'     => number_format( array_sum( $_aLogY ) ),
                        'chartID'   => 'PAAPIRequestCountChart',
                        'startTime' => $this->___iDefaultStartTime,
                        'endTime'   => $this->___iDefaultEndTime,
                    ),
                    'in_footer'     => true,
                )
            );
        }

}
