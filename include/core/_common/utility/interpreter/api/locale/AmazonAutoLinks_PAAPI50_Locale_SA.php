<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 */

/**
 *
 * Provides PA-API SA locale information.
 *
 * @since 4.3.4
 * @see https://webservices.amazon.com/paapi5/documentation/locale-reference/saudi-arabia.html
 */
class AmazonAutoLinks_PAAPI50_Locale_SA extends AmazonAutoLinks_PAAPI50_Locale_Base {

    /**
     * @var string e.g. US, UK, CA etc.
     */
    public $sSlug = 'SA';

    /**
     * @var string
     * @see https://webservices.amazon.com/paapi5/documentation/common-request-parameters.html#host-and-region
     */
    public $sServerRegion = 'eu-west-1';

    /**
     * The host name of the market place.
     * @var string
     * @see https://webservices.amazon.com/paapi5/documentation/locale-reference.html#locale-reference-for-product-advertising-api
     */
    public $sMarketPlaceHost = 'www.amazon.sa';

    /**
     * @var string
     * @see https://webservices.amazon.com/paapi5/documentation/common-request-parameters.html#host-and-region
     */
    public $sHost = 'webservices.amazon.sa';

    /**
     * @var string
     */
    public $sDefaultLanguage = 'en_AE';

    /**
     * @var string
     */
    public $sDefaultCurrency = 'SAR';

    /**
     * API license agreement URL.
     * @var string
     */
    public $sLicenseURL = 'https://affiliate-program.amazon.sa/help/operating/policies';

    /**
     * @var string
     * @since 4.5.0
     */
    public $sDisclaimer = 'CERTAIN CONTENT THAT APPEARS [IN THIS APPLICATION or ON THIS SITE, as applicable] COMES FROM AMAZON. THIS CONTENT IS PROVIDED ‘AS IS’ AND IS SUBJECT TO CHANGE OR REMOVAL AT ANY TIME.';

    /**
     * @return array
     */
    public function getLanguages() {
        return array(
            'en_AE'	=> __( 'English - UNITED ARAB EMIRATES', 'amazon-auto-links' ),
            'ar_AE'	=> __( 'Arabic - UNITED ARAB EMIRATES', 'amazon-auto-links' ),
        );
    }

    /**
     * @return array
     */
    public function getCurrencies() {
        return array(
            'SAR' => __( 'Saudi Riyal', 'amazon-auto-links' ),
        );
    }

    /**
     * @return array
     * @remark No need to translate items.
     */
    public function getSearchIndex() {
        return array(
            'All'                       => 'All Categories',
            'ArtsAndCrafts'             => 'Arts, Crafts & Sewing',
            'Automotive'                => 'Automotive Parts & Accessories',
            'Baby'                      => 'Baby',
            'Beauty'                    => 'Beauty & Personal Care',
            'Books'                     => 'Books',
            'Computers'                 => 'Computer & Accessories',
            'Electronics'               => 'Electronics',
            'Fashion'                   => 'Clothing, Shoes & Jewelry',
            'GardenAndOutdoor'          => 'Home & Garden',
            'GiftCards'                 => 'Gift Cards',
            'GroceryAndGourmetFood'     => 'Grocery & Gourmet Food',
            'HealthPersonalCare'        => 'Health, Household & Baby Care',
            'HomeAndKitchen'            => 'Kitchen & Dining',
            'Industrial'                => 'Industrial & Scientific',
            'KindleStore'               => 'Kindle Store',
            'Miscellaneous'             => 'Everything Else',
            'MoviesAndTV'               => 'Movies & TV',
            'Music'                     => 'CDs & Vinyl',
            'MusicalInstruments'        => 'Musical Instruments',
            'OfficeProducts'            => 'Office Products',
            'PetSupplies'               => 'Pet Supplies',
            'Software'                  => 'Software',
            'SportsAndOutdoors'         => 'Sports',
            'ToolsAndHomeImprovement'   => 'Tools & Home Improvement',
            'ToysAndGames'              => 'Toys & Games',
            'VideoGames'                => 'Video Games',
        );
    }

}