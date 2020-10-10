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
 * Tests accessing widget pages.
 *
 * @package Amazon Auto Links
 * @since   4.3.4
 * @see     AmazonAutoLinks_HTTPClient
 * @tags    http
*/
class Test_AmazonAutoLinks_HTTPClient_WidgetPages extends Test_AmazonAutoLinks_HTTPClient_BestSellers {

    /**
     * @tags  widget
     * @break
     */
    public function test_allLocales() {
        $_sASIN = 'B07FKR6KXF';
        foreach( AmazonAutoLinks_Locales::getLocaleObjects() as $_sLocale => $_oLocale ) {
            $this->_testUnblocked( $_oLocale->getProductRatingWidgetURL( $_sASIN ), $_sLocale );
        }
    }

}