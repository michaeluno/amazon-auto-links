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
 * Provides PA-API SG locale information.
 *
 * @since 4.3.4
 */
class AmazonAutoLinks_PAAPI50_Locale_SG extends AmazonAutoLinks_PAAPI50_Locale_Base {

    /**
     * @var string e.g. US, UK, CA etc.
     */
    public $sSlug = 'SG';

    /**
     * @var string
     * @remark Override this.
     * @see https://webservices.amazon.com/paapi5/documentation/common-request-parameters.html#host-and-region
     */
    public $sServerRegion = 'us-west-2';

    /**
     * The host name of the market place.
     * @var string
     * @see https://webservices.amazon.com/paapi5/documentation/locale-reference.html#locale-reference-for-product-advertising-api
     */
    public $sMarketPlaceHost = 'www.amazon.sg';

    /**
     * @var string
     * @see https://webservices.amazon.com/paapi5/documentation/common-request-parameters.html#host-and-region
     */
    public $sHost = 'webservices.amazon.sg';

    /**
     * @var string
     * @remark English - SINGAPORE
     */
    public $sDefaultLanguage = 'en_SG';

    /**
     * @var string
     * @remark Singapore Dollar
     */
    public $sDefaultCurrency = 'SGD';

    /**
     * API license agreement URL.
     * @var string
     * @remark Not found.
     */
    public $sLicenseURL = 'https://affiliate-program.amazon.sg/help/operating/policies';

    /**
     * @var string
     * @since 4.5.0
     */
    public $sDisclaimer = 'CERTAIN CONTENT THAT APPEARS [IN THIS APPLICATION or ON THIS SITE, as applicable] COMES FROM AMAZON. THIS CONTENT IS PROVIDED ‘AS IS’ AND IS SUBJECT TO CHANGE OR REMOVAL AT ANY TIME.';

    /**
     * @return array
     */
    public function getLanguages() {
        return array(
            'en_SG' => __( 'English - SINGAPORE', 'amazon-auto-links' ),
        );
    }

    /**
     * @return array
     */
    public function getCurrencies() {
        return array(
            'SGD' => __( 'Singapore Dollar', 'amazon-auto-links' ),
        );
    }

    /**
     * @return array
     * @remark No need to translate items.
     * @see https://webservices.amazon.com/paapi5/documentation/locale-reference/singapore.html#search-index
     */
    public function getSearchIndex() {
        return array(
            'All'	                    => 'All Departments',
            'Automotive'	            => 'Automotive',
            'Baby'	                    => 'Baby',
            'Beauty'	                => 'Beauty & Personal Care',
            'Computers' 	            => 'Computers',
            'Electronics'	            => 'Electronics',
            'GroceryAndGourmetFood'     => 'Grocery',
            'HealthPersonalCare'        => 'Health, Household & Personal Care',
            'HomeAndKitchen'            => 'Home, Kitchen & Dining',
            'OfficeProducts'            => 'Office Products',
            'PetSupplies'               => 'Pet Supplies',
            'SportsAndOutdoors'         => 'Sports & Outdoors',
            'ToolsAndHomeImprovement'   => 'Tools & Home Improvement',
            'ToysAndGames'              => 'Toys & Games',
            'VideoGames'                => 'Video Games',
        );
    }

}