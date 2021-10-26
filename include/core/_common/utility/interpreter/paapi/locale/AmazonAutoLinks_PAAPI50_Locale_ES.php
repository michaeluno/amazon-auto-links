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
 * Provides PA-API ES locale information.
 *
 * @since 4.3.4
 */
class AmazonAutoLinks_PAAPI50_Locale_ES extends AmazonAutoLinks_PAAPI50_Locale_Base {

    /**
     * @var string e.g. US, UK, CA etc.
     */
    public $sSlug = 'ES';

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
    public $sMarketPlaceHost = 'www.amazon.es';

    /**
     * @var string
     * @see https://webservices.amazon.com/paapi5/documentation/common-request-parameters.html#host-and-region
     */
    public $sHost = 'webservices.amazon.es';

    /**
     * @var string
     * @remark Spanish - SPAIN
     */
    public $sDefaultLanguage = 'es_ES';

    /**
     * @var string
     * @remark Euro.
     */
    public $sDefaultCurrency = 'EUR';

    /**
     * API license agreement URL.
     * @var string
     */
    public $sLicenseURL = 'https://afiliados.amazon.es/help/operating/policies#Associates%20Program%20IP%20License';

    /**
     * @var string
     * @since 4.5.0
     */
    public $sDisclaimer = 'PARTE DEL CONTENIDO QUE APARECE [EN ESTA APLICACIÓN o EN ESTE SITIO, según proceda] PROCEDE DE AMAZON. ESTE CONTENIDO SE OFRECE EN SU CONDICIÓN ACTUAL Y PODRÁ MODIFICARSE O ELIMINARSE EN CUALQUIER MOMENTO.';

    /**
     * @return array
     */
    public function getLanguages() {
        return array(
            'es_ES' => __( 'Spanish - SPAIN', 'amazon-auto-links' ),
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
     * @see https://webservices.amazon.com/paapi5/documentation/locale-reference/spain.html
     */
    public function getSearchIndex() {
        return array(
            'All'                       => 'Todos los departamentos',
            'Apparel'                   => 'Ropa y accesorios',
            'Appliances'                => 'Grandes electrodomésticos',
            'Automotive'                => 'Coche y moto',
            'Baby'                      => 'Bebé',
            'Beauty'                    => 'Belleza',
            'Books'                     => 'Libros',
            'Computers'                 => 'Informática',
            'DigitalMusic'              => 'Música Digital',
            'Electronics'               => 'Electrónica',
            'EverythingElse'            => 'Otros Productos',
            'Fashion'                   => 'Moda',
            'ForeignBooks'              => 'Libros en idiomas extranjeros',
            'GardenAndOutdoor'          => 'Jardín',
            'GiftCards'                 => 'Cheques regalo',
            'GroceryAndGourmetFood'     => 'Alimentación y bebidas',
            'Handmade'                  => 'Handmade',
            'HealthPersonalCare'        => 'Salud y cuidado personal',
            'HomeAndKitchen'            => 'Hogar y cocina',
            'Industrial'                => 'Industria y ciencia',
            'Jewelry'                   => 'Joyería',
            'KindleStore'               => 'Tienda Kindle',
            'Lighting'                  => 'Iluminación',
            'Luggage'                   => 'Equipaje',
            'MobileApps'                => 'Appstore para Android',
            'MoviesAndTV'               => 'Películas y TV',
            'Music'                     => 'Música: CDs y vinilos',
            'MusicalInstruments'        => 'Instrumentos musicales',
            'OfficeProducts'            => 'Oficina y papelería',
            'PetSupplies'               => 'Productos para mascotas',
            'Shoes'                     => 'Zapatos y complementos',
            'Software'                  => 'Software',
            'SportsAndOutdoors'         => 'Deportes y aire libre',
            'ToolsAndHomeImprovement'   => 'Bricolaje y herramientas',
            'ToysAndGames'              => 'Juguetes y juegos',
            'Vehicles'                  => 'Coche - renting',
            'VideoGames'                => 'Videojuegos',
            'Watches'                   => 'Relojes',
        );
    }

}