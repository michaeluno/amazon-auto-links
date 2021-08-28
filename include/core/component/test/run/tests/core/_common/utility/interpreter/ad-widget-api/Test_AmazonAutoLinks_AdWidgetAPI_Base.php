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
 * Tests AmazonAutoLinks_HTTPClient.
 *
 * @package Amazon Auto Links
 * @since   4.7.3
 * @see     AmazonAutoLinks_AdWidgetAPI_Base
 * @tags    ad-widget-api
*/
class Test_AmazonAutoLinks_AdWidgetAPI_Base extends AmazonAutoLinks_UnitTest_HTTPRequest_Base {

    /**
     * @tags JSONP
     */
    public function test_getJSONFromJSONP_01() {
        $_sJSONP = <<<JSONP
        search_callback({results : [ ], MarketPlace: "US", InstanceId: "0"})
JSONP;
        $_aJSON = AmazonAutoLinks_AdWidgetAPI_Base::getJSONFromJSONP( $_sJSONP );
        $this->_outputDetails( 'Converted JSON', $_aJSON );
        $this->_assertTrue( is_array( $_aJSON ) );
        $this->_assertEqual( array(), $_aJSON[ 'results' ] );
        $this->_assertEqual( 'US', $_aJSON[ 'MarketPlace' ] );
        $this->_assertEqual( '0', $_aJSON[ 'InstanceId' ] );

    }

    /**
     * @tags JSONP
     */
    public function test_getJSONFromJSONP_02() {
        $_sJSONP = <<<JSONP
 search_callback({results : [ { ASIN : "B002U3CB7A" , Title : "The Overlook (Harry Bosch Book 13)" , Price : "£4.99" , ListPrice : "" , ImageUrl : "https:\/\/m.media-amazon.com\/images\/I\/510NC9Gw7mL._SL160_.jpg" , DetailPageURL : "https:\/\/www.amazon.co.uk\/dp\/B002U3CB7A" , Rating : "4.6" , TotalReviews : "2885" , Subtitle : "" , IsPrimeEligible : "0" } ], MarketPlace: "GB", InstanceId: "0"}) 
JSONP;
        $_aJSON = AmazonAutoLinks_AdWidgetAPI_Base::getJSONFromJSONP( $_sJSONP );
        $this->_outputDetails( 'Converted JSON', $_aJSON );
        $this->_assertTrue( is_array( $_aJSON ) );
        $this->_assertEqual( 1, count( $_aJSON[ 'results' ] ) );
        $this->_assertEqual( 'GB', $_aJSON[ 'MarketPlace' ] );
        $this->_assertEqual( '0', $_aJSON[ 'InstanceId' ] );
    }

}
