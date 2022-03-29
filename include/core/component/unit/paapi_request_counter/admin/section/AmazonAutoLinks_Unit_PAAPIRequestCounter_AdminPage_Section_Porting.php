<?php
/**
 * Auto Amazon Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2022 Michael Uno
 */

/**
 * Adds the 'Porting' form section to the 'PA-API Request Counter' tab.
 * 
 * @since 4.4.0
 */
class AmazonAutoLinks_Unit_PAAPIRequestCounter_AdminPage_Section_Porting extends AmazonAutoLinks_AdminPage_Section_Base {

    /**
     * @return array
     * @since  4.4.0
     */
    protected function _getArguments() {
        return array(
            'section_id'    => '_porting',
            'save'          => false,
            'title'         => __( 'Log Data', 'amazon-auto-links' ),
            'description'   => array(
                __( 'Export/import/delete count log data.', 'amazon-auto-links' ) . ' '
                . __( 'The locale selected in the above line chart will be applied.', 'amazon-auto-links' ),
            ),
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

        $oFactory->addSettingFields(
            $sSectionID, // the target section id
            array(
                'title'             => __( 'Export', 'amazon-auto-links' ),
                'field_id'          => '_export_type',
                'save'              => false,
                'type'              => 'radio',
                'label'             => array(
                    'all'       => __( 'All', 'amazon-auto-links' ) . ' - ' . __( 'All the stored data.', 'amazon-auto-links' ),
                    'range'     => __( 'Date Range', 'amazon-auto-links' ) . ' - ' . __( 'Applies the time range set in the above line chart.', 'amazon-auto-links' ),
                ),
                'default'           => 'all',
            ),
            array(
                'field_id'          => '_export',
                'save'              => false,
                // 'title'             => __( 'Export', 'amazon-auto-links' ),
                'type'              => 'export',
                'value'             => __( 'Download', 'amazon-auto-links' ),
                'description'       => __( 'Export as CSV.', 'amazon-auto-links' ),
                'file_name'         => 'paapi-request-count.csv',
                'attributes' => array(
                    // 'class' => 'button button-secondary',
                ),
            ),
            array(
                'field_id'          => '_import',
                'title'             => __( 'Import', 'amazon-auto-links' ),
                'save'              => false,
                'type'              => 'import',
                'value'              => __( 'Import', 'amazon-auto-links' ),
                'attributes'        => array(
                    // 'class' => 'button button-secondary',
                    'file'  => array(
                        'accept' => '.csv, text/csv',
                    ),
                ),
            ),
            array(
                'field_id'          => '_delete',
                'title'             => __( 'Delete', 'amazon-auto-links' ),
                'save'              => false,
                'type'              => 'submit',
                'value'             => __( 'Delete', 'amazon-auto-links' ),
                'confirm'           => __( 'Confirm this deletes the entire count log data.', 'amazon-auto-links' ),
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
        if ( '_delete' === $this->getElement( $aSubmitInfo, array( 'field_id' ) ) ) {
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