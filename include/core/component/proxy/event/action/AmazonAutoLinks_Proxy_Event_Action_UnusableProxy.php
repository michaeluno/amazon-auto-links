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
 * Saves unusable proxies.
 *
 * @since        4.2.0
 */
class AmazonAutoLinks_Proxy_Event_Action_UnusableProxy extends AmazonAutoLinks_PluginUtility {

    /**
     * @var array
     */
    private $___aUnusableProxies = array();

    /**
     * @assmes  Already checked whether the proxy option is enabled or not.
     * @since   4.2.0
     */
    public function __construct() {

        add_action( 'aal_action_detected_unusable_proxy', array( $this, 'replyToCaptureUnusableProxies' ), 10 );

    }
        /**
         * Stores the failed proxy as unusable.
         *
         * The stored proxies will be saved at shutdown.
         * @param array $aArguments
         * @since 4.2.0
         */
        public function replyToCaptureUnusableProxies( array $aArguments ) {

            /**
             * Allows the user to disable the updates of the list of unusable proxies.
             * @since 5.4.3
             */
            if ( ! ( boolean ) apply_filters( 'aal_filter_http_request_proxy_update_unusable', true ) ) {
                return;
            }

            $_sEntry = $this->getElement( $aArguments,  array( 'proxy', 'raw' ),  '' );
            $this->___aUnusableProxies[ $_sEntry ] = $_sEntry;  // prevent duplicates by setting the value in key
            if ( 1 === count( $this->___aUnusableProxies ) ) {
                add_action( 'shutdown', array( $this, 'replyToSaveUnusableProxies' ) );
            }

        }

    /**
     * @since   4.2.0
     * @callback    action      shutdown
     */
    public function replyToSaveUnusableProxies() {

        $_oToolOption    = AmazonAutoLinks_ToolOption::getInstance();

        $_sProxyList     = $_oToolOption->get( array( 'proxies', 'proxy_list' ), '' );
        $_aProxies       = ( array ) preg_split( "/\s+/", trim( ( string ) $_sProxyList ), 0, PREG_SPLIT_NO_EMPTY );
        $_sUnusable      = $_oToolOption->get( array( 'proxies', 'unusable' ), '' );
        $_aUnusables     = ( array ) preg_split( "/\s+/", trim( ( string ) $_sUnusable ), 0, PREG_SPLIT_NO_EMPTY );

        foreach( $this->___aUnusableProxies as $_sProxy ) {

            $_aUnusables[] = $_sProxy;
            $_biIndex      = array_search( $_sProxy, $_aProxies );
            if ( false === $_biIndex ) {
                continue;
            }

            // Unusable one is found in the list
            unset( $_aProxies[ $_biIndex ] );

        }
        $_aUnusables     = $this->getBottomMostItems( array_unique( $_aUnusables ), 1000 );

        // Save
        $_oToolOption->set( array( 'proxies', 'proxy_list' ), implode( PHP_EOL, $_aProxies ) );
        $_oToolOption->set( array( 'proxies', 'unusable' ), implode( PHP_EOL, $_aUnusables ) );
        $_oToolOption->save();

    }

}