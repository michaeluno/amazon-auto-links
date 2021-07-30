<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 */

/**
 *
 * Provides PA-API UK locale information.
 *
 * @since 4.3.4
 */
class AmazonAutoLinks_PAAPI50_Locale_UK extends AmazonAutoLinks_PAAPI50_Locale_Base {

    /**
     * @var string e.g. US, UK, CA etc.
     */
    public $sSlug = 'UK';

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
    public $sMarketPlaceHost = 'www.amazon.co.uk';

    /**
     * @var string
     * @see https://webservices.amazon.com/paapi5/documentation/common-request-parameters.html#host-and-region
     */
    public $sHost = 'webservices.amazon.co.uk';

    /**
     * @var string
     * @remark English - UNITED KINGDOM
     */
    public $sDefaultLanguage = 'en_GB';

    /**
     * @var string
     * @remark British Pound
     */
    public $sDefaultCurrency = 'GBP';

    /**
     * API license agreement URL.
     * @var string
     */
    public $sLicenseURL = 'https://affiliate-program.amazon.co.uk/help/operating/policies#Associates%20Program%20IP%20License';

    /**
     * @return array
     */
    public function getLanguages() {
        return array(
            'en_GB' => __( 'English - UNITED KINGDOM', 'amazon-auto-links' ),
        );
    }

    /**
     * @return array
     */
    public function getCurrencies() {
        return array(
            'GBP' => __( 'British Pound', 'amazon-auto-links' ),
        );
    }

    /**
     * @return array
     * @remark No need to translate items.
     * @see https://webservices.amazon.com/paapi5/documentation/locale-reference/united-kingdom.html
     */
    public function getSearchIndex() {
        return array(
            'All'                       => 'All Departments',
            'AmazonVideo'               => 'Amazon Video',
            'Apparel'                   => 'Clothing',
            'Appliances'                => 'Large Appliances',
            'Automotive'                => 'Car & Motorbike',
            'Baby'                      => 'Baby',
            'Beauty'                    => 'Beauty',
            'Books'                     => 'Books',
            'Classical'                 => 'Classical Music',
            'Computers'                 => 'Computers & Accessories',
            'DigitalMusic'              => 'Digital Music',
            'Electronics'               => 'Electronics & Photo',
            'EverythingElse'            => 'Everything Else',
            'Fashion'                   => 'Fashion',
            'GardenAndOutdoor'          => 'Garden & Outdoors',
            'GiftCards'                 => 'Gift Cards',
            'GroceryAndGourmetFood'     => 'Grocery',
            'Handmade'                  => 'Handmade',
            'HealthPersonalCare'        => 'Health & Personal Care',
            'HomeAndKitchen'            => 'Home & Kitchen',
            'Industrial'                => 'Industrial & Scientific',
            'Jewelry'                   => 'Jewellery',
            'KindleStore'               => 'Kindle Store',
            'Lighting'                  => 'Lighting',
            'LuxuryBeauty'              => 'Luxury Beauty',
            'MobileApps'                => 'Apps & Games',
            'MoviesAndTV'               => 'DVD & Blu-ray',
            'Music'                     => 'CDs & Vinyl',
            'MusicalInstruments'        => 'Musical Instruments & DJ',
            'OfficeProducts'            => 'Stationery & Office Supplies',
            'PetSupplies'               => 'Pet Supplies',
            'Shoes'                     => 'Shoes & Bags',
            'Software'                  => 'Software',
            'SportsAndOutdoors'         => 'Sports & Outdoors',
            'ToolsAndHomeImprovement'   => 'DIY & Tools',
            'ToysAndGames'              => 'Toys & Games',
            'VHS'                       => 'VHS',
            'VideoGames'                => 'PC & Video Games',
            'Watches'                   => 'Watches',
        );
    }

}