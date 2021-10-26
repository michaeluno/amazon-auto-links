<?php
/**
 * Auto Amazon Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 */

/**
 * A class that provides methods to format product discount percentage.
 *
 * @since 4.7.8
 */
class AmazonAutoLinks_UnitOutput___ElementFormatter_DiscountPercentage extends AmazonAutoLinks_UnitOutput___ElementFormatter_Base {

    /**
     * @return string
     * @throws Exception
     * @since  4.7.8
     */
    public function get() {

        // For feed an PA-API search units, this value is already set.
        if ( isset ( $this->_aProduct[ 'formatted_discount' ] ) ) {
            return $this->_aProduct[ 'formatted_discount' ];
        }

        // Avoid accessing DB as it triggers a background routine when a value is not set
        if ( ! $this->_oUnitOption->hasItemFormatTags( array( '%discount%' ) ) ) {
            return '';
        }

        return $this->getFormattedDiscount(
            ( integer ) $this->getPercentage()  // floor positive decimal numbers by casting integer
        );

    }

    /**
     * @return double|null null when the data is not ready.
     * @since  4.7.8
     */
    public function getPercentage() {
        $_bSet      = $this->___setVariablesOfPrice( $this->_aProduct, $this->_aRow, $_iPrice, $_insLowestNew, $_insDiscounted );
        if ( ! $_bSet ) {
            return null;
        }
        $_aCompare  = array_filter( array( $_insLowestNew, $_insDiscounted ), 'strlen' ); // applying 'strlen' will drop null and empty strings
        $_iLowest   = empty( $_aCompare )
            ? $_iPrice
            : ( integer ) min( $_aCompare );
        $_dDiscountPercentage = $_iPrice
            ? 100 - round( ( $_iLowest / $_iPrice ) * 100, 2 )
            : 0;
        return ( double ) $_dDiscountPercentage;
    }
        /**
         * @param  array    $aProduct
         * @param  array    $aRow
         * @param  integer &$iPrice
         * @param  integer|string|null &$insLowestNew
         * @param  integer|string|null &$insDiscounted
         * @return boolean  true if variables are set. Otherwise, false.
         * @since  4.2.2
         * @since  4.7.8    Moved from `AmazonAutoLinks_UnitOutput__ProductFilter_ByDiscountRate`.
         */
        private function ___setVariablesOfPrice( $aProduct, $aRow, &$iPrice, &$insLowestNew, &$insDiscounted ) {

            // Case: Already set. This can occur with feed units.
            if ( isset( $aProduct[ 'price_amount' ], $aProduct[ 'discounted_price_amount' ], $aProduct[ 'lowest_new_price_amount' ] ) && is_numeric( $aProduct[ 'price_amount' ] ) ) {
                $iPrice        = ( integer ) $aProduct[ 'price_amount' ];
                $insLowestNew  = $aProduct[ 'discounted_price_amount' ] ? ( integer ) $aProduct[ 'discounted_price_amount' ] : $aProduct[ 'discounted_price_amount' ];
                $insDiscounted = $aProduct[ 'lowest_new_price_amount' ] ? ( integer ) $aProduct[ 'lowest_new_price_amount' ] : $aProduct[ 'lowest_new_price_amount' ];
                return true;
            }

            // The database column names and the product array keys are different
            $_inPrice      = $this->_getCell( 'price' );                   // corresponds to `price_amount` in the product array
            $_inDiscounted = $this->_getCell( 'discounted_price' );        // corresponds to `discounted_price_amount` in the product array
            $_inLowestNew  = $this->_getCell( 'lowest_new_price' );        // corresponds to `lowest_new_price_amount` in the product array

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
             * @param  null|integer $_inPrice
             * @param  null|integer $_inDiscounted
             * @param  null|integer $_inLowestNew
             * @return boolean
             * @since  3.5.0
             * @since  4.7.8        Moved from `AmazonAutoLinks_UnitOutput__ProductFilter_ByDiscountRate`.
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