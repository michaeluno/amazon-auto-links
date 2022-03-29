<?php
/**
 * Auto Amazon Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2022 Michael Uno
 */

/**
 *
 * Provides PA-API U.S. locale information.
 *
 * @since 4.3.4
 */
class AmazonAutoLinks_PAAPI50_Locale_US extends AmazonAutoLinks_PAAPI50_Locale_Base {

    /**
     * @var string e.g. US, UK, CA etc.
     */
    public $sSlug = 'US';

    /**
     * @var string
     * @see https://webservices.amazon.com/paapi5/documentation/common-request-parameters.html#host-and-region
     */
    public $sServerRegion = 'us-east-1';

    /**
     * The host name of the market place.
     * @var string
     * @see https://webservices.amazon.com/paapi5/documentation/locale-reference.html#locale-reference-for-product-advertising-api
     */
    public $sMarketPlaceHost = 'www.amazon.com';


    /**
     * @var string
     * @remark Override this.
     * @see https://webservices.amazon.com/paapi5/documentation/common-request-parameters.html#host-and-region
     */
    public $sHost = 'webservices.amazon.com';

    /**
     * @var string
     */
    public $sDefaultLanguage = 'en_US';

    /**
     * @var string
     */
    public $sDefaultCurrency = 'USD';

    /**
     * API license agreement URL.
     * @var string
     */
    public $sLicenseURL = 'https://affiliate-program.amazon.com/help/operating/policies/#Associates%20Program%20IP%20License';

    /**
     * @return array
     * @see    https://webservices.amazon.com/paapi5/documentation/locale-reference/united-states.html#valid-languages
     */
    public function getLanguages() {
        return array(
            'en_US' => __( 'English - UNITED STATES', 'amazon-auto-links' ),
            // ---
            'de_DE' => __( 'German - GERMANY', 'amazon-auto-links' ),
            'es_US' => __( 'Spanish - UNITED STATES', 'amazon-auto-links' ),
            'ko_KR' => __( 'Korean - KOREA', 'amazon-auto-links' ),
            'pt_BR' => __( 'Portuguese - BRAZIL', 'amazon-auto-links' ),
            'zh_CN' => __( 'Chinese - CHINA', 'amazon-auto-links' ),
            'zh_TW' => __( 'Chinese - TAIWAN', 'amazon-auto-links' ),
        );
    }

    /**
     * @return array
     * @see    https://webservices.amazon.com/paapi5/documentation/locale-reference/united-states.html#valid-currencies
     */
    public function getCurrencies() {
        return array(
            'USD' => __( 'United States Dollar', 'amazon-auto-links' ), // the default one is at the top
            'AED' => __( 'United Arab Emirates Dirham', 'amazon-auto-links' ),
            'AMD' => __( 'Armenian Dram', 'amazon-auto-links' ),
            'ARS' => __( 'Argentine Peso', 'amazon-auto-links' ),
            'AUD' => __( 'Australian Dollar', 'amazon-auto-links' ),
            'AWG' => __( 'Aruban Florin', 'amazon-auto-links' ),
            'AZN' => __( 'Azerbaijani Manat', 'amazon-auto-links' ),
            'BGN' => __( 'Bulgarian Lev', 'amazon-auto-links' ),
            'BND' => __( 'Bruneian Dollar', 'amazon-auto-links' ),
            'BOB' => __( 'Bolivian Boliviano', 'amazon-auto-links' ),
            'BRL' => __( 'Brazilian Real', 'amazon-auto-links' ),
            'BSD' => __( 'Bahamian Dollar', 'amazon-auto-links' ),
            'BZD' => __( 'Belize Dollar', 'amazon-auto-links' ),
            'CAD' => __( 'Canadian Dollar', 'amazon-auto-links' ),
            'CLP' => __( 'Chilean Peso', 'amazon-auto-links' ),
            'CNY' => __( 'Chinese Yuan Renminbi', 'amazon-auto-links' ),
            'COP' => __( 'Colombian Peso', 'amazon-auto-links' ),
            'CRC' => __( 'Costa Rican Colon', 'amazon-auto-links' ),
            'DOP' => __( 'Dominican Peso', 'amazon-auto-links' ),
            'EGP' => __( 'Egyptian Pound', 'amazon-auto-links' ),
            'EUR' => __( 'Euro', 'amazon-auto-links' ),
            'GBP' => __( 'British Pound', 'amazon-auto-links' ),
            'GHS' => __( 'Ghanaian Cedi', 'amazon-auto-links' ),
            'GTQ' => __( 'Guatemalan Quetzal', 'amazon-auto-links' ),
            'HKD' => __( 'Hong Kong Dollar', 'amazon-auto-links' ),
            'HNL' => __( 'Honduran Lempira', 'amazon-auto-links' ),
            'HUF' => __( 'Hungarian Forint', 'amazon-auto-links' ),
            'IDR' => __( 'Indonesian Rupiah', 'amazon-auto-links' ),
            'ILS' => __( 'Israeli Shekel', 'amazon-auto-links' ),
            'INR' => __( 'Indian Rupee', 'amazon-auto-links' ),
            'JMD' => __( 'Jamaican Dollar', 'amazon-auto-links' ),
            'JPY' => __( 'Japanese Yen', 'amazon-auto-links' ),
            'KES' => __( 'Kenyan Shilling', 'amazon-auto-links' ),
            'KHR' => __( 'Cambodian Riel', 'amazon-auto-links' ),
            'KRW' => __( 'South Korean Won', 'amazon-auto-links' ),
            'KYD' => __( 'Caymanian Dollar', 'amazon-auto-links' ),
            'KZT' => __( 'Kazakhstani Tenge', 'amazon-auto-links' ),
            'LBP' => __( 'Lebanese Pound', 'amazon-auto-links' ),
            'MAD' => __( 'Moroccan Dirham', 'amazon-auto-links' ),
            'MNT' => __( 'Mongolian Tughrik', 'amazon-auto-links' ),
            'MOP' => __( 'Macanese Pataca', 'amazon-auto-links' ),
            'MUR' => __( 'Mauritian Rupee', 'amazon-auto-links' ),
            'MXN' => __( 'Mexican Peso', 'amazon-auto-links' ),
            'MYR' => __( 'Malaysian Ringgit', 'amazon-auto-links' ),
            'NAD' => __( 'Namibian Dollar', 'amazon-auto-links' ),
            'NGN' => __( 'Nigerian Naira', 'amazon-auto-links' ),
            'NOK' => __( 'Norwegian Krone', 'amazon-auto-links' ),
            'NZD' => __( 'New Zealand Dollar', 'amazon-auto-links' ),
            'PAB' => __( 'Panamanian Balboa', 'amazon-auto-links' ),
            'PEN' => __( 'Peruvian Sol', 'amazon-auto-links' ),
            'PHP' => __( 'Philippine Peso', 'amazon-auto-links' ),
            'PYG' => __( 'Paraguayan Guaraní', 'amazon-auto-links' ),
            'QAR' => __( 'Qatari Riyal', 'amazon-auto-links' ),
            'RUB' => __( 'Russian Ruble', 'amazon-auto-links' ),
            'SAR' => __( 'Saudi Arabian Riyal', 'amazon-auto-links' ),
            'SGD' => __( 'Singapore Dollar', 'amazon-auto-links' ),
            'THB' => __( 'Thai Baht', 'amazon-auto-links' ),
            'TRY' => __( 'Turkish Lira', 'amazon-auto-links' ),
            'TTD' => __( 'Trinidadian Dollar', 'amazon-auto-links' ),
            'TWD' => __( 'Taiwan New Dollar', 'amazon-auto-links' ),
            'TZS' => __( 'Tanzanian Shilling', 'amazon-auto-links' ),
            'UYU' => __( 'Uruguayan Peso', 'amazon-auto-links' ),
            'VND' => __( 'Vietnamese Dong', 'amazon-auto-links' ),
            'XCD' => __( 'Eastern Caribbean Dollar', 'amazon-auto-links' ),
            'ZAR' => __( 'South African Rand', 'amazon-auto-links' ),
        );
    }

    /**
     * @see    https://webservices.amazon.com/paapi5/documentation/locale-reference/united-states.html
     * @remark No need to translate those items.
     * @return array
     */
    public function getSearchIndex() {
        return array(
            'All'                     => 'All Departments',
            'AmazonVideo'             => 'Prime Video',
            'Apparel'                 => 'Clothing & Accessories',
            'Appliances'              => 'Appliances',
            'ArtsAndCrafts'           => 'Arts, Crafts & Sewing',
            'Automotive'              => 'Automotive Parts & Accessories',
            'Baby'                    => 'Baby',
            'Beauty'                  => 'Beauty & Personal Care',
            'Books'                   => 'Books',
            'Classical'               => 'Classical',
            'Collectibles'            => 'Collectibles & Fine Art',
            'Computers'               => 'Computers',
            'DigitalMusic'            => 'Digital Music',
            'Electronics'             => 'Electronics',
            'EverythingElse'          => 'Everything Else',
            'Fashion'                 => 'Clothing, Shoes & Jewelry',
            'FashionBaby'             => 'Clothing, Shoes & Jewelry Baby',
            'FashionBoys'             => 'Clothing, Shoes & Jewelry Boys',
            'FashionGirls'            => 'Clothing, Shoes & Jewelry Girls',
            'FashionMen'              => 'Clothing, Shoes & Jewelry Men',
            'FashionWomen'            => 'Clothing, Shoes & Jewelry Women',
            'GardenAndOutdoor'        => 'Garden & Outdoor',
            'GiftCards'               => 'Gift Cards',
            'GroceryAndGourmetFood'   => 'Grocery & Gourmet Food',
            'Handmade'                => 'Handmade',
            'HealthPersonalCare'      => 'Health, Household & Baby Care',
            'HomeAndKitchen'          => 'Home & Kitchen',
            'Industrial'              => 'Industrial & Scientific',
            'Jewelry'                 => 'Jewelry',
            'KindleStore'             => 'Kindle Store',
            'LocalServices'           => 'Home & Business Services',
            'Luggage'                 => 'Luggage & Travel Gear',
            'LuxuryBeauty'            => 'Luxury Beauty',
            'Magazines'               => 'Magazine Subscriptions',
            'MobileAndAccessories'    => 'Cell Phones & Accessories',
            'MobileApps'              => 'Apps & Games',
            'MoviesAndTV'             => 'Movies & TV',
            'Music'                   => 'CDs & Vinyl',
            'MusicalInstruments'      => 'Musical Instruments',
            'OfficeProducts'          => 'Office Products',
            'PetSupplies'             => 'Pet Supplies',
            'Photo'                   => 'Camera & Photo',
            'Shoes'                   => 'Shoes',
            'Software'                => 'Software',
            'SportsAndOutdoors'       => 'Sports & Outdoors',
            'ToolsAndHomeImprovement' => 'Tools & Home Improvement',
            'ToysAndGames'            => 'Toys & Games',
            'VHS'                     => 'VHS',
            'VideoGames'              => 'Video Games',
            'Watches'                 => 'Watches',
        );
    }

}