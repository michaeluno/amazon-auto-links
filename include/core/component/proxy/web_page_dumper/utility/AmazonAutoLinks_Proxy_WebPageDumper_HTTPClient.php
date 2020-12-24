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
 * A wrapper class for the HTTP client class.
 *
 * @package      Amazon Auto Links
 * @since        4.5.0
 */
class AmazonAutoLinks_Proxy_WebPageDumper_HTTPClient extends AmazonAutoLinks_HTTPClient {

    /**
     * @var   string
     * @since 4.5.0
     */
    public $sRequestType = 'web_page_dumper';

    /**
     * @var   string
     * @since 4.5.0
     */
    public $sWebPageDumperURL;

    /**
     *
     * @param string  $sWebPageDumperURL
     * @param string  $sRequestURL
     * @param integer $iCacheDuration
     * @param array   $aArguments
     * @param string  $sRequestType
     * @param array   $aCache
     * @since 4.5.0
     */
    public function __construct( $sWebPageDumperURL, $sRequestURL, $iCacheDuration=86400, array $aArguments=array(), $sRequestType='web_page_dumper', array $aCache=array() ) {

        $this->sWebPageDumperURL = $sWebPageDumperURL;
        parent::__construct( $sRequestURL, $iCacheDuration, $aArguments, $sRequestType, $aCache );

    }

    /**
     * @remark This method is reached when there is no cache for the request URL.
     * @param  string $sURL
     * @param  array  $aArguments
     * @return array|WP_Error
     * @since  4.5.0
     */
    protected function _getHTTPRequested( $sURL, array $aArguments ) {

        $_aoResponse = $this->___getServerWakenUp();
        if ( is_wp_error( $_aoResponse ) ) {
            return $_aoResponse;
        }
        return parent::_getHTTPRequested( $this->___getWebPageDumperEndpoint( $this->sWebPageDumperURL, $sURL ), $aArguments );

    }
        /**
         * Web Page Dumper might be sleeping so try accessing multiple times.
         * @return array|WP_Error
         * @since  4.5.0
         */
        private function ___getServerWakenUp() {

            for ( $_i = 1 ; $_i <= 4; $_i++ ) {
                $_aArguments = array(
                    'timeout' => $_i * 5,
                );
                $_aoResponse = wp_remote_get( $this->sWebPageDumperURL, $_aArguments );
                if ( $this->hasPrefix( '2', $this->___getStatusCode( $_aoResponse ) ) ) {
                    return $_aoResponse;
                }
                if ( false === strpos( $this->___getStatusMessage( $_aoResponse ), 'timed out' ) ) {
                    return $_aoResponse;
                }
                // cURL error 28: Operation timed out after 30001 milliseconds with 0 bytes received
                sleep( 2 ); // try again
            }
            return new WP_Error( 'WEB_PAGE_DUMPER', 'The server does not wake up: ' . $this->sWebPageDumperURL );

        }

            private function ___getStatusMessage( $aoResponse ) {
                return is_wp_error( $aoResponse )
                   ? $aoResponse->get_error_message()
                   : $this->getElement( $aoResponse, array( 'response', 'message' ) );
            }
            private function ___getStatusCode( $aoResponse ) {
                return is_wp_error( $aoResponse )
                   ? 'WP_ERROR:' . $aoResponse->get_error_code()
                   : ( integer ) $this->getElement( ( array ) $aoResponse, array( 'response', 'code' ) );
            }

        /**
         * @param  string $sWebPageDumperURL
         * @param  string $sRequestURL
         *
         * @return string
         */
        private function ___getWebPageDumperEndpoint( $sWebPageDumperURL, $sRequestURL ) {
            return add_query_arg(
                array(
                    'url'    => urlencode( $sRequestURL ),
                    'output' => 'html',
                    'reload' => 1,
                ),
                preg_replace( '/\/(www(\/?))?$/', '', $sWebPageDumperURL ) . '/www/'
            );
        }

}