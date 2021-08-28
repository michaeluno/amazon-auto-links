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
 * @since   4.6.9
 * @see     AmazonAutoLinks_AdWidgetAPI_Search
 * @tags    ad-widget-api
*/
class Test_AmazonAutoLinks_AdWidgetAPI_Search extends AmazonAutoLinks_UnitTest_HTTPRequest_Base {

    /**
     * @tags UK
     */
    public function test_getEndPoint() {
        $_oAdWidgetAPISearch = new AmazonAutoLinks_AdWidgetAPI_Search( 'UK' );
        $_sEndpointURL       = $_oAdWidgetAPISearch->getEndpoint( array( 'B002U3CB7A' ) );
        $this->_getDetails( 'Endpoint', $_sEndpointURL );
        $this->_assertNotEmpty( filter_var( $_sEndpointURL, FILTER_VALIDATE_URL ) );
    }

    /**
     * @tags UK
     */
    public function test_UK() {
        $_oAdWidgetAPISearch = new AmazonAutoLinks_AdWidgetAPI_Search( 'UK' );
        $_aResponse          = $_oAdWidgetAPISearch->get(
            'B002U3CB7A|B0037472RA|B097CW7M57|B099C6SXY5|B0989WXYNX|'
            . 'B07ZZW7QCM|B005EJFLEM|B07BC4WJ9V|B018FBCUZI|B081KHS89M|'
            . 'B08WQWK1V5|B08WQWK1V5|B08ZP3V4FY|B085QW12YX|0769647197|'
            . '1786892731|0241988268|B08VDSBNB3|B0777RKM17|B08V315HWJ|'
            . 'B085581XT6',
            array(
                'multipageCount' => 20,
            )
        );
        $this->_outputDetails( 'response', $_aResponse );
        $this->_assertFalse( empty( $_aResponse ) );
    }

    /**
     * @tags multi words
     */
    public function test_multi_words() {
        $_oAdWidgetAPISearch = new AmazonAutoLinks_AdWidgetAPI_Search( 'US' );
        $_aResponse          = $_oAdWidgetAPISearch->get(
            'Microsoft Keyboard',
            array(
                'multipageCount' => 10,
            )
        );
        $this->_outputDetails( 'response', $_aResponse );
        $this->_assertFalse( empty( $_aResponse ) );
    }

    /**
     * @tags US
     */
    public function test_US() {
        $_oAdWidgetAPISearch = new AmazonAutoLinks_AdWidgetAPI_Search( 'US' );
        $_aResponse          = $_oAdWidgetAPISearch->get(
            array( 'B00000J3LO' ),
            array(
                'multipageCount' => 20,
            )
        );
        $this->_outputDetails( 'response', $_aResponse );
        $this->_assertFalse( empty( $_aResponse ) );
    }

    /**
     * @tags JP
     */
    public function test_JP() {
        $_oAdWidgetAPISearch = new AmazonAutoLinks_AdWidgetAPI_Search( 'JP' );
        $_aResponse          = $_oAdWidgetAPISearch->get(
            'iPhone',
            array(
                'multipageCount' => 20,
            )
        );
        $this->_outputDetails( 'response', $_aResponse );
        $this->_assertFalse( empty( $_aResponse ) );
    }

    /**
     * @tags CA
     */
    public function test_CA() {
        $_oAdWidgetAPISearch = new AmazonAutoLinks_AdWidgetAPI_Search( 'CA' );
        $_aResponse          = $_oAdWidgetAPISearch->get(
            array( 'mouse', 'tablet' ),
            array(
                'multipageCount' => 20,
            )
        );
        $this->_outputDetails( 'response', $_aResponse );
        $this->_assertFalse( empty( $_aResponse ) );
    }

}
