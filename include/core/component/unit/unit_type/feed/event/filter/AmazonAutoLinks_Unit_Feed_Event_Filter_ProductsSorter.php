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
class AmazonAutoLinks_Unit_Feed_Event_Filter_ProductsSorter extends AmazonAutoLinks_Unit_UnitType_Common_Event_Filter_ProductsFetcher_Base {

    public $sUnitType = 'feed';

    public $iHookPriority = 100;        // must be later than `AmazonAutoLinks_Unit_Feed_Event_Filter_ProductsFetcher`.

    /**
     * @param  array $aProducts
     * @return array
     * @since  5.0.0
     */
    protected function _getItemsFromSource( $aProducts ) {
        $_oSorter = new AmazonAutoLinks_Unit_Output_Sort( $aProducts, $this->oUnitOutput->oUnitOption->get( 'sort' ) );
        return $_oSorter->get();
    }

}