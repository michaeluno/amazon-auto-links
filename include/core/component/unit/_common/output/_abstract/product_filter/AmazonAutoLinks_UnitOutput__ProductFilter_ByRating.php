<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2018 Michael Uno
 */

/**
 * A class that filters parsed products by customer rating.
 *
 * @since   3.5.0
 */
class AmazonAutoLinks_UnitOutput__ProductFilter_ByRating extends AmazonAutoLinks_UnitOutput__ProductFilter_Base {

    /**
     * @return boolean
     */
    protected function _shouldProceed() {
        if ( ! $this->_oUnitOutput->bDBTableAccess ) {
            return false;
        }
        return ( boolean ) $this->_oUnitOutput->oUnitOption->get( '_filter_by_rating', 'enabled' );
    }

    /**
     *
     * @param $aProduct
     * @param $aRow
     * @param $aRowIdentifier
     * @callback        add_filter      aal_filter_unit_each_product_with_database_row
     * @return      array       The filtered product definition array. If it does not meet the user-set criteria, an empty array will be returend to be filtered out.
     */
    public function replyToFilterProduct( $aProduct, $aRow, $aRowIdentifier ) {

        $_oRow = new AmazonAutoLinks_UnitOutput___Database_Product(
            $aRowIdentifier[ 'asin' ],
            $aRowIdentifier[ 'locale' ],
            $aRowIdentifier[ 'associate_id' ],
            $aRow,
            $this->_oUnitOutput->oUnitOption
        );
        $_iRating = ( integer ) $_oRow->getCell( 'rating' );

        // The value is two digits such as 30 for 3.0.
        $_iAcceptedRate = ( integer ) ( $this->_oUnitOutput->oUnitOption->get( '_filter_by_rating', 'amount' ) * 10 );
        $_sCase = $this->_oUnitOutput->oUnitOption->get( '_filter_by_rating', 'case' );
        switch ( $_sCase ) {
            case 'below':
                return ( $_iAcceptedRate >= $_iRating )
                    ? $aProduct
                    : array();     // do not show

            default:
            case 'above':
                return ( $_iAcceptedRate <= $_iRating )
                    ? $aProduct
                    : array();   // do not show
        }

    }

}