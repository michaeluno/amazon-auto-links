<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 */

/**
 * Tests accessing Amazon top pages.
 *
 * @package Amazon Auto Links
 * @since   4.3.5
 * @see     AmazonAutoLinks_HTTPClient
 * @tags    http
*/
class Test_AmazonAutoLinks_HTTPClient_Top extends Test_AmazonAutoLinks_HTTPClient_BestSellers {

    /**
     * @tags top
     */
    public function test_allLocales() {
        foreach( AmazonAutoLinks_Locales::getLocaleObjects() as $_sLocale => $_oLocale ) {
            $this->_testUnblocked( $_oLocale->getMarketPlaceURL(), $_sLocale );
        }
    }

}