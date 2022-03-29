<?php
/**
 * Auto Amazon Links
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2022 Michael Uno
 *
 */

/**
 * A base class for product fetcher classes.
 *
 * @since 5.0.0
 */
abstract class AmazonAutoLinks_Unit_UnitType_Common_Event_Filter_ProductsFormatter_Base extends AmazonAutoLinks_Unit_Utility {

    /**
     * @var   string
     * @since 5.0.0
     */
    public $sUnitType = '';

    /**
     * @var   integer
     * @since 5.0.0
     */
    public $iHookPriority = 10;

    /**
     * @var   AmazonAutoLinks_UnitOutput_Base
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
        add_filter( 'aal_filter_unit_output_products_format_' . $this->sUnitType, array( $this, 'replyToGet' ), $this->iHookPriority, 2 );
    }

    /**
     * @param  array $aProducts
     * @param  AmazonAutoLinks_UnitOutput_Base $oUnitOutput
     * @return array
     * @since  5.0.0
     */
    public function replyToGet( $aProducts, $oUnitOutput ) {
        $this->oUnitOutput = $oUnitOutput;
        $_aProducts        = $this->_getItemsFormatted( $aProducts );
        unset( $this->oUnitOutput );
        return $_aProducts;
    }

    /**
     * @param  array $aProducts
     * @return array
     * @since  5.0.0
     * @remark Override this method in extended classes.
     */
    protected function _getItemsFormatted( $aProducts ) {
        return $aProducts;
    }

}