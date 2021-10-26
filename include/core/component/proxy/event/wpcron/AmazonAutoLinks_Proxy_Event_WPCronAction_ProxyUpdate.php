<?php
/**
 * Auto Amazon Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 */

/**
 * A WP Cron action that updates the proxy list.
 *
 * @since        4.2.0
 */
class AmazonAutoLinks_Proxy_Event_WPCronAction_ProxyUpdate extends AmazonAutoLinks_Event___Action_Base {

    protected $_sActionHookName = 'aal_action_proxy_update';

    protected function _construct() {

        $this->___scheduleNext();

    }
        /**
         * Schedule the automatic update.
         * @since   4.2.0
         */
        private function ___scheduleNext() {

            // Get the set interval in seconds.
            $_oToolOption   = AmazonAutoLinks_ToolOption::getInstance();
            $_iSize         = ( integer ) $_oToolOption->get( array( 'proxy', 'proxy_update_interval', 'size' ), 1 );
            $_iUnit         = ( integer ) $_oToolOption->get( array( 'proxy', 'proxy_update_interval', 'unit' ), 86400 );
            $_iInterval     = $_iSize * $_iUnit;
            $_iTime         = time() + $_iInterval;
            $this->scheduleSingleWPCronTask( $this->_sActionHookName, array(), $_iTime );

        }

    /**
     *
     * @callback        action        aal_action_proxy_update
     */
    protected function _doAction( /* $aArguments */ ) {

        $_oToolOption    = AmazonAutoLinks_ToolOption::getInstance();
        $_sProxyList     = $_oToolOption->get( array( 'proxies', 'proxy_list' ), '' );
        $_aProxies       = ( array ) preg_split( "/\s+/", trim( ( string ) $_sProxyList ), 0, PREG_SPLIT_NO_EMPTY );

        // Retrieve new proxies.
        $_aProxies       = array_merge( $_aProxies, $this->___getNewProxies() );
        $_aProxies       = array_unique( $_aProxies );
        $_aProxies       = $this->getTopMostItems( $_aProxies, 10000 );

        // Save
        /// Store the last run time.
        $_oToolOption->set( array( 'proxies', 'update_last_run_time' ), time() );
        $_oToolOption->set( array( 'proxies', 'proxy_list' ), implode( PHP_EOL, $_aProxies ) );
        $_oToolOption->save();

        // Reschedule
        $this->___scheduleNext();

    }
        /**
         * @return array
         */
        private function ___getNewProxies() {
            $_aProxies = apply_filters( 'aal_filter_imported_proxies', array() );
            $_aProxies = $this->getAsArray( $_aProxies );
            return ( array ) array_unique( $_aProxies );
        }


}