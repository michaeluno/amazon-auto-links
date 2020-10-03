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
 * @since       4.3.4
 * @tags        rating
 * @see         AmazonAutoLinks_Event___Action_HTTPRequestRating
*/
class Test_AmazonAutoLinks_Event___Action_HTTPRequestRating extends AmazonAutoLinks_UnitTest_Base {

    public $sASIN = 'B01B8R6V2E';
    
    /**
     * @return bool
     * @throws ReflectionException
     */
    public function testRatingAndReviewCount_US() {
        return is_integer( $this->___testRatingByLocale( $this->sASIN, 'US' ) );
    }
    /**
     * @return bool
     * @throws ReflectionException
     */
    public function testRatingAndReviewCount_CA() {
        return is_integer( $this->___testRatingByLocale( $this->sASIN, 'CA' ) );
    }
    /**
     * @return bool
     * @throws ReflectionException
     * @tags italy, blocked
     */
    public function testRatingAndReviewCount_IT() {
        return is_integer( $this->___testRatingByLocale( $this->sASIN, 'IT' ) );
    }
    /**
     * @return bool
     * @throws ReflectionException
     */
    public function testRatingAndReviewCount_ES() {
        return is_integer( $this->___testRatingByLocale( $this->sASIN, 'ES' ) );
    }
    /**
     * @return bool
     * @throws ReflectionException
     * @tags france, blocked
     */
    public function testRatingAndReviewCount_FR() {
        return is_integer( $this->___testRatingByLocale( $this->sASIN, 'FR' ) );
    }
    /**
     * @return bool
     * @throws ReflectionException
     * @tags german, blocked
     */
    public function testRatingAndReviewCount_DE() {
        return is_integer( $this->___testRatingByLocale( $this->sASIN, 'DE' ) );
    }
    /**
     * @return bool
     * @throws ReflectionException
     * @tags uk, blocked
     */
    public function testRatingAndReviewCount_UK() {
        return is_integer( $this->___testRatingByLocale( $this->sASIN, 'UK' ) );
    }
    /**
     * @return bool
     * @throws ReflectionException
     */
    public function testRatingAndReviewCount_JP() {
        return is_integer( $this->___testRatingByLocale( $this->sASIN, 'JP' ) );
    }
    /**
     * @return bool
     * @throws ReflectionException
     */
    public function testRatingAndReviewCount_CN() {
        return is_integer( $this->___testRatingByLocale( $this->sASIN, 'CN' ) );
    }
    /**
     * @return bool
     * @throws ReflectionException
     */
    public function testRatingAndReviewCount_IN() {
        return is_integer( $this->___testRatingByLocale( $this->sASIN, 'IN' ) );
    }
    /**
     * @return bool
     * @throws ReflectionException
     */
    public function testRatingAndReviewCount_BR() {
        return is_integer( $this->___testRatingByLocale( $this->sASIN, 'BR' ) );
    }
    /**
     * @return bool
     * @throws ReflectionException
     */
    public function testRatingAndReviewCount_MX() {
        return is_integer( $this->___testRatingByLocale( $this->sASIN, 'MX' ) );
    }
    /**
     * @return bool
     * @throws ReflectionException
     */
    public function testRatingAndReviewCount_AU() {
        return is_integer( $this->___testRatingByLocale( $this->sASIN, 'AU' ) );
    }
    /**
     * @return bool
     * @throws ReflectionException
     */
    public function testRatingAndReviewCount_TR() {
        return is_integer( $this->___testRatingByLocale( $this->sASIN, 'TR' ) );
    }
    /**
     * @return bool
     * @throws ReflectionException
     */
    public function testRatingAndReviewCount_AE() {
        return is_integer( $this->___testRatingByLocale( $this->sASIN, 'AE' ) );
    }

    /**
     * @return bool
     * @throws ReflectionException
     */
    public function testRatingAndReviewCount_SG() {
        return is_integer( $this->___testRatingByLocale( $this->sASIN, 'SG' ) );
    }
    /**
     * @return bool
     * @throws ReflectionException
     */
    public function testRatingAndReviewCount_NL() {
        return is_integer( $this->___testRatingByLocale( $this->sASIN, 'NL' ) );
    }

        /**
         * @param $sASIN
         * @param $sLocale
         * @return integer|null
         * @throws Exception
         * @throws ReflectionException
         * @see wp_remote_retrieve_cookies()
         */
        private function ___testRatingByLocale( $sASIN, $sLocale ) {

            $_oMock    = new AmazonAutoLinks_MockClass( 'AmazonAutoLinks_Event___Action_HTTPRequestRating' );

            $_sURL     = $_oMock->call( '___getRatingWidgetPageURL', array( $sASIN, $sLocale, true ) ); // B08FWDGDS5, B08HGKZC6T
            $this->_output( 'URL: ' . $_sURL );

            $_aCookies = $_oMock->call( '___getRequestCookiesByLocale', array( $sLocale ) );
            $this->_outputDetails( 'Request Cookies', $_aCookies );

            $_oHTTP       = null;
            $_aoResponse  = $_oMock->call( '___getWidgetPageResponse', array( &$_oHTTP, $_sURL, 86400, false, $sLocale ) );
            $this->_assertFalse( is_wp_error( $_aoResponse ), 'Check if the response contains an error.', $_aoResponse );
            /**
             * @var AmazonAutoLinks_HTTPClient $_oHTTP
             */
            $_sHTML       = $_oHTTP->getBody();
            if ( false !== strpos( $_sHTML, '<html' ) ) {
                $_oDOM    = new AmazonAutoLinks_DOM;
                $_oDoc    = $_oDOM->loadDOMFromHTML( $_sHTML );
                $_oXPath  = new DOMXPath( $_oDoc );
                $_oDOM->removeTags( $_oDoc, array( 'script', 'style' ) );
                $_noNode  = $_oXPath->query( './/body' )->item( 0 );
                $_sHTML   = $_oDOM->getInnerHTML( $_noNode );
            }

            $this->_output( 'HTML<hr />' . $_sHTML );
            $this->_assertTrue( false !== stripos( $_sHTML, 'acr-average-stars-rating-text'  ), 'Maybe blocked.', esc_html( $_sHTML ) );

            $_oScraper      = new AmazonAutoLinks_ScraperDOM_WidgetUserRating( $_sHTML );
            $_inRating      = $_oScraper->getRating();
            $this->_output( 'Rating: ' . $_inRating );
            $this->_assertTrue( isset( $_inRating ), 'Retrieve the product rating,', $_inRating );
            $this->_assertTrue( 0 === $_inRating || 2 === strlen( $_inRating ), 'The rating value must consist of two digits.', $_inRating );

            $_inNumberOfReviews = $_oScraper->getNumberOfReviews();
            $this->_output( 'Number of reviews: ' . $_inNumberOfReviews );
            $this->_assertTrue( is_integer( $_inNumberOfReviews ), 'The retrieve number of reviews must be an integer.', $_inNumberOfReviews );
            return $_inNumberOfReviews;

        }

}