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
 * A base class of PA-API locale classes.
 *
 * @since 4.3.4
 */
abstract class AmazonAutoLinks_PAAPI50_Locale_Base {

    /**
     * @var string e.g. US, UK, CA etc.
     */
    public $sSlug = '';

    /**
     * @var string
     * @remark Override this.
     * @see https://webservices.amazon.com/paapi5/documentation/common-request-parameters.html#host-and-region
     */
    public $sServerRegion = '';

    /**
     * The host name of the market place.
     * @var string
     * @ramrark Override this.
     * @see https://webservices.amazon.com/paapi5/documentation/locale-reference.html#locale-reference-for-product-advertising-api
     */
    public $sMarketPlaceHost = '';

    /**
     * The API server host name.
     * @var string
     * @remark Override this.
     * @see https://webservices.amazon.com/paapi5/documentation/common-request-parameters.html#host-and-region
     */
    public $sHost = '';

    /**
     * @var string
     * @remark Override this.
     */
    public $sDefaultLanguage = '';

    /**
     * @var string
     * @remark Override this.
     */
    public $sDefaultCurrency = '';

    /**
     * API license agreement URL.
     * @var string
     * @remark Override this.
     */
    public $sLicenseURL = '';

    /**
     * @return array
     * @remark Override this.
     */
    public function getLanguages() {
        return array();
    }

    /**
     * @return array
     * @remark Override this.
     */
    public function getCurrencies() {
        return array();
    }

    /**
     * @return array
     * @remark No need to translate items.
     * @remark Override this.
     */
    public function getSearchIndex() {
        return array();
    }

    // The following methods do not have to be overridden.

    /**
     * @return string
     */
    public function getMarketPlaceHost() {
        return $this->sMarketPlaceHost;
    }

    /**
     * @return string
     */
    public function getHost() {
        return $this->sHost;
    }

    /**
     * The server region name.
     * @return string
     */
    public function getServerRegion() {
        return $this->sServerRegion;
    }
    /**
     * @return string
     */
    public function getHostLabel() {
       return $this->sSlug . ' - ' . $this->sHost;
    }

    /**
     * @return string
     */
    public function getDefaultLanguage() {
        return $this->sDefaultLanguage;
    }

    /**
     * @return string
     */
    public function getDefaultCurrency() {
        return $this->sDefaultCurrency;
    }

}