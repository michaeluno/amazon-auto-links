<?php
/**
 * Auto Amazon Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2022 Michael Uno
 */

/**
 * A class that filters parsed products by discount rate.
 *
 * @since   3.5.0
 */
class AmazonAutoLinks_UnitOutput__ProductFilter_ByDiscountRate extends AmazonAutoLinks_UnitOutput__ProductFilter_Base {

    /**
     * @return boolean
     */
    protected function _shouldProceed() {
        if ( ! $this->_oUnitOutput->bDBTableAccess ) {
            return false;
        }
        return ( boolean ) $this->_oUnitOutput->oUnitOption->get( '_filter_by_discount_rate', 'enabled' );
    }

    /**
     *
     * @param       array   $aProduct
     * @param       array   $aRow
     * @param       array   $aRowIdentifier
     * ### Structure
     * - locale (string)
     * - asin (string)
     * - associate_id (string)
     * @callback    add_filter      aal_filter_unit_each_product_with_database_row
     * @return      array           The filtered product definition array. If it does not meet the user-set criteria, an empty array will be returned to be filtered out.
     */
    public function replyToFilterProduct( $aProduct, $aRow, $aRowIdentifier ) {

        $_oDiscount             = new AmazonAutoLinks_UnitOutput___ElementFormatter_DiscountPercentage( $aRowIdentifier[ 'asin' ], $aRowIdentifier[ 'locale' ], $aRowIdentifier[ 'associate_id' ], $aRow, $this->_oUnitOutput->oUnitOption, $aProduct );
        $_dnDiscountPercentage  = $_oDiscount->getPercentage();
        if ( ! isset( $_dnDiscountPercentage ) ) {
            return array();
        }
        $_dDiscountPercentage   = $_dnDiscountPercentage;
        $_dAcceptedDiscountRate = ( double ) $this->_oUnitOutput->oUnitOption->get( '_filter_by_discount_rate', 'amount' );
        switch ( $this->_oUnitOutput->oUnitOption->get( '_filter_by_discount_rate', 'case' ) ) {
            case 'below':
                return ( $_dAcceptedDiscountRate >= $_dDiscountPercentage )
                    ? $aProduct
                    : array();   // do not show;
            default:
            case 'above':
                return ( $_dAcceptedDiscountRate <= $_dDiscountPercentage )
                    ? $aProduct
                    : array();   // do not show
        }

    }

}