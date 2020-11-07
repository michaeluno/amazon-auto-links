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
 * Adds the 'Request Counts' form section to the 'PA-API Request Counts' tab.
 * 
 * @since 4.4.0
 */
class AmazonAutoLinks_Unit_PAAPIRequestCounter_AdminPage_Section_RequestCount extends AmazonAutoLinks_AdminPage_Section_Base {

    /**
     * @return array
     * @since  4.4.0
     */
    protected function _getArguments() {
        return array(
            'section_id'    => 'paapi_request_counts',
            'title'         => __( 'Product Advertising API Request Counter', 'amazon-auto-links' ),
        );
    }

    /**
     * Adds form fields.
     * @param       AmazonAutoLinks_AdminPageFramework $oFactory
     * @param       string $sSectionID
     * @since       4.4.0
     * @return      void
     */
    protected function _addFields( $oFactory, $sSectionID ) {

        $_oOption            = AmazonAutoLinks_Option::getInstance();
        $_bDateRange         = $_oOption->isPAAPIRequestCountChartDateRangeSupported();
        $oFactory->addSettingFields(
            $sSectionID, // the target section id
            array(
                'field_id'          => 'enable',
                'type'              => 'checkbox',
                'title'             => __( 'Enable', 'amazon-auto-links' ),
                'label'             => __( 'Log PA-API request counts.', 'amazon-auto-links' ),
                'description'       => __( 'Unchecking this option will clear all existing count log data.', 'amazon-auto-links' ),
            ),
            array(
                'field_id'          => 'retention_period',
                'title'             => __( 'Retention Period', 'amazon-auto-links' ),
                'description'       => array(
                    __( 'Sets how long the log should be kept.', 'amazon-auto-links' ),
                ),
                'type'              => 'size',
                'units'             => array(
                    86400    => __( 'day(s)', 'amazon-auto-links' ),
                    604800   => __( 'week(s)', 'amazon-auto-links' ),
                ),
                'attributes'        => array(
                    'size'      => array(
                        'step' => 1
                    ),
                    'disabled' => $_bDateRange ? null : 'disabled',
                ),
                'default'           => array(
                    'size'      => 7,
                    'unit'      => 86400,
                ),
            ),
            array(
                'field_id'          => '_save',
                'save'              => false,
                'type'              => 'submit',
                'value'             => __( 'Save', 'amazon-auto-links' ),
            ),
            array()
        );

    }

    /**
     * @param  array $aInputs
     * @param  array $aOldInputs
     * @param  AmazonAutoLinks_AdminPageFramework $oAdminPage
     * @param  array $aSubmitInfo
     * @return array
     * @since  4.4.0
     */
    public function validate( $aInputs, $aOldInputs, $oAdminPage, $aSubmitInfo ) {
        if ( ! $aInputs[ 'enable' ] ) {
            $this->___deleteCountLog( $oAdminPage );
        }
        return $aInputs;
    }
        /**
         * @param AmazonAutoLinks_AdminPageFramework $oAdminPage
         * @since 4.4.0
         */
        private function ___deleteCountLog( $oAdminPage ) {

            foreach( AmazonAutoLinks_Registry::$aOptionKeys[ 'paapi_request_counter' ] as $_sOptionKey ) {
                delete_option( $_sOptionKey );
            }
            $_sDirPath = AmazonAutoLinks_Registry::getPluginSiteTempDirPath() . '/paapi_request_count';
            $_bEmptied = $this->emptyDirectory( $_sDirPath );
            if ( $_bEmptied ) {
                $oAdminPage->setSettingNotice( __( 'Count log has been deleted.', 'amazon-auto-links' ) );
                return;
            }
            new AmazonAutoLinks_Error( 'PAAPI_REQUEST_COUNT_REMOVE_DIR', 'Could not empty the log directory.', $_sDirPath );

        }
}