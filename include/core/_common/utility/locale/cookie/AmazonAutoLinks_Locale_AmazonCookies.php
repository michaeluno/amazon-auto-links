<?php
/**
 * Amazon Auto Links
 *
 * http://en.michaeluno.jp/amazn-auto-links/
 * Copyright (c) 2013-2020 Michael Uno
 *
 */

/**
 * Retrieve Amazon sites' cookies.
 *
 * @since   4.3.4
 * @uses    AmazonAutoLinks_HTTPClient
 */
class AmazonAutoLinks_Locale_AmazonCookies extends AmazonAutoLinks_PluginUtility {

    /**
     * @var AmazonAutoLinks_Locale_Base
     */
    public $oLocale;

    /**
     * @var string
     */
    public $sLocale;

    /**
     * @var string
     */
    public $sLanguage;

    /**
     * @var int
     */
    public $iCacheDuration = 604800;    // ( 60 * 60 * 24 * 7 ) : 7 days

    public $sTransientKey  = '';

    public $sTransientKeyTimeout = '';

    /**
     * Sets up properties and hooks.
     * @param AmazonAutoLinks_Locale_Base $oLocale
     * @param string $sLanguage
     */
    public function __construct( AmazonAutoLinks_Locale_Base $oLocale, $sLanguage='' ) {
        $this->oLocale              = $oLocale;
        $this->sLocale              = $oLocale->sSlug;
        $this->sLanguage            = $sLanguage;
        $_sTransientPrefix          = AmazonAutoLinks_Registry::TRANSIENT_PREFIX;
        $this->sTransientKey        = "_transient_{$_sTransientPrefix}_cookies_{$this->oLocale->sSlug}";
        $this->sTransientKeyTimeout = "_transient_timeout_{$_sTransientPrefix}_cookies_{$this->oLocale->sSlug}";
    }

    /**
     * @see     WP_Http_Cookie
     * @return  array WP_Http_Cookie[]
     * @remark  Be aware that this method takes time, meaning slow as this performs at least two HTTP requests if not cached.
     */
    public function get() {

        // Check cache.
        $_aCachedCookies = $this->___getCookieTransient();
        if ( ! empty( $_aCachedCookies ) ) {
            return $_aCachedCookies;
        }

        $_aCookies = $this->___getCookies();
        // It seems Amazon servers parses cookies from last.
        // This is important when there are cookies with the same name.
        $_aCookies = array_reverse( $_aCookies );
        $this->___setCookieTransient( $_aCookies );
        return $_aCookies;

    }
        /**
         * @return WP_Http_Cookie[]
         */
        private function ___getCookies() {

            $_sURL            = $this->oLocale->getAssociatesURL();
            $_aRequestCookies = $this->___getAssociatesRequestCookiesGenerated( $_sURL, $this->sLocale, $this->sLanguage );

            $_sLastSessionID  = null;
            $_aLastCookies    = $_aRequestCookies;
            $_iIndex          = 1;
            while( method_exists( $this, $_sMethodName = "_getResponseCookiesWithHTTPRequest_" . sprintf( '%02d', $_iIndex ) ) ) {

                $_aThisCookies = call_user_func_array( array( $this, $_sMethodName ), array( $_aLastCookies, &$_sThisSessionID ) );
                $_aThisCookies = $this->___getCookieMerged( $_aThisCookies, $_aLastCookies );
                if ( $_sLastSessionID === $_sThisSessionID ) {
                    return $_aThisCookies;
                }
                // For the next iteration
                $_iIndex++;
                $_sLastSessionID = $_sThisSessionID;
                $_aLastCookies   = $_aThisCookies;

            }

            // The method does not exists any more.
            return $_aLastCookies;

        }
            /**
             * @param  array $aPrecede
             * @param  array $aSub
             * @return array
             * @since  4.3.4
             */
            private function ___getCookieMerged( $aPrecede, $aSub ) {
                foreach( $aSub as $_isIndexOrName => $_aoCookie ) {
                    if ( $this->___hasSameCookie( $aPrecede, $_isIndexOrName, $_aoCookie ) ) {
                        continue;
                    }
                    if ( $_aoCookie instanceof WP_Http_Cookie ) {
                        $aPrecede[] =  $_aoCookie;
                        continue;
                    } 
                    $aPrecede[ $_isIndexOrName ] = $_aoCookie;
                }
                return $aPrecede;
            }
                /**
                 * @param  array $aCookies
                 * @param  string|integer $isIndexOrName
                 * @param  array $aoCookie
                 * @return boolean
                 * @since  4.3.4
                 */
                private function ___hasSameCookie( $aCookies, $isIndexOrName, $aoCookie ) {

                    $_sSearchName   = $aoCookie instanceof WP_Http_Cookie ? $aoCookie->name   : $isIndexOrName;
                    $_sSearchDomain = $aoCookie instanceof WP_Http_Cookie ? $aoCookie->domain : null;
                    foreach( $aCookies as $_isIndexOrName => $_aoCookie ) {
                        $_sThisName   = $_aoCookie instanceof WP_Http_Cookie ? $_aoCookie->name   : $_isIndexOrName;
                        $_sThisDomain = $_aoCookie instanceof WP_Http_Cookie ? $_aoCookie->domain : null;
                        if ( $_sSearchDomain === $_sThisDomain && $_sSearchName === $_sThisName ) {
                            return true;
                        }
                    }
                    return false;

                }

        /**
         * @return WP_Http_Cookie[]
         * @since  4.3.4
         */
        private function ___getCookieTransient() {
            if ( $this->isExpired( ( integer ) get_option( $this->sTransientKeyTimeout, 0 ) ) ) {
                return array();
            }
            return $this->getAsArray( get_option( $this->sTransientKey, array() ) );
        }
        /**
         * Sets an option looking like a transient in the options table.
         * The data is stored as an option but with the transient name.
         * This is to enable the autoload option but with an expiration time.
         * By using set_transient(), if an expiration time is given, the autoload option will be disabled.
         * @param  array   $aCookies
         * @return boolean
         * @since  4.3.4
         */
        private function ___setCookieTransient( array $aCookies ) {
            $_sTransientPrefix = AmazonAutoLinks_Registry::TRANSIENT_PREFIX;
            $_sNameTimeout     = "_transient_timeout_{$_sTransientPrefix}_cookies_{$this->oLocale->sSlug}";
            $_sName            = "_transient_{$_sTransientPrefix}_cookies_{$this->oLocale->sSlug}";
            update_option( $_sNameTimeout, time() + $this->iCacheDuration );
            return update_option( $_sName, $aCookies );
        }

        /**
         * @param  array   $aCookies
         * @param  string &$sSessionID This is by reference so that it can be modified.
         * @return array
         */
        protected function _getResponseCookiesWithHTTPRequest_01( array $aCookies, &$sSessionID ) {
            $_aCookies1  = $this->___getAssociatesCookies( $aCookies, $sSessionID );
            $_aCookies2  = $this->___getMarketPlacePrefFormCookies( $aCookies, $sSessionID );
            if ( ! empty( $_aCookies2 ) ) {
                return $_aCookies2;
            }
            return $this->___getMarketPlaceCookies( $_aCookies1, $sSessionID );
        }
            /**
             * @param array $aCookies
             * @param string $sSessionID
             * @return WP_Http_Cookie[]
             */
            private function ___getAssociatesCookies( array $aCookies, &$sSessionID ) {
                $_sURL       = $this->oLocale->getAssociatesURL();
                $_oHTTP      = new AmazonAutoLinks_HTTPClient(
                    $_sURL,
                    $this->iCacheDuration,
                    array(
                        'headers' => array( 'Referer' => '' ),
                        'method'  => 'HEAD',
                        'cookies' => array_reverse( $aCookies ),
                    )
                );
                $_aCookies   = $_oHTTP->getCookies();
                $sSessionID  = $this->_getSessionIDCookie( $_aCookies, $_sURL );
                return $_aCookies;
            }
            /**
             * @remark Some locals do not support the page and those locale returns 404.
             * @param array $aCookies
             * @param string $sSessionID
             * @return WP_Http_Cookie[]
             */
            private function ___getMarketPlacePrefFormCookies( array $aCookies, &$sSessionID ) {

                $_sFormURL   = $this->oLocale->getMarketPlaceURL( '/cookieprefs?ref_=portal_banner_all' );
                $_oHTTP      = new AmazonAutoLinks_HTTPClient(
                    $_sFormURL,
                    $this->iCacheDuration,
                    array(
                        'headers' => array( 'Referer' => '' ),
                        'cookies' => array_reverse( $aCookies ),
                    )
                );
                if ( ! $this->hasPrefix( '2', $_oHTTP->getStatusCode() ) ) {
                    return array();
                }

                $_sHTML      = $_oHTTP->getBody();
                $_aPostBody  = $this->___getPostBodyCookiePrefForm( $_sHTML );
                if ( empty( $_aPostBody ) ) {
                    return array();
                }

                $_oHTTP2     = new AmazonAutoLinks_HTTPClient(
                    add_query_arg( $_aPostBody, $_sFormURL ),   // made the method GET as well
                    $this->iCacheDuration,
                    array(
                        'method'  => 'POST',
                        'headers' => array( 'Referer' => $_sFormURL ),
                        'cookies' => array_reverse( $_oHTTP->getCookies() ),
                        'body'    => $_aPostBody,
                    )
                );
                $_aCookies   = $_oHTTP2->getCookies();
                $sSessionID  = $this->_getSessionIDCookie( $_aCookies, $_sFormURL );
                return $_aCookies;

            }
                /**
                 * @param  string $sHTML
                 * @return array
                 */
                private function ___getPostBodyCookiePrefForm( $sHTML ) {
                    $_aPostBody    = array();
                    $_oDOM         = new AmazonAutoLinks_DOM;
                    $_oDoc         = $_oDOM->loadDOMFromHTML( $sHTML );
                    $_oXPath       = new DOMXPath( $_oDoc );
                    $_noFormNode   = $_oXPath->query( ".//form[@action='' and @method='post' ]//input" );
                    if ( ! $_noFormNode ) {
                        return $_aPostBody;
                    }
                    /**
                     * @var DOMElement $_oNode
                     */
                    foreach( $_noFormNode as $_oInputNode ) {
                        $_sName = $_oInputNode->getAttribute( 'name' );
                        if ( 0 === strlen( $_sName ) ) {
                            continue;
                        }
                        if ( ! in_array( $_sName, array( 'accept', 'anti-csrftoken-a2z' ) ) ) {
                            continue;
                        }
                        $_aPostBody[ $_sName ] = $_oInputNode->getAttribute( 'value' );
                    }
                    return $_aPostBody;
                }

            /**
             * @param  array  $aCookies
             * @param  string $sSessionID
             * @return WP_Http_Cookie[]
             */
            private function ___getMarketPlaceCookies( array $aCookies, &$sSessionID ) {

                $_sTopURL    = $this->oLocale->getMarketPlaceURL();
                $_oHTTP      = new AmazonAutoLinks_HTTPClient(
                    $_sTopURL,
                    $this->iCacheDuration,
                    array(
                        'headers' => array( 'Referer' => '' ),
                        'cookies' => array_reverse( $aCookies ),
                    )
                );
                $_aCookies   = $_oHTTP->getCookies();
                $sSessionID  = $this->_getSessionIDCookie( $_aCookies, $_sTopURL );
                return $_aCookies;

            }

        /**
         * @param  array  $aCookies
         * @param  string $sSessionID
         * @return array
         */
        protected function _getResponseCookiesWithHTTPRequest_02( array $aCookies, &$sSessionID ) {
            $_sURL       = $this->oLocale->getBestSellersURL();
            $_oHTTP      = new AmazonAutoLinks_HTTPClient(
                $_sURL,
                $this->iCacheDuration,
                array(
                    // The GET method is used as those pages do not accept the HEAD method.
                    'headers'     => array( 'Referer' => $this->oLocale->getMarketPlaceURL() ),
                    'cookies'     => array_reverse( $aCookies ),
                )
            );
            $_aCookies   = $_oHTTP->getCookies();
            $sSessionID  = $this->_getSessionIDCookie( $_aCookies, $_sURL );
            return $_aCookies;
        }        
        /**
         * @param  array  $aCookies
         * @param  string $sSessionID
         * @return array
         */
        protected function _getResponseCookiesWithHTTPRequest_03( array $aCookies, &$sSessionID ) {
            $_sURL       = $this->oLocale->getBestSellersURL();
            $_oHTTP      = new AmazonAutoLinks_HTTPClient(
                $_sURL,
                $this->iCacheDuration,
                array(
                    // The GET method is used as those pages do not accept the HEAD method.
                    'headers'     => array( 'Referer' => $this->oLocale->getMarketPlaceURL() ),
                    'cookies'     => array_reverse( $aCookies ),
                    'renew_cache' => true,
                )
            );
            $_aCookies   = $_oHTTP->getCookies();
            $sSessionID  = $this->_getSessionIDCookie( $_aCookies, $_sURL );
            return $_aCookies;
        }

    /**
     * Retrieves cookies given by a Amazon Associates site of the given locale.
     * @param  string $sURL
     * @param  string $sLocale
     * @param  string $sLanguage
     * @return WP_Http_Cookie[]
     * @since  4.3.4
     */
    private function ___getAssociatesRequestCookiesGenerated( $sURL, $sLocale, $sLanguage ) {

        $_sLocaleKey  = 'ubid-acb' . strtolower( $sLocale );
        $_sToken      = sprintf( '%03d', mt_rand( 1, 999 ) )
            . '-' . sprintf( '%07d', mt_rand( 1, 9999999 ) )
            . '-' . sprintf( '%07d', mt_rand( 1, 9999999 ) );
        $_iExpires    = time() + ( 86400 * 365 ); // one year
        $_sDomain     = $this->___getCookieDomain( $sURL );
        $_aAttributes = array(
            'expires' => $_iExpires, 'domain' => $_sDomain, 'path' => '/',
        );
        $_aCookies    = array(
            new WP_Http_Cookie( array( 'name' => 'ubid-main',  'value' => $_sToken,  ) + $_aAttributes ),
            new WP_Http_Cookie( array( 'name' => $_sLocaleKey, 'value' => $_sToken,  ) + $_aAttributes ),
        );
        if ( $sLanguage ) {
            $_aCookies[] = new WP_Http_Cookie( array( 'name' => 'ac-language-preference', 'value' => $sLanguage, ) + $_aAttributes );
        }
        return $_aCookies;

    }
        /**
         * Returns the domain part of the given URL for a cookie.
         * e.g.
         * https://affiliate-program.amazon.com/ -> .amazon.com
         * https://amazon.com/                   -> .amazon.com
         * https://www.amazon.co.uk/             -> .amazon.co.uk
         * @param  string $sURL
         * @return string
         * @since  4.3.4
         */
        private function ___getCookieDomain( $sURL ) {
            $_sHost = parse_url( $sURL, PHP_URL_HOST );
            return preg_replace("/.*?([^.]+)(\.((co\.\w+)|\w+))$/i",'.$1$2', $_sHost  );   // strip sub-domain part
        }

    /**
     * @param  array $aCookies
     * @param  string $sURL
     * @return string
     * @since  4.3.4
     */
    protected function _getSessionIDCookie( array $aCookies, $sURL ) {
        $_sCookieDomain = $this->___getCookieDomain( $sURL );
        $_aSessionIDs   = array(); // consider a case of multiple cookies with the same name.
        foreach( $aCookies as $_isIndexOrName => $_soValueOrWPHttpCookie ) {
            $_bObject = ( $_soValueOrWPHttpCookie instanceof WP_Http_Cookie );  // backward compatibility for below WP 4.6.0.
            $_sName   = $_bObject ? $_soValueOrWPHttpCookie->name : $_isIndexOrName;
            if (
                $_bObject
                && isset( $_soValueOrWPHttpCookie->domain )
                && $_soValueOrWPHttpCookie->domain !== $_sCookieDomain
            ) {
                continue;
            }
            if ( 'session-id' !== $_sName ) {
                continue;
            }
            $_aSessionIDs[] = $_bObject
                ? $_soValueOrWPHttpCookie->value
                : $_soValueOrWPHttpCookie;
        }
        if ( empty( $_aSessionIDs ) ) {
            return '';
        }
        if ( count( $_aSessionIDs ) === 1 ) {
            return reset( $_aSessionIDs );
        }
        // At this point, multiple `session-id` cookie entry exist.
        $_iIndex = array_search( '-', $_aSessionIDs ); // remove the entry with the value '-'
        unset( $_aSessionIDs[ $_iIndex ] );
        return reset( $_aSessionIDs ); // the first found item

    }

}