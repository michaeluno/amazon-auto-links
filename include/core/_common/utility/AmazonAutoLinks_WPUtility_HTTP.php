<?php
/**
 * Auto Amazon Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 */

/**
 * Provides utility methods regarding wp_remote_request().
 *
 * @package Auto Amazon Links
 * @since   4.3.5
 */
class AmazonAutoLinks_WPUtility_HTTP extends AmazonAutoLinks_WPUtility_Post {

    /**
     * Extracts the HTTP header from the given response.
     * @param WP_Error|array $aoResponse
     * @return array
     * @since 4.3.4
     */
    static public function getHeaderFromResponse( $aoResponse ) {
        if ( is_wp_error( $aoResponse ) ) {
            return array();
        }
        if ( ! isset( $aoResponse[ 'headers' ] ) ) {
            return array();
        }
        $_aoHeader   = $aoResponse[ 'headers' ];
        // Since WordPress 4.6.0 The return value has changed from array to Requests_Utility_CaseInsensitiveDictionary.
        $_aHeader    = ( $_aoHeader instanceof Requests_Utility_CaseInsensitiveDictionary )
            ? reset( $_aoHeader )
            : $_aoHeader;
        return self::getAsArray( $_aHeader );
    }

    /**
     * Converts the 'cookies' response element into an array for parsing.
     * @remrak Do not use this to set cookies for wp_remote_request() as the structure is different.
     * @param  array|WP_Error $aoResponse
     * @param  string         $sURL
     * @return array
     * @see    WP_Http_Cookie
     * @since  4.3.4
     */
    static public function getCookiesToParseFromResponse( $aoResponse, $sURL='' ) {
        $_aCookies = self::getRequestCookiesFromResponse( $aoResponse, $sURL );
        return self::getCookiesToParse( $_aCookies );
    }

    /**
     * @param  array|WP_Http_Cookie $aoCookies   The response 'cookies' element can be single `WP_Http_Cookie` object.
     * @return array
     * @since  4.3.4
     */
    static public function getCookiesToParse( $aoCookies ) {
        $_aCookies = $aoCookies instanceof WP_Http_Cookie
            ? array( $aoCookies )
            : self::getAsArray( $aoCookies );
        $_aToParse = array();
        foreach( $_aCookies as $_siNameOrIndex => $_soCookie ) {
            if ( ! ( $_soCookie instanceof WP_Http_Cookie ) ) {
                $_aToParse[] = array(
                    'name'  => $_siNameOrIndex,
                    'value' => $_soCookie,
                );
                continue;
            }
            $_aToParse[] = array(
                'name'  => $_soCookie->name,
                'value' => $_soCookie->value,
            ) + $_soCookie->get_attributes();
        }
        return $_aToParse;
    }

    /**
     * @param  array $aHaystackCookies
     * @param  string|integer $isIndexOrName
     * @param  string|WP_Http_Cookie $soSearchCookie A cookie value or a WP_Http_Cookie object.
     * @param  string $sURL
     * @return boolean
     * @since  4.3.4
     */
    static public function hasSameCookie( array $aHaystackCookies, $isIndexOrName, $soSearchCookie, $sURL='' ) {

        $_sDomain = $sURL ? '.' . parse_url( $sURL, PHP_URL_HOST ) : null;
        self::___setVariablesForHasSameCookie( $_sSearchName, $_sSearchPath, $_sSearchDomain, $isIndexOrName, $soSearchCookie, $_sDomain );
        foreach( $aHaystackCookies as $_isIndexOrName => $_aoCookie ) {
            self::___setVariablesForHasSameCookie( $_sThisName, $_sThisPath, $_sThisDomain, $_isIndexOrName, $_aoCookie, $_sDomain );
            // Check name
            if ( $_sSearchName !== $_sThisName ) {
                continue;
            }
            // Check path
            $_aPaths = in_array( $_sThisPath, array( null, '/' ), true )
                ? array( $_sThisPath, null )
                : array( $_sThisPath );
            if ( ! in_array( $_sSearchPath, $_aPaths, true ) ) {
                continue;
            }
            // Check domain
            $_sThisDomainWODot   = ltrim( $_sThisDomain, '.' ); // can be subdomain or main domain and either with a dot (.) prefixed or not.
            $_sThisDomainWithDot = '.' . $_sThisDomainWODot;
            $_sSubDomain         = self::getSubDomainFromHostName( $_sThisDomainWODot ); // possible sub-domain. If the given domain is already a sub domain,
            $_aDomains           = array( $_sThisDomain, $_sThisDomainWODot, $_sThisDomainWithDot, $_sSubDomain, ".{$_sSubDomain}" ); // @since 4.3.5 removed "www.{$_sSubDomain}", ".www.{$_sSubDomain}" as in a real browser, they seem to be handled differently.
            if ( ! in_array( $_sSearchDomain, $_aDomains, true ) ) {
                continue;
            }
            return true;
        }
        return false;
    }

        /**
         * Sets a value to the variables passed by reference.
         * @param &$sName
         * @param &$sPath
         * @param &$sDomain
         * @param $isIndexOrName
         * @param $soCookie
         * @param $sCookieDomain
         * @since 4.3.5
         */
        static private function ___setVariablesForHasSameCookie( &$sName, &$sPath, &$sDomain, $isIndexOrName, $soCookie, $sCookieDomain ) {
            $_bObject = $soCookie instanceof WP_Http_Cookie;
            $sName    = $_bObject ? $soCookie->name : $isIndexOrName;
            $sDomain  = $_bObject ? $soCookie->domain : $sCookieDomain;
            $sDomain  = $sDomain ? $sDomain : $sCookieDomain;
            $sPath    = $_bObject ? $soCookie->path : '/';
            $sPath    = $sPath ? $sPath : '/';
        }

    /**
     * @remark Amazon servers seem to parse cookies from last.
     * @param  array  $aPrecede
     * @param  array  $aSub
     * @param  string $sURL
     * @return WP_Http_Cookie[]
     * @since  4.3.4
     */
    static public function getCookiesMerged( array $aPrecede, array $aSub, $sURL='' ) {
        foreach( $aSub as $_isIndexOrName => $_soCookie ) {
            $_oCookie = self::getWPHTTPCookieFromCookieItem( $_soCookie, $_isIndexOrName, $sURL );
            if ( self::hasSameCookie( $aPrecede, $_isIndexOrName, $_oCookie, $sURL ) ) {
                continue;
            }
            $aPrecede[] = $_oCookie;
        }
        return $aPrecede;
    }

    /**
     * @param  string|WP_Http_Cookie $soCookie
     * @param  integer|string $isIndexOrName
     * @param  string $sURL Needed to calculate a domain.
     * @return WP_Http_Cookie
     * @since  4.3.5
     */
    static public function getWPHTTPCookieFromCookieItem( $soCookie, $isIndexOrName='', $sURL='' ) {
        $_sDomain = $sURL ? '.' . parse_url( $sURL, PHP_URL_HOST ) : null;
        if ( $soCookie instanceof WP_Http_Cookie ) {
            $soCookie->domain = $soCookie->domain ? $soCookie->domain : $_sDomain; /* @see WP_Http_Cookie::__construct() */
            $soCookie->path   = $soCookie->path ? $soCookie->path : '/';
            return $soCookie;
        }
        // Not passing a URL for the second parameter because it overrides the path and domain arguments.
        return new WP_Http_Cookie( array( 'name' => $isIndexOrName, 'value' => $soCookie, 'path' => '/', 'domain' => $_sDomain ) );
    }

    /**
     * Retrieves cookies to perform HTTP requests form a `wp_remote_request()` response.
     *
     * Check 'set-cookie' entries directly from a given response header, not referring to the 'cookies' response element.
     * This is to support multiple cookies with the same name. The 'cookies' response element does not support it.
     * For WordPress 4.6.0 or below, it's not supported.
     *
     * @param  WP_Error|array $aoResponse
     * @param  string $sURL Needs to generate a cookie domain.
     * @return WP_Http_Cookie[]
     * @since  4.3.4
     */
    static public function getRequestCookiesFromResponse( $aoResponse, $sURL='' ) {
        if ( is_wp_error( $aoResponse ) ) {
            return array();
        }
        if ( ! isset( $aoResponse[ 'cookies' ] ) ) {
            return array();
        }
        $_aResponseCookies  = $aoResponse[ 'cookies' ];
        if ( version_compare( $GLOBALS[ 'wp_version' ], '4.6.0', '<' ) ) {
            return $_aResponseCookies instanceof WP_Http_Cookie
                ? array( $_aResponseCookies )   // for a case of a single item
                : self::getAsArray( $_aResponseCookies );
        }

        // Sometimes response 'cookies' items and the header set-cookie items are different.
        // Especially when there are multiple items with the same name, the response 'cookies' only picks one of them.//

        $_aHeader            = self::getHeaderFromResponse( $aoResponse );
        $_aCookiesFromHeader = self::___getWPHTTPCookiesFromResponseHeader( $_aHeader, $sURL );

        /// There is a case that the set-cookie entry is empty but the response 'cookies' element has items.

        /// Sometimes the 'cookies' element is not an array.
        $_aResponseCookies = $aoResponse[ 'cookies' ] instanceof WP_Http_Cookie
            ? array( $aoResponse[ 'cookies' ] )
            : self::getAsArray( $aoResponse[ 'cookies' ] );
        return self::getCookiesMerged( $_aCookiesFromHeader, $_aResponseCookies, $sURL );

    }
        /**
         * Converts each set-cookie entry to a WP_Http_Cookie object.
         * @param  array  $aResponseHeader
         * @param  string $sURL
         * @return WP_Http_Cookie[]
         * @since  4.3.5
         */
        static private function ___getWPHTTPCookiesFromResponseHeader( array $aResponseHeader, $sURL='' ) {

            $_aRequestCookies = array();
            // There is a case that the 'set-cookie' element is not an array.
            $_asSetCookies    = self::getElement( $aResponseHeader, 'set-cookie', array() ); // there is a case that this is a string of a single entry
            $_aSetCookies     = is_scalar( $_asSetCookies )
                ? ( array ) preg_split( "/[\r\n]+/", $_asSetCookies, 0, PREG_SPLIT_NO_EMPTY )
                : $_asSetCookies;
            $_sSubDomain      = self::getSubDomain( $sURL );

            foreach( $_aSetCookies as $_iIndex => $_sSetCookieEntry ) {
                if ( ! $_sSetCookieEntry ) {
                    continue;
                }
                $_oWPHttpCookie = self::___getSetCookieEntryConvertedToWPHTTPCookie( $_sSetCookieEntry, $sURL );

                // Drop third party cookies if the $sURL parameter is given and the cookie has the domain attribute.
                $_bCheckDomain  = isset( $_oWPHttpCookie->domain ) && $_sSubDomain;
                if ( $_bCheckDomain && false === stripos( $_oWPHttpCookie->domain, $_sSubDomain ) ) {
                    continue;
                }
                $_aRequestCookies[] = $_oWPHttpCookie;
            }
            return $_aRequestCookies;

        }
        /**
         * Parses a given 'set-cookie' entry present in a HTTP header.
         * @param  string $sSetCookieEntry
         * @param  string $sURL
         * @return WP_Http_Cookie
         * @since  4.3.4
         */
        static private function ___getSetCookieEntryConvertedToWPHTTPCookie( $sSetCookieEntry, $sURL='' ) {
            $_aParts     = self::getStringIntoArray( $sSetCookieEntry, ';', '=' );
            $_aNameValue = array_shift( $_aParts ) + array( null, null ); // extract the first element
            $_aCookie    = array(
                'name'  => $_aNameValue[ 0 ],
                'value' => $_aNameValue[ 1 ],
            );
            foreach( $_aParts as $_aElement ) {
                if ( ! isset( $_aElement[ 0 ], $_aElement[ 1 ] ) ) {
                    continue;
                }
                $_aCookie[ strtolower( $_aElement[ 0 ] ) ] = $_aElement[ 1 ];
            }
            $_oCookie = new WP_Http_Cookie( $_aCookie );
            return self::getWPHTTPCookieFromCookieItem( $_oCookie, '', $sURL );
        }

    /**
     * @param WP_Error|array $aoResponse
     * @return array
     * @since 4.3.4
     * @deprecated This does not pick up cookies with duplicate names. Use `getRequestCookiesFromResponse()`.
     */
    static public function getCookiesFromResponse( $aoResponse ) {
        if ( is_wp_error( $aoResponse ) ) {
            return array();
        }
        if ( ! isset( $aoResponse[ 'cookies' ] ) ) {
            return array();
        }
        $_aResponseCookies = array();
        $_aCookieObjects   = $aoResponse[ 'cookies' ];
        foreach( $_aCookieObjects as $_isNameOrIndex => $_oCookie ) {
            // Below WP 4.6.0, the cookies elements are not object.
            if ( is_scalar( $_oCookie ) ) {
                $_aResponseCookies[ $_isNameOrIndex ] = $_oCookie;
                continue;
            }
            $_aResponseCookies[ $_oCookie->name ] = $_oCookie->value;
        }
        return $_aResponseCookies;
    }

}