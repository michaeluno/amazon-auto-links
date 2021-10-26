<?php
/**
 * Auto Amazon Links
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 *
 */

/**
 * A base class of locale classes.
 *
 * @since       4.3.4
 */
abstract class AmazonAutoLinks_Locale_Base extends AmazonAutoLinks_PluginUtility {

    /**
     * The locale code.
     * @var string e.g. US, IT, UK, JP
     * @remark Override it.
     */
    public $sSlug = '';

    /**
     * Two digits locale number.
     *
     * This can be checked by visiting the store site of the locale to check
     * and opening the Sources tab in the developer (DevTool) panel in Chrome
     * and navigate to an image directory with `G/NN` where `NN` is the locale number.
     *
     * This is for the Canadian locale where 15 denotes the locale number.
     * ```
     * https://images-eu.ssl-images-amazon.com/images/G/15/ShoppingPortal/logo._TTD_.png
     * ```
     * Interestingly, 34 is for amazon.ru which does not exist.
     * ```
     * https://images-eu.ssl-images-amazon.com/images/G/34/ShoppingPortal/logo._TTD_.png
     * ```
     *
     * @var string
     * @remark Override it.
     */
    public $sLocaleNumber = '01';

    /**
     * @var string e.g. www.amazon.com
     * @remark Override it.
     */
    public $sDomain = '';

    /**
     * @var string
     * @remark Override it.
     */
    public $sAssociatesURL = '';

    /**
     * @var string
     * @remark Override it.
     * @see https://www.iconfinder.com/iconsets/142-mini-country-flags-16x16px
     */
    public $sFlagImg = '';

    /**
     * @var string
     * @remark Override it.
     */
    public $sBlackCurtainURL = '';

    /**
     * @var string
     * @remark Override it.
     */
    public $sNoImageURL = '';

    /**
     * @remark There are locales which do not support this such as AU.
     * Currently, the supported locales are US, CA, FR, IT, DE, ES, JP, IN
     * @var string
     * @since 4.6.9
     */
    public $sAdSystemServer = '';

    // Methods to override.

    /**
     * @return string The country name.
     * @since  4.5.0
     */
    public function getName() {
        return 'THE COUNTRY NAME (Default)';
    }

    /**
     * @return string
     * @remark Override it.
     */
    public function getLabel() {
        return $this->sSlug . ' - ' . 'DEFAULT LABEL';
    }

    /**
     * @return string
     * @remark The supported locales: US, CA, FR, DE, UK, JP.
     * @remark Override this.
     */
    protected function _getImpressionCounterScript() {
        return '';
    }

    // No need to override these methods.

    /**
     * @param  string   $sPath  The URL path preceded by the store URL.
     * @return string   The top store URL without a trailing slash. e.g. https://amazon.com
     * @since  4.3.4
     */
    public function getMarketPlaceURL( $sPath='' ) {
        $sPath = $sPath
            ? '/' . ltrim( $sPath, '/' )
            : '';
        return 'https://' . untrailingslashit( $this->getDomain() ) . $sPath;
    }

    /**
     * @return string   e.g. www.amazon.com
     * @since  4.3.4
     */
    public function getDomain() {
        return $this->sDomain;
    }

    /**
     * @param  string $sASIN
     * @return string Format: https://{host}/gp/customer-reviews/widgets/average-customer-review/popover/ref=dpx_acr_pop_?contextId=dpx&asin='{asin}
     * @since  4.3.4
     */
    public function getProductRatingWidgetURL( $sASIN ) {
        return $this->getMarketPlaceURL(
            '/gp/customer-reviews/widgets/average-customer-review/popover/ref=dpx_acr_pop_?contextId=dpx&asin=' . $sASIN
        );
    }

    /**
     * @param  string $sASIN
     * @param  string $sAssociateID
     * @param  string $sLanguage
     * @return string
     * @since  4.3.4
     */
    public function getCustomerReviewURL( $sASIN, $sAssociateID='', $sLanguage='' ) {
        $_aArguments = array(
            'language' => $sLanguage,
            'tag'      => $sAssociateID,
        );
        $_aArguments = array_filter( $_aArguments );
        $_sURL       = $this->getMarketPlaceURL( '/product-reviews/' . $sASIN );
        return empty( $_aArguments )
            ? $_sURL
            : add_query_arg( $_aArguments, $_sURL );
    }

    /**
     * @param  string $sPath
     * @return string
     * @since  4.3.4
     * @since  4.3.5  Added the `$sPath` parameter.
     */
    public function getBestSellersURL( $sPath='' ) {
        return $this->getMarketPlaceURL() . '/gp/bestsellers/' . ltrim( $sPath, '/' );
    }

    /**
     * @param  string $sPath
     * @return string
     * @since  4.3.4
     * @since  4.3.5  Added the `$sPath` parameter.
     */
    public function getAssociatesURL( $sPath='' ) {
        return $sPath
            ? untrailingslashit( $this->sAssociatesURL ) . '/' . $sPath
            : $this->sAssociatesURL;
    }

    /**
     * @return string
     * @since  4.3.4
     */
    public function getBlackCurtainURL() {
        return $this->sBlackCurtainURL;
    }

    /**
     * @return string
     * @since  4.3.4
     */
    public function getNoImageURL() {
        if ( false !== filter_var( $this->sNoImageURL, FILTER_VALIDATE_URL ) ) {
            return $this->sNoImageURL;
        }
        $_sPath   = "images/G/{$this->sLocaleNumber}/x-site/icons/no-img-sm.gif";
        return is_ssl()
            ? "https://images-na.ssl-images-amazon.com/{$_sPath}"
            : "http://g-images.amazon.com/{$_sPath}";
    }

    /**
     * @return string
     * @since  4.3.4
     */
    public function getLocaleNumber() {
        return $this->sLocaleNumber;
    }

    /**
     * @return string
     * @since  4.3.4
     */
    public function getFlagImg() {
        return $this->sFlagImg;
    }

    /**
     * @return string
     * @since  4.3.4
     */
    public function getAddToCartURL() {
        return $this->getMarketPlaceURL() . '/gp/aws/cart/add.html';
    }

    /**
     * Returns the ISO 3166 country code.
     * Mostly the same as the slug. But the UK locale will be GB.
     * @see https://en.wikipedia.org/wiki/List_of_ISO_3166_country_codes
     * @since  4.6.9
     * @return string
     */
    public function getCountryCode() {
        return $this->sSlug;
    }

    /**
     * @since  4.6.9
     * @param  array    $aPayload   API request parameters.
     * @return string   The endpoint URI
     */
    public function getAdWidgetAPIEndpoint( array $aPayload=array() ) {
        return $this->sAdSystemServer
            ? add_query_arg( $aPayload, 'https://' . $this->sAdSystemServer . '/widgets/q' )
            : '';
    }

    /**
     * @see    WP_Http_Cookie
     * @param  string  $sLanguage      The preferred language.
     * @param  boolean $bRenewCookies  Whether to renew cookies. If this is true, the transient will not be used.
     * @return array|WP_Http_Cookie[]  An array for the `cookies` argument of `wp_remote_request()`.
     * @remark Be aware that this method takes time, meaning slow as this performs at least two HTTP requests if not cached.
     * @since  4.3.4
     * @since  4.5.0    Added the `$bRenewCookies` parameter.
     */
    public function getHTTPRequestCookies( $sLanguage='', $bRenewCookies=false ) {

        if ( ! $bRenewCookies ) {
            $_aCachedCookies = $this->___getCookieTransient( $sLanguage );
            if ( ! empty( $_aCachedCookies ) ) {
                return $_aCachedCookies;
            }
        }

        $_oCookieGetter = new AmazonAutoLinks_Locale_AmazonCookies( $this, $sLanguage );
        $_aCookies = $_oCookieGetter->get();
        $this->___setCookieTransient( $_aCookies, $_oCookieGetter );
        return $_aCookies;

    }
        /**
         * @param  string $sLanguage
         * @return WP_Http_Cookie[]
         * @since  4.3.4
         * @since  4.5.0    Moved from AmazonAutoLinks_Locale_AmazonCookies
         */
        private function ___getCookieTransient( $sLanguage ) {
            $_sTransientPrefix     = AmazonAutoLinks_Registry::TRANSIENT_PREFIX;
            $_sTransientKey        = "_transient_{$_sTransientPrefix}_cookies_{$this->sSlug}";
            $_sTransientKeyTimeout = "_transient_timeout_{$_sTransientPrefix}_cookies_{$this->sSlug}";
            $_aCookies             = $this->getAsArray( get_option( $_sTransientKey, array() ) );
            $_aOldCookies          = $_aCookies;
            $_iLifespan            = ( integer ) get_option( $_sTransientKeyTimeout, 0 );
            if ( $this->isExpired( $_iLifespan ) ) {
                $_aCookies = array();
            }
            return $this->getAsArray( apply_filters( 'aal_filter_amazon_cookies_cache', $_aCookies, $_iLifespan, $this, $sLanguage, $_aOldCookies ) );
        }
        /**
         * Sets an option looking like a transient in the options table.
         * The data is stored as an option but with the transient name.
         * This is to enable the autoload option but with an expiration time.
         * By using set_transient(), if an expiration time is given, the autoload option will be disabled.
         * @param  array   $aCookies
         * @param  AmazonAutoLinks_Locale_AmazonCookies $oCookieGetter
         * @return boolean
         * @since  4.3.4
         * @since  4.5.0    Moved from AmazonAutoLinks_Locale_AmazonCookies
         */
        private function ___setCookieTransient( array $aCookies, AmazonAutoLinks_Locale_AmazonCookies $oCookieGetter ) {
            $_sTransientPrefix = AmazonAutoLinks_Registry::TRANSIENT_PREFIX;
            $_sNameTimeout     = "_transient_timeout_{$_sTransientPrefix}_cookies_{$this->sSlug}";
            $_sName            = "_transient_{$_sTransientPrefix}_cookies_{$this->sSlug}";
            update_option( $_sNameTimeout, time() + $oCookieGetter->iCacheDuration );
            return update_option( $_sName, $aCookies );
        }

    /**
     * @remark The supported locales: US, CA, FR, DE, UK, JP.
     * @see    https://www.assoc-amazon.com/s/impression-counter-common.js
     * @see    https://ir-na.amazon-adsystem.com/s/impression-counter?tag=%ASSOCIATE_TAG%&o=15
     * @param  string $sAssociatesTag
     * @return string
     * @since  4.3.4
     */
    public function getImpressionCounterScriptTag( $sAssociatesTag ) {
        $_sLocale = strtolower( $this->sSlug );
        $_sClass  = esc_attr( "aal_impression_counter_{$_sLocale}" );
        $_sURL    = esc_url( "https://ir-ca.amazon-adsystem.com/s/noscript?tag={$sAssociatesTag}" );
        $_sLabel  = esc_attr( __( 'Impression Counter', 'amazon-auto-links' ) );
        $_sScript = $this->_getImpressionCounterScript();
        if ( ! $_sScript ) {
            return '';
        }
        return "<script class='{$_sClass}' type='text/javascript'>"
                . $_sScript
            . "</script>"
            . "<noscript>"
                . "<img class='{$_sClass}' src='{$_sURL}' alt='{$_sLabel}' />"
            . "</noscript>";
    }

}