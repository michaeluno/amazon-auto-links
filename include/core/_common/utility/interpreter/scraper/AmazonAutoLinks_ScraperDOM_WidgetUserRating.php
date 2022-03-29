<?php
/**
 * Auto Amazon Links
 * 
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2022 Michael Uno
 * 
 */

/**
 * Provides methods to extracts product rating information from the widget API.
 * 
 * @since       4.3.4
 */
class AmazonAutoLinks_ScraperDOM_WidgetUserRating extends AmazonAutoLinks_ScraperDOM_Base {

    /**
     * @return  integer|null    null on failure. Otherwise, a rating value with two digits like 45 for 4.5.
     * @since   4.3.4
     */
    public function getRating() {

        $_oXpath = new DOMXPath( $this->oDoc );
        $_oSpan  = $_oXpath->query(
            "//i[contains(@data-hook, 'average-stars-rating-anywhere')]"
        )->item( 0 );
        // e.g. 5 out of 5, 4.5 out of 5. // /\d[\.,]\d/
        $_sText = $_oSpan ? trim( $_oSpan->nodeValue ) : '';

        // Extract numbers. There should be two numbers. For example, 4.5 and 5 for "4.5 out of 5"
        $_sText = trim( $_sText, '.' );
        preg_match_all( '/\d[\.,]?\d?/', $_sText, $_aMatches );
        $_aMatches = $_aMatches[ 0 ];
        if ( empty( $_aMatches ) ) {
            return null;
        }
        if ( count( $_aMatches ) === 1 ) {
            return $this->___getRatingNumberSanitized( reset( $_aMatches ) );
        }

        // Convert string numbers to integer such as `4,4`.
        // Then, compare and return the one with the smallest.
        $_aMatches = array_map( array( $this, '___getRatingNumberSanitized' ), $_aMatches );
        return min( $_aMatches );

    }
        /**
         * @param  string  $sNumber
         * @return integer The sanitized number.
         * @since  4.6.2
         */
        private function ___getRatingNumberSanitized( $sNumber ) {
            $_sNumber = ( double ) str_replace( ',', '.', $sNumber );
            return ( integer ) ( $_sNumber * 10 );
        }

    /**
     * @return integer|null     null on failure.
     * @since   4.3.4
     */
    public function getNumberOfReviews() {
        $_oXpath            = new DOMXPath( $this->oDoc );
        $_oNode_RatingCount = $_oXpath->query(
            "//span[contains(@class, 'totalRatingCount')]"
        )->item( 0 );
        return $_oNode_RatingCount
            ? ( integer ) preg_replace(
                '/[^\d]/', // needle
                '', // replacement
                $_oNode_RatingCount->nodeValue   // subject
            )
            : null;
    }

}