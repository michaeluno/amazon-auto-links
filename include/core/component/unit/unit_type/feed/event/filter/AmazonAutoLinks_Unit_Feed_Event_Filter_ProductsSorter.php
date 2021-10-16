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
class AmazonAutoLinks_Unit_Feed_Event_Filter_ProductsSorter extends AmazonAutoLinks_Unit_UnitType_Common_Event_Filter_ProductsFetcher_Base {

    public $sUnitType = 'feed';

    public $iHookPriority = 100;        // must be later than `AmazonAutoLinks_Unit_Feed_Event_Filter_ProductsFetcher`.

    /**
     * @param  array $aProducts
     * @param  AmazonAutoLinks_UnitOutput_category $oUnitOutput
     * @return array
     * @since  5.0.0
     */
    public function replyToGet( $aProducts, $oUnitOutput ) {
        $_oSorter   = new AmazonAutoLinks_Unit_Output_Sort( $aProducts, $oUnitOutput->oUnitOption->get( 'sort' ) );
        return $_oSorter->get();
    }

}