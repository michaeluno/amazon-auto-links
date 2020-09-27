<?php
/**
 * Amazon Auto Links
 * 
 * http://en.michaeluno.jp/amazn-auto-links/
 * Copyright (c) 2013-2020 Michael Uno
 * 
 */

/**
 * Provides methods to extracts product rating information from the widget API.
 * 
 * @since       4.3.3
 */
class AmazonAutoLinks_ScraperDOM_WidgetUserRating extends AmazonAutoLinks_ScraperDOM_Base {

    /**
     * @return  integer|null    null on failure. Otherwise, a rating value with two digits like 45 for 4.5.
     * @since   4.3.3
     */
    public function getRating() {

        $_oXpath = new DOMXPath( $this->oDoc );
        $_oSpan  = $_oXpath->query(
            "//i[contains(@data-hook, 'average-stars-rating-anywhere')]"
        )->item( 0 );
        // e.g. 5 out of 5, 4.5 out of 5. // /\d[\.,]\d/
        $_sText = $_oSpan ? trim( $_oSpan->nodeValue ) : '';

        // Extract numbers. There should be two numbers. For example, 4.5 and 5 for "4.5 out of 5"
        preg_match_all( '/\d[\.,]?\d?(?!$)/', $_sText, $_aMatches );
        $_aMatches = $_aMatches[ 0 ];
        if ( empty( $_aMatches ) ) {
            return null;
        }
        if ( count( $_aMatches ) === 1 ) {
            return ( integer ) ( ( ( double ) reset( $_aMatches ) ) * 10 );
        }
        // Compare them and return the one with the least number
        return ( integer ) ( ( ( double ) min( $_aMatches ) ) * 10 );

    }

    /**
     * @return integer|null     null on failure.
     * @since   4.3.3
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