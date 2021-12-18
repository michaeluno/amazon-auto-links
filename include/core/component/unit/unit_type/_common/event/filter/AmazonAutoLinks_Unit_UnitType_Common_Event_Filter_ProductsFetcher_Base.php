<?php
/**
 * Auto Amazon Links
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

    /**
     * @var string
     * @since 5.0.0
     */
    public $sUnitType = '';

    /**
     * @var integer
     * @since 5.0.0
     */
    public $iHookPriority = 10;

    /**
     * @var AmazonAutoLinks_UnitOutput_Base
     * @since 5.0.0
     */
    public $oUnitOutput;

    public function __construct() {
        if ( $this->hasBeenCalled( get_class( $this ) . '::' . __METHOD__ ) ) {
            return;
        }
        if ( ! $this->sUnitType ) {
            return;
        }
        add_filter( 'aal_filter_unit_output_products_from_source_' . $this->sUnitType, array( $this, 'replyToGet' ), $this->iHookPriority, 2 );
    }

    /**
     * @param  array $aProducts
     * @param  AmazonAutoLinks_UnitOutput_Base $oUnitOutput
     * @return array
     * @since  5.0.0
     */
    public function replyToGet( $aProducts, $oUnitOutput ) {
        $this->oUnitOutput = $oUnitOutput;
        $_aProducts = $this->_getItemsFromSource( $aProducts );
        unset( $this->oUnitOutput );
        return $_aProducts;
    }

    /**
     * @param  array $aProducts
     * @return array
     * @since  5.0.0
     */
    protected function _getItemsFromSource( $aProducts ) {
        return $aProducts;
    }

}