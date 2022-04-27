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
 * Provides PA-API Poland locale information.
 *
 * @since 4.5.10
 */
class AmazonAutoLinks_PAAPI50_Locale_PL extends AmazonAutoLinks_PAAPI50_Locale_Base {

    /**
     * @var string e.g. US, UK, CA etc.
     */
    public $sSlug = 'PL';

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
    public $sMarketPlaceHost = 'www.amazon.pl';


    /**
     * @var string
     * @remark Override this.
     * @see https://webservices.amazon.com/paapi5/documentation/common-request-parameters.html#host-and-region
     */
    public $sHost = 'webservices.amazon.pl';

    /**
     * @var string
     */
    public $sDefaultLanguage = 'pl_PL';

    /**
     * @var string
     */
    public $sDefaultCurrency = 'PLN';

    /**
     * API license agreement URL.
     * @var string
     */
    public $sLicenseURL = 'https://affiliate-program.amazon.pl/help/operating/agreement/';

    /**
     * @var string
     */
    public $sDisclaimer = 'CERTAIN CONTENT THAT APPEARS [IN THIS APPLICATION or ON THIS SITE, as applicable] COMES FROM AMAZON. THIS CONTENT IS PROVIDED ‘AS IS’ AND IS SUBJECT TO CHANGE OR REMOVAL AT ANY TIME.';


    /**
     * @return array
     * @see    https://webservices.amazon.com/paapi5/documentation/locale-reference/poland.html#valid-languages
     */
    public function getLanguages() {
        return array(
            'pl_PL' => __( 'Polish - POLAND', 'amazon-auto-links' ),
        );
    }

    /**
     * @return array
     * @see    https://webservices.amazon.com/paapi5/documentation/locale-reference/poland.html#valid-currencies
     */
    public function getCurrencies() {
        return array(
            'PLN' => __( 'Polish złoty', 'amazon-auto-links' ), // the default one is at the top
        );
    }

    /**
     * @see    https://webservices.amazon.com/paapi5/documentation/locale-reference/united-states.html
     * @remark No need to translate those items.
     * @return array
     */
    public function getSearchIndex() {
        return array(
            'All'                       => 'Wszystkie kategorie',
            'ArtsAndCrafts'             => 'Arts & crafts',
            'Automotive'                => 'Motoryzacja',
            'Baby'                      => 'Dziecko',
            'Beauty'                    => 'Uroda',
            'Books'                     => 'Książki',
            'Electronics'               => 'Elektronika',
            'Fashion'                   => 'Odzież, obuwie i akcesoria',
            'GardenAndOutdoor'          => 'Ogród',
            'GiftCards'                 => 'Karty podarunkowe',
            'HealthPersonalCare'        => 'Zdrowie i gospodarstwo domowe',
            'HomeAndKitchen'            => 'Dom i kuchnia',
            'Industrial'                => 'Biznes, przemysł i nauka',
            'MoviesAndTV'               => 'Filmy i programy TV',
            'Music'                     => 'Muzyka',
            'MusicalInstruments'        => 'Instrumenty muzyczne',
            'OfficeProducts'            => 'Biuro',
            'PetSupplies'               => 'Zwierzęta',
            'Software'                  => 'Oprogramowanie',
            'SportsAndOutdoors'         => 'Sport i turystyka',
            'ToolsAndHomeImprovement'   => 'Renowacja domu',
            'ToysAndGames'              => 'Zabawki i gry',
            'VideoGames'                => 'Gry wideo',
        );
    }

}