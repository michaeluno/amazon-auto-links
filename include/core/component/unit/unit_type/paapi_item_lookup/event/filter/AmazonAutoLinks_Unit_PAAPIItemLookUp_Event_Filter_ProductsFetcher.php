<?php
/**
 * Auto Amazon Links
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 *
 */

/**
 * Fetches product data from outside source.
 *
 * @since 5.0.0
 */
class AmazonAutoLinks_Unit_PAAPIItemLookUp_Event_Filter_ProductsFetcher extends AmazonAutoLinks_Unit_PAAPISearch_Event_Filter_ProductsFetcher {

    public $sUnitType = 'item_lookup';

    /**
     * @var AmazonAutoLinks_UnitOutput_item_lookup
     */
    public $oUnitOutput;

    /**
     * Stores the unit option key that is used for the search.
     * This is needed for the `search_per_keyword` option.
     * @var   string
     * @since 3.2.0
     * @since 5.0.0  Moved from `AmazonAutoLinks_UnitOutput_search`.
     */
    public $sSearchTermKey = 'ItemId';

    /**
     * The array element key name that contains `Items` element.
     * PA-API 5 operations such as `GetItems`, `SearchItems` have different key names such as `ItemsResult` abd `SearchResult`.
     * @var   string
     * @since 3.9.0
     * @since 5.0.0  Moved from `AmazonAutoLinks_UnitOutput_search`.
     */
    protected $_sResponseItemsParentKey = 'ItemsResult';

    /**
     * @since  3.1.4
     * @since  3.8.1 Added the `$aURLs` parameter.
     * @since  5.0.0 Removed the first parameter `$aURLs`. Moved from `AmazonAutoLinks_UnitOutput_search`.
     * @return array
     */
    protected function _getResponses() {
        return $this->oUnitOutput->getAPIResponse( $this->oUnitOutput->oUnitOption->get( 'count' ) );
    }

}