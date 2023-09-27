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
 * Provides outputs for Ajax unit loading using REST API.
 *
 * If the `load_with_javascript` unit option is enabled, the unit displays a minimal output
 * and the Javascript script with Ajax replaces it with the content generated with this class method.
 *
 * @since 5.4.0
 */
class AmazonAutoLinks_Unit_EventAjax_UnitLoading_RESTAPI extends AmazonAutoLinks_RestEvent_Base {

    public $sRoute        = 'aal_ajax_unit_loading';

    protected function _loadResources() {

        // Do only once per page load
        if ( $this->hasBeenCalled( __METHOD__ ) ) {
            return;
        }
        if (
            $this->isDoingAjax()
            || 'wp-cron.php' === $GLOBALS[ 'pagenow' ]
            || AmazonAutoLinks_Shadow::isBackground()
        ) {
            return;
        }

        $_sScriptHandle = 'aal-ajax-unit-loading';
        $_aScriptData   = array(
            'ajaxURL'            => rest_url('wp/v2/' . $this->sRoute ),
            'spinnerURL'         => admin_url( 'images/loading.gif' ),
            'nonce'              => wp_create_nonce( 'wp_rest' ), // when not declared in class properties, the value will be the action name suffix
            // 'actionHookSuffix'   => $this->_sActionHookSuffix,
            'delay'              => apply_filters( 'aal_filter_ajax_unit_loading_delay', 0 ),    // [5.3.4] Made it possible to customize the delay. It used to be `1000`.
            'messages'           => array(
                'ajax_error'     => __( 'Failed to load product links.', 'amazon-auto-links' ),
            ),
        ) + AmazonAutoLinks_Unit_AjaxUnitLoading::getPageTypeInformationForContextualUnits();

        $_sFileBaseName = defined( 'WP_DEBUG' ) && WP_DEBUG
            ? 'ajax-unit-loading.js'
            : 'ajax-unit-loading.min.js';
        wp_enqueue_script( 'jquery' );
        wp_enqueue_script(
            $_sScriptHandle,
            $this->getSRCFromPath( AmazonAutoLinks_Unit_Loader::$sDirPath . '/asset/js/' . $_sFileBaseName ),
            array( 'jquery' ),
            false,
            true
        );

        // in JavaScript, object properties are accessed as ajax_object.ajax_url, ajax_object.we_value
        wp_localize_script(
            $_sScriptHandle,
            'aalAjaxUnitLoading', // variable name
            $_aScriptData
        );

    }

    /**
     * @param  WP_REST_Request $oWPRESTRequest
     * @return string|array
     */
    protected function _respond( WP_REST_Request $oWPRESTRequest ) {

        if ( empty( $_POST[ 'data' ] ) ) {
            return array(
                'success' => false,
                // the front-end js script parse these and remove from the session array from the key one by one
                'result'  => "<p>" . __( 'Failed to load product links.', 'amazon-auto-links' ) . "</p>",
            );
        }

        $_oAjaxUnitOutput = new AmazonAutoLinks_Unit_AjaxUnitLoading();
        return array(
            'success' => true,
            // the front-end js script parse these and remove from the session array from the key one by one
            'result'  => $_oAjaxUnitOutput->get( $this->getArrayMappedRecursive( 'sanitize_text_field', $_POST[ 'data' ] ) ),
        );

    }


}