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
 * Provides PA-API DE locale information.
 *
 * @since 4.3.4
 */
class AmazonAutoLinks_PAAPI50_Locale_DE extends AmazonAutoLinks_PAAPI50_Locale_Base {

    /**
     * @var string e.g. US, UK, CA etc.
     */
    public $sSlug = 'DE';

    /**
     * @var string
     * @see https://webservices.amazon.com/paapi5/documentation/common-request-parameters.html#host-and-region
     */
    public $sServerRegion = 'eu-west-1';

    /**
     * @var string
     * @see https://webservices.amazon.com/paapi5/documentation/locale-reference.html#locale-reference-for-product-advertising-api
     */
    public $sMarketPlaceHost = 'www.amazon.de';

    /**
     * @var string
     * @see https://webservices.amazon.com/paapi5/documentation/common-request-parameters.html#host-and-region
     */
    public $sHost = 'webservices.amazon.de';

    /**
     * @var string
     * @remark German - GERMANY
     */
    public $sDefaultLanguage = 'de_DE';

    /**
     * @var string
     * @remark Euro
     */
    public $sDefaultCurrency = 'EUR';

    /**
     * API license agreement URL.
     * @var string
     */
    public $sLicenseURL = 'https://partnernet.amazon.de/help/operating/license';

    /**
     * @var string
     * @since 4.5.0
     */
    public $sDisclaimer = 'BESTIMMTE INHALTE, DIE [IN DIESER ANWENDUNG oder AUF DIESER WEBSITE ERSCHEINEN, je nachdem, was zutrifft] STAMMT VON AMAZON EUROPE CORE S.à r.l. DIESE INHALTE WERDEN IN DER VORLIEGENDEN FORM BEREITGESTELLT UND KÖNNEN JEDERZEIT GEÄNDERT ODER ENTFERNT WERDEN.';

    /**
     * @return array
     */
    public function getLanguages() {
        return array(
            'de_DE' => __( 'German - GERMANY', 'amazon-auto-links' ),
            // ---
            'cs_CZ' => __( 'Czech - CZECHIA', 'amazon-auto-links' ),
            'en_GB' => __( 'English - UNITED KINGDOM', 'amazon-auto-links' ),
            'nl_NL' => __( 'Dutch - NETHERLANDS', 'amazon-auto-links' ),
            'pl_PL' => __( 'Polish - POLAND', 'amazon-auto-links' ),
            'tr_TR' => __( 'Turkish - TURKEY', 'amazon-auto-links' ),
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
     * @see https://webservices.amazon.com/paapi5/documentation/locale-reference/germany.html
     */
    public function getSearchIndex() {
        return array(
            'All'                       => 'Alle Kategorien',
            'AmazonVideo'               => 'Prime Video',
            'Apparel'                   => 'Bekleidung',
            'Appliances'                => 'Elektro-Großgeräte',
            'Automotive'                => 'Auto & Motorrad',
            'Baby'                      => 'Baby',
            'Beauty'                    => 'Beauty',
            'Books'                     => 'Bücher',
            'Classical'                 => 'Klassik',
            'Computers'                 => 'Computer & Zubehör',
            'DigitalMusic'              => 'Musik-Downloads',
            'Electronics'               => 'Elektronik & Foto',
            'EverythingElse'            => 'Sonstiges',
            'Fashion'                   => 'Fashion',
            'ForeignBooks'              => 'Bücher (Fremdsprachig)',
            'GardenAndOutdoor'          => 'Garten',
            'GiftCards'                 => 'Geschenkgutscheine',
            'GroceryAndGourmetFood'     => 'Lebensmittel & Getränke',
            'Handmade'                  => 'Handmade',
            'HealthPersonalCare'        => 'Drogerie & Körperpflege',
            'HomeAndKitchen'            => 'Küche, Haushalt & Wohnen',
            'Industrial'                => 'Gewerbe, Industrie & Wissenschaft',
            'Jewelry'                   => 'Schmuck',
            'KindleStore'               => 'Kindle-Shop',
            'Lighting'                  => 'Beleuchtung',
            'Luggage'                   => 'Koffer, Rucksäcke & Taschen',
            'LuxuryBeauty'              => 'Luxury Beauty',
            'Magazines'                 => 'Zeitschriften',
            'MobileApps'                => 'Apps & Spiele',
            'MoviesAndTV'               => 'DVD & Blu-ray',
            'Music'                     => 'Musik-CDs & Vinyl',
            'MusicalInstruments'        => 'Musikinstrumente & DJ-Equipment',
            'OfficeProducts'            => 'Bürobedarf & Schreibwaren',
            'PetSupplies'               => 'Haustier',
            'Photo'                     => 'Kamera & Foto',
            'Shoes'                     => 'Schuhe & Handtaschen',
            'Software'                  => 'Software',
            'SportsAndOutdoors'         => 'Sport & Freizeit',
            'ToolsAndHomeImprovement'   => 'Baumarkt',
            'ToysAndGames'              => 'Spielzeug',
            'VHS'                       => 'VHS',
            'VideoGames'                => 'Games',
            'Watches'                   => 'Uhren',
        );
    }

}