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
 * Adds the 'Graph' form section to the 'PA-API Request Counter' tab.
 * 
 * @since 4.4.0
 */
class AmazonAutoLinks_Unit_PAAPIRequestCounter_AdminPage_Section_Chart extends AmazonAutoLinks_AdminPage_Section_Base {

    /**
     * @return array
     * @since  4.4.0
     */
    protected function _getArguments() {
        return array(
            'section_id'    => '_graph',
            'save'          => false,
            'title'         => __( 'Graph', 'amazon-auto-links' ),
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
        $_oUtil              = new AmazonAutoLinks_Unit_PAAPIRequestCounter_Utility;
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
                    'from' => date( 'Y/m/d', $_oUtil->getDefaultChartStartTime( true ) ),
                    'to'   => date( 'Y/m/d', $_oUtil->getDefaultChartEndTime( true ) ),
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
            array()
        );

    }

}