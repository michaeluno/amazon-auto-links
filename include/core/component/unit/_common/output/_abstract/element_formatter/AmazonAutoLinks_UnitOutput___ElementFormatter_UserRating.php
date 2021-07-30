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
 * A class that provides methods to format user rating outputs.
 *
 * @since       3.5.0
 */
class AmazonAutoLinks_UnitOutput___ElementFormatter_UserRating extends AmazonAutoLinks_UnitOutput___ElementFormatter_Base {

    /**
     * @return      string
     * @throws      Exception
     * @since       3.5.0
     */
    public function get() {

        // For search-type and feed units, this value is already set.
        if ( isset ( $this->_aProduct[ 'formatted_rating' ] ) ) {
            return $this->_aProduct[ 'formatted_rating' ];
        }

        // Avoid accessing DB as it triggers a background routine when a value is not set
        if ( ! $this->_oUnitOption->hasItemFormatTags( array( '%rating%' ) ) ) {
            return '';
        }

        // Backward compatibility for 3.8.x or below
        $_snEncodedHTML = $this->_getCell( 'rating_html' );
        if ( $_snEncodedHTML ) {
            return $this->___getFormattedOutput( $_snEncodedHTML );
        }
        // For 3.9.0 or above, generate the output from the rating value.
        $_inRating      = isset( $this->_aProduct[ 'rating' ] ) // embed unit type sets it with the ad widget api
            ? $this->_aProduct[ 'rating' ]
            : $this->_getCell( 'rating' );
        if ( $_inRating ) {
            return $this->___getRatingOutput(
                $_inRating,
                isset( $this->_aProduct[ 'number_of_reviews' ] ) ? ( integer ) $this->_aProduct[ 'number_of_reviews' ] : null
            );
        }
        if ( null === $_snEncodedHTML && null === $_inRating ) {
            return $this->_getPendingMessage(
                __( 'Now retrieving the rating.', 'amazon-auto-links' ),
                $this->_sLocale,
                'formatted_rating'
            );
        }
        return '';

    }
        /**
         * @since   3.9.0
         * @param   integer $iRating
         * @param   integer|null $iNumberOfReviews
         * @return  string
         */
        private function ___getRatingOutput( $iRating, $iNumberOfReviews ) {
            $_iReviewCount  = isset( $iNumberOfReviews )
                ? $iNumberOfReviews
                : ( integer ) $this->_getCell( 'number_of_reviews' );
            $_oLocale       = new AmazonAutoLinks_Locale( $this->_sLocale );
            $_sReviewURL    = $_oLocale->getCustomerReviewURL( $this->_sASIN, $this->_sAssociateID, $this->_oUnitOption->get( 'language' ) );
            return "<div class='amazon-customer-rating-stars'>"
                    . AmazonAutoLinks_Unit_Utility::getRatingOutput( $iRating, $_sReviewURL, $_iReviewCount )
                . "</div>";
        }

        /**
         * @since   3.5.0
         * @return  string
         * @remark  kept for backward compatibility
         */
        private function ___getFormattedOutput( $_snEncodedHTML ) {
            if ( '' === $_snEncodedHTML ) {
                return '';
            }
            $_snEncodedHTML = htmlspecialchars_decode( $_snEncodedHTML ); // 3.8.0 Not sure why but some unicode characters becomes broken without this.
            $_oScraper      = new AmazonAutoLinks_ScraperDOM_UserRating(
                $_snEncodedHTML,
                true // character set - auto detect
            );
            return "<div class='amazon-customer-rating-stars'>"
                    . $_oScraper->get()
                . "</div>";
        }

}