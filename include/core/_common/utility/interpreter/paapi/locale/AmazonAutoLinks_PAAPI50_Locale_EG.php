<?php
/**
 * Auto Amazon Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2024 Michael Uno
 */

/**
 *
 * Provides PA-API BR locale information.
 *
 * @since 5.4.0
 */
class AmazonAutoLinks_PAAPI50_Locale_EG extends AmazonAutoLinks_PAAPI50_Locale_Base {

    /**
     * @var string e.g. US, UK, CA etc.
     */
    public $sSlug = 'EG';

    /**
     * @var string
     * @see https://webservices.amazon.com/paapi5/documentation/common-request-parameters.html#host-and-region
     */
    public $sServerRegion = 'eu-west-1';

    /**
     * @var string
     * @see https://webservices.amazon.com/paapi5/documentation/locale-reference.html#locale-reference-for-product-advertising-api
     */
    public $sMarketPlaceHost = 'www.amazon.eg';

    /**
     * @var string
     * @see https://webservices.amazon.com/paapi5/documentation/common-request-parameters.html#host-and-region
     */
    public $sHost = 'webservices.amazon.eg';

    /**
     * @var string
     * @remark Portuguese - BRAZIL
     */
    public $sDefaultLanguage = 'en_AE';

    /**
     * @var string
     * @remark Brazilian Real
     */
    public $sDefaultCurrency = 'EGP';

    /**
     * API license agreement URL.
     * @var string
     */
    public $sLicenseURL = 'https://affiliate-program.amazon.eg/help/operating/agreement';

    /**
     * @return array
     */
    public function getLanguages() {
        return array(
            'en_AE' => __( 'English - UNITED ARAB EMIRATES', 'amazon-auto-links' ),
            'ar_AE' => __( 'Arabic - UNITED ARAB EMIRATES', 'amazon-auto-links' ),
        );
    }

    /**
     * @return array
     */
    public function getCurrencies() {
        return array(
            'EGP' => __( 'Egyptian pound', 'amazon-auto-links' ),
        );
    }

    /**
     * @return array
     * @remark No need to translate items.
     */
    public function getSearchIndex() {
        return array(
            'All'                       => 'All',
            'ArtsAndCrafts'             => 'Arts, Crafts & Sewing',
            'Automotive'                => 'Automotive Parts & Accessories',
            'Baby'                      => 'Baby',
            'Beauty'                    => 'Beauty & Personal Care',
            'Books'                     => 'Books',
            'Electronics'               => 'Electronics',
            'Fashion'                   => 'Amazon Fashion',
            'Garden'                    => 'Home & Garden',
            'Grocery'                   => 'Grocery & Gourmet Food',
            'HealthPersonalCareGrocery' => 'Health, Household & Baby Care',
            'Home'                      => 'Home Related',
            'HomeImprovement'           => 'Tools & Home Improvement',
            'Industrial'                => 'Industrial & Scientific',
            'MusicalInstruments'        => 'Musical Instruments',
            'OfficeProducts'            => 'Office Products',
            'PetSupplies'               => 'Pet Supplies',
            'Software'                  => 'Pet Software',
            'SportsAndOutdoors'         => 'Sports',
            'Toys'                      => 'Toys & Games',
            'VideoGames'                => 'Video Games',
        );

    }

}