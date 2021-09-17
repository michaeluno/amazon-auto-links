<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
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

        $_bSet                  = $this->___setVariablesOfPrice( $aProduct, $aRow, $aRowIdentifier, $_iPrice, $_insLowestNew, $_insDiscounted );
        if ( ! $_bSet ) {
            return array();
        }
        $_aCompare              = array_filter( array( $_insLowestNew, $_insDiscounted ), 'strlen' ); // applying 'strlen' will drop null and empty strings
        $_iLowest               = empty( $_aCompare )
            ? $_iPrice
            : ( integer ) min( $_aCompare );
        $_dDiscountPercentage   = $_iPrice
            ? 100 - round( ( $_iLowest / $_iPrice ) * 100, 2 )
            : 0;
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
        /**
         * @param  array    $aProduct
         * @param  array    $aRow
         * @param  array    $aRowIdentifier
         * @param  integer &$iPrice
         * @param  integer|string|null &$insLowestNew
         * @param  integer|string|null &$insDiscounted
         * @return boolean  true if variables are set. Otherwise, false.
         * @since  4.2.2
         */
        private function ___setVariablesOfPrice( $aProduct, $aRow, $aRowIdentifier, &$iPrice, &$insLowestNew, &$insDiscounted ) {

            // Case: Already set. This can occur with feed units.
            if ( isset( $aProduct[ 'price_amount' ], $aProduct[ 'discounted_price_amount' ], $aProduct[ 'lowest_new_price_amount' ] ) && is_numeric( $aProduct[ 'price_amount' ] ) ) {
                $iPrice        = ( integer ) $aProduct[ 'price_amount' ];
                $insLowestNew  = $aProduct[ 'discounted_price_amount' ] ? ( integer ) $aProduct[ 'discounted_price_amount' ] : $aProduct[ 'discounted_price_amount' ];
                $insDiscounted = $aProduct[ 'lowest_new_price_amount' ] ? ( integer ) $aProduct[ 'lowest_new_price_amount' ] : $aProduct[ 'lowest_new_price_amount' ];
                return true;
            }

            $_oRow = new AmazonAutoLinks_UnitOutput___Database_Product(
                $aRowIdentifier[ 'asin' ],
                $aRowIdentifier[ 'locale' ],
                $aRowIdentifier[ 'associate_id' ],
                $aRow,
                $this->_oUnitOutput->oUnitOption
            );

            // The database column names and the product array keys are different
            $_inPrice      = $_oRow->getCell( 'price' );                   // corresponds to `price_amount` in the product array
            $_inDiscounted = $_oRow->getCell( 'discounted_price' );        // corresponds to `discounted_price_amount` in the product array
            $_inLowestNew  = $_oRow->getCell( 'lowest_new_price' );        // corresponds to `lowest_new_price_amount` in the product array

            // If the data is not ready, do not show them.
            if ( ! $this->___isProductReady( $_inPrice, $_inDiscounted, $_inLowestNew ) ) {
                return false;
            }

            $iPrice        = ( integer ) $_inPrice;
            $insLowestNew  = $_inLowestNew  ? ( integer ) $_inLowestNew : $_inLowestNew;
            $insDiscounted = $_inDiscounted ? ( integer ) $_inDiscounted : $_inDiscounted;
            return true;

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