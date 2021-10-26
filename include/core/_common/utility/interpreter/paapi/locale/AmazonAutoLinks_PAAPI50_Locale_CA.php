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
 *
 * Provides PA-API Canadian locale information.
 *
 * @since 4.3.4
 */
class AmazonAutoLinks_PAAPI50_Locale_CA extends AmazonAutoLinks_PAAPI50_Locale_Base {

    /**
     * @var string e.g. US, UK, CA etc.
     */
    public $sSlug = 'CA';

    /**
     * @var string
     * @remark Override this.
     * @see https://webservices.amazon.com/paapi5/documentation/common-request-parameters.html#host-and-region
     */
    public $sServerRegion = 'us-east-1';

    /**
     * @var string
     * @see https://webservices.amazon.com/paapi5/documentation/locale-reference.html#locale-reference-for-product-advertising-api
     */
    public $sMarketPlaceHost = 'www.amazon.ca';

    /**
     * @var string
     * @see https://webservices.amazon.com/paapi5/documentation/common-request-parameters.html#host-and-region
     */
    public $sHost = 'webservices.amazon.ca';

    /**
     * @var string
     */
    public $sDefaultLanguage = 'en_CA';

    /**
     * @var string
     */
    public $sDefaultCurrency = 'CAD';

    /**
     * API license agreement URL.
     * @var string
     */
    public $sLicenseURL = 'https://associates.amazon.ca/help/operating/policies#Associates%20Program%20IP%20License';

    /**
     * @return array
     * @see    https://webservices.amazon.com/paapi5/documentation/locale-reference/canada.html#valid-languages
     */
    public function getLanguages() {
        return array(
            'en_CA' => __( 'English - CANADA', 'amazon-auto-links' ),
            'fr_CA' => __( 'French - CANADA', 'amazon-auto-links' ),
        );
    }

    /**
     * @return array
     * @see    https://webservices.amazon.com/paapi5/documentation/locale-reference/canada.html#valid-currencies
     */
    public function getCurrencies() {
        return array(
            'CAD' => __( 'Canadian Dollar', 'amazon-auto-links' ),
        );
    }

    /**
     * @return array
     * @see    https://webservices.amazon.com/paapi5/documentation/locale-reference/canada.html#search-index
     */
    public function getSearchIndex() {
        return array(
            'All'                       => 'All Department',
            'Apparel'                   => 'Clothing & Accessories',
            'Automotive'                => 'Automotive',
            'Baby'                      => 'Baby',
            'Beauty'                    => 'Beauty',
            'Books'                     => 'Books',
            'Classical'                 => 'Classical Music',
            'Electronics'               => 'Electronics',
            'EverythingElse'            => 'Everything Else',
            'ForeignBooks'              => 'English Books',
            'GardenAndOutdoor'          => 'Patio, Lawn & Garden',
            'GiftCards'                 => 'Gift Cards',
            'GroceryAndGourmetFood'     => 'Grocery & Gourmet Food',
            'Handmade'                  => 'Handmade',
            'HealthPersonalCare'        => 'Health & Personal Care',
            'HomeAndKitchen'            => 'Home & Kitchen',
            'Industrial'                => 'Industrial & Scientific',
            'Jewelry'                   => 'Jewelry',
            'KindleStore'               => 'Kindle Store',
            'Luggage'                   => 'Luggage & Bags',
            'LuxuryBeauty'              => 'Luxury Beauty',
            'MobileApps'                => 'Apps & Games',
            'MoviesAndTV'               => 'Movies & TV',
            'Music'                     => 'Music',
            'MusicalInstruments'        => 'Musical Instruments, Stage & Studio',
            'OfficeProducts'            => 'Office Products',
            'PetSupplies'               => 'Pet Supplies',
            'Shoes'                     => 'Shoes & Handbags',
            'Software'                  => 'Software',
            'SportsAndOutdoors'         => 'Sports & Outdoors',
            'ToolsAndHomeImprovement'   => 'Tools & Home Improvement',
            'ToysAndGames'              => 'Toys & Games',
            'VHS'                       => 'VHS',
            'VideoGames'                => 'Video Games',
            'Watches'                   => 'Watches',
        );
    }

}