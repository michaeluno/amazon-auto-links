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
     * @see    https://webservices.amazon.com/paapi5/documentation/locale-reference/japan.html
     */
    public function getSearchIndex() {
        return array(
            'All'                       => 'すべてのカテゴリー', // 'All Departments'
            'AmazonVideo'               => 'Prime Video',    // 'Prime Video'
            'Apparel'                   => '服＆アクセサリー', // 'Clothing & Accessories'
            'Appliances'                => '大型家電', // 'Large Appliances'
            'Automotive'                => '車＆バイク', // 'Car & Bike Products'
            'Baby'                      => 'ベビー＆マタニティー', // 'Baby & Maternity'
            'Beauty'                    => 'ビューティー', // 'Beauty'
            'Books'                     => '本', // 'Japanese Books'
            'Classical'                 => 'クラシック', // 'Classical'
            'Computers'                 => 'パソコン・周辺機器', // 'Computers & Accessories'
            'CreditCards'               => 'クレジットカード', // 'Credit Cards'
            'DigitalMusic'              => 'デジタルミュージック', // 'Digital Music'
            'Electronics'               => '家電＆カメラ', // 'Electronics & Cameras'
            'EverythingElse'            => 'その他', // 'Everything Else'
            'Fashion'                   => 'ファッション', // 'Fashion'
            'FashionBaby'               => 'ファッション（キッズ＆ベビー）', // 'Kids & Baby'
            'FashionMen'                => 'ファッション（メンズ）', // 'Men'
            'FashionWomen'              => 'ファッション（レディース）', // 'Women'
            'ForeignBooks'              => '洋書', // 'English Books'
            'GiftCards'                 => 'ギフトカード', // 'Gift Cards'
            'GroceryAndGourmetFood'     => '食品＆飲料', // 'Food & Beverage'
            'HealthPersonalCare'        => '健康＆パーソナルケア', // 'Health & Personal Care'
            'Hobbies'                   => 'ホビー', // 'Hobby'
            'HomeAndKitchen'            => 'ホーム＆キッチン', // 'Kitchen & Housewares'
            'Industrial'                => '産業・研究開発用品', // 'Industrial & Scientific'
            'Jewelry'                   => '産業・研究開発用品', // 'Jewelry'
            'KindleStore'               => 'Kindleストア', // 'Kindle Store'
            'MobileApps'                => 'アプリ＆ゲーム', // 'Apps & Games'
            'MoviesAndTV'               => '動画＆TV', // 'Movies & TV'
            'Music'                     => 'ミュージック', // 'Music'
            'MusicalInstruments'        => '楽器・音響機器', // 'Musical Instruments'
            'OfficeProducts'            => '文房具・オフィス用品', // 'Stationery and Office Products'
            'PetSupplies'               => 'ペット用品', // 'Pet Supplies'
            'Shoes'                     => 'シューズ＆バッグ', // 'Shoes & Bags'
            'Software'                  => 'PCソフト', // 'Software'
            'SportsAndOutdoors'         => 'スポーツ＆アウトドア', // 'Sports'
            'ToolsAndHomeImprovement'   => 'DIY・工具・ガーデン', // 'DIY, Tools & Garden'
            'Toys'                      => 'おもちゃ', // 'Toys'
            'VideoGames'                => 'ビデオゲーム', // 'Computer & Video Games'
            'Watches'                   => '腕時計', // 'Watches'
        );
    }

}