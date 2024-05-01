<?php
/**
 * Auto Amazon Links
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2022 Michael Uno
 *
 */

/**
 * Sort products array.
 *
 * @since 5.0.0
 */
class AmazonAutoLinks_Unit_PAAPIItemLookUp_Event_Filter_ProductsSorter extends AmazonAutoLinks_Unit_PAAPISearch_Event_Filter_ProductsFormatter {

    /**
     * @var string
     * @since 5.0.0
     */
    public $sUnitType = 'item_lookup';

    /**
     * @var    integer
     * @since  5.0.0
     * @remark A larger value is set because the callback function must be called later than `AmazonAutoLinks_Unit_Category_Event_Filter_ProductsFetcher`.
     */
    public $iHookPriority = 100;

    /**
     * @param  array $aProducts
     * @return array
     * @since  5.3.5
     */
    protected function _getItemsFormatted( $aProducts ) {
        $_oSorter   = new AmazonAutoLinks_Unit_Output_Sort(
            $aProducts,
            $this->oUnitOutput->oUnitOption->get( array( '_sort' ), 'raw' )
        );
        // [5.4.3] The Item Look-up unit type needs to truncate the products array after applying the sort order
        return array_slice( $_oSorter->get(), 0, $this->oUnitOutput->oUnitOption->get( 'count' ) ); // truncate items
    }

}