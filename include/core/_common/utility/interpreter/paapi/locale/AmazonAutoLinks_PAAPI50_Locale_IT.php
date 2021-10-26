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
 * Provides PA-API IT locale information.
 *
 * @since 4.3.4
 */
class AmazonAutoLinks_PAAPI50_Locale_IT extends AmazonAutoLinks_PAAPI50_Locale_Base {

    /**
     * @var string e.g. US, UK, CA etc.
     */
    public $sSlug = 'IT';

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
    public $sMarketPlaceHost = 'www.amazon.it';

    /**
     * @var string
     * @see https://webservices.amazon.com/paapi5/documentation/common-request-parameters.html#host-and-region
     */
    public $sHost = 'webservices.amazon.it';

    /**
     * @var string
     * @remark Italian - ITALY
     */
    public $sDefaultLanguage = 'it_IT';

    /**
     * @var string
     * @remark Euro
     */
    public $sDefaultCurrency = 'EUR';

    /**
     * API license agreement URL.
     * @var string
     */
    public $sLicenseURL = 'https://programma-affiliazione.amazon.it/help/operating/license';

    /**
     * @var string
     * @since 4.5.0
     */
    public $sDisclaimer = 'DETERMINATI CONTENUTI CHE COMPAIONO [IN QUESTA APPLICAZIONE o SU QUESTO SITO, in base al caso] PROVENGONO DA AMAZON EUROPE CORE S.à r.l. QUESTI CONTENUTI VENGONO FORNITI "COSÌ COME SONO" E SONO SOGGETTI A MODIFICHE O RIMOZIONE IN QUALSIASI MOMENTO.';

    /**
     * @return array
     */
    public function getLanguages() {
        return array(
            'it_IT' => __( 'Italian - ITALY', 'amazon-auto-links' ),
        );
    }

    /**
     * @return array
     * @remark Override this.
     */
    public function getCurrencies() {
        return array(
            'EUR' => __( 'Euro', 'amazon-auto-links' ),
        );
    }

    /**
     * @return array
     * @remark No need to translate items.
     * @see https://webservices.amazon.com/paapi5/documentation/locale-reference/italy.html
     */
    public function getSearchIndex() {
        return array(
            'All'                       => 'Tutte le categorie',
            'Apparel'                   => 'Abbigliamento',
            'Appliances'                => 'Grandi elettrodomestici',
            'Automotive'                => 'Auto e Moto',
            'Baby'                      => 'Prima infanzia',
            'Beauty'                    => 'Bellezza',
            'Books'                     => 'Libri',
            'Computers'                 => 'Informatica',
            'DigitalMusic'              => 'Musica Digitale',
            'Electronics'               => 'Elettronica',
            'EverythingElse'            => 'Altro',
            'Fashion'                   => 'Moda',
            'ForeignBooks'              => 'Libri in altre lingue',
            'GardenAndOutdoor'          => 'Giardino e giardinaggio',
            'GiftCards'                 => 'Buoni Regalo',
            'GroceryAndGourmetFood'     => 'Alimentari e cura della casa',
            'Handmade'                  => 'Handmade',
            'HealthPersonalCare'        => 'Salute e cura della persona',
            'HomeAndKitchen'            => 'Casa e cucina',
            'Industrial'                => 'Industria e Scienza',
            'Jewelry'                   => 'Gioielli',
            'KindleStore'               => 'Kindle Store',
            'Lighting'                  => 'Illuminazione',
            'Luggage'                   => 'Valigeria',
            'MobileApps'                => 'App e Giochi',
            'MoviesAndTV'               => 'Film e TV',
            'Music'                     => 'CD e Vinili',
            'MusicalInstruments'        => 'Strumenti musicali e DJ',
            'OfficeProducts'            => 'Cancelleria e prodotti per ufficio',
            'PetSupplies'               => 'Prodotti per animali domestici',
            'Shoes'                     => 'Scarpe e borse',
            'Software'                  => 'Software',
            'SportsAndOutdoors'         => 'Sport e tempo libero',
            'ToolsAndHomeImprovement'   => 'Fai da te',
            'ToysAndGames'              => 'Giochi e giocattoli',
            'VideoGames'                => 'Videogiochi',
            'Watches'                   => 'Orologi',
        );
    }

}