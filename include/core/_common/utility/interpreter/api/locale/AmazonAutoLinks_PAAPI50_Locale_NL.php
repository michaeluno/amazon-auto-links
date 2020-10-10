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
 * Provides PA-API NL locale information.
 *
 * @since 4.3.4
 */
class AmazonAutoLinks_PAAPI50_Locale_NL extends AmazonAutoLinks_PAAPI50_Locale_Base {

    /**
     * @var string e.g. US, UK, CA etc.
     */
    public $sSlug = 'NL';

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
    public $sMarketPlaceHost = 'www.amazon.nl';

    /**
     * @var string
     * @see https://webservices.amazon.com/paapi5/documentation/common-request-parameters.html#host-and-region
     */
    public $sHost = 'webservices.amazon.nl';

    /**
     * @var string
     * @remark Dutch - NETHERLANDS
     */
    public $sDefaultLanguage = 'nl_NL';

    /**
     * @var string
     * @remark Euro
     */
    public $sDefaultCurrency = 'EUR';

    /**
     * API license agreement URL.
     * @var string
     * @remark Not found.
     */
    public $sLicenseURL = '';

    /**
     * @return array
     */
    public function getLanguages() {
        return array(
            'nl_NL' => __( 'Dutch - NETHERLANDS', 'amazon-auto-links' ),
        );
    }

    /**
     * @return array
     */
    public function getCurrencies() {
        return array(
            'EUR' => __( 'Euro', 'amazon-auto-links' ),
        );
    }

    /**
     * @return array
     * @remark No need to translate items.
     * @see https://webservices.amazon.com/paapi5/documentation/locale-reference/singapore.html#search-index
     */
    public function getSearchIndex() {
        return array(
            'All'	                           => 'Alle afdelingen',
            'Baby'                             => 'Babyproducten',
            'Beauty'	                       => 'Beauty en persoonlijke verzorging',
            'Computers' 	                   => 'Boeken',
            'Electronics'	                   => 'Elektronica',
            'EverythingElse'                   => 'Overig',
            'Fashion'                          => 'Kleding, schoenen en sieraden',
            'GardenAndOutdoor'                 => 'Tuin, terras en gazon',
            'GiftCards'                        => 'Cadeaubonnen',
            'GroceryAndGourmetFood'            => 'Levensmiddelen',
            'HealthPersonalCare'               => 'Gezondheid en persoonlijke verzorging',
            'HomeAndKitchen'                   => 'Wonen en keuken',
            'Industrial'                       => 'Zakelijk, industrie en wetenschap',
            'KindleStore'                      => 'Kindle Store',
            'MoviesAndTV'                      => 'Films en tv',
            'Music'                            => "Cd's en lp's",
            'MusicalInstruments'               => 'Muziekinstrumenten',
            'OfficeProducts'                   => 'Huisdierbenodigdheden',
            'Software'                         => 'Software',
            'SportsAndOutdoors'                => 'Sport en outdoor',
            'ToolsAndHomeImprovement'          => 'Klussen en gereedschap',
            'ToysAndGames'                     => 'Speelgoed en spellen',
        );
    }

}