<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2018 Michael Uno
 */
 
/**
 * Performs PA-API 5.0 requests.
 *
 * @sicne       3.9.0
 */
class AmazonAutoLinks_PAAPI50 extends AmazonAutoLinks_PluginUtility {

    private $___aConstructorParameters = array();
    private $___sLocale                = 'US';
    private $___sPublicKey             = '';
    private $___sSecretKey             = '';
    private $___sRequestType           = 'api';
    private $___aPayload               = array(
        'PartnerType'   => 'Associates',
        'Marketplace'   => 'www.amazon.com',
    );
    private $___sHTTPMethod            = 'POST';

    /**
     * @var array
     */
    private $___aHTTPArguments        = array(
        'timeout'       => 20,
        'redirection'   => 5,
        'sslverify'     => false,
    );

    /**
     * AmazonAutoLinks_PAAPI50 constructor.
     *
     * @param $sLocale
     * @param $sPublicKey
     * @param $sSecretKey
     * @param string $sAssociateID
     * @param array $aHTTPArguments
     * @param string $sRequestType
     */
    public function __construct( $sLocale, $sPublicKey, $sSecretKey, $sAssociateID='', array $aHTTPArguments=array(), $sRequestType='api' ) {
        $sLocale                         = strtoupper( $sLocale );
        $this->___aConstructorParameters = func_get_args() + array( '', '', '', '', array(), 'api' );
        $this->___aConstructorParameters[ 0 ] = $sLocale;  
        $this->___sLocale                = $sLocale;
        $this->___sPublicKey             = $sPublicKey;
        $this->___sSecretKey             = $sSecretKey;
        $this->___aPayload               = array(
            'PartnerTag'    => $sAssociateID,
        ) + $this->___aPayload;
        $this->___aHTTPArguments         = $aHTTPArguments + $this->___aHTTPArguments;
        $this->___sRequestType           = $sRequestType;
    }

    /**
     * Performs a test request.
     *
     * @remark      Used in the Authentication plugin setting admin page.
     * @return      boolean|string  If succeeds, returns true; otherwise, the error message.
     * @since       3.9.0
     */
    public function test() {

        $_sPrevRequestType     = $this->___sRequestType;
        $this->___sRequestType = 'api50_test';
        $_aPayload    = array(
                'Keywords'      => 'WordPress',
                'ItemPage'      => 71,
                'ItemCount'     => 1,
                'Operation'     => 'SearchItems',
                // 'LanguagesOfPreference' => array( 'en_US', ),
            ) + $this->___aPayload;

        $_aResponse = $this->request( $_aPayload, 60 * 10 );
        $this->___sRequestType = $_sPrevRequestType;
        if ( isset( $_aResponse[ 'Error' ] ) ) {
            $_sError = $this->getElement( $_aResponse, array( 'Error', 'Code' ) )
                . ' ' . $this->getElement( $_aResponse, array( 'Error', 'Message' ) );
AmazonAutoLinks_Debug::log( $_sError );
            return $_sError;
        }
        return true;

    }
        private function ___getHTTPStatusError( array $aWPRemoteResponse ) {
            $_sCode    = $this->getElement( $aWPRemoteResponse, array( 'response', 'code' ) );
            $_s1stChar = substr( $_sCode, 0, 1 );
            if ( in_array( $_s1stChar, array( 2, 3 ) ) ) {
                return '';
            }
            return $_sCode . ': ' . $this->getElement( $aWPRemoteResponse, array( 'response', 'message' ) );
        }
        private function ___getAPIResponseError( array $aResponse ) {
            if ( ! isset( $aResponse[ 'Errors' ] ) ) {
                return '';
            }
            $_sError = '';
            foreach( $aResponse[ 'Errors' ] as $_aError ) {
                $_sError .= $_aError[ 'Code' ] . ': ' . $_aError[ 'Message' ] . ' ';
            }
            return trim( $_sError );
        }


    /**
     * Performs an API request from the given request API parameters and returns the result as associative array.
     *
     * @remark      Accessed publicly
     * @since       3.9.0
     * @return      array
     */
    public function request( array $aPayload, $iCacheDuration=86400, $bForceCaching=false ) {
        $aPayload     = $aPayload + $this->___aPayload;
        $_oAPIHeader  = new AmazonAutoLinks_PAAPI50___RequestHeaderGenerator(
            $this->___sPublicKey,
            $this->___sSecretKey,
            $this->___sLocale,
            $aPayload,
            $this->___sHTTPMethod
        );
        $_sRequestURI    = $_oAPIHeader->getRequestURI();
        $_aConstructorParams = $this->___aConstructorParameters;
        $_aConstructorParams[ 5 ] = $this->___sRequestType;
        $_aHTTPArguments = array(
            'method'    => $this->___sHTTPMethod,
            'headers'   => $_oAPIHeader->getHeaders(),
            'body'      => $_oAPIHeader->getPayload(),
        ) + array(
            // Inject additional arguments for the background cache renewal event.
            'constructor_parameters' => $_aConstructorParams,
            'api_parameters'         => $aPayload,
        );

        $_oAPIRequestCache      = new AmazonAutoLinks_PAAPI50___Cache(
            $_sRequestURI,
            $_aHTTPArguments,
            $iCacheDuration,
            $bForceCaching,
            $this->___sRequestType
        );
        $_aoResponse = $_oAPIRequestCache->get();

        // Error Handling
        if ( is_wp_error( $_aoResponse ) ) {
            return array(
                'Error' => array(
                    'Message' => $_aoResponse->get_error_message(),
                    'Code'    => $_aoResponse->get_error_code()
                )
            );
        }

        $_sHTTPStatusError = $this->___getHTTPStatusError( $_aoResponse );
        if ( $_sHTTPStatusError ) {
            $_sHTTPBody = wp_remote_retrieve_body( $_aoResponse );
            if ( $this->isJSON( $_sHTTPBody ) ) {
                $_aResponse = json_decode( $_sHTTPBody, true );
                $_sError    = '; ' . $this->___getAPIResponseError( $_aResponse );
            }
            return array(
                'Error' => array(
                    'Message' => $this->getElement( $_aoResponse, array( 'response', 'message' ) ) . ' ' . $_sError,
                    'Code'    => $this->getElement( $_aoResponse, array( 'response', 'code' ) ),
                )
            );
        }

        $_sResponseJSON = wp_remote_retrieve_body( $_aoResponse );
        $_aResponse     = $this->getAsArray( json_decode( $_sResponseJSON, true ) );
        $_sAPIError     = $this->___getAPIResponseError( $_aResponse );
        if ( $_sAPIError ) {
            return array(
                'Error' => array(
                    'Message' => $_sAPIError,
                    'Code'    => 'PAAPIError',
                )
            );
        }
AmazonAutoLinks_Debug::log( $_aResponse );
        return $_aResponse;

    }
        // @deprecated moved to the utility class
//        private function ___isJSON( $sString ) {
//           json_decode( $sString );
//           return (json_last_error() == JSON_ERROR_NONE);
//        }

    public function scheduleInBackground( array $aPayload, $iCacheDuration=86400 ) {
    }

}