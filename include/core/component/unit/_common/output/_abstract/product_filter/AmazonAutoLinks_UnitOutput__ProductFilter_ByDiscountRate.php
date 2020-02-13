<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2020 Michael Uno
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
     * @callback    add_filter      aal_filter_unit_each_product_with_database_row
     * @return      array           The filtered product definition array. If it does not meet the user-set criteria, an empty array will be returend to be filtered out.
     */
    public function replyToFilterProduct( $aProduct, $aRow, $aRowIdentifier ) {

        // Case: Already set. This can occur with feed units.
        if ( isset( $aProduct[ 'price' ], $aProduct[ 'discounted_price' ], $aProduct[ 'lowest_new_price' ] ) && is_numeric( $aProduct[ 'price' ] ) ) {

            $_iPrice      = ( integer ) $aProduct[ 'price' ];
            $_iLowestNew  = ( integer ) $aProduct[ 'discounted_price' ];
            $_iDiscounted = ( integer ) $aProduct[ 'lowest_new_price' ];

        } else {

            $_oRow = new AmazonAutoLinks_UnitOutput___Database_Product(
                $aRowIdentifier[ 'asin' ],
                $aRowIdentifier[ 'locale' ],
                $aRowIdentifier[ 'associate_id' ],
                $aRow,
                $this->_oUnitOutput->oUnitOption
            );

            $_inPrice      = $_oRow->getCell( 'price' );
            $_inDiscounted = $_oRow->getCell( 'discounted_price' );
            $_inLowestNew  = $_oRow->getCell( 'lowest_new_price' );

            // If the data is not ready, do not show them.
            if ( ! $this->___isProductReady( $_inPrice, $_inDiscounted, $_inLowestNew ) ) {
                return array();
            }

            $_iPrice      = ( integer ) $_inPrice;
            $_iLowestNew  = ( integer ) $_inLowestNew;
            $_iDiscounted = ( integer ) $_inDiscounted;

        }

        $_iLowest               = min( $_iLowestNew, $_iDiscounted );
        $_dDiscountPercentage   = 100 - round( ( $_iLowest / $_iPrice ) * 100, 2 );
        $_dAcceptedDiscountRate = ( double ) $this->_oUnitOutput->oUnitOption->get( '_filter_by_discount_rate', 'amount' );
        $_sCase                 = $this->_oUnitOutput->oUnitOption->get( '_filter_by_discount_rate', 'case' );
        switch ( $_sCase ) {
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

        /**
         * @param   null|integer $_inPrice
         * @param   null|integer $_inDiscounted
         * @param   null|integer $_inLowestNew
         * @return  boolean
         */
        private function ___isProductReady( $_inPrice, $_inDiscounted, $_inLowestNew ) {

            if ( empty( $_inPrice ) ) {
                return false;
            }
            if ( null === $_inLowestNew ) {
                return false;
            }
            if ( null === $_inDiscounted ) {
                return false;
            }
            return true;

        }

}