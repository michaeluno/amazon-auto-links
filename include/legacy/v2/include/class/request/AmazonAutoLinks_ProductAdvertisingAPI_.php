<?php
/**
 * Performs requests to the Product Advertising API.
 * 
 * @package         Amazon Auto Links
 * @copyright       Copyright (c) 2013, Michael Uno
 * @license         http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

class AmazonAutoLinks_ProductAdvertisingAPI_ extends AmazonAutoLinks_APIRequestTransient {

    /**
     * Add the prefix, https:, for a SSL server, otherwise, http:.
     * 
     * @see            http://docs.aws.amazon.com/AWSECommerceService/latest/DG/AnatomyOfaRESTRequest.html
     */
    protected static $arrTopLevelDomains = array(
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
        // The followings locales are not supported by Amazon Product Advertising API.
        // 'BR'    => '',
        // 'MX'    => '',        
    );

    protected static $arrMandatoryParameters = array(
        'Service'            => 'AWSECommerceService',
        'AssociateTag'        => 'amazon-auto-links-20',        // the key must be provided; otherwise, API returns an error.
    );
    
    protected $strLocale = 'US';
    
    protected $strUserAgent = "Amazon Auto Links";
    
    function __construct( $strLocale, $strAccessKey, $strSecretAccessKey, $strAssociateID='', $strVersion='2011-08-01' ) {
        
        $this->strScheme = is_ssl() ? "https" : "http";        
        
        $this->strLocale = $strLocale;
        $this->strDomain = $this->getDomain( $strLocale );
        $this->strSecretAccessKey = $strSecretAccessKey;
                
        $this->arrParams = array(
            'AWSAccessKeyId'    => $strAccessKey,
            'Version'            => $strVersion,
            'AssociateTag'        => empty ( $strAssociateID ) ? self::$arrMandatoryParameters['AssociateTag'] : $strAssociateID,
        );                
                
        // The parent constructor has a cache renewal scheduling task so it must be called.
        parent::__construct();            
                
    }
    
    /**
     * Generates a domain url from the given locale identifier.
     */
    protected function getDomain( $strLocale ) {
        $strLocale = ( $strKey = array_search( strtolower( $strLocale ), self::$arrTopLevelDomains ) ) 
            ? $strKey     // This allows the user to pass the top level domain as the locale. 
            : strtoupper( $strLocale );    // sanitize the locale key
        $strTopLevelDomain = isset( self::$arrTopLevelDomains[ $strLocale ] )
            ? self::$arrTopLevelDomains[ $strLocale ]
            : 'US';    // when a transient gets cleared unexpectedly, it might occur 
        return "webservices.amazon.{$strTopLevelDomain}";    
    }
    
    /**
     * Perfomrs a simple request and checks if the authentication is velified.
     * 
     * @return            boolean            If suceeds, returns true; otherwise, false.
     */
    public function test() {
        
        $arrResponse = $this->request(
            array(
                'Operation' => 'BrowseNodeLookup',
                'BrowseNodeId' => '1000',    // the Books node 
            ),
            'US',    // or 'com' would work
            null     // do not use cache
        );        
    
        return isset( $arrResponse['Error'] ) || empty( $arrResponse )
            ? false
            : true;
        
    }
    
    /**
     * Preforms an API request in the background if no cache is available.
     * 
     * @since            2.0.4.1b
     */
    public function scheduleInBackground( array $aParams, $sLocale='' ) {

        if ( ! $this->isCached( $aParams ) ) {
            return $this->_scheduleCacheRenewal(
                array(
                    'parameters' => $aParams,
                    'locale' => $sLocale ? $sLocale : $this->strLocale,
                )            
            );
        }
        return false;
    }
    
    /**
     * Performs an API request from the given request API parameters and returns the result as associative array.
     * 
     * @param            string            $strType            The return type, either 'array', 'json'
     */
    public function request( array $arrParams, $strLocale='', $intCacheDuration=3600, $strType='array', $intTimeout=20, $intRedirection=5, $strHeaders='', $strUserAgent='' ) {
        
        // Arguments
        $arrHTTPArgs = array(
            'timeout' => $intTimeout,
            'redirection' => $intRedirection,
            'sslverify' => $this->strScheme == 'https' ? false : true,    // this is missing in WP_SimplePie_File
            'headers' => ! empty( $strHeaders ) ? $strHeaders : null,
            'user-agent' => $strUserAgent ? $strUserAgent : $this->strUserAgent,
        );
        $arrHTTPArgs = array_filter( $arrHTTPArgs );    // drop non value elements.
        
        // Request
        $vResponse = $this->requestWithCache( 
            $this->getSignedRequestURI( $arrParams, $strLocale ), 
            $arrHTTPArgs, 
            $arrParams,
            $intCacheDuration,
            $strLocale ? $strLocale : $this->strLocale
        );
    
        // If an error occurs, 
        if ( ! is_string( $vResponse ) ) {    
            return $vResponse;
        }
            
        $_sXMLResponse = $vResponse;
        
        // It returns a string if it's not a valid XML content.
        $_osXML = AmazonAutoLinks_Utilities::getXMLObject( $_sXMLResponse );
        if ( is_string( $_osXML ) ) {
            return array( 'Error' => array( 'Message' => $_osXML, 'Code' => 'Invalid XML' ) );    // compose an error array. 
        }
                    
        // Return the result with the specified type.
        if ( $strType == 'xml' ) return $_sXMLResponse;
        if ( $strType == 'array' ) return AmazonAutoLinks_Utilities::convertXMLtoArray( $_osXML );
        if ( $strType == 'json' ) return AmazonAutoLinks_Utilities::convertXMLtoJSON( $_osXML );
        
    }
    
    /**
     * 
     * 
     * @remark            Used by the parent class.
     * @return            string|array            Returns the retrieved HTML body string, and an error array on failure.
     */
    public function requestSigned( $strRequestURI, $arrHTTPArgs=array() ) {
        
        // Arguments
        $arrHTTPArgs = $arrHTTPArgs + array(
            'timeout' => 20,
            'redirection' => 5,
            'sslverify' => $this->strScheme == 'https' ? false : true,    // this is missing in WP_SimplePie_File
            'headers' => null,
            'user-agent' => $this->strUserAgent,
        );
        $arrHTTPArgs = array_filter( $arrHTTPArgs );    // drop non value elements.        
        
        $vResponse = wp_remote_get( $strRequestURI, $arrHTTPArgs );
        // $vResponse = wp_safe_remote_request( $strRequestURI, $arrHTTPArgs );    // not supported in WP version 3.5.x or below.

        if ( is_wp_error( $vResponse ) ) {
            return array( 'Error' => array(
                    'Code' => $vResponse->get_error_code(),
                    'Message' => 'WP HTTP Error: ' . $vResponse->get_error_message(),
                ) 
            );
        }

        return wp_remote_retrieve_body( $vResponse );    // returns the xml document.
                
    }
        
    /**
     * The aws_signed_request() function, Modified by Michael Uno.
     * 
     * @see                    http://www.ulrichmierendorff.com/software/aws_hmac_signer.html
     * @copyright            Copyright (c) 2013, Michael Uno
     * @copyright            Copyright (c) 2009-2012 Ulrich Mierendorff
    
    Copyright (c) 2009-2012 Ulrich Mierendorff

    Permission is hereby granted, free of charge, to any person obtaining a
    copy of this software and associated documentation files (the "Software"),
    to deal in the Software without restriction, including without limitation
    the rights to use, copy, modify, merge, publish, distribute, sublicense,
    and/or sell copies of the Software, and to permit persons to whom the
    Software is furnished to do so, subject to the following conditions:

    The above copyright notice and this permission notice shall be included in
    all copies or substantial portions of the Software.

    THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
    IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
    FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL
    THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
    LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
    FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
    DEALINGS IN THE SOFTWARE.
    */
    public function getSignedRequestURI( $arrParams=array(), $strLocale='' ) {

        // some paramters
        $strMethod = 'GET';
        $strURI = '/onca/xml';
        $strDomain = empty( $strLocale ) ? $this->strDomain : $this->getDomain( $strLocale );

        // Compose the parameter array.
        $arrParams['Timestamp'] = gmdate('Y-m-d\TH:i:s\Z');        // GMT timestamp
        $arrParams = array_filter( $arrParams + $this->arrParams );        // Omits empty values.
        $arrParams = $arrParams + self::$arrMandatoryParameters;    // Append mandatory elements.
        ksort( $arrParams );
        
        // Build the query string.
        $arrQuery = array();
        foreach ( $arrParams as $param => $value ) {
            $param = str_replace( '%7E', '~', rawurlencode( $param ) );
            $value = str_replace( '%7E', '~', rawurlencode( $value ) );
            $arrQuery[] = $param . '=' . $value;
        }
        $strQuery = implode( '&', $arrQuery );
                
        // Create a signature - see http://docs.aws.amazon.com/AWSECommerceService/latest/DG/rest-signature.html
        $strSign = $strMethod . "\n"
            . $strDomain . "\n"
            . $strURI . "\n"
            . $strQuery;
        
        $strSignature = base64_encode( hash_hmac( 'sha256', $strSign, $this->strSecretAccessKey, TRUE ) );
        $strSignature = str_replace( '%7E', '~', rawurlencode( $strSignature ) );
        
        // Returns the request URI with the signature.
        return $this->strScheme . "://" . $strDomain . $strURI . '?' . $strQuery . '&Signature=' . $strSignature;
                
    }
        
}