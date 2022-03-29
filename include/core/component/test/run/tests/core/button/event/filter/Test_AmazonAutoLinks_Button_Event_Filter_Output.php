<?php
/**
 * Auto Amazon Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2022 Michael Uno
 */

/**
 * Tests the class.
 *
 * @since       4.3.1
*/
class Test_AmazonAutoLinks_Button_Event_Filter_Output extends AmazonAutoLinks_UnitTest_Base {

    /**
     * @return bool
     * @throws Exception
     */
    public function test_buttonOutput_type1() {

        $_aButtonArguments = array(
            'type'              => 1,
            'id'                => 0,    // can be omitted
            'asin'              => 'B07WKNQ8JT, B00QQ4EZNM',   // comma delimited ASINs
            'quantity'          => '1',  // comma delimited ASINs
            'country'           => 'US',   // the locale (country)
            'associate_id'      => 'test-20',
            'access_key'        => '',   // public PA-API access key
            'label'             => 'TESTING',
            'offer_listing_id'  => '',   // offer listing id that Amazon gives
        );
        $_oMockedClass = new AmazonAutoLinks_MockClass( 'AmazonAutoLinks_Button_Event_Filter_Output' );
        $_sButtonHTML  = $_oMockedClass->call( 'replyToGetLinkedButton', array( '', $_aButtonArguments ) );

        if ( false === strpos( $_sButtonHTML, 'cart/add.html?' ) ) {
            throw new Exception( esc_html( $_sButtonHTML ), 'BUTTON_TYPE_NOT_MATCH' );
        }
        if ( false === strpos( $_sButtonHTML, 'B07WKNQ8JT' ) || false === strpos( $_sButtonHTML, 'B00QQ4EZNM' ) ) {
            throw new Exception( esc_html( $_sButtonHTML ), 'BUTTON_MISSING_ASIN' );
        }
        if ( false === strpos( $_sButtonHTML, 'TESTING' ) ) {
            throw new Exception( esc_html( $_sButtonHTML ), 'BUTTON_LABEL_NOT_MATCH' );
        }
        return esc_html( $_sButtonHTML );

    }
    /**
     * @purpose Tests the link type button.
     * @return bool
     * @throws Exception
     */
    public function test_buttonOutput_type0() {

        $_aButtonArguments = array(
            'type'              => 0,
            'id'                => 0,    // can be omitted
            'asin'              => 'B07WKNQ8JT, B00QQ4EZNM',   // comma delimited ASINs
            'quantity'          => '1',  // comma delimited ASINs
            'country'           => 'US',   // the locale (country)
            'associate_id'      => 'test-20',
            'access_key'        => '',   // public PA-API access key
            'label'             => 'TESTING',
            'offer_listing_id'  => '',   // offer listing id that Amazon gives
        );
        $_oMockedClass = new AmazonAutoLinks_MockClass( 'AmazonAutoLinks_Button_Event_Filter_Output' );
        $_sButtonHTML  = $_oMockedClass->call( 'replyToGetLinkedButton', array( '', $_aButtonArguments ) );
        if ( false === strpos( $_sButtonHTML, '<a ' ) ) {
            throw new Exception( 'The button is not a link.' . esc_html( $_sButtonHTML ) );
        }
        if ( false !== strpos( $_sButtonHTML, 'B00QQ4EZNM' ) ) {
            throw new Exception( 'Multiple ASINs are not supported for the link button type.<hr>' . esc_html( $_sButtonHTML ) );
        }
        if ( false === strpos( strip_tags( $_sButtonHTML ), 'TESTING' ) ) {
            throw new Exception( 'The button label does not match.<hr />' . esc_html( $_sButtonHTML ) );
        }
        if ( false === strpos( $_sButtonHTML, 'test-20' ) ) {
            throw new Exception( 'The associate tag is missing<hr />' . esc_html( $_sButtonHTML ) );
        }
        return esc_html( $_sButtonHTML );

    }

    /**
     * @purpose Checks if the button link URL is valid.
     * @return false|string If the URL is not valid, false; otherwise, the URL.
     * @throws ReflectionException
     */
    public function test_getAddToCartButton() {
        $_oMockedClass = new AmazonAutoLinks_MockClass( 'AmazonAutoLinks_Button_Event_Filter_Output' );
        $_sButtonHTML  = $_oMockedClass->call( '___getAddToCartButton', array( 'B00QQ4EZNM, B00QQ4EZNL', '', 'US', 'tester-20', 0, '' ) );
        $_oDOM         = new AmazonAutoLinks_DOM;
        $_oDoc         = $_oDOM->loadDOMFromHTMLElement( $_sButtonHTML );
        $_oAs          = $_oDoc->getElementsByTagName( 'a' );
        $_sHref        = '';
        foreach( $_oAs as $_oNodeA ) {
            $_sHref = $_oNodeA->getAttribute( 'href' );
            break;
        }
        return filter_var( $_sHref, FILTER_VALIDATE_URL );
    }

    /**
     * @purpose Checks if the button link URL is valid.
     * @return false|string If the URL is not valid, false; otherwise, the URL.
     * @throws ReflectionException
     * @throws Exception
     */
    public function test___getProductURL() {
        $_oMockedClass = new AmazonAutoLinks_MockClass( 'AmazonAutoLinks_Button_Event_Filter_Output' );
        $_aArguments   = array(
            'associate_id' => 'tester-20',
            'asin'         => 'B00QQ4EZNM, B00QQ4EZNL',
            'country'      => 'US',
        );
        $_sButtonURL  = $_oMockedClass->call( '___getProductURL', array( $_aArguments ) );
//        $_oDOM         = new AmazonAutoLinks_DOM;
//        $_oDoc         = $_oDOM->loadDOMFromHTMLElement( $_sButtonHTML );
//        $_oAs          = $_oDoc->getElementsByTagName( 'a' );
//        $_sHref        = '';
//        foreach( $_oAs as $_oNodeA ) {
//            $_sHref = $_oNodeA->getAttribute( 'href' );
//            break;
//        }
        if ( ! filter_var( $_sButtonURL, FILTER_VALIDATE_URL ) ) {
            throw new Exception( 'the href is not a url: ' . esc_url( $_sButtonURL ) );
        }
        return esc_url( $_sButtonURL );

    }

}