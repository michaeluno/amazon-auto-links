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
 * Provides PA-API Swedish locale information.
 *
 * @since 4.4.0
 */
class AmazonAutoLinks_PAAPI50_Locale_SE extends AmazonAutoLinks_PAAPI50_Locale_Base {

    /**
     * @var string e.g. US, UK, CA etc.
     */
    public $sSlug = 'SE';

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
    public $sMarketPlaceHost = 'www.amazon.se';


    /**
     * @var string
     * @remark Override this.
     * @see https://webservices.amazon.com/paapi5/documentation/common-request-parameters.html#host-and-region
     */
    public $sHost = 'webservices.amazon.se';

    /**
     * @var string
     */
    public $sDefaultLanguage = 'sv_SE';

    /**
     * @var string
     */
    public $sDefaultCurrency = 'SEK';

    /**
     * API license agreement URL.
     * @var string
     */
    public $sLicenseURL = 'https://affiliate-program.amazon.se/help/operating/policies/#Associates%20Program%20IP%20License';

    /**
     * @var string
     * @since 4.5.0
     */
    public $sDisclaimer = 'CERTAIN CONTENT THAT APPEARS [IN THIS APPLICATION or ON THIS SITE, as applicable] COMES FROM AMAZON. THIS CONTENT IS PROVIDED ‘AS IS’ AND IS SUBJECT TO CHANGE OR REMOVAL AT ANY TIME.';

    /**
     * @return array
     * @see    https://webservices.amazon.com/paapi5/documentation/locale-reference/united-states.html#valid-languages
     */
    public function getLanguages() {
        return array(
            'sv_SE'	=> __( 'Swedish - SWEDEN', 'amazon-auto-links' ),
        );
    }

    /**
     * @return array
     * @see    https://webservices.amazon.com/paapi5/documentation/locale-reference/united-states.html#valid-currencies
     */
    public function getCurrencies() {
        return array(
            'SEK' => __( 'Swedish Krona', 'amazon-auto-links' ),
        );
    }

    /**
     * @see    https://webservices.amazon.com/paapi5/documentation/locale-reference/united-states.html
     * @remark No need to translate those items.
     * @return array
     */
    public function getSearchIndex() {
        return array(
            'All'                     => 'Alla avdelningar',
            'Automotive'              => 'Delar och tillbehör till bilar',
            'Baby'                    => 'Baby',
            'Beauty'                  => 'Skönhet och kroppsvård',
            'Books'                   => 'Böcker',
            'Electronics'             => 'Elektronik',
            'Fashion'                 => 'Kläder, skor och smycken',
            'GroceryAndGourmetFood'	  => 'Livsmedel och gourmetmat',
            'HealthPersonalCare'      => 'Hälsa, hushåll och barnvård',
            'HomeAndKitchen'          => 'Hem',
            'MoviesAndTV'             => 'Filmer och TV',
            'Music'                   => 'CD och vinyl',
            'OfficeProducts'          => 'Kontorsprodukter',
            'PetSupplies'             => 'Husdjursprodukter',
            'SportsAndOutdoors'	      => 'Sport och outdoor',
            'ToolsAndHomeImprovement' => 'Verktyg och husrenovering',
            'ToysAndGames'            => 'Leksaker och spel',
            'VideoGames'              => 'Videospel',
        );
    }

}