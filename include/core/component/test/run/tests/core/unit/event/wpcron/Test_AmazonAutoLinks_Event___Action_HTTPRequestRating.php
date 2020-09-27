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
 *
 * @package     Amazon Auto Links
 * @since       4.3.3
 * @tags        rating
*/
class Test_AmazonAutoLinks_Event___Action_HTTPRequestRating extends AmazonAutoLinks_UnitTest_Base {


    public function testRatingAndReviewCount_URL() {
        $_oClass = new AmazonAutoLinks_MockClass( 'AmazonAutoLinks_Event___Action_HTTPRequestRating' );
        return $_oClass->call( '___getRatingWidgetPageURL', 'B08FWDGDS5', 'US' );
    }
    public function testRatingAndReviewCount() {
        $_oClass = new AmazonAutoLinks_MockClass( 'AmazonAutoLinks_Event___Action_HTTPRequestRating' );
        $_sURL   = $_oClass->call( '___getRatingWidgetPageURL', 'B087T7D45T', 'US' ); // B08FWDGDS5, B08HGKZC6T
        $_sHTML  = $_oClass->call( '___getDataFromWidgetPage', $_sURL, 86400, false );
        if ( false === stripos( $_sHTML, 'acr-average-stars-rating-text'  ) ) {
            throw new Exception( 'It is not an html output.<hr />' . esc_html( $_sHTML ) );
        }

        $_oScraper      = new AmazonAutoLinks_ScraperDOM_WidgetUserRating( $_sHTML );
        $_inRating       = $_oScraper->getRating();
        if ( ! isset( $_inRating ) ) {
            throw new Exception( 'Failed to retrieve the rating.' );
        }
        if ( 2 !== strlen( $_inRating ) ) {
            throw new Exception( 'The rating value must consist of two digits.' );
        }
        $_inReviewCount = $_oScraper->getNumberOfReviews();
        return is_integer( $_inReviewCount );

    }

}