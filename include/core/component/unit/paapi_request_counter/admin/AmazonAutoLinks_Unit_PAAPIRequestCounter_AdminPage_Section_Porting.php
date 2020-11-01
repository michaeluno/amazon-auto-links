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
 * Adds the 'Porting' form section to the 'PA-API Request Counts' tab.
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
            'title'         => __( 'Data Porting', 'amazon-auto-links' ),
            'description'   => array(
                __( 'Export/import count log data.', 'amazon-auto-links' ) . ' '
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
            array()
        );

    }

}