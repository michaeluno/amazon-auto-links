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
 * A class that provides methods to format customer review outputs.
 *
 * @since       3.5.0
 */
class AmazonAutoLinks_UnitOutput___ElementFormatter_CustomerReview extends AmazonAutoLinks_UnitOutput___ElementFormatter_Base {

    /**
     * @return      string
     * @throws      Exception
     * @since       3.5.0
     */
    public function get() {

        // Avoid accessing DB as it triggers a background routine when a value is not set
        if ( ! ( boolean ) $this->hasCustomVariable(
            $this->_oUnitOption->get( 'item_format' ),
            array( '%review%' )
        ) ) {
            return '';
        }

        // 4.0.0 Feed units already has the value.
        if ( isset( $this->_aProduct[ 'review' ] ) ) {
            return $this->_aProduct[ 'review' ];
        }

        $_snEncodedHTML = $this->_getCell( 'customer_reviews' );
        if ( null === $_snEncodedHTML ) {
            return $this->_getPendingMessage(
                __( 'Now retrieving customer reviews.', 'amazon-auto-links' ),
                $this->_sLocale
            );
        }
        return $this->___getFormattedOutput( $_snEncodedHTML );

    }

        /**
         * @since   3.5.0
         * @return  string
         */
        private function ___getFormattedOutput( $_snEncodedHTML, $sLocale='', $sAssociateID='' ) {
            if ( ! $_snEncodedHTML ) {
                return '';
            }
            $_oScraper  = new AmazonAutoLinks_ScraperDOM_CustomerReview2_Each(
                $_snEncodedHTML,
                true,
                $this->_oUnitOption->get( 'customer_review_max_count' ),
                $this->_oUnitOption->get( 'customer_review_include_extra' )
            );
            $_sReviews = $_oScraper->get( $this->_sLocale, $this->_sAssociateID );
            if ( $_sReviews ) {
                return "<div class='amazon-customer-reviews'>"
                        . $_sReviews
                    . "</div>";
            }

            // Backward compatibility with 3.8.x or below
            $_oScraper  = new AmazonAutoLinks_ScraperDOM_CustomerReview_Each(
                $_snEncodedHTML,
                true,
                $this->_oUnitOption->get( 'customer_review_max_count' ),
                $this->_oUnitOption->get( 'customer_review_include_extra' )
            );
            return "<div class='amazon-customer-reviews'>" 
                    . $_oScraper->get()
                . "</div>";       
        }

}