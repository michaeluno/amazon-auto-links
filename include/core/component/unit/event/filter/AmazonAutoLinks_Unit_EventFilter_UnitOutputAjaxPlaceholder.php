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
 * Updates unit status via ajax calls.
 * @since   4.3.0
 */
class AmazonAutoLinks_Unit_EventFilter_UnitOutputAjaxPlaceholder extends AmazonAutoLinks_PluginUtility {


    public function __construct() {

        add_filter( 'aal_filter_pre_unit_output', array( $this, 'replyToGetAjaxPlaceholder' ), 10, 2 );

    }

    /**
     * @param array $aArguments
     * @param array $aRawArguments
     *
     * @return string
     */
    public function replyToGetAjaxPlaceholder( array $aArguments, array $aRawArguments ) {

        $_bJSLoading = $this->___getWhetherToLoadWithJavaScript( $aArguments, $aRawArguments );
        if ( ! $_bJSLoading ) {
            return '';
        }
        return $this->___getOutputForAjax( $aArguments, $aRawArguments );

    }
        /**
         * Checks if the product outputs should be loaded with JavaScript or not.
         * @since       3.6.0
         * @return      boolean
         * @since       4.3.0       Moved from `AmazonAutoLinks_Output___Ajax`.
         */
        private function ___getWhetherToLoadWithJavaScript( array $aArguments, array $aRawArguments ) {

            // Check the direct arguments.
            // It is possible that the user explicitly sets it `false` to disable it.
            // Also, it is possible that the user left it unset to use the default option value.
            if ( isset( $aRawArguments[ 'load_with_javascript' ] ) ) {
                return ( boolean ) $aRawArguments[ 'load_with_javascript' ];
            }

            // At this point, in the direct arguments, it is not specified.

            // It is possible that this is enabled in unit arguments.
            // For multiple unit IDs, the first item's setting gets applied to all the rest.
            // This is because performing Ajax requests for each unit causes resource overheads.
            $_aIDs    = $this->getAsArray( $aArguments[ '_unit_ids' ] );
            if ( ! empty( $_aIDs ) ) {
                $_iPostID = $this->getFirstElement( $_aIDs );
                return ( boolean ) $this->getPostMeta( $_iPostID, 'load_with_javascript' );
            }

            // At this point, units are not specified, meaning the user wants to load product outputs by direct arguments.
            // At an earlier point, it was figured that the `load_with_javascript` argument was not set.
            // It is possible that this is enabled in the default arguments.
            $_oOption = AmazonAutoLinks_Option::getInstance();
            return ( boolean ) $_oOption->get( 'unit_default', 'load_with_javascript' );

        }

    /**
     * Generates outputs for Ajax request replacements.
     *
     * Possible cases:
     *  a) given _single_ unit ID
     *      1. JavaScript loading
     *      2. normal loading (rendered with PHP)
     *  b) given _multiple_ unit IDs
     *      1. JavaScript loading for all the units
     *      2. JavaScript loading for partial units
     *      3. normal loading (rendered with PHP)
     *
     * The case b-2 will be treated as b-1 as performing Ajax request for each unit is a resource burden.
     *
     * @since       3.6.0
     * @since       4.3.0       Moved from `AmazonAutoLinks_Output___Ajax`.
     * @returen     string
     */
    private function ___getOutputForAjax( array $aArguments, array $aRawArguments ) {

        // Keep options minimum, especially for widgets
        // to avoid errors caused by invalid characters embedded in data attributes
        // as there are options that contain HTML tags.
        if ( $this->getElement( $aArguments, array( '_widget_option_name' ) ) ) {
            $aRawArguments = array(
                '_widget_option_name'   => $aArguments[ '_widget_option_name' ],
                '_widget_number'        => $aArguments[ '_widget_number' ],
            );
        }

        $_aDataAttributes = $aRawArguments;
        $_aAttributes     = $this->getDataAttributeArray( $_aDataAttributes );
        $_aAttributes[ 'class' ]       = 'amazon-auto-links aal-js-loading';

        $this->___enqueueScript();

        $_sNowLoadingText = $this->___getNowLoadingText( $aArguments );
        $_sPNowLoading    = $_sNowLoadingText
            ? "<p>" . $_sNowLoadingText . "</p>"
            : '';

        return "<div " . $this->getAttributes( $_aAttributes ) . ">"
                . apply_filters( 'aal_filter_element_now_loading', $_sPNowLoading )   // allows third parties to set custom element
            . "</div>";

    }
        private function ___getNowLoadingText( array $aArguments ) {

            $_aIDs    = $this->getAsArray( $aArguments[ '_unit_ids' ] );

            // For direct arguments
            if ( empty( $_aIDs ) ) {
                return $this->getElement( $aArguments, array( '_now_loading_text' ) );
            }

            $_oOption = AmazonAutoLinks_Option::getInstance();
            $_iPostID = $this->getFirstElement( $_aIDs );
            return $_iPostID
                ? $this->getPostMeta( $_iPostID, '_now_loading_text' )
                : $_oOption->get( 'unit_default', '_now_loading_text' );

        }

        private function ___enqueueScript() {

            // Do only once per page load
            if ( $this->hasBeenCalled( __METHOD__ ) ) {
                return;
            }

            $_sScriptHandle = 'aal-ajax-unit-loading';
            $_sAjaxNonce    = wp_create_nonce( 'aal_nonce_ajax_unit_loading' );
            $_aScriptData   = array(
                'ajax_url'           => admin_url( 'admin-ajax.php' ),
                'nonce'              => $_sAjaxNonce,
                'action_hook_suffix' => 'aal_unit_ajax_loading',
                'messages'           => array(
                    'ajax_error'    => __( 'Failed to load product links.', 'amazon-auto-links' ),
                ),
            ) + $this->___getPageTypeInformationForContextualUnits();

            $_sFileBaseName = defined( 'WP_DEBUG' ) && WP_DEBUG
                ? 'ajax-unit-loading.js'
                : 'ajax-unit-loading.min.js';
            wp_enqueue_script( 'jquery' );
            wp_enqueue_script(
                $_sScriptHandle,
                $this->getSRCFromPath( AmazonAutoLinks_UnitTypesLoader::$sDirPath . '/asset/js/' . $_sFileBaseName ),
                array( 'jquery' ),
                false,
                true
            );

            // in JavaScript, object properties are accessed as ajax_object.ajax_url, ajax_object.we_value
            wp_localize_script(
                $_sScriptHandle,
                'aal_ajax_unit_loading', // variable name
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