<?php
/**
 * Amazon Auto Links
 *
 * http://en.michaeluno.jp/amazn-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 *
 */

/**
 * Appends a debug output to the unit output.
 *
 * @since 4.4.0
 */
class AmazonAutoLinks_Unit_URL_Event_DebugOutput extends AmazonAutoLinks_PluginUtility {

    /**
     * Stores retrieved HTML bodies for debug outputs.
     * @since   3.2.2
     * @since   4.4.0   Moved from `AmazonAutoLinks_UnitOutput_url`.
     */
    private $___aHTMLs = array();    
    
    public function __construct() {
        $_oOption = AmazonAutoLinks_Option::getInstance();
        if ( ! $_oOption->isDebug() ) {
            return;
        }        
        add_filter( 'aal_filter_http_request_result', array( $this, 'replyToCaptureHTTPResponse' ), 10, 5 );
        add_filter( "aal_filter_unit_output_url", array( $this, 'replyToGetOutput' ), 10, 2 );
    }

    /**
     * @param  array|WP_Error $aoResponse
     * @param  string         $sURL
     * @param  array          $aArguments
     * @param  integer        $iCacheDuration
     * @param  string         $sRequestType
     * @return WP_Error|array
     */
    public function replyToCaptureHTTPResponse( $aoResponse, $sURL, $aArguments, $iCacheDuration, $sRequestType ) {
        if ( $sRequestType !== 'url_unit_type' ) {
            return $aoResponse;
        }
        $this->___aHTMLs[ $sURL ] = $this->___getResponseBody( $aoResponse );
        return $aoResponse;
    }
        /**
         * @param  WP_Error|array $aoResponse
         * @return string
         */
        private function ___getResponseBody( $aoResponse ) {
            if ( is_wp_error( $aoResponse ) ) {
                return trim( $aoResponse->get_error_code() . ' ' . $aoResponse->get_error_message() );
            }
            return wp_remote_retrieve_body( $aoResponse );
        }

    /**
     * @param  string $sContent
     * @param  array  $aUnitOptions
     * @return string
     * @since  4.4.0
     */
    public function replyToGetOutput( $sContent, $aUnitOptions ) {
        $_aHTMLs = $this->___aHTMLs;
        $this->___aHTMLs = array();   // reset for next outputs.
        return $sContent . $this->___getDebugOutput( $_aHTMLs );
    }
        /**
         * @return string
         * @since  3.3.2
         * @since  4.4.0    Moved from `AmazonAutoLinks_UnitOutput_url`.
         */
        private function ___getDebugOutput( array $aHTMLs ) {
            $_aAttributes = array(
                'class' => 'debug',
                'style' => $this->getInlineCSS( array(
                    'max-height' => '300px',
                    'overflow-y' => 'scroll',
                    'overflow-x' => 'auto',
                    'margin'     => '1em 0',
                    'padding'    => '1em',
                    'word-wrap'  => 'break-word',
                    'word-break' => 'break-all',
                ) ),
            );
            return '<pre ' . $this->getAttributes( $_aAttributes ) . '>'
                    . '<h4>'
                        . __( 'Debug Info', 'amazon-auto-links' )
                        . ' - ' . __( 'HTTP Bodies', 'amazon-auto-links' )
                    . '</h4>'
                    . AmazonAutoLinks_Debug::get( $aHTMLs )
                . "</pre>";
        }

}