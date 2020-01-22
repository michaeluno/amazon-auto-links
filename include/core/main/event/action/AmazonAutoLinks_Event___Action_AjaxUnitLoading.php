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
 */
class AmazonAutoLinks_Event___Action_AjaxUnitLoading extends AmazonAutoLinks_Event___Action_Base {

    protected $_sActionHookName     = 'wp_ajax_nopriv_aal_unit_ajax_loading';

    protected function _construct() {
        /**
         * The both `wp_ajax_nopriv_{...}` and `wp_ajax_{...}` need to be hooked.
         * `wp_ajax_{...}` is for logged-in users
         * and `wp_ajax_nopriv_{...}` is for non-logged-in users.
         */
        add_action(
            'wp_ajax_aal_unit_ajax_loading',
            array( $this, 'replyToDoAction' )   // defined in the base class
        );

    }

    /**
     * @since       3.6.0
     */
    protected function _doAction() {

        check_ajax_referer( 'aal_nonce_ajax_unit_loading', 'aal_ajax_unit_loading_security' );

        if ( ! isset( $_POST[ 'data' ] ) ) {
            echo "<div class='amazon-auto-links'>"
                . __( 'Failed to load the unit.', 'amazon-auto-links' )
                . "</div>";
        }

        // At this point, it is an ajax request (admin-ajax.php + `{wp_ajax_/wp_ajax_nopriv_}aal_unit_ajax_loading` action hook )

        // For the contextual widget
        add_filter( 'aal_filter_http_get', array( $this, 'replyToSetReferrerHTTPGET' ) );
        add_filter( 'aal_filter_post_object', array( $this, 'replyToSetReferrerPostObject' ) );
        add_filter( 'aal_filter_current_page_type', array( $this, 'replyToSetReferrerPageType' ) );
        add_filter( 'aal_filter_current_queried_term_object', array( $this, 'replyToSetReferrerTermObject' ) );
        add_filter( 'aal_filter_current_queried_author', array( $this, 'replyToSetReferrerAuthor' ) );

        $_aData = $_POST[ 'data' ];

        // For widget outputs, retrieve the widget instance options.
        if ( isset( $_aData[ '_widget_option_name' ] ) ) {
            $_aWidgetOptions  = get_option( $_aData[ '_widget_option_name' ] );
            $_aData           = $this->getElement( $_aWidgetOptions, $_aData[ '_widget_number' ] );
        }

        echo $this->___getOutput( $_aData );
        die(); // this is required to return a proper result

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


}