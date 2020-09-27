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
 * @since       4.3.2
 * @tags        rating
*/
class Test_AmazonAutoLinks_Event___Action_HTTPRequestRating extends AmazonAutoLinks_UnitTest_Base {


    public function testRatingAndReviewCount_URL() {
        $_oClass = new AmazonAutoLinks_MockClass( 'AmazonAutoLinks_Event___Action_HTTPRequestRating' );
        return $_oClass->call( '___getRatingWidgetPageURL', 'B08FWDGDS5', 'US' );
    }
    public function testRatingAndReviewCount() {
        $_oClass = new AmazonAutoLinks_MockClass( 'AmazonAutoLinks_Event___Action_HTTPRequestRating' );
        $_sURL  = $_oClass->call( '___getRatingWidgetPageURL', 'B08FWDGDS5', 'US' );
        $_sHTML  = $_oClass->call( '___getDataFromWidgetPage', $_sURL, 86400, false );
        // ___getDataFromWidgetPage( $sASIN, $sLocale, $iCacheDuration, $bForceRenew )
return $_sHTML;
        $_oScraper      = new AmazonAutoLinks_ScraperDOM_WidgetUserRating( $_sHTML );
        $_inRating       = $_oScraper->getRating();
        $_inReviewCount = $_oScraper->getNumberOfReviews();
        return array(
            'rating' => $_inRating,
            'count' => $_inReviewCount,
        );
    }

}