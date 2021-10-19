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
 * Creates Amazon product links by ItemSearch.
 * 
 * @package         Amazon Auto Links
 */
class AmazonAutoLinks_UnitOutput_search extends AmazonAutoLinks_UnitOutput_Base_ElementFormat {
    
    /**
     * Stores the unit type.
     * @remark      Note that the base constructor will create a unit option object based on this value.
     */    
    public $sUnitType = 'search';
    
    /**
     * Lists the tags (variables) used in the Item Format unit option that require to access the custom database.
     * @since 3.5.0
     * @var   array
     */
    protected $_aItemFormatDatabaseVariables = array(
        '%review%', '%rating%', '%similar%',
        '%_discount_rate%', '%_review_rate%', // 3.9.2  - used for advanced filters
        '%price%',                            // 3.10.0 - as preferred currency is now supported, the `GetItem` operation is more up-to-date than `SearchItem` then sometimes it gives a different result so use it if available.
        '%discount%'                          // 4.7.8
    );

    /**
     * The array element key name that contains `Items` element.
     * PA-API 5 operations such as `GetItems`, `SearchItems` have different key names such as `ItemsResult` abd `SearchResult`.
     * @var   string
     * @since 3.9.0
     */
    protected $_sResponseItemsParentKey = 'SearchResult';

    /**
     * @var   string A PA-API response data.
     * @since 5.0.0
     */
    public $sResponseDate = '';

    /**
     * Performs PA-API requests.
     *
     * This enables to retrieve more than 10 items. However, for it, it performs multiple requests, thus, it will be slow.
     *
     * @param  integer $iCount
     *
     * @return array
     * @since  5.0.0   Renamed from `getRequest()` to `getPAAPIResponse()`.
     * @since  2.0.1
     * @since  4.3.1   Changed the scope to public from protected. This is for the background routine to fetch products.
     */
    public function getAPIResponse( $iCount ) {
        $_sLocale       = $this->oUnitOption->get( 'country' );
        $_oPAAPIRequest = new AmazonAutoLinks_Unit_PAAPI5_Request_SearchItems(
            $this->oUnitOption,
            $this->oOption->getPAAPIAccessKey( $_sLocale ),
            $this->oOption->getPAAPISecretKey( $_sLocale )
        );
        return $_oPAAPIRequest->getPAAPIResponse( $iCount );
    }

}