<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2017 Michael Uno
 */
 
/**
 * Performs requests to the Product Advertising API.
 */
class AmazonAutoLinks_ProductAdvertisingAPI extends AmazonAutoLinks_ProductAdvertisingAPI_Base {

    /**
     * Add the prefix, https:, for a SSL server, otherwise, http:.
     * 
     * @see            http://docs.aws.amazon.com/AWSECommerceService/latest/DG/AnatomyOfaRESTRequest.html
     */
    protected static $aTopLevelDomains = array(
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
    );

    protected $sLocale = 'US';
    
    protected $sUserAgent = "Amazon Auto Links";
    
    public function __construct( $sLocale, $sAccessKey, $sSecretAccessKey, $sAssociateID='', $sVersion='2013-08-01' ) {  // 
         
        $this->sLocale          = $sLocale;
        $this->sDomain          = $this->getDomain( $sLocale );
        $this->sSecretAccessKey = $sSecretAccessKey;
                
        $this->aParams = array(
            'AWSAccessKeyId'    => $sAccessKey,
            'Version'           => $sVersion,   // 2011-08-01 etc
            'AssociateTag'      => empty ( $sAssociateID ) 
                ? self::$aMandatoryParameters[ 'AssociateTag' ] 
                : $sAssociateID,
        );                
                
        // The parent constructor has a cache renewal scheduling task so it must be called.
        parent::__construct();            
                
    }        
        /**
         * Generates a domain url from the given locale identifier.
         * @return      string
         */
        protected function getDomain( $sLocale ) {
            $sLocale = ( $sKey = array_search( strtolower( $sLocale ), self::$aTopLevelDomains ) ) 
                ? $sKey     // This allows the user to pass the top level domain as the locale. 
                : strtoupper( $sLocale );    // sanitize the locale key
            $_sTopLevelDomain = isset( self::$aTopLevelDomains[ $sLocale ] )
                ? self::$aTopLevelDomains[ $sLocale ]
                : 'com';    // when a transient gets cleared unexpectedly, it might occur 
            return "webservices.amazon.{$_sTopLevelDomain}";    
        }
    
    /**
     * Performs a simple request and checks if the authentication is velified.
     * 
     * @remark      Used in the Authentication plugin seetting admin page.
     * @return      boolean            If succeeds, returns true; otherwise, false.
     */
    public function test() {

        $_aResponse = $this->request(
            array(
                'Operation'     => 'BrowseNodeLookup',
                'BrowseNodeId'  => '1000',    // the Books node 
            ),
            $this->sLocale,
            60     // 1 minutes
        );            
        return ( boolean ) ( 
            ! isset( $_aResponse[ 'Error' ] ) 
            && ! empty( $_aResponse ) 
        );
        
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
                    'locale'     => $sLocale
                        ? $sLocale 
                        : $this->sLocale,
                )            
            );
        }
        return false;
    }
    
    /**
     * Performs an API request from the given request API parameters and returns the result as associative array.
     * 
     * @param            string            $sType            The return type, either 'array', 'json'
     */
    public function request( array $aParams, $sLocale='', $iCacheDuration=3600, $sType='array', $iTimeout=20, $iRedirection=5, $sHeaders='', $sUserAgent='' ) {
        
        // Arguments
        $aHTTPArgs = array(
            'timeout'       => $iTimeout,
            'redirection'   => $iRedirection,
            'sslverify'     => false,
            'headers'       => ! empty( $sHeaders ) 
                ? $sHeaders 
                : null,
            'user-agent'    => $sUserAgent
                ? $sUserAgent 
                : $this->sUserAgent,
        );
        $aHTTPArgs = array_filter( $aHTTPArgs );    // drop non value elements.

        // Request
        $vResponse = $this->requestWithCache( 
            $this->getSignedRequestURI( $aParams, $sLocale ), 
            $aHTTPArgs, 
            $aParams,
            $iCacheDuration,
            $sLocale 
                ? $sLocale 
                : $this->sLocale
        );
    
        // If an error occurs, 
        if ( ! is_string( $vResponse ) ) {    
            return $vResponse;
        }
            
        $_sXMLResponse = $vResponse;
        
        // It returns a string if it's not a valid XML content.
        $_boXML = AmazonAutoLinks_Utility::getXMLObject( $_sXMLResponse );
        if ( false === $_boXML ) {
            return array( 
                'Error' => 
                    array( 
                        'Message' => strip_tags( $_sXMLResponse ), 
                        'Code'    => 'Invalid XML' 
                    ) 
            );    
        }
                    
        // Return the result with the specified type.
        if ( 'xml' === $sType ) {
            return $_sXMLResponse;
        }
        else if ( 'array' === $sType ) {
            return AmazonAutoLinks_Utility::convertXMLtoArray( $_boXML );
        }
        else if ( 'json' == $sType ) { 
            return AmazonAutoLinks_Utility::convertXMLtoJSON( $_boXML );
        }
        
    }
            
    /**
     * The aws_signed_request() function, Modified by Michael Uno.
     * 
     * @see                  http://www.ulrichmierendorff.com/software/aws_hmac_signer.html
     * @copyright            Copyright (c) 2013-2017, Michael Uno
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
    public function getSignedRequestURI( $aParams=array(), $sLocale='' ) {
        
        // Build the query string.
        $_sQuery     = $this->_formatQueryPart( 
            $_aParams = $this->_formatQueryArguments( $aParams )
        );

        $_sURIPart   = '/onca/xml';
        $_sDomain    = empty( $sLocale ) 
            ? $this->sDomain 
            : $this->getDomain( $sLocale );
        $_sScheme    = is_ssl() 
            ? "https" 
            : "http";              
        $_sSignature = $this->_getAPIRequestSignature(
            $this->sSecretAccessKey,
            $_sQuery, 
            'GET', 
            $_sDomain, 
            $_sURIPart  
        );
        
        // Returns the request URI with the signature.
        return $_sScheme . "://" . $_sDomain . $_sURIPart 
            . '?' . $_sQuery . '&Signature=' . $_sSignature;
                
    }
        /**
         * @return      array
         */
        private function _formatQueryArguments( array $aArguments ) {
            
            // Required keys
            $aArguments = array(
                    'Timestamp' => gmdate( 'Y-m-d\TH:i:s\Z' ),
                )
                + array_filter( $aArguments )
                + $this->aParams
                + self::$aMandatoryParameters
            ;

            // Sort - required for matching with embedded query string in the signature.
            ksort( $aArguments );
            return $aArguments;
            
        }         
        /**
         * 
         * @since       3
         * @return      string      The formatted query part of the request URI.
         */
        private function _formatQueryPart( array $aArguments ) {
            
            $_aQuery = array();
            foreach ( $aArguments as $_sKey => $_sValue ) {
                $_sKey     = str_replace( '%7E', '~', rawurlencode( $_sKey ) );
                $_sValue   = str_replace( '%7E', '~', rawurlencode( $_sValue ) );
                $_aQuery[] = $_sKey . '=' . $_sValue;
            }
            return implode( '&', $_aQuery );                    
            
        }   
        /**
         * Creates a API request signature.
         * 
         * @see         http://docs.aws.amazon.com/AWSECommerceService/latest/DG/rest-signature.html
         * @since       3
         * @return      string
         */
        private function _getAPIRequestSignature( $sSecretAcesssKey, $sQuery, $sMethod, $sDomain, $sURIPath ) {

            // Create a signature 
            $_sSign = "GET" . "\n"
                . $sDomain . "\n"
                . $sURIPath . "\n"
                . $sQuery;                
            
            return str_replace( 
                '%7E', '~', 
                rawurlencode( 
                    base64_encode( 
                        hash_hmac( 
                            'sha256', 
                            $_sSign, 
                            $sSecretAcesssKey, 
                            true 
                        ) 
                    )
                ) 
            );
        }              
        
}