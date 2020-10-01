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
//        return is_integer( $this->___testRatingByLocale( $this->sASIN, 'CA' ) );
    }
    /**
     * @return bool
     * @throws ReflectionException
     */
    public function testRatingAndReviewCount_IT() {
//        return is_integer( $this->___testRatingByLocale( $this->sASIN, 'IT' ) );
    }
    /**
     * @return bool
     * @throws ReflectionException
     */
    public function testRatingAndReviewCount_ES() {
//        return is_integer( $this->___testRatingByLocale( $this->sASIN, 'ES' ) );
    }
    /**
     * @return bool
     * @throws ReflectionException
     */
    public function testRatingAndReviewCount_FR() {
//        return is_integer( $this->___testRatingByLocale( $this->sASIN, 'FR' ) );
    }
    /**
     * @return bool
     * @throws ReflectionException
     */
    public function testRatingAndReviewCount_DE() {
//        return is_integer( $this->___testRatingByLocale( $this->sASIN, 'DE' ) );
    }
    /**
     * @return bool
     * @throws ReflectionException
     */
    public function testRatingAndReviewCount_UK() {
//        return is_integer( $this->___testRatingByLocale( $this->sASIN, 'UK' ) );
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
//        return is_integer( $this->___testRatingByLocale( $this->sASIN, 'CN' ) );
    }
    /**
     * @return bool
     * @throws ReflectionException
     */
    public function testRatingAndReviewCount_IN() {
//        return is_integer( $this->___testRatingByLocale( $this->sASIN, 'IN' ) );
    }
    /**
     * @return bool
     * @throws ReflectionException
     */
    public function testRatingAndReviewCount_BR() {
//        return is_integer( $this->___testRatingByLocale( $this->sASIN, 'BR' ) );
    }
    /**
     * @return bool
     * @throws ReflectionException
     */
    public function testRatingAndReviewCount_MX() {
//        return is_integer( $this->___testRatingByLocale( $this->sASIN, 'MX' ) );
    }
    /**
     * @return bool
     * @throws ReflectionException
     */
    public function testRatingAndReviewCount_AU() {
//        return is_integer( $this->___testRatingByLocale( $this->sASIN, 'AU' ) );
    }
    /**
     * @return bool
     * @throws ReflectionException
     */
    public function testRatingAndReviewCount_TR() {
//        return is_integer( $this->___testRatingByLocale( $this->sASIN, 'TR' ) );
    }
    /**
     * @return bool
     * @throws ReflectionException
     */
    public function testRatingAndReviewCount_AE() {
//        return is_integer( $this->___testRatingByLocale( $this->sASIN, 'AE' ) );
    }

    /**
     * @return bool
     * @throws ReflectionException
     */
    public function testRatingAndReviewCount_SG() {
//        return is_integer( $this->___testRatingByLocale( $this->sASIN, 'SG' ) );
    }
    /**
     * @return bool
     * @throws ReflectionException
     */
    public function testRatingAndReviewCount_NL() {
//        return is_integer( $this->___testRatingByLocale( $this->sASIN, 'NL' ) );
    }

        /**
         * @param $sASIN
         * @param $sLocale
         * @return integer|null
         * @throws Exception
         * @throws ReflectionException
         */
        private function ___testRatingByLocale( $sASIN, $sLocale ) {
            $_oClass = new AmazonAutoLinks_MockClass( 'AmazonAutoLinks_Event___Action_HTTPRequestRating' );
            $_sURL   = $_oClass->call( '___getRatingWidgetPageURL', $sASIN, $sLocale ); // B08FWDGDS5, B08HGKZC6T
            $this->_output( 'URL: ' . $_sURL );

            $_sHTML  = $_oClass->call( '___getWidgetPage', $_sURL, 86400, false );
            $this->_output( 'HTML<hr />' . $_sHTML );
            if ( false === stripos( $_sHTML, 'acr-average-stars-rating-text'  ) ) {
                $this->_throw( 'Maybe blocked.<hr />' );
            }
            $this->_output( 'Escaped HTML<hr />' . esc_html( $_sHTML ) );

            $_oScraper      = new AmazonAutoLinks_ScraperDOM_WidgetUserRating( $_sHTML );
            $_inRating       = $_oScraper->getRating();
            $this->_output( 'Rating: ' . $_inRating );
            if ( ! isset( $_inRating ) ) {
                $this->_throw( 'Failed to retrieve the rating.' );
            }
            if ( 2 !== strlen( $_inRating ) ) {
                $this->_throw( 'The rating value must consist of two digits.' );
            }
            $_inNumberOfReviews = $_oScraper->getNumberOfReviews();
            $this->_output( 'Number of reviews: ' . $_inNumberOfReviews );
            return $_inNumberOfReviews;
        }

}