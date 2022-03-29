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
 * Tests accessing widget api responses.
 *
 * @since   4.6.9
 * @see     AmazonAutoLinks_HTTPClient
 * @tags    http
*/
class Test_AmazonAutoLinks_HTTPClient_Widget_API extends Test_AmazonAutoLinks_HTTPClient_BestSellers {

    /**
     * @tags US
     */
    public function test_US() {
        $_sASIN   = 'B07FKR6KXF';
        $_sLocale = 'US';
        $_oAdWidgetAPI = new AmazonAutoLinks_AdWidgetAPI_Search( $_sLocale );
        $this->_testUnblocked( $_oAdWidgetAPI->getEndpoint( $_sASIN ), $_sLocale, false, false );
    }

    /**
     * @tags CA
     */
    public function test_CA() {
        $_sASIN   = 'B085M66LH1';
        $_sLocale = 'CA';
        $_oLocale = new AmazonAutoLinks_Locale( $_sLocale );
        $_oAdWidgetAPI = new AmazonAutoLinks_AdWidgetAPI_Search( $_sLocale );
        $this->_testUnblocked( $_oAdWidgetAPI->getEndpoint( $_sASIN ), $_sLocale, false, false );
    }

        /**
         * @tags CN
         * @skip
         */
        public function test_CN() {
            $_sASIN   = 'B01JRE0IU6';
            $_sLocale = 'CN';
            $_oAdWidgetAPI = new AmazonAutoLinks_AdWidgetAPI_Search( $_sLocale );
            $this->_testUnblocked( $_oAdWidgetAPI->getEndpoint( $_sASIN ), $_sLocale, false, false );
        }

    /**
     * @tags FR
     */
    public function test_FR() {
        $_sASIN   = 'B07FQ4DJ7X';
        $_sLocale = 'FR';
        $_oAdWidgetAPI = new AmazonAutoLinks_AdWidgetAPI_Search( $_sLocale );
        $this->_testUnblocked( $_oAdWidgetAPI->getEndpoint( $_sASIN ), $_sLocale, false, false );
    }

    /**
     * @tags DE
     */
    public function test_DE() {
        $_sASIN   = 'B07FQ4DJ7X';
        $_sLocale = 'DE';
        $_oAdWidgetAPI = new AmazonAutoLinks_AdWidgetAPI_Search( $_sLocale );
        $this->_testUnblocked( $_oAdWidgetAPI->getEndpoint( $_sASIN ), $_sLocale, false, false );
    }

    /**
     * @tags IT
     */
    public function test_IT() {
        $_sASIN   = 'B07FQ4DJ7X';
        $_sLocale = 'IT';
        $_oAdWidgetAPI = new AmazonAutoLinks_AdWidgetAPI_Search( $_sLocale );
        $this->_testUnblocked( $_oAdWidgetAPI->getEndpoint( $_sASIN ), $_sLocale, false, false );
    }

    /**
     * @tags ES
     */
    public function test_ES() {
        $_sASIN   = 'B07ZZVWB4L';
        $_sLocale = 'ES';
        $_oAdWidgetAPI = new AmazonAutoLinks_AdWidgetAPI_Search( $_sLocale );
        $this->_testUnblocked( $_oAdWidgetAPI->getEndpoint( $_sASIN ), $_sLocale, false, false );
    }

    /**
     * @tags UK
     */
    public function test_UK() {
        $_sASIN   = 'B07PJV3JPR';
        $_sLocale = 'UK';
        $_oAdWidgetAPI = new AmazonAutoLinks_AdWidgetAPI_Search( $_sLocale );
        $this->_testUnblocked( $_oAdWidgetAPI->getEndpoint( $_sASIN ), $_sLocale, false, false );
    }

        /**
         * @tags PL
         * @skip
         */
        public function test_PL() {
            $_sASIN   = 'B085K45C3S';
            $_sLocale = 'PL';
            $_oAdWidgetAPI = new AmazonAutoLinks_AdWidgetAPI_Search( $_sLocale );
            $this->_testUnblocked( $_oAdWidgetAPI->getEndpoint( $_sASIN ), $_sLocale, false, false );
        }

        /**
         * @tags NL
         * @skip
         */
        public function test_NL() {
            $_sASIN   = 'B085K45C3S';
            $_sLocale = 'NL';
            $_oAdWidgetAPI = new AmazonAutoLinks_AdWidgetAPI_Search( $_sLocale );
            $this->_testUnblocked( $_oAdWidgetAPI->getEndpoint( $_sASIN ), $_sLocale, false, false );
        }

        /**
         * @tags SE
         * @skip
         */
        public function test_SE() {
            $_sASIN   = 'B085K45C3S';
            $_sLocale = 'SE';
            $_oAdWidgetAPI = new AmazonAutoLinks_AdWidgetAPI_Search( $_sLocale );
            $this->_testUnblocked( $_oAdWidgetAPI->getEndpoint( $_sASIN ), $_sLocale, false, false );
        }

    /**
     * @tags JP
     */
    public function test_JP() {
        $_sASIN   = 'B07PFFMQ64';
        $_sLocale = 'JP';
        $_oAdWidgetAPI = new AmazonAutoLinks_AdWidgetAPI_Search( $_sLocale );
        $this->_testUnblocked( $_oAdWidgetAPI->getEndpoint( $_sASIN ), $_sLocale, false, false );
    }

    /**
     * @tags IN
     */
    public function test_IN() {
        $_sASIN   = 'B086978F2L';
        $_sLocale = 'IN';
        $_oAdWidgetAPI = new AmazonAutoLinks_AdWidgetAPI_Search( $_sLocale );
        $this->_testUnblocked( $_oAdWidgetAPI->getEndpoint( $_sASIN ), $_sLocale, false, false );
    }

        /**
         * @tags AU
         * @skip
         */
        public function test_AU() {
            $_sASIN   = 'B07ZZW1B82';
            $_sLocale = 'AU';
            $_oAdWidgetAPI = new AmazonAutoLinks_AdWidgetAPI_Search( $_sLocale );
            $this->_testUnblocked( $_oAdWidgetAPI->getEndpoint( $_sASIN ), $_sLocale, false, false );
        }

        /**
         * @tags MX
         * @skip
         */
        public function test_MX() {
            $_sASIN   = 'B085K45C3S';
            $_sLocale = 'MX';
            $_oAdWidgetAPI = new AmazonAutoLinks_AdWidgetAPI_Search( $_sLocale );
            $this->_testUnblocked( $_oAdWidgetAPI->getEndpoint( $_sASIN ), $_sLocale, false, false );
        }

        /**
         * @tags TR
         * @skip
         */
        public function test_TR() {
            $_sASIN   = 'B01DF29XFW';
            $_sLocale = 'TR';
            $_oAdWidgetAPI = new AmazonAutoLinks_AdWidgetAPI_Search( $_sLocale );
            $this->_testUnblocked( $_oAdWidgetAPI->getEndpoint( $_sASIN ), $_sLocale, false, false );
        }
        /**
         * @tags BR
         * @skip
         */
        public function test_BR() {
            $_sASIN   = 'B07ZZW745X';
            $_sLocale = 'BR';
            $_oAdWidgetAPI = new AmazonAutoLinks_AdWidgetAPI_Search( $_sLocale );
            $this->_testUnblocked( $_oAdWidgetAPI->getEndpoint( $_sASIN ), $_sLocale, false, false );
        }
        /**
         * @tags SG
         * @skip
         */
        public function test_SG() {
            $_sASIN   = 'B095LQBP4Y';
            $_sLocale = 'SG';
            $_oAdWidgetAPI = new AmazonAutoLinks_AdWidgetAPI_Search( $_sLocale );
            $this->_testUnblocked( $_oAdWidgetAPI->getEndpoint( $_sASIN ), $_sLocale, false, false );
        }

}