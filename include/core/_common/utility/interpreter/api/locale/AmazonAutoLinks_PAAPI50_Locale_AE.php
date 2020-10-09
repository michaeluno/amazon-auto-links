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
 *
 * Provides PA-API AE locale information.
 *
 * @since 4.3.4
 */
class AmazonAutoLinks_PAAPI50_Locale_AE extends AmazonAutoLinks_PAAPI50_Locale_Base {

    /**
     * @var string e.g. US, UK, CA etc.
     */
    public $sSlug = 'AE';

    /**
     * @var string
     * @see https://webservices.amazon.com/paapi5/documentation/common-request-parameters.html#host-and-region
     */
    public $sServerRegion = 'eu-west-1';

    /**
     * @var string
     * @remark Override this.
     * @see https://webservices.amazon.com/paapi5/documentation/common-request-parameters.html#host-and-region
     */
    public $sHost = 'webservices.amazon.ae';

    /**
     * @var string
     * @remark English - UNITED ARAB EMIRATES
     */
    public $sDefaultLanguage = 'en_AE';

    /**
     * @var string
     * @remark Arab Emirates Dirham
     */
    public $sDefaultCurrency = 'AED';

    /**
     * API license agreement URL.
     * @var string
     */
    public $sLicenseURL = 'https://affiliate-program.amazon.ae/help/operating/policies/#Associates%20Program%20IP%20License';

    /**
     * @return array
     */
    public function getLanguages() {
        return array(
            'en_AE' => __( 'English - UNITED ARAB EMIRATES', 'amazon-auto-links' ),
            // ---
            'ar_AE' => __( 'Arabic - UNITED ARAB EMIRATES', 'amazon-auto-links' ),
        );
    }

    /**
     * @return array
     * @remark Override this.
     */
    public function getCurrencies() {
        return array(
            'AED' => __( 'Arab Emirates Dirham', 'amazon-auto-links' ),
        );
    }

    /**
     * @return array
     * @remark No need to translate items.
     * @see https://webservices.amazon.com/paapi5/documentation/locale-reference/united-arab-emirates.html
     */
    public function getSearchIndex() {
        return array(
            'All'                       => 'All Departments', 
            'Automotive'                => 'Automotive Parts & Accessories', 
            'Baby'                      => 'Baby', 
            'Beauty'                    => 'Beauty & Personal Care', 
            'Books'                     => 'Books', 
            'Computers'                 => 'Computers', 
            'Electronics'               => 'Electronics', 
            'EverythingElse'            => 'Everything Else', 
            'Fashion'                   => 'Clothing, Shoes & Jewelry', 
            'HomeAndKitchen'            => 'Home & Kitchen', 
            'Lighting'                  => 'Lighting', 
            'ToysAndGames'              => 'Toys & Games', 
            'VideoGames'                => 'Video Games', 
        );
    }
    
}