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
 * Provides outputs for Ajax unit loading.
 *
 * If the `load_with_javascript` unit option is enabled, the unit displays a minimal output
 * and the Javascript script with Ajax replaces it with the content generated with this class method.
 *
 * @package      Amazon Auto Links
 * @since        3.6.0
 * @since        4.3.0 Renamed from `AmazonAutoLinks_Event___Action_AjaxUnitLoading`
 * @since        4.3.0 Changed the base class from `AmazonAutoLinks_Event___Action_Base`.
 */
class AmazonAutoLinks_Unit_EventAjax_UnitLoading extends AmazonAutoLinks_AjaxEvent_Base {

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
     * @param array $aPost
     *
     * @return string|array
     * @throws Exception        Throws a string value of an error message.
     */
    protected function _getResponse( array $aPost ) {

        if ( ! isset( $aPost[ 'data' ] ) ) {
            throw new Exception( __( 'Failed to load the unit.', 'amazon-auto-links' ) );
        }

        // At this point, it is an ajax request (admin-ajax.php + `{wp_ajax_/wp_ajax_nopriv_}aal_unit_ajax_loading` action hook )

        // For the contextual widget
        add_filter( 'aal_filter_http_get', array( $this, 'replyToSetReferrerHTTPGET' ) );
        add_filter( 'aal_filter_post_object', array( $this, 'replyToSetReferrerPostObject' ) );
        add_filter( 'aal_filter_current_page_type', array( $this, 'replyToSetReferrerPageType' ) );
        add_filter( 'aal_filter_current_queried_term_object', array( $this, 'replyToSetReferrerTermObject' ) );
        add_filter( 'aal_filter_current_queried_author', array( $this, 'replyToSetReferrerAuthor' ) );

        $_aData = $aPost[ 'data' ];

        // For widget outputs, retrieve the widget instance options.
        if ( isset( $_aData[ '_widget_option_name' ] ) ) {
            $_aWidgetOptions  = get_option( $_aData[ '_widget_option_name' ] );
            $_aData           = $this->getElement( $_aWidgetOptions, $_aData[ '_widget_number' ] );
        }

        return $this->___getOutput( $_aData );

    }
        /**
         * @param   array   $aArguments
         * @since   3.6.0
         * @return  string
         */
        private function ___getOutput( $aArguments ) {

            // `load_with_javascript` must be set to false as it just returns the Ajax replacement output.
            $aArguments[ 'load_with_javascript' ] = false;

            return AmazonAutoLinks( $aArguments, false );

        }

    /**
     * @param       $aGET
     * @return      array
     * @since       3.6.0
     */
    public function replyToSetReferrerHTTPGET( $aGET ) {
        return isset( $_POST[ 'REQUEST' ] )
            ? $_POST[ 'REQUEST' ]
            : $aGET;
    }
    /**
     * @since       3.6.0
     * @return      object          The referrer's post object.
     */
    public function replyToSetReferrerPostObject( $oPost ) {
        return isset( $_POST[ 'post_id' ] ) && $_POST[ 'post_id' ]
            ? get_post( $_POST[ 'post_id' ] )
            : $oPost;
    }

    /**
     * @return  string
     * @since   3.6.0
     */
    public function replyToSetReferrerPageType( $sPageType ) {
        return isset( $_POST[ 'page_type' ] ) && $_POST[ 'page_type' ]
            ? $_POST[ 'page_type' ]
            : $sPageType;
    }

    /**
     * @return  object
     * @since   3.6.0
     */
    public function replyToSetReferrerTermObject( $oTerm ) {
        return isset( $_POST[ 'term_id' ] ) && $_POST[ 'term_id' ]
            ? get_term( $_POST[ 'term_id' ] )
            : $oTerm;
    }
    /**
     * @return  string
     * @since   3.6.0
     */
    public function replyToSetReferrerAuthor( $sAuthor ) {
        return isset( $_POST[ 'author_name' ] ) && $_POST[ 'author_name' ]
            ? $_POST[ 'author_name' ]
            : $sAuthor;
    }

    /**
     * @since   4.3.0
     */
    public function replyToEnqueueScripts() {

        // Do only once per page load
        if ( $this->hasBeenCalled( __METHOD__ ) ) {
            return;
        }

        $_sScriptHandle = 'aal-ajax-unit-loading';
        $_aScriptData   = array(
            'ajaxURL'            => admin_url( 'admin-ajax.php' ),
            'spinnerURL'         => admin_url( 'images/loading.gif' ),
            'nonce'              => wp_create_nonce( $this->_sNonceKey ), // when not declared in class properties, the value will be the action name suffix
            'actionHookSuffix'   => $this->_sActionHookSuffix,
            'messages'           => array(
                'ajax_error'     => __( 'Failed to load product links.', 'amazon-auto-links' ),
            ),
        ) + $this->___getPageTypeInformationForContextualUnits();

        $_sFileBaseName = defined( 'WP_DEBUG' ) && WP_DEBUG
            ? 'ajax-unit-loading.js'
            : 'ajax-unit-loading.min.js';
        wp_enqueue_script( 'jquery' );
        wp_enqueue_script(
            $_sScriptHandle,
            $this->getSRCFromPath( AmazonAutoLinks_UnitLoader::$sDirPath . '/asset/js/' . $_sFileBaseName ),
            array( 'jquery' ),
            false,
            true
        );

        // in JavaScript, object properties are accessed as ajax_object.ajax_url, ajax_object.we_value
        wp_localize_script(
            $_sScriptHandle,
            'aalAjaxUnitLoading ', // variable name
            $_aScriptData
        );
    }

        /**
         * @return  array
         */
        private function ___getPageTypeInformationForContextualUnits() {
            $_sPageType     = $this->getCurrentPageType();
            $_aPageTypeInfo = array(
                'term_id'       => 0,
                'author_name'   => '',
                'page_type'     => $_sPageType,
                'post_id'       => get_the_ID(),
                'REQUEST'       => $_REQUEST,
            );
            if ( 'taxonomy' === $_sPageType ) {
                $_oTerm = $this->getCurrentQueriedObject();
                $_aPageTypeInfo[ 'term_id' ] = isset( $_oTerm->term_id )
                    ? $_oTerm->term_id
                    : 0;
                return $_aPageTypeInfo;
            }
            if ( 'author' === $_sPageType ) {
                $_oAuthor = $this->getCurrentQueriedObject();
                $_aPageTypeInfo[ 'author_name' ] = isset( $_oAuthor->display_name )
                    ? $_oAuthor->display_name
                    : '';
                return $_aPageTypeInfo;
            }
            return $_aPageTypeInfo;
        }


}