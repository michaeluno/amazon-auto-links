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
 *
 * @package     Auto Amazon Links
 * @since       4.3.4
 * @tags        review
 * @see         AmazonAutoLinks_Event___Action_HTTPRequestCustomerReview
*/
class Test_AmazonAutoLinks_Event___Action_HTTPRequestCustomerReview extends AmazonAutoLinks_UnitTest_Base {

    public $sASIN = 'B01B8R6V2E';

    /**
     * @throws ReflectionException
     * @tags us, slow
     */
    public function test_getReview() {

        $_sASIN      = 'B01B8R6V2E';
        $_sLocale    = 'US';
        $_oLocale    = new AmazonAutoLinks_PAAPI50_Locale( $_sASIN );
        $_sLanguage  = $_oLocale->getDefaultLanguage();
        $_oMock      = new AmazonAutoLinks_MockClass( 'AmazonAutoLinks_Event___Action_HTTPRequestCustomerReview' );
        $_sURL       = $_oMock->call( '___getReviewPageURL', array( $_sASIN, $_sLocale ) );
        $_sURL       = add_query_arg( array( 'tag' => uniqid() ), $_sURL );
        $this->_output( 'URL: ' . $_sURL );
        /**
         * @var AmazonAutoLinks_HTTPClient $_oHTTP
         */
        $_aoResponse = $_oMock->call( '___getReviewPageResponse', array( &$_oHTTP, $_sURL, $_sLocale, 86400, true, $_sLanguage ) );
        $this->_output( 'Character Set: ' . $_oHTTP->getCharacterSet() );
        $this->_outputDetails( 'Cookies: ', $this->getRequestCookiesFromResponse( $_aoResponse ) );
        $this->_assertFalse( is_wp_error( $_aoResponse ), 'Maybe blocked', $_aoResponse );

        // Get review elements
        $_oScraper      = new AmazonAutoLinks_ScraperDOM_CustomerReview2( $_oHTTP->getBody() );
        $_inRating      = $_oScraper->getRating();
        $this->_output( 'Rating: ' . $_inRating );
        $this->_assertTrue( 0 === $_inRating | 2 === strlen( $_inRating ) );
        $_inReviewCount = $_oScraper->getNumberOfReviews();
        $this->_output( 'Number of reviews: ' . $_inReviewCount );
        $this->_assertTrue( is_integer( $_inReviewCount ) );
        $this->_outputDetails( 'reviews', $_oScraper->getCustomerReviews() );

    }

}