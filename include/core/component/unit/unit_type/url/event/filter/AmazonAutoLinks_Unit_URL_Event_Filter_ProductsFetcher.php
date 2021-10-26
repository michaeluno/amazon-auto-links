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
class AmazonAutoLinks_Unit_URL_Event_Filter_ProductsFetcher extends AmazonAutoLinks_Unit_PAAPIItemLookUp_Event_Filter_ProductsFetcher {

    public $sUnitType = 'url';

    /**
     * @var AmazonAutoLinks_UnitOutput_url
     */
    public $oUnitOutput;

}