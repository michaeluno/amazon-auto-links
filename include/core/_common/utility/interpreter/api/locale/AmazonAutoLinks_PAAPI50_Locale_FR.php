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
 * Provides PA-API FR locale information.
 *
 * @since 4.3.4
 */
class AmazonAutoLinks_PAAPI50_Locale_FR extends AmazonAutoLinks_PAAPI50_Locale_Base {

    /**
     * @var string e.g. US, UK, CA etc.
     */
    public $sSlug = 'FR';

    /**
     * @var string
     * @see https://webservices.amazon.com/paapi5/documentation/common-request-parameters.html#host-and-region
     */
    public $sServerRegion = 'eu-west-1';

    /**
     * @var string
     * @see https://webservices.amazon.com/paapi5/documentation/locale-reference.html#locale-reference-for-product-advertising-api
     */
    public $sMarketPlaceHost = 'www.amazon.fr';

    /**
     * @var string
     * @see https://webservices.amazon.com/paapi5/documentation/common-request-parameters.html#host-and-region
     */
    public $sHost = 'webservices.amazon.fr';

    /**
     * @var string
     * @remark French - FRANCE
     */
    public $sDefaultLanguage = 'fr_FR';

    /**
     * @var string
     * @remark Euro
     */
    public $sDefaultCurrency = 'EUR';

    /**
     * API license agreement URL.
     * @var string
     */
    public $sLicenseURL = 'https://partenaires.amazon.fr/help/operating/license';

    /**
     * @since 4.5.0
     * @var string
     */
    public $sDisclaimer = 'UNE PARTIE DU CONTENU FIGURANT [DANS CETTE APPLICATION ou, le cas échéant, SUR CE SITE] VIENT D\'AMAZON EUROPE CORE S.à r.l. CE CONTENU EST FOURNI "EN L\'ÉTAT" ET PEUT ÊTRE MODIFIÉ OU SUPPRIMÉ À TOUT MOMENT.';

    /**
     * @return array
     */
    public function getLanguages() {
        return array(
            'fr_FR' => __( 'French - FRANCE', 'amazon-auto-links' ),
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
     * @see https://webservices.amazon.com/paapi5/documentation/locale-reference/france.html
     */
    public function getSearchIndex() {
        return array(
            'All'                       => 'Toutes nos catégories',
            'Apparel'                   => 'Vêtements et accessoires',
            'Appliances'                => 'Gros électroménager',
            'Automotive'                => 'Auto et Moto',
            'Baby'                      => 'Bébés & Puériculture',
            'Beauty'                    => 'Beauté et Parfum',
            'Books'                     => 'Livres en français',
            'Computers'                 => 'Informatique',
            'DigitalMusic'              => 'Téléchargement de musique',
            'Electronics'               => 'High-Tech',
            'EverythingElse'            => 'Autres',
            'Fashion'                   => 'Mode',
            'ForeignBooks'              => 'Livres anglais et étrangers',
            'GardenAndOutdoor'          => 'Jardin',
            'GiftCards'                 => 'Boutique chèques-cadeaux',
            'GroceryAndGourmetFood'     => 'Epicerie',
            'Handmade'                  => 'Handmade',
            'HealthPersonalCare'        => 'Hygiène et Santé',
            'HomeAndKitchen'            => 'Cuisine & Maison',
            'Industrial'                => 'Secteur industriel & scientifique',
            'Jewelry'                   => 'Bijoux',
            'KindleStore'               => 'Boutique Kindle',
            'Lighting'                  => 'Luminaires et Eclairage',
            'Luggage'                   => 'Bagages',
            'LuxuryBeauty'              => 'Beauté Prestige',
            'MobileApps'                => 'Applis & Jeux',
            'MoviesAndTV'               => 'DVD & Blu-ray',
            'Music'                     => 'Musique : CD & Vinyles',
            'MusicalInstruments'        => 'Instruments de musique & Sono',
            'OfficeProducts'            => 'Fournitures de bureau',
            'PetSupplies'               => 'Animalerie',
            'Shoes'                     => 'Chaussures et Sacs',
            'Software'                  => 'Logiciels',
            'SportsAndOutdoors'         => 'Sports et Loisirs',
            'ToolsAndHomeImprovement'   => 'Bricolage',
            'ToysAndGames'              => 'Jeux et Jouets',
            'VHS'                       => 'VHS',
            'VideoGames'                => 'Jeux vidéo',
            'Watches'                   => 'Montres',
        );
    }

}