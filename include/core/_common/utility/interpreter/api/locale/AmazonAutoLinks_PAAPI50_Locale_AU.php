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
 * Provides PA-API AU locale information.
 *
 * @since 4.3.4
 */
class AmazonAutoLinks_PAAPI50_Locale_AU extends AmazonAutoLinks_PAAPI50_Locale_Base {

    /**
     * @var string e.g. US, UK, CA etc.
     */
    public $sSlug = 'AU';

    /**
     * @var string
     * @remark Override this.
     * @see https://webservices.amazon.com/paapi5/documentation/common-request-parameters.html#host-and-region
     */
    public $sServerRegion = 'us-west-2';

    /**
     * @var string
     * @see https://webservices.amazon.com/paapi5/documentation/locale-reference.html#locale-reference-for-product-advertising-api
     */
    public $sMarketPlaceHost = 'www.amazon.com.au';

    /**
     * @var string
     * @see https://webservices.amazon.com/paapi5/documentation/common-request-parameters.html#host-and-region
     */
    public $sHost = 'webservices.amazon.com.au';

    /**
     * @remark English - AUSTRALIA
     * @var string
     */
    public $sDefaultLanguage = 'en_AU';

    /**
     * @var string
     * @remark Australian Dollar
     */
    public $sDefaultCurrency = 'AUD';

    /**
     * @var string
     */
    public $sLicenseURL = 'https://affiliate-program.amazon.com.au/help/operating/policies#Associates%20Program%20IP%20License';

    /**
     * @return array
     */
    public function getLanguages() {
        return array(
            'en_AU' => __( 'English - AUSTRALIA', 'amazon-auto-links' ),
        );
    }

    /**
     * @return array
     */
    public function getCurrencies() {
        return array(
            'AUD' => __( 'Australian Dollar', 'amazon-auto-links' ),
        );
    }

    /**
     * @return array
     */
    public function getSearchIndex() {
        return array(
            'All'                       => 'All Departments', 
            'Automotive'                => 'Automotive', 
            'Baby'                      => 'Baby', 
            'Beauty'                    => 'Beauty', 
            'Books'                     => 'Books', 
            'Computers'                 => 'Computers', 
            'Electronics'               => 'Electronics', 
            'EverythingElse'            => 'Everything Else', 
            'Fashion'                   => 'Clothing & Shoes', 
            'GiftCards'                 => 'Gift Cards', 
            'HealthPersonalCare'        => 'Health,  Household & Personal Care', 
            'HomeAndKitchen'            => 'Home & Kitchen', 
            'KindleStore'               => 'Kindle Store', 
            'Lighting'                  => 'Lighting', 
            'Luggage'                   => 'Luggage & Travel Gear', 
            'MobileApps'                => 'Apps & Games', 
            'MoviesAndTV'               => 'Movies & TV', 
            'Music'                     => 'CDs & Vinyl', 
            'OfficeProducts'            => 'Stationery & Office Products', 
            'PetSupplies'               => 'Pet Supplies', 
            'Software'                  => 'Software', 
            'SportsAndOutdoors'         => 'Sports,  Fitness & Outdoors', 
            'ToolsAndHomeImprovement'   => 'Home Improvement', 
            'ToysAndGames'              => 'Toys & Games', 
            'VideoGames'                => 'Video Games', 
        );
    }
    
}