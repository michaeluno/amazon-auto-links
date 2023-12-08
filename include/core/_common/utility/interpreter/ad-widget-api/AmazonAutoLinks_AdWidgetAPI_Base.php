<?php
/**
 * Auto Amazon Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2022 Michael Uno
 */
 
/**
 * Performs Ad Widget API Search requests.
 *
 * @since       4.6.11
 */
class AmazonAutoLinks_AdWidgetAPI_Base extends AmazonAutoLinks_PluginUtility {

    /**
     * @var AmazonAutoLinks_Locale_Base
     */
    public $oLocale;

    /**
     * @var int
     */
    public $iCacheDuration = 86400;

    /**
     * @var array
     * @since 4.6.11
     */
    public $aHTTPArguments = array();

    /**
     * Sets up properties and hooks.
     */
    public function __construct( $sLocale, $iCacheDuration=86400, array $aHTTPArguments=array() ) {
        $this->oLocale        = new AmazonAutoLinks_Locale( $sLocale );
        $this->iCacheDuration = $iCacheDuration;
        $this->aHTTPArguments = $aHTTPArguments;
    }

    /**
     * @param  string $sEndpoint
     * @return string
     * @since  4.6.9
     */
    public function getResponse( $sEndpoint ) {

        $_aArguments = $this->aHTTPArguments + array(
            'user-agent' => 'WordPress/' . $GLOBALS[ 'wp_version' ],
            'timeout'    => 29,
        );

        // [5.3.6+] Sometimes, the API returns an empty response with the status code 200. If that happens, retry
        $_sHTTPBody = '';
        $_iAttempts = 0;
        $_iMax      = 30;
        While ( $_iAttempts <= $_iMax ) {
            $_oHTTP      = new AmazonAutoLinks_HTTPClient( $sEndpoint, $this->iCacheDuration, $_aArguments, 'ad_widget_api' );
            $_sHTTPBody  = $_oHTTP->getBody();
            if ( 200 !== $_oHTTP->getStatusCode() || '' !== $_sHTTPBody ) {
                break;
            }
            $_iAttempts++;
            $_aArguments[ 'renew_cache' ] = true;
            usleep( 500 );
        }

        return $_sHTTPBody;

    }

    /**
     * @remark This might need to be moved a utility class.
     * @param  string $sJSONP
     * @return array|null|false <strong>NULL</strong> is returned if the <i>json</i> cannot be decoded or if the encoded
     * data is deeper than the recursion limit.
     * @since  4.6.9
     */
    static public function getJSONFromJSONP( $sJSONP ) {

        // Strip the enclosing JS function
        // @see https://gist.github.com/umutakturk/3804958
        // Not using preg_replace() to cover malformed JSONP enclosed in HTML tags, which occurs with Web Page Dumper.
        preg_match( "/[^(]*\((.*)\)/", $sJSONP, $_aMatches );
        $_sJSONJS   = isset( $_aMatches[ 1 ] ) ? $_aMatches[ 1 ] : '';

        // Sanitize the text which prevents the below code from converting it
        $_sJSONJS   = self::___getJSONPSanitized( $_sJSONJS );

        // The JSON syntax is still JS based. Enclose keys with double quotes
        // @see https://stackoverflow.com/a/40326949
        // (?<!\\\)" <-- this avoids matching \" which is escaped double quotes
        $_sJSONJS   = preg_replace('/("(.*?)(?<!\\\)"|(\w+))(\s*:\s*(".*?(?<!\\\)"|.))/s', '"$2$3"$4', $_sJSONJS );
        $_sJSON     = preg_replace('/("(.*?)(?<!\\\)"|(\w+))(\s*:\s*)\+?(0+(?=\d))?(".*?(?<!\\\)"|.)/s', '"$2$3"$4$6', $_sJSONJS );
        return json_decode( $_sJSON, true );

    }

        /**
         * @param  $sRawJSONP
         * @return string
         * @since  5.3.6
         */
        static private function ___getJSONPSanitized( $sRawJSONP ) {

            // When the endpoint omits the InstanceId parameter, JavaScript code is included in the JSON element in the response
            // e.g. ..." } ], MarketPlace: "DE", InstanceId: "$toolsFactory.first($params.get("InstanceId"))"})
            return str_replace(
                ', InstanceId: "$toolsFactory.first($params.get("InstanceId"))"',
                '',
                $sRawJSONP
            );

        }

}