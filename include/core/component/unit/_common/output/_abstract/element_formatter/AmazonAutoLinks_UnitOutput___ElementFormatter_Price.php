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
 * A class that provides methods to format price outputs.
 *
 * @since       3.5.0
 */
class AmazonAutoLinks_UnitOutput___ElementFormatter_Price extends AmazonAutoLinks_UnitOutput___ElementFormatter_Base {

    /**
     * @return      string
     * @throws      Exception
     * @since       3.5.0
     */
    public function get() {
        
        $_snEncodedHTML = $this->_getCell( 'price_formatted' );
        if ( null === $_snEncodedHTML ) {
            return $this->_getPendingMessage(
                __( 'Now retrieving the price.', 'amazon-auto-links' )
            );
        }
        return $this->___getFormattedOutput( $_snEncodedHTML );

    }
        /**
         * @since   3.5.0
         * @return  string
         */
        private function ___getFormattedOutput( $_snEncodedHTML ) {
            if ( '' === $_snEncodedHTML ) {
                return '';
            }
            return $this->___getPriceOutput( $_snEncodedHTML );
        }

            /**
             * Generates a price output with a discount price if available.
             * @since       3.4.11
             * @since       3.5.0       Moved from `AmazonAutoLinks_UnitOutput_Base_ElementFormatter`.
             * @return      string
             */
            private function ___getPriceOutput( $_sPriceFormatted ) {

                $_inLowestNew           = $this->_getCell( 'lowest_new_price' );
                $_inDiscount            = $this->_getCell( 'discounted_price' );
                $_inOffered             = $this->___getLowestPrice( $_inLowestNew, $_inDiscount );
                $_sLowestColumnName     = $_inDiscount === $_inOffered ? 'discounted_price_formatted' : 'lowest_new_price_formatted';
                $_sLowestFormatted      = $this->_getCell( $_sLowestColumnName );
                return $_sPriceFormatted !== $_sLowestFormatted
                    ? '<s>' . $_sPriceFormatted . '</s> ' . $_sLowestFormatted
                    : $_sPriceFormatted;

            }
                /**
                 * @param   integer $_iLowestNew
                 * @param   integer $_iDiscount
                 * @return  integer|null
                 * @since   3.4.11
                 * @since   3.5.0       Moved from `AmazonAutoLinks_UnitOutput_Base_ElementFormatter`.
                 */
                private function ___getLowestPrice( $_iLowestNew, $_iDiscount ) {
                    $_aOfferedPrices        = array();
                    if ( null !== $_iLowestNew ) {
                        $_aOfferedPrices[] = ( integer ) $_iLowestNew;
                    }
                    if ( null !== $_iDiscount ) {
                        $_aOfferedPrices[] = ( integer ) $_iDiscount;
                    }
                    return ! empty( $_aOfferedPrices )
                        ? min( $_aOfferedPrices )
                        : null;
                }

}