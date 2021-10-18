<?php
/**
 * Amazon Auto Links
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 *
 */

/**
 * Sort products array.
 *
 * @since 5.0.0
 */
class AmazonAutoLinks_Unit_PAAPIItemLookUp_Event_Filter_ProductsSorter extends AmazonAutoLinks_Unit_UnitType_Common_Event_Filter_ProductsFetcher_Base {

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
     * @since  5.0.0
     */
    protected function _getItemsFromSource( $aProducts ) {
        $_oSorter   = new AmazonAutoLinks_Unit_Output_Sort(
            $aProducts,
            $this->oUnitOutput->oUnitOption->get( array( '_sort' ), 'raw' )
        );
        return $_oSorter->get();
    }

}