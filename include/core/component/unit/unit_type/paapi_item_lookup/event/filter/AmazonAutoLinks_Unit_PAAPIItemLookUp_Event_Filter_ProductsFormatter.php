<?php
/**
 * Amazon Auto Links
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 *
 */

/**
 * Formats products array.
 *
 * @since 5.0.0
 */
class AmazonAutoLinks_Unit_PAAPIItemLookUp_Event_Filter_ProductsFormatter extends AmazonAutoLinks_Unit_PAAPISearch_Event_Filter_ProductsFormatter {

    /**
     * @var string
     * @since 5.0.0
     */
    public $sUnitType = 'item_lookup';

    /**
     * @var   AmazonAutoLinks_UnitOutput_item_lookup
     * @sicne 5.0.0
     */
    public $oUnitOutput;

    /**
     * Unit type specific product structure.
     * @var   array
     * @since 5.0.0
     * @todo not implemented yet
     */
    // public static $aStructure_Product = array();

    /**
     * @param  array $aProducts
     * @return array
     * @since  5.0.0
     * @todo not implemented yet
     */
    // protected function _getItemsFormatted( $aProducts ) {
    //     return $aProducts;
    // }

}