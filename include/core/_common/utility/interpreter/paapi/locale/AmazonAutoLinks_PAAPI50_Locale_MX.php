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
 * Provides PA-API AU locale information.
 *
 * @since 4.3.4
 */
class AmazonAutoLinks_PAAPI50_Locale_MX extends AmazonAutoLinks_PAAPI50_Locale_Base {

    /**
     * @var string e.g. US, UK, CA etc.
     */
    public $sSlug = 'MX';

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
    public $sMarketPlaceHost = 'www.amazon.com.mx';

    /**
     * @var string
     * @remark Override this.
     * @see https://webservices.amazon.com/paapi5/documentation/common-request-parameters.html#host-and-region
     */
    public $sHost = 'webservices.amazon.com.mx';

    /**
     * @var string
     * @remark Spanish - MEXICO
     */
    public $sDefaultLanguage = 'es_MX';

    /**
     * @var string
     * @remark Mexican Peso
     */
    public $sDefaultCurrency = 'MXN';

    /**
     * API license agreement URL.
     * @var string
     */
    public $sLicenseURL = 'https://afiliados.amazon.com.mx/help/operating/policies#Associates%20Program%20IP%20License';

    /**
     * @var string
     * @since 4.5.0
     */
    public $sDisclaimer = 'PARTE DEL CONTENIDO QUE APARECE [EN ESTA APLICACIÓN o EN ESTE SITIO, según fuera aplicable] PROCEDE DE AMAZON. ESTE CONTENIDO SE PROPORCIONA ‘EN LAS CONDICIONES EN QUE SE ENCUENTRA’ Y ESTÁ SUJETO A MODIFICACIÓN O ELIMINACIÓN EN CUALQUIER MOMENTO.';

    /**
     * @return array
     */
    public function getLanguages() {
        return array(
            'es_MX' => __( 'Spanish - MEXICO', 'amazon-auto-links' ),
        );
    }

    /**
     * @return array
     * @remark Override this.
     */
    public function getCurrencies() {
        return array(
            'MXN' => __( 'Mexican Peso', 'amazon-auto-links' ),
        );
    }

    /**
     * @return array
     * @remark No need to translate items.
     * @see https://webservices.amazon.com/paapi5/documentation/locale-reference/mexico.html
     */
    public function getSearchIndex() {
        return array(
            'All'                       => 'Todos los departamentos',
            'Automotive'                => 'Auto',
            'Baby'                      => 'Bebé',
            'Books'                     => 'Libros',
            'Electronics'               => 'Electrónicos',
            'Fashion'                   => 'Ropa, Zapatos y Accesorios',
            'FashionBaby'               => 'Ropa, Zapatos y Accesorios Bebé',
            'FashionBoys'               => 'Ropa, Zapatos y Accesorios Niños',
            'FashionGirls'              => 'Ropa, Zapatos y Accesorios Niñas',
            'FashionMen'                => 'Ropa, Zapatos y Accesorios Hombres',
            'FashionWomen'              => 'Ropa, Zapatos y Accesorios Mujeres',
            'GroceryAndGourmetFood'     => 'Alimentos y Bebidas',
            'Handmade'                  => 'Productos Handmade',
            'HealthPersonalCare'        => 'Salud, Belleza y Cuidado Personal',
            'HomeAndKitchen'            => 'Hogar y Cocina',
            'IndustrialAndScientific'   => 'Industria y ciencia',
            'KindleStore'               => 'Tienda Kindle',
            'MoviesAndTV'               => 'Películas y Series de TV',
            'Music'                     => 'Música',
            'MusicalInstruments'        => 'Instrumentos musicales',
            'OfficeProducts'            => 'Oficina y Papelería',
            'PetSupplies'               => 'Mascotas',
            'Software'                  => 'Software',
            'SportsAndOutdoors'         => 'Deportes y Aire Libre',
            'ToolsAndHomeImprovement'   => 'Herramientas y Mejoras del Hogar',
            'ToysAndGames'              => 'Juegos y juguetes',
            'VideoGames'                => 'Videojuegos',
            'Watches'                   => 'Relojes',
        );
    }

}