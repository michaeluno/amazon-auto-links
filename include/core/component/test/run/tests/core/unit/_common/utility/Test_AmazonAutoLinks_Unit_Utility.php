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
 * Tests the AmazonAutoLinks_Unit_Utility class methods.
 *
 * @package     Amazon Auto Links
 * @since       4.3.2
 * @see         AmazonAutoLinks_Unit_Utility
*/
class Test_AmazonAutoLinks_Unit_Utility extends AmazonAutoLinks_UnitTest_Base {

    /**
     * @var AmazonAutoLinks_Unit_Utility
     */
    public $oUtil;

    /**
     * @var AmazonAutoLinks_MockClass
     */
    public $oMock;

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
        $this->oMock = new AmazonAutoLinks_MockClass( 'AmazonAutoLinks_Unit_Utility' );
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
        return $this->oUtil->getFormattedRatingFromItem( $this->aItem, 'US', 'amazonwidget-20' );
    }


}