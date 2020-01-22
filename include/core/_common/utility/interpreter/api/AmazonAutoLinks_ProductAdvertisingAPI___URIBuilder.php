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
 * Provides methods to construct Amazon Product Advertising API request URIs.
 * @since       3.5.0
 * @deprecated  3.9.0   PA-API 4.0 was deprecated as of Oct 31st, 2019.
 */
class AmazonAutoLinks_ProductAdvertisingAPI___URIBuilder {

    private $___aArguments       = array();
    private $___sSecretAccessKey = '';
    private $___sDomain          = '';

    /**
     * Sets up properties.
     *
     * @param array     $aArguments
     * @param string $sLocale
     */
    public function __construct( array $aArguments, $sSecretAccessKey, $sDomain ) {
        $this->___aArguments        = $aArguments;
        $this->___sSecretAccessKey  = $sSecretAccessKey;
        $this->___sDomain           = $sDomain;
    }

    /**
     *
     * @since       3.5.0
     * @return      string
     */
    public function get() {
        // 3.6.7+
        return apply_filters(
            'aal_filter_api_request_uri',
            $this->___getSignedRequestURI( $this->___aArguments ),   // the subject URI
            $this->___aArguments
        );
    }

        /**
         * The aws_signed_request() function, Modified by Michael Uno.
         *
         * @see                  http://www.ulrichmierendorff.com/software/aws_hmac_signer.html
         * @copyright            Copyright (c) 2013-2020, Michael Uno
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
        private function ___getSignedRequestURI( array $aParams=array() ) {

            $_sQuery     = $this->___getQueryPartFormatted( $this->___getQueryArgumentsFormatted( $aParams ) );
            $_sURIPart   = '/onca/xml';
            $_sScheme    = is_ssl()
                ? "https"
                : "http";
            $_sSignature = $this->___getAPIRequestSignature(
                $this->___sSecretAccessKey,
                $_sQuery,
                $this->___sDomain,
                $_sURIPart
            );

            // Returns the request URI with the signature.
            return $_sScheme . "://" . $this->___sDomain . $_sURIPart
                . '?' . $_sQuery . '&Signature=' . $_sSignature;

        }
            /**
             * @since       3
             * @since       3.5.0       Moved from `AmazonAutoLinks_ProductAdvertisingAPI`.
             * @return      array
             */
            private function ___getQueryArgumentsFormatted( array $aArguments ) {

                // Required keys
                $aArguments = array(
                        'Timestamp' => gmdate( 'Y-m-d\TH:i:s\Z' ),
                    )
                    + array_filter( $aArguments );

                // Sort - required for matching with embedded query string in the signature.
                ksort( $aArguments );
                return $aArguments;

            }
            /**
             * Construct a query part of a URL query string.
             * @since       3
             * @since       3.5.0       Moved from `AmazonAutoLinks_ProductAdvertisingAPI`.
             * @return      string      The formatted query part of the request URI.
             */
            private function ___getQueryPartFormatted( array $aArguments ) {

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
             * @since       3.5.0       Moved from `AmazonAutoLinks_ProductAdvertisingAPI`.
             * @return      string
             */
            private function ___getAPIRequestSignature( $sSecretAccessKey, $sQuery, $sDomain, $sURIPath ) {

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
                                $sSecretAccessKey,
                                true
                            )
                        )
                    )
                );
            }

}