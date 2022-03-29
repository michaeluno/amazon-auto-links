<?php
/**
 * Auto Amazon Links
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2022 Michael Uno
 *
 */

/**
 * Formats products array.
 *
 * @since 5.0.0
 */
class AmazonAutoLinks_Unit_Feed_Event_Filter_ProductsFormatter extends AmazonAutoLinks_Unit_Category_Event_Filter_ProductsFormatter {

    public $sUnitType = 'feed';

    /**
     * @var AmazonAutoLinks_UnitOutput_feed
     */
    public $oUnitOutput;

    /**
     * Unit type specific product structure.
     * @var array
     */
    public static $aStructure_Product = array();

}