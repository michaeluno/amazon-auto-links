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
 * Tests accessing top pages.
 *
 * @since   4.3.4
 * @see     AmazonAutoLinks_HTTPClient
 * @tags    http, top, marketplace
*/
class Test_AmazonAutoLinks_HTTPClient_MarketPlace extends Test_AmazonAutoLinks_HTTPClient_BestSellers {

    /**
     * @purpose The IT locale has a more strict blocking standard.
     * @tags    IT
     * @break
     */
    public  function test_IT() {

        $_sLocale    = 'IT';
        $_oLocale    = new AmazonAutoLinks_Locale( $_sLocale );
        $_sURL       = $_oLocale->getMarketPlaceURL();
//        $this->_testUnblocked( $_sURL, $_sLocale );
        $_oHTTP      = new AmazonAutoLinks_HTTPClient( $_sURL, 86400, array() );
        $_aoResponse = $_oHTTP->getRawResponse();
        $_sHTTPBody  = wp_remote_retrieve_body( $_aoResponse );
        $_sHTMLBody  = $this->getHTMLBody( $_sHTTPBody );
//        $this->_output( 'HTTP' );
//        $this->_output( $_sHTMLBody );
        $this->_outputDetails( 'HTTP (Escaped)', $_sHTMLBody );

    }

}