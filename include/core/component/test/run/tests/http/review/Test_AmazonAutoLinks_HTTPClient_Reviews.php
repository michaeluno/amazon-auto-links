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
 * Tests accessing review pages.
 *
 * @package Amazon Auto Links
 * @since   4.3.4
 * @see     AmazonAutoLinks_HTTPClient
 * @tags    http
*/
class Test_AmazonAutoLinks_HTTPClient_Reviews extends Test_AmazonAutoLinks_HTTPClient_BestSellers {

    /**
     * @tags  review
     * @break
     */
    public function test_allLocales() {
        $_sASIN    = 'B08JVQDV6W'; // B07FKR6KXF // B085M66LH1
        $_aLocales = array_keys( AmazonAutoLinks_Property::$aStoreDomains );
        foreach( $_aLocales as $_iIndex => $_sLocale ) {
            $_sURL = AmazonAutoLinks_Unit_Utility::getCustomerReviewURL( $_sASIN, $_sLocale );
            $this->_testUnblocked( $_sURL, $_sLocale );
        }
    }

}