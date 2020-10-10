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
 * Provides PA-API TR locale information.
 *
 * @since 4.3.4
 */
class AmazonAutoLinks_PAAPI50_Locale_TR extends AmazonAutoLinks_PAAPI50_Locale_Base {

    /**
     * @var string e.g. US, UK, CA etc.
     */
    public $sSlug = 'TR';

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
    public $sMarketPlaceHost = 'www.amazon.com.tr';

    /**
     * @var string
     * @see https://webservices.amazon.com/paapi5/documentation/common-request-parameters.html#host-and-region
     */
    public $sHost = 'webservices.amazon.com.tr';

    /**
     * @var string
     * @remark Turkish - TURKEY
     */
    public $sDefaultLanguage = 'tr_TR';

    /**
     * @var string
     * @remark Turkish Lira
     */
    public $sDefaultCurrency = 'TRY';

    /**
     * @return array
     */
    public function getLanguages() {
        return array(
            'tr_TR' => __( 'Turkish - TURKEY', 'amazon-auto-links' ),
        );
    }

    /**
     * @return array
     */
    public function getCurrencies() {
        return array(
            'TRY' => __( 'Turkish Lira', 'amazon-auto-links' ),
        );
    }

    /**
     * @return array
     * @remark No need to translate items.
     * @see https://webservices.amazon.com/paapi5/documentation/locale-reference/turkey.html
     */
    public function getSearchIndex() {
        return array(
            'All'                       => 'Tüm Kategoriler',
            'Baby'                      => 'Bebek',
            'Books'                     => 'Kitaplar',
            'Computers'                 => 'Bilgisayarlar',
            'Electronics'               => 'Elektronik',
            'EverythingElse'            => 'Diğer Her Şey',
            'Fashion'                   => 'Moda',
            'HomeAndKitchen'            => 'Ev ve Mutfak',
            'OfficeProducts'            => 'Ofis Ürünleri',
            'SportsAndOutdoors'         => 'Spor',
            'ToolsAndHomeImprovement'   => 'Yapı Market',
            'ToysAndGames'              => 'Oyuncaklar ve Oyunlar',
            'VideoGames'                => 'PC ve Video Oyunları',
        );
    }

}