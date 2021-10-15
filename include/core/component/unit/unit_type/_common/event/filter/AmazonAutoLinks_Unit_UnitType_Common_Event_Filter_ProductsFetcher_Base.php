<?php
/**
 * Amazon Auto Links
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 *
 */

/**
 * A base class for product fetcher classes.
 *
 * @since 5.0.0
 */
abstract class AmazonAutoLinks_Unit_UnitType_Common_Event_Filter_ProductsFetcher_Base extends AmazonAutoLinks_PluginUtility {

    public $sUnitType = '';

    public $iHookPriority = 10;

    public function __construct() {
        add_filter( 'aal_filter_unit_output_products_from_source_' . $this->sUnitType, array( $this, 'replyToGet' ), $this->iHookPriority, 2 );
    }

    /**
     * @param  array $aProducts
     * @param  AmazonAutoLinks_UnitOutput_Base $oUnitOutput
     * @return array
     * @since  5.0.0
     */
    public function replyToGet( $aProducts, $oUnitOutput ) {
        return $aProducts;
    }

}