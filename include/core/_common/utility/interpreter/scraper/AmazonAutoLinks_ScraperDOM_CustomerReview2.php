<?php
/**
 * Amazon Auto Links
 * 
 * http://en.michaeluno.jp/amazn-auto-links/
 * Copyright (c) 2013-2020 Michael Uno
 * 
 */

/**
 * Provides methods to extracts each customer review by using DOM objects.
 * 
 * @since       3.9.0
 */
class AmazonAutoLinks_ScraperDOM_CustomerReview2 extends AmazonAutoLinks_ScraperDOM_Base {

    /**
     * @return  integer
     */
    public function getRating() {

        $_oXpath = new DOMXPath( $this->oDoc );
        $_oDIV  = $_oXpath->query(
            "//div[contains(@class, 'AverageCustomerReviews')]//i[contains(@class, 'averageStarRating')]"
        )->item( 0 );
        $_sText = $_oDIV
            ? $_oDIV->nodeValue
            : 0;
        return AmazonAutoLinks_Unit_Utility::getRatingExtracted( $_sText );

    }

    /**
     * @return integer
     */
    public function getNumberOfReviews() {
        $_oXpath                = new DOMXPath( $this->oDoc );
        $_oNode_NumberOfReviews = $_oXpath->query(
            "//div[contains(@class, 'averageStarRatingNumerical')]"
        )->item( 0 );
        return $_oNode_NumberOfReviews
            ? ( integer ) preg_replace(
                '/[^\d]/', // needle
                '', // replacement
                $_oNode_NumberOfReviews->nodeValue   // subject
            )
            : 0;
    }

    /**
     * @return string
     */
    public function getCustomerReviews() {
        $this->oDOM->removeTags( $this->oDoc, array( 'script' ) );
        $_oXpath           = new DOMXPath( $this->oDoc );
        $_oReviewContainer = $_oXpath->query(
            "//div[contains(@class, 'review-views')]"
        )->item( 0 );
        return $_oReviewContainer
            ? $this->oDoc->saveXml( $_oReviewContainer, LIBXML_NOEMPTYTAG )
            : '';

    }
}