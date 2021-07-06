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
 * Provides PA-API IN locale information.
 *
 * @since 4.3.4
 */
class AmazonAutoLinks_PAAPI50_Locale_IN extends AmazonAutoLinks_PAAPI50_Locale_Base {
    
    /**
     * @var string e.g. US, UK, CA etc.
     */
    public $sSlug = 'IN';

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
    public $sMarketPlaceHost = 'www.amazon.in';

    /**
     * @var string
     * @see https://webservices.amazon.com/paapi5/documentation/common-request-parameters.html#host-and-region
     */
    public $sHost = 'webservices.amazon.in';

    /**
     * @var string
     * @remark English - INDIA
     */
    public $sDefaultLanguage = 'en_IN';

    /**
     * @var string
     * @remark Indian Rupee
     */
    public $sDefaultCurrency = 'INR';

    /**
     * API license agreement URL.
     * @var string
     */
    public $sLicenseURL = 'https://affiliate-program.amazon.in/help/operating/agreement';

    /**
     * @var string
     * @since 4.5.0
     */
    public $sDisclaimer = 'CERTAIN CONTENT THAT APPEARS [IN THIS APPLICATION or ON THIS SITE, as applicable] COMES FROM AMAZON SELLER SERVICES PRIVATE LIMITED. THIS CONTENT IS PROVIDED ‘AS IS’ AND IS SUBJECT TO CHANGE OR REMOVAL AT ANY TIME.';

    /**
     * @return array
     */
    public function getLanguages() {
        return array(
            'en_IN' => __( 'English - INDIA', 'amazon-auto-links' ),
        );
    }

    /**
     * @return array
     */
    public function getCurrencies() {
        return array(
            'INR' => __( 'Indian Rupee', 'amazon-auto-links' ),
        );
    }

    /**
     * @return array
     * @remark No need to translate items.
     * @see https://webservices.amazon.com/paapi5/documentation/locale-reference/india.html
     */
    public function getSearchIndex() {
        return array(
            'All'                       => 'All Categories',
            'Apparel'                   => 'Clothing & Accessories', 
            'Appliances'                => 'Appliances', 
            'Automotive'                => 'Car & Motorbike', 
            'Baby'                      => 'Baby', 
            'Beauty'                    => 'Beauty', 
            'Books'                     => 'Books', 
            'Collectibles'              => 'Collectibles', 
            'Computers'                 => 'Computers & Accessories', 
            'Electronics'               => 'Electronics', 
            'EverythingElse'            => 'Everything Else', 
            'Fashion'                   => 'Amazon Fashion', 
            'Furniture'                 => 'Furniture', 
            'GardenAndOutdoor'          => 'Garden & Outdoors', 
            'GiftCards'                 => 'Gift Cards', 
            'GroceryAndGourmetFood'     => 'Grocery & Gourmet Foods', 
            'HealthPersonalCare'        => 'Health & Personal Care', 
            'HomeAndKitchen'            => 'Home & Kitchen', 
            'Industrial'                => 'Industrial & Scientific', 
            'Jewelry'                   => 'Jewellery', 
            'KindleStore'               => 'Kindle Store', 
            'Luggage'                   => 'Luggage & Bags', 
            'LuxuryBeauty'              => 'Luxury Beauty', 
            'MobileApps'                => 'Apps & Games', 
            'MoviesAndTV'               => 'Movies & TV Shows', 
            'Music'                     => 'Music', 
            'MusicalInstruments'        => 'Musical Instruments', 
            'OfficeProducts'            => 'Office Products', 
            'PetSupplies'               => 'Pet Supplies', 
            'Shoes'                     => 'Shoes & Handbags', 
            'Software'                  => 'Software', 
            'SportsAndOutdoors'         => 'Sports, Fitness & Outdoors', 
            'ToolsAndHomeImprovement'   => 'Tools & Home Improvement', 
            'ToysAndGames'              => 'Toys & Games', 
            'VideoGames'                => 'Video Games',
            'Watches'                   => 'Watches',
        );
    }

}