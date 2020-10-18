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
 * Provides utility methods that uses WordPress built-in functions.
 *
 * @package     Amazon Auto Links
 * @since       2
 * @since       3       Changed the name from `AmazonAutoLinks_WPUtilities`.
 */
class AmazonAutoLinks_WPUtility extends AmazonAutoLinks_WPUtility_Post {

    /**
     * Extracts the 'set-cookie' element from header.
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
     * @param $aoResponse
     * @return array
     * @see WP_Http_Cookie
     * @since 4.3.4
     */
    static public function getCookiesToParseFromResponse( $aoResponse ) {
        $_aCookies = self::getRequestCookiesFromResponse( $aoResponse );
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
            $_oCookie = self::___getWPHTTPCookieFromCookieItem( $_soCookie, $_isIndexOrName, $sURL );
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
        static private function ___getWPHTTPCookieFromCookieItem( $soCookie, $isIndexOrName='', $sURL='' ) {
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
            $_aSetCookies     = self::getElementAsArray( $aResponseHeader, 'set-cookie' ); // there is a case that this is a string of a single entry
            foreach( $_aSetCookies as $_iIndex => $_sSetCookieEntry ) {
                if ( ! $_sSetCookieEntry ) {
                    continue;
                }
                $_aRequestCookies[] = self::___getSetCookieEntryConvertedToWPHTTPCookie( $_sSetCookieEntry, $sURL );
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
                $_aCookie[ $_aElement[ 0 ] ] = $_aElement[ 1 ];
            }
            $_oCookie = new WP_Http_Cookie( $_aCookie );
            return self::___getWPHTTPCookieFromCookieItem( $_oCookie, '', $sURL );
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

    /**
     * Schedules a WP Cron single event.
     * @param  string  $sActionName
     * @param  array   $aArguments
     * @param  integer $iTime
     * @return boolean True if scheduled, false otherwise.
     * @since  3.5.0
     */
    static public function scheduleSingleWPCronTask( $sActionName, array $aArguments=array(), $iTime=0 ) {

        if ( wp_next_scheduled( $sActionName, $aArguments ) ) {
            return false;
        }
        $_iTime = $iTime ? $iTime : time();
        return false !== wp_schedule_single_event( $_iTime, $sActionName, $aArguments );

    }

    /**
     * Returns the readable date-time string.
     * @param integer     $iTimeStamp
     * @param null|string $sDateTimeFormat
     * @param boolean     $bAdjustGMT
     * @return string
     */
    static public function getSiteReadableDate( $iTimeStamp, $sDateTimeFormat=null, $bAdjustGMT=false ) {
                
        static $_iOffsetSeconds, $_sDateFormat, $_sTimeFormat;
        $_iOffsetSeconds = $_iOffsetSeconds 
            ? $_iOffsetSeconds 
            : get_option( 'gmt_offset' ) * HOUR_IN_SECONDS;
        $_sDateFormat = $_sDateFormat
            ? $_sDateFormat
            : get_option( 'date_format' );
        $_sTimeFormat = $_sTimeFormat
            ? $_sTimeFormat
            : get_option( 'time_format' );    
        $sDateTimeFormat = $sDateTimeFormat
            ? $sDateTimeFormat
            : $_sDateFormat . ' ' . $_sTimeFormat;
        
        if ( ! $iTimeStamp ) {
            return 'n/a';
        }
        $iTimeStamp = $bAdjustGMT ? $iTimeStamp + $_iOffsetSeconds : $iTimeStamp;
        return date_i18n( $sDateTimeFormat, $iTimeStamp );
            
    }       
    
    /**
     * Finds scheduled cron tasks by the given action name.
     *  
     * @since       3
     * @param       string  $sActionHookName
     * @return      array
     */
    static public function getScheduledCronTasksByActionName( $sActionHookName ) {
        
        $_aTheTasks = array();        
        $_aTasks    = ( array ) _get_cron_array();
        foreach ( $_aTasks as $_iTimeStamp => $_aScheduledActionHooks ) {            
            foreach ( ( array ) $_aScheduledActionHooks as $_sScheduledActionHookName => $_aArgs ) {
                if ( ! in_array( $_sScheduledActionHookName, array( $sActionHookName ) ) ) {
                    continue;
                }
                $_aTheTasks[ $_iTimeStamp ][ $_sScheduledActionHookName ] = $_aArgs;
            }            
        }
        return $_aTheTasks;
                
    }    
    
    /**
     * Stores whether the server installs the mbstring extension or not.
     * @since       3
     */
    static protected $_bMBStringInstalled;
    
    /**
     * Converts a given string into a specified character set.
     * @since       3
     * @return      string      The converted string.
     * @see         http://php.net/manual/en/mbstring.supported-encodings.php
     * @param       string          $sText                      The subject text string.
     * @param       string          $sCharSetTo                 The character set to convert to.
     * @param       string|boolean  $bsCharSetFrom              The character set to convert from. If a character set is not specified, it will be auto-detected.
     * @param       boolean         $bConvertToHTMLEntities     Whether or not the string should be converted to HTML entities.
     */
    static public function convertCharacterEncoding( $sText, $sCharSetTo='', $bsCharSetFrom=true, $bConvertToHTMLEntities=false ) {
        
        if ( ! function_exists( 'mb_detect_encoding' ) ) {
            return $sText;
        }
        if ( ! is_string( $sText ) ) {
            return $sText;
        }
        
        $sCharSetTo = $sCharSetTo
            ? $sCharSetTo
            : get_bloginfo( 'charset' );
        
        $_bsDetectedEncoding = $bsCharSetFrom && is_string( $bsCharSetFrom )
            ? $bsCharSetFrom
            : self::getDetectedCharacterSet(
                $sText,
                $bsCharSetFrom              
            );
        $sText = false !== $_bsDetectedEncoding
            ? mb_convert_encoding( 
                $sText, 
                $sCharSetTo, // encode to
                $_bsDetectedEncoding // from
            )
            : mb_convert_encoding( 
                $sText, 
                $sCharSetTo // encode to      
                // auto-detect
            );
        
        if ( $bConvertToHTMLEntities ) {            
            $sText  = mb_convert_encoding( 
                $sText, 
                'HTML-ENTITIES', // to
                $sCharSetTo  // from
            );
        }
        
        return $sText;
        
    }

    /**
     *
     * @param       string          $sText
     * @param       string          $sCandidateCharSet
     * @return      boolean|string  False when not found. Otherwise, the found encoding character set.
     */
    static public function getDetectedCharacterSet( $sText, $sCandidateCharSet='' ) {
        
        $_aEncodingDetectOrder = array(
            get_bloginfo( 'charset' ),
            "auto",
        );
        if ( is_string( $sCandidateCharSet ) && $sCandidateCharSet ) {
            array_unshift( $_aEncodingDetectOrder, $sCandidateCharSet );
        }        

        // Returns false or the found encoding character set
        return mb_detect_encoding( 
            $sText, // subject string
            $_aEncodingDetectOrder, // candidates
            true // strict detection - true/false
        );
        
    }

    /**
     * Redirect the user to a post definition edit page.
     * @sine   3
     * @param  integer $iPostID
     * @param  string  $sPostType
     * @param  array   $aGET
     * @return void
     */
    static public function goToPostDefinitionPage( $iPostID, $sPostType, array $aGET=array() ) {
        exit( 
            wp_redirect( 
                self::getPostDefinitionEditPageURL( 
                    $iPostID, 
                    $sPostType, 
                    $aGET 
                )
            ) 
        );        
    }

    /**
     * Returns a url of a post definition edit page.
     * @param  integer $iPostID
     * @param  string  $sPostType
     * @param  array   $aGET
     * @return string
     */
    static public function getPostDefinitionEditPageURL( $iPostID, $sPostType, array $aGET=array() ) {
         // e.g. http://.../wp-admin/post.php?post=196&action=edit&post_type=amazon_auto_links
        return add_query_arg( 
            array( 
                'action'    => 'edit',
                'post'      => $iPostID,
            ) + $aGET, 
            admin_url( 'post.php' ) 
        );
    }

    /**
     * Used by auto-insert form field definitions.
     * @return      array
     */
    static public function getPredefinedFilters() {            
        return array(                        
            'the_content'       => __( 'Post / Page Content', 'amazon-auto-links' ),
            'the_excerpt'       => __( 'Excerpt', 'amazon-auto-links' ),
            'comment_text'      => __( 'Comment', 'amazon-auto-links' ),
            'the_content_feed'  => __( 'Feed', 'amazon-auto-links' ),
            'the_excerpt_rss'   => __( 'Feed Excerpt', 'amazon-auto-links' ),
        );
    }
    /**
     * Used by auto-insert form field definitions.
     * @param       boolean $bDescription
     * @return      array
     */
    static public function getPredefinedFiltersForStatic( $bDescription=true ) {
        return array(
            'wp_insert_post_data' => __( 'Post / Page Content on Publish', 'amazon-auto-links' ) 
                . ( 
                    $bDescription 
                        ? "&nbsp;&nbsp;<span class='description'>(" . __( 'inserts product links into the database so they will be static.', 'amazon-auto-links' ) . ")</span>" 
                        : '' 
                ),
        );        
    }
    
    /**
     * Checks multiple file existence.
     *
     * @param       array|string    $asFilePaths
     * @return      boolean
     */
    static public function doFilesExist( $asFilePaths ) {        
        foreach( self::getAsArray( $asFilePaths ) as $_sFilePath ) {
            if ( ! file_exists( $_sFilePath ) ) {
                return false;
            }
        }                
        return true;
    }

    /**
     * Returns an array of the installed taxonomies on the site.
     * 
     */
    public static function getSiteTaxonomies() {
        
        $_aTaxonomies = get_taxonomies( '', 'names' );
        unset( 
            $_aTaxonomies[ 'nav_menu' ],
            $_aTaxonomies[ 'link_category' ],
            $_aTaxonomies[ 'post_format' ]
        );
        return $_aTaxonomies;
        
    }

    /**
     * Returns an array of associated taxonomies of the given post.
     * 
     * @param       string|integer|object            $isoPost            Either the post ID or the post object.
     * @return      array
     */
    public static function getPostTaxonomies( $isoPost ) {
        $_oPost = get_post( $isoPost );
        return is_object( $_oPost )
            ? ( array ) get_object_taxonomies( $_oPost, 'objects' )
            : array();
    }    
    
    /**
     * Returns the current url of admin page.
     * @return      string
     */
    public static function getCurrentAdminURL() {
        return add_query_arg( 
            $_GET, 
            admin_url( $GLOBALS[ 'pagenow' ] )
        );
    }
        
    /**
     * Escapes the given string for the KSES filter with the criteria of allowing/disallowing tags and the protocol.
     * 
     * @remark      Attributes are not supported at this moment.
     * @param       string      $sString
     * @param       array       $aAllowedTags               e.g. array( 'noscript', 'style', )
     * @param       array       $aAllowedProtocols
     * @param       array       $aDisallowedTags            e.g. array( 'table', 'tbody', 'thoot', 'thead', 'th', 'tr' )
     * @param       array       $aAllowedAttributes         e.g. array( 'rel', 'itemtype', 'style' )
     * @since       2.0.0
     * @since       3.1.0       Added the $aAllowedAttributes parameter.
     * @return      string
     */
    static public function escapeKSESFilter( $sString, $aAllowedTags=array(), $aDisallowedTags=array(), $aAllowedProtocols=array(), $aAllowedAttributes=array() ) {

        $aFormatAllowedTags = array();
        foreach( $aAllowedTags as $sTag ) {
            $aFormatAllowedTags[ $sTag ] = array();    // activate the inline style attribute.
        }
        $aAllowedHTMLTags = AmazonAutoLinks_Utility::uniteArrays( $aFormatAllowedTags, $GLOBALS['allowedposttags'] );    // the first parameter takes over the second.
        
        foreach ( $aDisallowedTags as $sTag ) {
            if ( isset( $aAllowedHTMLTags[ $sTag ] ) ) {
                unset( $aAllowedHTMLTags[ $sTag ] );
            }
        }
        
        // Set allowed attributes.
        $_aFormattedAllowedAttributes = array_fill_keys( $aAllowedAttributes, 1 );
        foreach( $aAllowedHTMLTags as $_sTagName => $_aAttributes ) {
            $aAllowedHTMLTags[ $_sTagName ] = $_aAttributes + $_aFormattedAllowedAttributes;
        }
        
        if ( empty( $aAllowedProtocols ) ) {
            $aAllowedProtocols = wp_allowed_protocols();            
        }
            
        $sString = addslashes( $sString );                    // the original function call was doing this - could be redundant but haven't fully tested it
        $sString = stripslashes( $sString );                    // wp_filter_post_kses()
        $sString = wp_kses_no_null( $sString );                // wp_kses()
        $sString = wp_kses_normalize_entities( $sString );    // wp_kses()
        $sString = wp_kses_hook( $sString, $aAllowedHTMLTags, $aAllowedProtocols ); // WP changed the order of these funcs and added args to wp_kses_hook
        $sString = wp_kses_split( $sString, $aAllowedHTMLTags, $aAllowedProtocols );        
        $sString = addslashes( $sString );                // wp_filter_post_kses()
        $sString = stripslashes( $sString );                // the original function call was doing this - could be redundant but haven't fully tested it
        return $sString;
        
    }        

}