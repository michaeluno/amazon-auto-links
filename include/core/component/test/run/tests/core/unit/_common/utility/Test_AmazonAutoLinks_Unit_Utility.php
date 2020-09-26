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
 * Tests the AmazonAutoLinks_Unit_Utility class methods.
 *
 * @package     Amazon Auto Links
 * @since       4.3.2
*/
class Test_AmazonAutoLinks_Unit_Utility extends AmazonAutoLinks_UnitTest_Base {

    /**
     * @var AmazonAutoLinks_Unit_Utility
     */
    public $oUtil;

    /**
     * @var array An example product item imitating API resopnse item.
     */
    public $aItem = array(
        'ASIN' => 'ABCDEFGHIJ',
        'CustomerReviews' => array(
            'Count' => 1803,
            'StarRating' => array(
                'Value' => 4.6
            ),
        ),
    );

    public function __construct() {
        $this->oUtil = new AmazonAutoLinks_Unit_Utility;
    }

    /**
     * @throws Exception
     */
    public function test_getRatingFromItem() {
        return 46 === $this->oUtil->getRatingFromItem( $this->aItem );
    }

    public function test_getReviewCountFromItem() {
        return 1803 === $this->oUtil->getReviewCountFromItem( $this->aItem );
    }

    public function test_getFormattedRatingFromItem() {
        return $this->oUtil->getFormattedRatingFromItem( $this->aItem, 'US' );
    }

    public function test_getCustomerReviewURL() {
        return 'https://www.amazon.com/product-reviews/1234567890'
            === $this->oUtil->getCustomerReviewURL( '1234567890', 'US' );
    }

}