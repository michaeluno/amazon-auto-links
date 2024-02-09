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
class AmazonAutoLinks_PAAPI50_Locale_BE extends AmazonAutoLinks_PAAPI50_Locale_Base {

    /**
     * @var string e.g. US, UK, CA etc.
     */
    public $sSlug = 'BE';

    /**
     * @var string
     * @see https://webservices.amazon.com/paapi5/documentation/common-request-parameters.html#host-and-region
     */
    public $sServerRegion = 'eu-west-1';

    /**
     * @var string
     * @see https://webservices.amazon.com/paapi5/documentation/locale-reference.html#locale-reference-for-product-advertising-api
     */
    public $sMarketPlaceHost = 'www.amazon.com.be';

    /**
     * @var string
     * @see https://webservices.amazon.com/paapi5/documentation/common-request-parameters.html#host-and-region
     */
    public $sHost = 'webservices.amazon.com.be';

    /**
     * @var string
     * @remark Portuguese - BRAZIL
     */
    public $sDefaultLanguage = 'fr_BE';

    /**
     * @var string
     * @remark Brazilian Real
     */
    public $sDefaultCurrency = 'EUR';

    /**
     * API license agreement URL.
     * @var string
     */
    public $sLicenseURL = 'https://affiliate-program.amazon.com.be/help/operating/agreement';

    /**
     * @return array
     */
    public function getLanguages() {
        return array(
            'fr_BE' => __( 'French - BELGIUM', 'amazon-auto-links' ),
            'nl_BE' => __( 'Dutch - BELGIUM', 'amazon-auto-links' ),
            'en_GB' => __( 'English - UNITED KINGDOM', 'amazon-auto-links' ),
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
     */
    public function getSearchIndex() {
        return array(
            'All'                       => 'Toutes nos catégories',
            'Automotive'                => 'Auto et Moto',
            'Baby'                      => 'Bébé',
            'Beauty'                    => 'Beauté et Parfum',
            'Books'                     => 'Livres',
            'Electronics'               => 'High-Tech',
            'Fashion'                   => 'Mode',
            'Garden'                    => 'Jardin',
            'GiftCards'                 => 'Boutique chèques-cadeaux',
            'HomeImprovement'           => 'Bricolage',
            'HealthPersonalCare'        => 'Santé & Hygiène personnelle',
            'Industrial'                => 'Secteur industriel et scientifique',
            'Music'                     => 'Musique : CD & Vinyles',
            'MusicalInstruments'        => 'Instruments de musique',
            'MoviesAndTV	Cinéma'     => 'Cinéma & TV',
            'OfficeProducts'            => 'Office Produits de bureau',
            'PetSupplies'               => 'Animalerie',
            'Software'                  => 'Logiciels',
            'SportsAndOutdoors'         => 'Sports & Activités en plein-air',
            'Toys'                      => 'Jeux et Jouets',
            'VideoGames'                => 'Jeux vidéo',
        );

    }

}