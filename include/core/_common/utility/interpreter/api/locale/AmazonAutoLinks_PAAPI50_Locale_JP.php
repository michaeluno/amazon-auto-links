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
 * Provides PA-API JP locale information.
 *
 * @since 4.3.4
 */
class AmazonAutoLinks_PAAPI50_Locale_JP extends AmazonAutoLinks_PAAPI50_Locale_Base {

    /**
     * @var string e.g. US, UK, CA etc.
     */
    public $sSlug = 'JP';

    /**
     * @var string
     * @see https://webservices.amazon.com/paapi5/documentation/common-request-parameters.html#host-and-region
     */
    public $sServerRegion = 'us-west-2';

    /**
     * The host name of the market place.
     * @var string
     * @see https://webservices.amazon.com/paapi5/documentation/locale-reference.html#locale-reference-for-product-advertising-api
     */
    public $sMarketPlaceHost = 'www.amazon.co.jp';

    /**
     * @var string
     * @see https://webservices.amazon.com/paapi5/documentation/common-request-parameters.html#host-and-region
     */
    public $sHost = 'webservices.amazon.co.jp';

    /**
     * @var string
     * @remark Japanese - JAPAN
     */
    public $sDefaultLanguage = 'ja_JP';

    /**
     * @var string
     * @remark Japanese Yen
     */
    public $sDefaultCurrency = 'JPY';

    /**
     * API license agreement URL.
     * @var string
     */
    public $sLicenseURL = 'https://affiliate.amazon.co.jp/help/operating/paapilicenseagreement';

    /**
     * @var string
     * @since 4.5.0
     */
    public $sDisclaimer = '［「本アプリケーション内」／「本サイト上」］で表示されるコンテンツの一部は、アマゾンジャパン合同会社またはその関連会社により提供されたものです。これらのコンテンツは「現状有姿」で提供されており、随時変更または削除される場合があります。';

    /**
     * @return array
     */
    public function getLanguages() {
        return array(
            'ja_JP' => __( 'Japanese - JAPAN', 'amazon-auto-links' ),
            // ----
            'en_US' => __( 'English - UNITED STATES', 'amazon-auto-links' ),
            'zh_CN' => __( 'Chinese - CHINA', 'amazon-auto-links' ),
        );
    }

    /**
     * @return array
     */
    public function getCurrencies() {
        return array(
            'JPY' => __( 'Japanese Yen', 'amazon-auto-links' ),
        );
    }

    /**
     * @return array
     * @remark Unlike the other locales, these items should be translated.
     * @see https://webservices.amazon.com/paapi5/documentation/locale-reference/japan.html
     */
    public function getSearchIndex() {
        return array(
            'All'                       => __( 'All Departments', 'amazon-auto-links' ),
            'AmazonVideo'               => __( 'Prime Video', 'amazon-auto-links' ),
            'Apparel'                   => __( 'Clothing & Accessories', 'amazon-auto-links' ),
            'Appliances'                => __( 'Large Appliances', 'amazon-auto-links' ),
            'Automotive'                => __( 'Car & Bike Products', 'amazon-auto-links' ),
            'Baby'                      => __( 'Baby & Maternity', 'amazon-auto-links' ),
            'Beauty'                    => __( 'Beauty', 'amazon-auto-links' ),
            'Books'                     => __( 'Japanese Books', 'amazon-auto-links' ),
            'Classical'                 => __( 'Classical', 'amazon-auto-links' ),
            'Computers'                 => __( 'Computers & Accessories', 'amazon-auto-links' ),
            'CreditCards'               => __( 'Credit Cards', 'amazon-auto-links' ),
            'DigitalMusic'              => __( 'Digital Music', 'amazon-auto-links' ),
            'Electronics'               => __( 'Electronics & Cameras', 'amazon-auto-links' ),
            'EverythingElse'            => __( 'Everything Else', 'amazon-auto-links' ),
            'Fashion'                   => __( 'Fashion', 'amazon-auto-links' ),
            'FashionBaby'               => __( 'Kids & Baby', 'amazon-auto-links' ),
            'FashionMen'                => __( 'Men', 'amazon-auto-links' ),
            'FashionWomen'              => __( 'Women', 'amazon-auto-links' ),
            'ForeignBooks'              => __( 'English Books', 'amazon-auto-links' ),
            'GiftCards'                 => __( 'Gift Cards', 'amazon-auto-links' ),
            'GroceryAndGourmetFood'     => __( 'Food & Beverage', 'amazon-auto-links' ),
            'HealthPersonalCare'        => __( 'Health & Personal Care', 'amazon-auto-links' ),
            'Hobbies'                   => __( 'Hobby', 'amazon-auto-links' ),
            'HomeAndKitchen'            => __( 'Kitchen & Housewares', 'amazon-auto-links' ),
            'Industrial'                => __( 'Industrial & Scientific', 'amazon-auto-links' ),
            'Jewelry'                   => __( 'Jewelry', 'amazon-auto-links' ),
            'KindleStore'               => __( 'Kindle Store', 'amazon-auto-links' ),
            'MobileApps'                => __( 'Apps & Games', 'amazon-auto-links' ),
            'MoviesAndTV'               => __( 'Movies & TV', 'amazon-auto-links' ),
            'Music'                     => __( 'Music', 'amazon-auto-links' ),
            'MusicalInstruments'        => __( 'Musical Instruments', 'amazon-auto-links' ),
            'OfficeProducts'            => __( 'Stationery and Office Products', 'amazon-auto-links' ),
            'PetSupplies'               => __( 'Pet Supplies', 'amazon-auto-links' ),
            'Shoes'                     => __( 'Shoes & Bags', 'amazon-auto-links' ),
            'Software'                  => __( 'Software', 'amazon-auto-links' ),
            'SportsAndOutdoors'         => __( 'Sports', 'amazon-auto-links' ),
            'ToolsAndHomeImprovement'   => __( 'DIY, Tools & Garden', 'amazon-auto-links' ),
            'Toys'                      => __( 'Toys', 'amazon-auto-links' ),
            'VideoGames'                => __( 'Computer & Video Games', 'amazon-auto-links' ),
            'Watches'                   => __( 'Watches', 'amazon-auto-links' ),
        );
    }

}