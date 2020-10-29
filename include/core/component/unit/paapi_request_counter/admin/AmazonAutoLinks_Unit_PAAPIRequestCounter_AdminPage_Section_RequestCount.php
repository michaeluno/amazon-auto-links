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
            'title'         => __( 'PA-API Request Counts', 'amazon-auto-links' ),
            'description'   => __( 'Shows how many times the plugin performed PA-API requests.', 'amazon-auto-links' ),
        );
    }

    /**
     * A user constructor.
     *
     * @param       AmazonAutoLinks_AdminPageFramework $oFactory
     * @since       4.4.0
     * @return      void
     */
    protected function _construct( $oFactory ) {
        new AmazonAutoLinks_DateRangeCustomFieldType( $oFactory->oProp->sClassName );
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
        $_sProFeatureMessage = $_bDateRange ? null : sprintf( __( 'This feature requires <a href="%1$s" target="_blank">Pro</a>.', 'amazon-auto-links' ), AmazonAutoLinks_Registry::STORE_URI_PRO );
        $oFactory->addSettingFields(
            $sSectionID, // the target section id
            array(
                'field_id'          => '_chart',
                'show_title_column' => false,
                'save'              => false,
                'content'           => ""
                    . '<div style="max-width: 1000px; min-width: 480px; position: relative;" data-minWidth="480">'
                        . '<canvas id="PAAPIRequestCountChart" ></canvas>'
                                       // width="800" height="400"
                    . "</div>",
                    // . '<canvas id="goodCanvas1" width="400" height="100" aria-label="Hello ARIA World" role="img"></canvas>'
                    // . '<canvas id="okCanvas2" width="400" height="100">'
                    //     . '<p>Hello Fallback World</p>'
                    // . '</canvas>',
            ),
            array(
                'field_id'          => '_locale',
                'save'              => false,
                'title'             => __( 'Locale', 'amazon-auto-links' ),
                'type'              => 'select',
                'label'             => AmazonAutoLinks_PAAPI50___Locales::getHostLabels(),
                'class'             => array(
                    'field'  => 'locales',
                ),
                'default'           => AmazonAutoLinks_Unit_PAAPIRequestCounter_Utility::getDefaultLocale(),
            ),
            array(
                'field_id'          => '_date_range',
                'title'             => __( 'Date Range', 'amazon-auto-links' ),
                'type'              => 'date_range',
                'save'              => false,
                'default'           => array(
                    'from' => date( 'Y/m/d', time() - ( 86400 * 6 ) ),
                    'to'   => date( 'Y/m/d', time() ),
                ),
                'attributes' => array(
                    'disabled' => $_bDateRange ? null : 'disabled',
                    'title'    => strip_tags( $_sProFeatureMessage ),
                ),
                'description' => array(
                    $_sProFeatureMessage,
                ),
            ),
            array(
                'field_id'          => '_update_chart',
                // 'show_title_column' => false,
                'type'              => 'submit',
                'href'              => add_query_arg( array() ),
                'value'              => __( 'Set', 'amazon-auto-links' ),
                'attributes'        => array(
                    'class' => 'chart_option_update button button-secondary',
                ),
            ),
            array(
                'field_id'          => '_separator',
                'show_title_column' => false,
                'content'           => '<hr />',
                'save'              => false,
            ),
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
            ),
            array()
        );

    }


    // public function validate( $aInputs, $aOldInputs, $oAdminPage, $aSubmitInfo ) {
    //     return $aInputs;
    // }

}