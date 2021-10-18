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
class AmazonAutoLinks_Unit_PAAPISearch_Event_Filter_ProductsSorter extends AmazonAutoLinks_Unit_UnitType_Common_Event_Filter_ProductsFormatter_Base {

    public $sUnitType = 'search';

    public $iHookPriority = 100;        // must be later than `AmazonAutoLinks_Unit_Category_Event_Filter_ProductsFetcher`.

    /**
     * @param  array $aProducts
     * @return array
     * @since  5.0.0
     */
    protected function _getItemsFromSource( $aProducts ) {
        if ( $this->oUnitOutput->oUnitOption->get( 'shuffle' ) ) {
            shuffle( $aProducts ); // [4.7.0+] For the Product Search units, as they don't have the 'random' sort order, this option makes it possible to shuffule products.
        }
        return $aProducts;
    }

}