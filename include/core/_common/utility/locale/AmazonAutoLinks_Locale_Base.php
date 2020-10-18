<?php
/**
 * Amazon Auto Links
 *
 * http://en.michaeluno.jp/amazn-auto-links/
 * Copyright (c) 2013-2020 Michael Uno
 *
 */

/**
 * A base class of locale classes.
 *
 * @since       4.3.4
 */
abstract class AmazonAutoLinks_Locale_Base {

    /**
     * The locale code.
     * @var string e.g. US, IT, UK, JP
     * @remark Override it.
     */
    public $sSlug = '';

    /**
     * Two digits locale number.
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

    // Methods to override.

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
     * @see    WP_Http_Cookie
     * @param  string $sLanguage The preferred language.
     * @return array|WP_Http_Cookie[]  An array for the `cookies` argument of `wp_remote_request()`.
     * @remark Be aware that this method takes time, meaning slow as this performs at least two HTTP requests if not cached.
     * @since  4.3.4
     */
    public function getHTTPRequestCookies( $sLanguage='' ) {
        $_oCookieGetter = new AmazonAutoLinks_Locale_AmazonCookies( $this, $sLanguage );
        return $_oCookieGetter->get();
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