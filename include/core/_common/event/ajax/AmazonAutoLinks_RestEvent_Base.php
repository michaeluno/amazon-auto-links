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
 * A base class for REST API event handler classes.
 * @since 5.4.0
 */
abstract class AmazonAutoLinks_RestEvent_Base extends AmazonAutoLinks_PluginUtility {

    public $sRoute = '';

    /**
     * Performs necessary set-ups.
     */
    public function __construct() {

        add_action( 'rest_api_init', array( $this, 'replyToRegister' ) );
        add_action( 'wp', array( $this, 'replyToLoadResources' ) );

    }

    public function replyToLoadResources() {
        $this->_loadResources();
    }

    protected function _loadResources() {
    }

    public function replyToRegister() {
        $_bResult = $this->_register();
    }

    protected function _register() {
        return register_rest_route(
            'wp/v2',
            $this->sRoute,
            array(
                'methods'             => array( 'GET', 'POST' ),
                'callback'            => array( $this, 'replyToRespond' ),
                'permission_callback' => '__return_true', // publicly available
            )
        );
    }

    public function replyToRespond( WP_REST_Request $oWPRESTRequest ) {
        return $this->_respond( $oWPRESTRequest );
    }

    /**
     * @param  WP_REST_Request $oWPRESTRequest
     * @return string|array If an array is returned, it will be printed as JSON
     */
    protected function _respond( WP_REST_Request $oWPRESTRequest ) {
        return '';
    }

}