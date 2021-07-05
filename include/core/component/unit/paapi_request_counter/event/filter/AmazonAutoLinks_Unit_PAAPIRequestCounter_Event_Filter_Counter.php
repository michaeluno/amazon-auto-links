<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 */

/**
 * Counts PA-API requests.
 *
 * @since 4.4.0
 */
class AmazonAutoLinks_Unit_PAAPIRequestCounter_Event_Filter_Counter extends AmazonAutoLinks_PluginUtility {

    /**
     * Sets up hooks.
     */
    public function __construct() {
        add_filter( 'aal_filter_http_request_response', array( $this, 'replyToCountRequests' ), 10, 5 );
    }

    /**
     * @param  WP_Error|array $aoResponse
     * @param  string  $sURL
     * @param  array   $aArguments
     * @param  string  $sRequestType
     * @param  integer $iCacheDuration
     * @return WP_Error|array
     * @since  4.0.0
     */
    public function replyToCountRequests( $aoResponse, $sURL, $aArguments, $sRequestType, $iCacheDuration ) {
        $this->___count( $this->getAsArray( $aArguments ) );
        return $aoResponse;
    }
        /**
         * @param array $aArguments
         * @since 4.4.0
         */
        private function ___count( array $aArguments ) {
            $_aPAAPIRequestConstructorParameters = $this->getElementAsArray( $aArguments, array( 'constructor_parameters' ) );
            if ( empty( $_aPAAPIRequestConstructorParameters ) ) {
                return;
            }
            $_sLocale  = reset( $_aPAAPIRequestConstructorParameters );
            $_oCounter = new AmazonAutoLinks_VersatileFileManager_PAAPI_RequestCounter( $_sLocale );
            $_oCounter->increment();
        }

}