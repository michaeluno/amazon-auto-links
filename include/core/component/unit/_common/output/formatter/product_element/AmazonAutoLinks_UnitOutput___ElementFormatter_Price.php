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
 * A class that provides methods to format price outputs.
 *
 * @since 3.5.0
 */
class AmazonAutoLinks_UnitOutput___ElementFormatter_Price extends AmazonAutoLinks_UnitOutput___ElementFormatter_Base {

    /**
     * @return string
     * @throws Exception
     * @since  3.5.0
     */
    public function get() {

        // For search-type units, this value is already set with API response.
        // @deprecated 3.10.0 - The API SearchItems and GetItems operations give different results and GetItems is more up-to-date.
        // @since 4.0.0 Re-added. For feed units, the element is already set
        // @since 4.7.8 - Category unit prices which are already set are not accurate.
        // if ( isset ( $this->_aProduct[ 'formatted_price' ] ) ) {
        //     return $this->_aProduct[ 'formatted_price' ];
        // }
        if ( 'category' !== $this->_oUnitOption->sUnitType ) {
            if ( isset ( $this->_aProduct[ 'formatted_price' ] ) ) {
                return $this->_aProduct[ 'formatted_price' ];
            }
        }

        $_sPriceFormatted = $this->_getCell( 'price_formatted' );
        if ( null === $_sPriceFormatted ) {

            // In a case that the price is already set.
            // @remark The key name is a bit confusing
            // `formatted_price` is a native product array element key for HTML formatted price.
            // `price_formatted` is a string value with the currency character.
            if ( $this->_aProduct[ 'formatted_price' ] ) {
                return $this->_aProduct[ 'formatted_price' ];
            }

            return $this->_getPendingMessage(
                __( 'Now retrieving the price.', 'amazon-auto-links' ),
                $this->_sLocale,
                'formatted_price'
            );
        }

        // 3.10.1 Not sure but there are reported cases that prices do not show up
        if ( ! $_sPriceFormatted ) {
            return $this->_aProduct[ 'formatted_price' ];
        }

        return $this->___getFormattedOutput( $_sPriceFormatted );

    }
        /**
         * @since  3.5.0
         * @return string
         */
        private function ___getFormattedOutput( $sPriceFormatted ) {
            if ( '' === $sPriceFormatted ) {
                return '';
            }
            $inLowestNew          = $this->_getCell( 'lowest_new_price' );
            $inDiscounted         = $this->_getCell( 'discounted_price' );
            $sDiscountedFormatted = $this->_getCell( 'discounted_price_formatted' );
            $sLowestNewFormatted  = $this->_getCell( 'lowest_new_price_formatted' );
            return AmazonAutoLinks_Unit_Utility::getPrice(
                $sPriceFormatted,
                $inDiscounted,
                $inLowestNew,
                $sDiscountedFormatted,
                $sLowestNewFormatted
            );
        }

}