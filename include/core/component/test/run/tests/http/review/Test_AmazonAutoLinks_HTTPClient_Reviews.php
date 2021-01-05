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
     * @skip
     */
    public function test_allLocales() {
        $_sASIN    = 'B07FKR6KXF'; // B07FKR6KXF // B085M66LH1
        foreach( AmazonAutoLinks_Locales::getLocaleObjects() as $_sLocale => $_oLocale ) {
            $this->_testUnblocked( $_oLocale->getCustomerReviewURL( $_sASIN ), $_sLocale );
        }
    }

    /**
     * @tags CA
     */
    public function test_CA() {
        $_sASIN   = 'B085M66LH1';
        $_sLocale = 'CA';
        $_oLocale = new AmazonAutoLinks_Locale( $_sLocale );
        $this->_testUnblocked( $_oLocale->getCustomerReviewURL( $_sASIN ), $_sLocale );
    }

    /**
     * @tags CN
     */
    public function test_CN() {
        $_sASIN   = 'B01JRE0IU6';
        $_sLocale = 'CN';
        $_oLocale = new AmazonAutoLinks_Locale( $_sLocale );
        $this->_testUnblocked( $_oLocale->getCustomerReviewURL( $_sASIN ), $_sLocale );
    }    

    /**
     * @tags FR
     */
    public function test_FR() {
        $_sASIN   = 'B07FKR6KXF';
        $_sLocale = 'FR';
        $_oLocale = new AmazonAutoLinks_Locale( $_sLocale );
        $this->_testUnblocked( $_oLocale->getCustomerReviewURL( $_sASIN ), $_sLocale );
    }    

    /**
     * @tags DE
     */
    public function test_DE() {
        $_sASIN   = 'B07FKR6KXF';
        $_sLocale = 'DE';
        $_oLocale = new AmazonAutoLinks_Locale( $_sLocale );
        $this->_testUnblocked( $_oLocale->getCustomerReviewURL( $_sASIN ), $_sLocale );
    } 
    
    /**
     * @tags IT
     */
    public function test_IT() {
        $_sASIN   = 'B07FKR6KXF';
        $_sLocale = 'IT';
        $_oLocale = new AmazonAutoLinks_Locale( $_sLocale );
        $this->_testUnblocked( $_oLocale->getCustomerReviewURL( $_sASIN ), $_sLocale );
    }   
    
    /**
     * @tags JP
     */
    public function test_JP() {
        $_sASIN   = 'B07PFFMQ64';
        $_sLocale = 'JP';
        $_oLocale = new AmazonAutoLinks_Locale( $_sLocale );
        $this->_testUnblocked( $_oLocale->getCustomerReviewURL( $_sASIN ), $_sLocale );
    }

    /**
     * @tags UK
     */
    public function test_UK() {
        $_sASIN   = 'B07PJV3JPR';
        $_sLocale = 'UK';
        $_oLocale = new AmazonAutoLinks_Locale( $_sLocale );
        $this->_testUnblocked( $_oLocale->getCustomerReviewURL( $_sASIN ), $_sLocale );
    }

    /**
     * @tags ES
     */
    public function test_ES() {
        $_sASIN   = 'B07PJV3JPR';
        $_sLocale = 'ES';
        $_oLocale = new AmazonAutoLinks_Locale( $_sLocale );
        $this->_testUnblocked( $_oLocale->getCustomerReviewURL( $_sASIN ), $_sLocale );
    }

    /**
     * @tags US
     */
    public function test_US() {
        $_sASIN   = 'B07FKR6KXF';
        $_sLocale = 'US';
        $_oLocale = new AmazonAutoLinks_Locale( $_sLocale );
        $this->_testUnblocked( $_oLocale->getCustomerReviewURL( $_sASIN ), $_sLocale );
    }    
    
    
    
}