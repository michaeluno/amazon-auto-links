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
 * Provides outputs for Ajax unit loading.
 *
 * If the `load_with_javascript` unit option is enabled, the unit displays a minimal output
 * and the Javascript script with Ajax replaces it with the content generated with this class method.
 *
 * @since 3.6.0
 * @since 4.3.0 Renamed from `AmazonAutoLinks_Event___Action_AjaxUnitLoading`
 * @since 4.3.0 Changed the base class from `AmazonAutoLinks_Event___Action_Base`.
 * @since 5.4.0 Renamed from `AmazonAutoLinks_Unit_EventAjax_UnitLoading`.
 */
class AmazonAutoLinks_Unit_EventAjax_UnitLoading_AdminAjax extends AmazonAutoLinks_AjaxEvent_Base {

    /**
     * The part after `wp_ajax_` or `wp_ajax_nopriv_`.
     * @var string
     */
    protected $_sActionHookSuffix = 'aal_unit_ajax_loading';

    protected $_bLoggedIn = true;
    protected $_bGuest    = true;

    protected function _construct() {
        add_action( 'aal_action_enqueue_scripts_ajax_unit_loading', array( $this, 'replyToEnqueueScripts' ) );
    }

    /**
     * @param  array $aPost Passed POST data.
     * @return array
     * @since  4.6.18
     */
    protected function _getPostSanitized( array $aPost ) {
        return array(
            'data' => $this->getElementAsArray( $aPost, 'data' ),
        );
    }

    /**
     * @return string|array
     * @throws Exception    Throws a string value of an error message.
     * @param  array        $aPost  POST data, containing the `data' element.
     */
    protected function _getResponse( array $aPost ) {

        if ( empty( $aPost[ 'data' ] ) ) {
            throw new Exception( __( 'Failed to load the unit.', 'amazon-auto-links' ) );
        }

        // At this point, it is an ajax request (admin-ajax.php + `{wp_ajax_/wp_ajax_nopriv_}aal_unit_ajax_loading` action hook )

        $_oAjaxUnitOutput = new AmazonAutoLinks_Unit_AjaxUnitLoading();
        return $_oAjaxUnitOutput->get( $aPost[ 'data' ] );

    }


    /**
     * @since   4.3.0
     */
    public function replyToEnqueueScripts() {

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
            'ajaxURL'            => admin_url( 'admin-ajax.php' ),
            'spinnerURL'         => admin_url( 'images/loading.gif' ),
            'nonce'              => wp_create_nonce( $this->_sNonceKey ), // when not declared in class properties, the value will be the action name suffix
            'actionHookSuffix'   => $this->_sActionHookSuffix,
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
     * @since 5.3.1
     */
    protected function _doAction() {
        $this->_sNonceKey = ''; // disable nonce check
        parent::_doAction();
    }

}