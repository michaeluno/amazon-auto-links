<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright(c) 2013-2020 Michael Uno
 */


/**
 * Generates HTTP headers for PA-API requests.
 * @since   3.9.0
 */
class AmazonAutoLinks_PAAPI50___RequestHeaderGenerator {

    private $___sAccessKey      = null;
    private $___sSecretKey      = null;
    private $___sPath           = '/paapi5/searchitems';    // the url part that follows after the domain
    private $___sRegionName     = null;
    private $___sServiceName    = 'ProductAdvertisingAPI';
    private $___sHTTPMethod     = 'POST';
    private $___aHeaderItems    = array();
    private $___sHMACAlgorithm  = "AWS4-HMAC-SHA256";
    private $___sPAAPI5Request  = "aws4_request";
    private $___sSignedHeader   = null;
    private $___sAmazonDate     = null;
    private $___sCurrentDate    = null;
    private $___sLocale         = 'US';
    private $___sHost           = '';
    private $___sOperation      = '';
    private $___aPayload        = array();
    private $___sPayload        = "";

    public function __construct( $sAccessKey, $sSecretKey, $sLocale, array $aPayload=array(), $sHTTPMethod='POST' ) {

        $this->___sAccessKey   = $sAccessKey;
        $this->___sSecretKey   = $sSecretKey;
        $this->___sLocale      = $sLocale;
        $this->setLocale( $this->___sLocale );
        $this->___sAmazonDate  = $this->___getTimeStamp();
        $this->___sCurrentDate = $this->___getDate();
        $this->setPayload( $aPayload ); // this also sets the operation property
        $this->___sHTTPMethod = $sHTTPMethod;

    }
        private function ___getTimeStamp() {
            return gmdate( "Ymd\THis\Z" );
        }

    /**
     * Determines the locale of the request.
     *
     * This also sets the region and the host.
     *
     * @param   string  $sLocale    Accepts 'AU', 'BR', 'CA', 'FR', 'DE', 'IN', 'IT', 'JP', 'MX', 'ES', 'TR', 'AE', 'UK', 'US'
     */
    public function setLocale( $sLocale ) {
        $_oLocale = new AmazonAutoLinks_PAAPI50___Locales;
        $_sRegion = isset( $_oLocale->aRegionNames[ $sLocale ] )
            ? $_oLocale->aRegionNames[ $sLocale ]
            : $_oLocale->aRegionNames[ 'US' ];
        $this->setRegionName( $_sRegion );
        $this->___sHost = $this->___getHostByLocale( $sLocale );
    }
        private function ___getHostByLocale( $sLocale ) {
            $_oLocale = new AmazonAutoLinks_PAAPI50___Locales;
            return isset( $_oLocale->aHosts[ $sLocale ] )
                ? $_oLocale->aHosts[ $sLocale ]
                : $_oLocale->aHosts[ 'US' ];
        }

    public function setPath( $sPath ) {
        $this->___sPath = $sPath;
    }

    public function setServiceName( $sServiceName ) {
        $this->___sServiceName = $sServiceName;
    }

    public function setRegionName( $sRegionName ) {
        $this->___sRegionName = $sRegionName;
    }

    public function setPayload( array $aPayload ) {
        foreach( $aPayload as $_k => $_v ) {
            if ( null === $_v ) {
                unset( $aPayload[ $_k ] );
            }
        }
        $aPayload = $aPayload + array(
            'Marketplace' => $this->___getMarketplaceByLocale( $this->___sLocale ),
        );
        $this->___aPayload = $aPayload;
        $this->___sPayload = json_encode( $aPayload );
        // if the `Operation` argument is set, set the Operation property
        $this->___sOperation = isset( $aPayload[ 'Operation' ] )
            ? $aPayload[ 'Operation' ]
            : '';

    }
        private function ___getMarketplaceByLocale( $sLocale ) {
            $_oLocale = new AmazonAutoLinks_PAAPI50___Locales;
            return isset( $_oLocale->aMarketPlaces[ $sLocale ] )
                ? $_oLocale->aMarketPlaces[ $sLocale ]
                : $_oLocale->aMarketPlaces[ 'US' ];
        }
    public function setRequestMethod( $sMethod ) {
        $this->___sHTTPMethod = $sMethod;
    }

    public function addHeader( $sHeaderName, $sHeaderValue ) {
        $this->___aHeaderItems[ $sHeaderName ] = $sHeaderValue;
    }

    /**
     * Returns payload parameters as JSON.
     * @return  string
     */
    public function getPayload() {
        return $this->___sPayload;
    }

    public function getRequestURI() {
        return 'https://'. $this->___sHost . $this->___sPath;
    }

    /**
     * @return string
     */
    public function getHeadersAsString() {
        $_sHeaderString = "";
        foreach( $this->getHeaders() as $_sKey => $_sValue ) {
            $_sHeaderString .= $_sKey . ': ' . $_sValue . "\r\n";
        }
        return $_sHeaderString;
    }

    /**
     * Generates an HTTP header.
     * @return array
     */
    public function getHeaders() {

        $this->___aHeaderItems[ 'content-encoding' ]  = 'amz-1.0';
        $this->___aHeaderItems[ 'content-type' ]      = 'application/json; charset=utf-8';
        $this->___aHeaderItems[ 'host' ]              = $this->___sHost;
        $this->___aHeaderItems[ 'x-amz-target' ]      = 'com.amazon.paapi5.v1.ProductAdvertisingAPIv1.' . $this->___sOperation;

        $this->___aHeaderItems[ 'x-amz-date' ]        = $this->___sAmazonDate;
        ksort( $this->___aHeaderItems );

        // Create a canonical request
        $_sCanonicalURL = $this->___getCanonicalRequestPrepared();

        // Create the string to sign
        $_sStringToSign = $this->___getStringToSignPrepared( $_sCanonicalURL );

        // Calculate the signature
        $_sSignature = $this->___getSignatureCalculated( $_sStringToSign );

        // Calculate authorization header
        if ( $_sSignature ) {
            $this->___aHeaderItems[ 'Authorization' ] = $this->___getAuthorizationString( $_sSignature );
            return $this->___aHeaderItems;
        }
        return array();

    }
        /**
         * @remark  Payload must be set before calling this method
         * @return  string
         */
        private function ___getCanonicalRequestPrepared() {
            $_sCanonicalURL   = "";
            $_sCanonicalURL  .= $this->___sHTTPMethod . "\n";
            $_sCanonicalURL  .= $this->___sPath . "\n" . "\n";
            $_sSignedHeaders  = '';
            foreach ( $this->___aHeaderItems as $_sKey => $_sValue ) {
                $_sSignedHeaders .= $_sKey . ";";
                $_sCanonicalURL  .= $_sKey . ":" . $_sValue . "\n";
            }
            $_sCanonicalURL           .= "\n";
            $this->___sSignedHeader    = substr( $_sSignedHeaders, 0, - 1 );
            $_sCanonicalURL           .= $this->___sSignedHeader . "\n";
            $_sCanonicalURL           .= $this->___getHexGenerated( $this->___sPayload );
            return $_sCanonicalURL;
        }
        private function ___getStringToSignPrepared( $sCanonicalURL ) {
            $_sToSign  = '';
            $_sToSign .= $this->___sHMACAlgorithm . "\n";
            $_sToSign .= $this->___sAmazonDate . "\n";
            $_sToSign .= $this->___sCurrentDate . "/"
                . $this->___sRegionName . "/"
                . $this->___sServiceName . "/"
                . $this->___sPAAPI5Request . "\n";
            $_sToSign .= $this->___getHexGenerated( $sCanonicalURL );
            return $_sToSign;
        }
        /**
         * @param $sStringToSign
         *
         * @return string
         */
        private function ___getSignatureCalculated( $sStringToSign ) {
            $_sSignatureKey = $this->___getSignatureKey(
                $this->___sSecretKey,
                $this->___sCurrentDate,
                $this->___sRegionName,
                $this->___sServiceName
            );
            $_sSignature      = hash_hmac( "sha256", $sStringToSign, $_sSignatureKey, true );
            $_sSignatureHex   = strtolower( bin2hex( $_sSignature ) );
            return $_sSignatureHex;
        }
            private function ___getSignatureKey( $sKey, $sDate, $sRegionName, $sServiceName ) {
                $_sSecret    = "AWS4" . $sKey;
                $_sDate      = hash_hmac( "sha256", $sDate, $_sSecret, true );
                $_sRegion    = hash_hmac( "sha256", $sRegionName, $_sDate, true );
                $_sService   = hash_hmac( "sha256", $sServiceName, $_sRegion, true );
                $_sSigning   = hash_hmac( "sha256", $this->___sPAAPI5Request, $_sService, true );
                return $_sSigning;
            }
        private function ___getAuthorizationString( $sSignature ) {
            return $this->___sHMACAlgorithm . " "
                   . "Credential=" . $this->___sAccessKey . "/"
                   . $this->___getDate() . "/"
                   . $this->___sRegionName . "/"
                   . $this->___sServiceName . "/"
                   . $this->___sPAAPI5Request . ","
                   . "SignedHeaders=" . $this->___sSignedHeader . ","
                   . "Signature=" . $sSignature;
        }

    /* Common private methods */

    private function ___getHexGenerated( $sData ) {
        return strtolower( bin2hex( hash( "sha256", $sData, true ) ) );
    }

    private function ___getDate() {
        return gmdate( "Ymd" );
    }

}
