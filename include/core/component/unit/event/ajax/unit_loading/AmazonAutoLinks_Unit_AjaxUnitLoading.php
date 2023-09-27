<?php
/**
 * Auto Amazon Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2023 Michael Uno
 */

/**
 * Provides common class methods of response outputs for Ajax unit-loading event handler classes.
 *
 * @since 5.4.0
 */
class AmazonAutoLinks_Unit_AjaxUnitLoading extends AmazonAutoLinks_PluginUtility {

    /**
     * @since 5.4.0
     */
    public function __construct() {

        // For the contextual widget
        add_filter( 'aal_filter_http_get', array( $this, 'replyToGetHTTPGETRequest' ) );
        add_filter( 'aal_filter_post_object', array( $this, 'replyToSetReferrerPostObject' ) );
        add_filter( 'aal_filter_current_page_type', array( $this, 'replyToSetReferrerPageType' ) );
        add_filter( 'aal_filter_current_queried_term_object', array( $this, 'replyToSetReferrerTermObject' ) );
        add_filter( 'aal_filter_current_queried_author', array( $this, 'replyToSetReferrerAuthor' ) );

    }

    /**
     * @since  5.4.0
     * @param  array  $aPostData the _sanitized_ $_POST[ 'data' ] value
     * @return string
     */
    public function get( array $aPostData ) {

        // For widget outputs, retrieve the widget instance options.
        if ( isset( $aPostData[ '_widget_option_name' ] ) ) {
            $_aWidgetOptions  = get_option( $aPostData[ '_widget_option_name' ] );
            $aPostData           = $this->getElement( $_aWidgetOptions, $aPostData[ '_widget_number' ] );
        }

        return $this->___getUnitOutput( $aPostData );

    }
        /**
         * @param  array  $aArguments
         * @since  3.6.0
         * @since  5.4.0  Renamed from `___getOutput()`. Moved from `AmazonAutoLinks_Unit_EventAjax_UnitLoading`.
         * @return string
         */
        private function ___getUnitOutput( $aArguments ) {
            $aArguments[ 'load_with_javascript' ] = false;  // this must be set to false as it just returns the Ajax replacement output.
            return apply_filters( 'aal_filter_output', '', $aArguments )
                . apply_filters( 'aal_filter_svg_definitions', '' );
        }

    /**
     * @param  $aGET
     * @return array
     * @since  3.6.0
     * @since  5.4.0 Moved from `AmazonAutoLinks_Unit_EventAjax_UnitLoading`.
     * @remark Will be sanitized later.
     */
    public function replyToGetHTTPGETRequest( $aGET ) {
        return isset( $_POST[ 'REQUEST' ] )
            ? array(
                's' => _sanitize_text_fields( $this->getElement( $_POST, array( 's' ) ) ),   // sanitization done
            )
            : $aGET;
    }
    /**
     * @since  3.6.0
     * @since  5.4.0  Moved from `AmazonAutoLinks_Unit_EventAjax_UnitLoading`.
     * @return object The referrer's post object.
     */
    public function replyToSetReferrerPostObject( $oPost ) {
        return isset( $_POST[ 'post_id' ] ) && $_POST[ 'post_id' ]
            ? get_post( absint( $_POST[ 'post_id' ] ) )
            : $oPost;
    }

    /**
     * @return string
     * @since  3.6.0
     * @since  5.4.0 Moved from `AmazonAutoLinks_Unit_EventAjax_UnitLoading`.
     */
    public function replyToSetReferrerPageType( $sPageType ) {
        return isset( $_POST[ 'page_type' ] ) && $_POST[ 'page_type' ]
            ? sanitize_text_field( $_POST[ 'page_type' ] )
            : $sPageType;
    }

    /**
     * @return object
     * @since  3.6.0
     * @since  5.4.0  Moved from `AmazonAutoLinks_Unit_EventAjax_UnitLoading`.
     */
    public function replyToSetReferrerTermObject( $oTerm ) {
        return isset( $_POST[ 'term_id' ] ) && $_POST[ 'term_id' ]
            ? get_term( absint( $_POST[ 'term_id' ] ) )
            : $oTerm;
    }
    /**
     * @return string
     * @since  3.6.0
     * @since  5.4.0 Moved from `AmazonAutoLinks_Unit_EventAjax_UnitLoading`.
     */
    public function replyToSetReferrerAuthor( $sAuthor ) {
        return isset( $_POST[ 'author_name' ] ) && $_POST[ 'author_name' ]
            ? sanitize_text_field( $_POST[ 'author_name' ] )
            : $sAuthor;
    }

    /**
     * @return array
     * @since  5.4.0 Moved from `AmazonAutoLinks_Unit_EventAjax_UnitLoading`
     */
    static public function getPageTypeInformationForContextualUnits() {
        $_sPageType     = self::getCurrentPageType();
        $_aPageTypeInfo = array(
            'term_id'       => 0,
            'author_name'   => '',
            'page_type'     => $_sPageType,
            'post_id'       => get_the_ID(),
            'REQUEST'       => array(
                // Currently, contextual units only use the `s` field.
                's' => sanitize_text_field( self::getElement( $_REQUEST, array( 's' ) ) ),
            ),
        );
        if ( 'taxonomy' === $_sPageType ) {
            $_oTerm = self::getCurrentQueriedObject();
            $_aPageTypeInfo[ 'term_id' ] = isset( $_oTerm->term_id )
                ? $_oTerm->term_id
                : 0;
            return $_aPageTypeInfo;
        }
        if ( 'author' === $_sPageType ) {
            $_oAuthor = self::getCurrentQueriedObject();
            $_aPageTypeInfo[ 'author_name' ] = isset( $_oAuthor->display_name )
                ? $_oAuthor->display_name
                : '';
            return $_aPageTypeInfo;
        }
        return $_aPageTypeInfo;
    }

}