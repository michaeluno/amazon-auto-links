<?php
/**
 * Auto Amazon Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 */

/**
 * Adds the 'Request Counts' form section to the 'PA-API Request Counter' tab.
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

}