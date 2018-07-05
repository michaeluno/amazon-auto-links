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
 * Performs requests to the Product Advertising API.
 *
 * @sicne       unknown
 * @since       3.5.0           Extends `AmazonAutoLinks_PluginUtility`.
 */
class AmazonAutoLinks_ProductAdvertisingAPI extends AmazonAutoLinks_PluginUtility {

    /**
     * Add the prefix, https:, for a SSL server, otherwise, http:.
     * 
     * @see            http://docs.aws.amazon.com/AWSECommerceService/latest/DG/AnatomyOfaRESTRequest.html
     */
    private static $___aTopLevelDomains = array(
        'CA'    => 'ca',
        'CN'    => 'cn',
        'DE'    => 'de',
        'ES'    => 'es',
        'FR'    => 'fr',
        'IT'    => 'it',
        'JP'    => 'co.jp',
        'UK'    => 'co.uk',
        'US'    => 'com',
        'IN'    => 'in',            // 2.1.0+        
        'BR'    => 'com.br',        // 3.4.4+
        'MX'    => 'com.mx',        // 3.4.4+
        'AU'    => 'com.au',        // 3.5.5+
    );

    private $___aConstructorParameters = array();

    private $___sLocale = 'US';

    private $___sDomain = '';

    private $___sSecretAccessKey = '';

    private $___sRequestType = 'api';

    private $___aAPIParameters = array(
        'Version'      => '2013-08-01',
        'Service'      => 'AWSECommerceService',
        // a dummy Amazon Associate tag - required as the API returns an error without it.
        'AssociateTag' => 'amazon-auto-links-20',
    );

    /**
     * @var array
     */
    private $___aHTTPArguments = array(
        'timeout'       => 20,
        'redirection'   => 5,
        'sslverify'     => false,
    );

    /**
     * AmazonAutoLinks_ProductAdvertisingAPI constructor.
     *
     * @param       string   $sLocale
     * @param       string   $sAccessKey
     * @param       string   $sSecretAccessKey
     * @param       string   $sAssociateID
     * @since       unknown
     * @since       3.5.0       Removed the `$sVersion` parameter.
     */
    public function __construct( $sLocale, $sAccessKey, $sSecretAccessKey, $sAssociateID='', array $aHTTPArguments=array(), $sRequestType='api' ) {

        $this->___aConstructorParameters = func_get_args() + array( '', '', '', '', array(), 'api' );
        $this->___sLocale                = $sLocale;
        $this->___sDomain                = $this->___getDomain( $sLocale );
        $this->___sSecretAccessKey       = $sSecretAccessKey;
        $this->___aAPIParameters         = $this->___getAPIParametersFormatted(
            $sAccessKey,
            $sAssociateID
        );
        $this->___aHTTPArguments         = $aHTTPArguments + $this->___aHTTPArguments;
        $this->___sRequestType           = $sRequestType;

    }
        /**
         * @param       string      $sAccessKey
         * @param       string      $sAssociateID
         * @return      array
         * @since       3.5.0
         */
        private function ___getAPIParametersFormatted( $sAccessKey, $sAssociateID ) {
            $_aAPIParameters = array(
                'AWSAccessKeyId'    => $sAccessKey,
            ) + $this->___aAPIParameters;
            if ( ! empty( $sAssociateID ) ) {
                $_aAPIParameters[ 'AssociateTag' ] = $sAssociateID;
            }
            return $_aAPIParameters;
        }

        /**
         * Generates a domain url from the given locale identifier.
         * @return      string
         */
        private function ___getDomain( $sLocale ) {
            $sLocale = ( $sKey = array_search( strtolower( $sLocale ), self::$___aTopLevelDomains ) ) 
                ? $sKey     // This allows the user to pass the top level domain as the locale. 
                : strtoupper( $sLocale );    // sanitize the locale key
            $_sTopLevelDomain = isset( self::$___aTopLevelDomains[ $sLocale ] )
                ? self::$___aTopLevelDomains[ $sLocale ]
                : 'com';    // when a transient gets cleared unexpectedly, it might occur 
            return "webservices.amazon.{$_sTopLevelDomain}";    
        }
    
    /**
     * Performs a simple request and checks if the authentication is verified.
     *
     * @remark      Used in the Authentication plugin seetting admin page.
     * @return      boolean|string  If succeeds, returns true; otherwise, the error message.
     * @since       unknown
     * @since       3.5.6           Changed the return value to return the error message if failed.
     */
    public function test() {

        $this->___sRequestType = 'api_test';
        $_aResponse = $this->request(
            array(
                'Operation'     => 'BrowseNodeLookup',
                'BrowseNodeId'  => '1000',    // the Books node 
            ),
            60 * 5     // 5 minutes
        );

        $_bsResponseErrorStatus = $this->___getResponseError( $_aResponse );
        if ( false === $_bsResponseErrorStatus ) {
            return true;    // Succeeded
        }
        // Failed: error message
        return $_bsResponseErrorStatus
            . ' ' . sprintf( __( 'Locale: %1$s', 'amazon-auto-links' ), $this->___sLocale )
            . ' ' . sprintf( __( 'Domain: %1$s', 'amazon-auto-links' ), $this->___sDomain );

    }
        /**
         * @param $aResponse
         *
         * @return boolean|string       If no error, false; otherwise, the error message.
         * @since       3.5.6
         */
        private function ___getResponseError( $aResponse ) {
            if ( empty( $aResponse ) ) {
                return __( 'The API response is empty.', 'amazon-auto-links' );
            }
            if ( isset( $aResponse[ 'Error' ] ) ) {
                $_sError = $this->getElement( $aResponse, array( 'Error', 'Code' ) )
                    . ': ' . $this->getElement( $aResponse, array( 'Error', 'Message' ) );
                return $_sError
                    ? $_sError
                    : __( 'The API returned an error but could not retrieve the error message.', 'amazon-auto-links' );
            }
            // No need to check the error of items as this is for checking the connectivity.
//            if ( isset( $aResponse[ 'Items' ][ 'Request' ][ 'Errors' ] ) ) {
//                return $aResponse[ 'Items' ][ 'Request' ][ 'Errors' ];
//            }
            return false;
        }
    /**
     * Preforms an API request in the background if no cache is available.
     * 
     * @since           2.0.4.1b
     * @remark          Accessed publicly.
     * @since           3.5.0       Removed the `$sLocale` parameter.
     * @return          boolean     True if scheduled; otherwise, false.
     */
    public function scheduleInBackground( array $aAPIParameters, $iCacheDuration=3600 ) {
        return $this->scheduleSingleWPCronTask(
            'aal_action_api_transient_renewal',
            array(
                array(
                    'parameters'     => $aAPIParameters,
                    'locale'         => $this->___sLocale,
                    'cache_duration' => $iCacheDuration,
                )
            ),
            time()
        );
    }
    
    /**
     * Performs an API request from the given request API parameters and returns the result as associative array.
     *
     * @remark      Accessed publicly
     * @since       unknown
     * @since       3.5.0       Removed the $sLocale, $sType, $iTimeout, $iRedirection, $sHeaders, $sUserAgent parameters.
     */
    public function request( array $aAPIParameters, $iCacheDuration=3600, $bForceCaching=false ) {

        $_oAPIRequestURIBuilder = new AmazonAutoLinks_ProductAdvertisingAPI___URIBuilder(
            $aAPIParameters + $this->___aAPIParameters,
            $this->___sSecretAccessKey,
            $this->___sDomain
        );
        // Inject additional arguments for the background cache renewal event.
        $_aHTTPArguments        = $this->___aHTTPArguments + array(
            'constructor_parameters' => $this->___aConstructorParameters,
            'api_parameters'         => $aAPIParameters,    
        );

        $_oAPIRequestCache      = new AmazonAutoLinks_ProductAdvertisingAPI___Cache(
            $_oAPIRequestURIBuilder->get(), // Request URI
            $_aHTTPArguments,
            $iCacheDuration,
            $bForceCaching,
            $this->___sRequestType
        );
        $_asResponse = $_oAPIRequestCache->get();

        // If an error occurs, an array will be returned.
        if ( ! is_string( $_asResponse ) ) {
            return $_asResponse;
        }

        // At this point, it is an XML response.
        $_sXMLResponse = $_asResponse;
        
        // It returns a string if it's not a valid XML content.
        $_boXML = $this->getXMLObject( $_sXMLResponse );
        if ( false === $_boXML ) {
            return array( 
                'Error' => 
                    array( 
                        'Message' => strip_tags( $_sXMLResponse ), 
                        'Code'    => 'Invalid XML' 
                    ) 
            );    
        }
        return $this->convertXMLtoArray( $_boXML );

    }

}