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
class AmazonAutoLinks_Unit_PAAPICustomPayload_Event_Filter_ProductsFetcher extends AmazonAutoLinks_Unit_PAAPIItemLookUp_Event_Filter_ProductsFetcher {

    /**
     * @var   string
     * @since 5.0.0
     */
    public $sUnitType = 'scratchpad_payload';

    /**
     * @var   AmazonAutoLinks_UnitOutput_scratchpad_payload
     * @since 5.0.0
     */
    public $oUnitOutput;

    /**
     * The array element key name that contains `Items` element.
     * PA-API 5 operations such as `GetItems`, `SearchItems` have different key names such as `ItemsResult` abd `SearchResult`.
     *
     * For this unit type, the value depends on the user's input.
     * The value is automatically assigned with the `___getItemsKey()` method.
     *
     * @var string
     * @since ?
     * @since 5.0.0 Moved from `AmazonAutoLinks_UnitOutput_scratchpad_payload`.
     */
    protected $_sResponseItemsParentKey = '';

    /**
     * Performs API requests and get response.
     *
     * @return array
     * @since  ?
     * @since  5.0.0  Removed the first parameter of `$aURLs`. Moved from `AmazonAutoLinks_UnitOutput_scratchpad_payload`.
     */
    protected function _getResponses() {
        $_sLocale       = $this->oUnitOutput->oUnitOption->get( 'country' );
        $_oPAAPIRequest = new AmazonAutoLinks_Unit_PAAPI5_Request_CustomPayload(
            $this->oUnitOutput->oUnitOption,
            $this->oUnitOutput->oOption->getPAAPIAccessKey( $_sLocale ),
            $this->oUnitOutput->oOption->getPAAPISecretKey( $_sLocale )
        );
        return $_oPAAPIRequest->getPAAPIResponse( $this->oUnitOutput->oUnitOption->get( 'count' ) );
    }
    
}