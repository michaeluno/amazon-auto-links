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
 * Tests AmazonAutoLinks_HTTPClient.
 *
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
    /**
     * @tags JSONP
     */
    public function test_getJSONFromJSONP_03() {
        $_sJSONP = <<<JSONP
search_callback({results : [ { ASIN : "1501110365" , Title : "It Ends with Us: A Novel" , Price : "$10.80" , ListPrice : "$16.99" , ImageUrl : "https:\/\/m.media-amazon.com\/images\/I\/517s9eYVoHS._SL160_.jpg" , DetailPageURL : "https:\/\/www.amazon.com\/dp\/1501110365" , Rating : "4.7" , TotalReviews : "27546" , Subtitle : "Hoover, Colleen (Paperback)" , IsPrimeEligible : "1" } , { ASIN : "B095NWYQBC" , Title : "Wyze Cam Spotlight, Wyze Cam v3 Security Camera with Spotlight Kit, 1080p HD Security Camera with Two-Way Audio and Siren, IP65 Weatherproof, Compatible with Alexa and Google Assistant" , Price : "$49.96" , ListPrice : "$52.96" , ImageUrl : "https:\/\/m.media-amazon.com\/images\/I\/21AKV7x8WpS._SL160_.jpg" , DetailPageURL : "https:\/\/www.amazon.com\/dp\/B095NWYQBC" , Rating : "4.5" , TotalReviews : "29" , Subtitle : "" , IsPrimeEligible : "1" } , { ASIN : "B09DVQ3L98" , Title : "1-Box,3 Count" , Price : "$13.99" , ListPrice : "" , ImageUrl : "https:\/\/m.media-amazon.com\/images\/I\/41uf0qweYlL._SL160_.jpg" , DetailPageURL : "https:\/\/www.amazon.com\/dp\/B09DVQ3L98" , Rating : "" , TotalReviews : "" , Subtitle : "" , IsPrimeEligible : "0" } , { ASIN : "B09DKY241W" , Title : "FUNTAINER 10 Ounce Stainless Steel Vacuum Insulated Kids Food Jar, Lime Green New" , Price : "$34.80" , ListPrice : "" , ImageUrl : "https:\/\/m.media-amazon.com\/images\/I\/31AXAs8ALqL._SL160_.jpg" , DetailPageURL : "https:\/\/www.amazon.com\/dp\/B09DKY241W" , Rating : "" , TotalReviews : "" , Subtitle : "" , IsPrimeEligible : "0" } , { ASIN : "B09C117PC3" , Title : "FUNTAINER 10 Ounce Stainless Steel Vacuum Insulated Kids Food Jar with Folding Spoon, Navy" , Price : "$26.95" , ListPrice : "" , ImageUrl : "https:\/\/m.media-amazon.com\/images\/I\/41CBYp1hZqS._SL160_.jpg" , DetailPageURL : "https:\/\/www.amazon.com\/dp\/B09C117PC3" , Rating : "" , TotalReviews : "" , Subtitle : "THERMOS" , IsPrimeEligible : "0" } , { ASIN : "9124136530" , Title : "Colleen Hoover 3 Books Collection Set (November 9, Ugly Love, It Ends with Us)" , Price : "$69.99" , ListPrice : "" , ImageUrl : "https:\/\/m.media-amazon.com\/images\/I\/51NIeIwATWL._SL160_.jpg" , DetailPageURL : "https:\/\/www.amazon.com\/dp\/9124136530" , Rating : "" , TotalReviews : "" , Subtitle : "Colleen Hoover (Paperback)" , IsPrimeEligible : "0" } , { ASIN : "B09CKBKX6H" , Title : "Cool Coolers Freezer Slim Ice Pack for Lunch Box, Set of 4, Large, Blue" , Price : "$38.99" , ListPrice : "" , ImageUrl : "https:\/\/m.media-amazon.com\/images\/I\/410xL8ALuXL._SL160_.jpg" , DetailPageURL : "https:\/\/www.amazon.com\/dp\/B09CKBKX6H" , Rating : "4.7" , TotalReviews : "7816" , Subtitle : "Fit & Fresh" , IsPrimeEligible : "0" } , { ASIN : "B09DPC5SGP" , Title : "Vacuum Insulated Kids Straw Bottle, Glitter Gold, Stainless Steel (12 Ounce)" , Price : "$43.00" , ListPrice : "" , ImageUrl : "https:\/\/m.media-amazon.com\/images\/I\/31v96Jen8JL._SL160_.jpg" , DetailPageURL : "https:\/\/www.amazon.com\/dp\/B09DPC5SGP" , Rating : "" , TotalReviews : "" , Subtitle : "" , IsPrimeEligible : "0" } , { ASIN : "B089M9MNZF" , Title : "by Hoover, Colleen :: It Ends with Us: A Novel-Paperback" , Price : "$15.00" , ListPrice : "" , ImageUrl : "https:\/\/m.media-amazon.com\/images\/I\/51VnlTzfMML._SL160_.jpg" , DetailPageURL : "https:\/\/www.amazon.com\/dp\/B089M9MNZF" , Rating : "" , TotalReviews : "" , Subtitle : "" , IsPrimeEligible : "0" } , { ASIN : "B09CYXVDKD" , Title : "Autoseal West Loop Vaccuum-Insulated Stainless Steel Travel Mug, 20 Oz, Black - new" , Price : "$40.00" , ListPrice : "" , ImageUrl : "https:\/\/m.media-amazon.com\/images\/I\/319D3Nn1QZL._SL160_.jpg" , DetailPageURL : "https:\/\/www.amazon.com\/dp\/B09CYXVDKD" , Rating : "4.7" , TotalReviews : "111321" , Subtitle : "Contigo" , IsPrimeEligible : "0" } ], MarketPlace: "US", InstanceId: "0"}) 
JSONP;
        $_aJSON = AmazonAutoLinks_AdWidgetAPI_Base::getJSONFromJSONP( $_sJSONP );
        $this->_outputDetails( 'Converted JSON', $_aJSON );
        $this->_assertTrue( is_array( $_aJSON ) );
        $this->_assertEqual( 10, count( $_aJSON[ 'results' ] ) );
        $this->_assertEqual( 'US', $_aJSON[ 'MarketPlace' ] );
        $this->_assertEqual( '0', $_aJSON[ 'InstanceId' ] );
    }
    /**
     * @tags JSONP
     * @purpose The parsing text includes an escaped double quote (\") in it.
     */
    public function test_getJSONFromJSONP_04() {
        $_sJSONP = <<<JSONP
search_callback({results : [ { ASIN : "B08H6YZY3Y" , Title : "HP Chromebook 11-inch Laptop - Up to 15 Hour Battery Life - MediaTek - MT8183 - 4 GB RAM - 32 GB eMMC Storage - 11.6-inch HD Display - with Chrome OS - (11a-na0021nr, 2020 Model, Snow White)" , Price : "$208.00" , ListPrice : "$222.35" , ImageUrl : "https:\/\/m.media-amazon.com\/images\/I\/319yaFJesXL._SL160_.jpg" , DetailPageURL : "https:\/\/www.amazon.com\/dp\/B08H6YZY3Y" , Rating : "4.5" , TotalReviews : "1128" , Subtitle : "" , IsPrimeEligible : "0" } , { ASIN : "B08YKBYP62" , Title : "HP Chromebook x360 14a 2-in-1 Laptop, Intel Pentium Silver N5000 Processor, 4 GB RAM, 64 GB eMMC, 14\" HD Display, Chrome OS with Webcam & Dual Mics, Work, Play, Long Battery Life (14a-ca0022nr, 2021)" , Price : "$289.99" , ListPrice : "$359.99" , ImageUrl : "https:\/\/m.media-amazon.com\/images\/I\/41GmM8jHrdL._SL160_.jpg" , DetailPageURL : "https:\/\/www.amazon.com\/dp\/B08YKBYP62" , Rating : "4.5" , TotalReviews : "411" , Subtitle : "" , IsPrimeEligible : "1" } , { ASIN : "B086383HC7" , Title : "Lenovo Chromebook Flex 5 13\" Laptop, FHD (1920 x 1080) Touch Display, Intel Core i3-10110U Processor, 4GB DDR4 Onboard RAM, 64GB eMMC, Intel Integrated Graphics, Chrome OS, 82B80006UX, Graphite Grey" , Price : "$367.27" , ListPrice : "$429.99" , ImageUrl : "https:\/\/m.media-amazon.com\/images\/I\/41V9WE7K19S._SL160_.jpg" , DetailPageURL : "https:\/\/www.amazon.com\/dp\/B086383HC7" , Rating : "4.5" , TotalReviews : "2962" , Subtitle : "" , IsPrimeEligible : "1" } , { ASIN : "1911174827" , Title : "Essential Chromebook: The Illustrated Guide to Using Chromebook (Computer Essentials)" , Price : "$13.99" , ListPrice : "$23.99" , ImageUrl : "https:\/\/m.media-amazon.com\/images\/I\/41TtrNtABfL._SL160_.jpg" , DetailPageURL : "https:\/\/www.amazon.com\/dp\/1911174827" , Rating : "4.3" , TotalReviews : "170" , Subtitle : "Kevin Wilson (Paperback)" , IsPrimeEligible : "1" } , { ASIN : "B08529CQPK" , Title : "HP Chromebook 14-inch FHD Laptop, Intel Celeron N4000, 4 GB RAM, 32 GB eMMC, Chrome (14a-na0050nr, Mineral Silver)" , Price : "$220.99" , ListPrice : "$339.99" , ImageUrl : "https:\/\/m.media-amazon.com\/images\/I\/51-CT1+nmqL._SL160_.jpg" , DetailPageURL : "https:\/\/www.amazon.com\/dp\/B08529CQPK" , Rating : "4.5" , TotalReviews : "4883" , Subtitle : "" , IsPrimeEligible : "1" } , { ASIN : "B08DTJC9N7" , Title : "Acer - Chromebook Spin 713 2-in-1 13.5\" 2K VertiView 3:2 Touch - Intel i5-10210U - 8GB Memory - 128GB SSD – Steel Gray" , Price : "$502.99" , ListPrice : "$528.00" , ImageUrl : "https:\/\/m.media-amazon.com\/images\/I\/41Rq80RUiqL._SL160_.jpg" , DetailPageURL : "https:\/\/www.amazon.com\/dp\/B08DTJC9N7" , Rating : "4.5" , TotalReviews : "139" , Subtitle : "" , IsPrimeEligible : "0" } , { ASIN : "B094K28536" , Title : "ASUS Chromebook Detachable CM3, 10.5\" Touchscreen WUXGA 16:10 Display, MediaTek 8183 Processor 64GB Storage, 4GB RAM, Garaged USI Stylus, Chrome OS, Aluminum, Mineral Gray, CM3000DVA-DS44T-S" , Price : "$369.99" , ListPrice : "" , ImageUrl : "https:\/\/m.media-amazon.com\/images\/I\/31q+Tbz32mS._SL160_.jpg" , DetailPageURL : "https:\/\/www.amazon.com\/dp\/B094K28536" , Rating : "4.2" , TotalReviews : "11" , Subtitle : "" , IsPrimeEligible : "1" } , { ASIN : "B088TDDNGT" , Title : "Galaxy Chromebook (256GB Storage, 8GB RAM), Mercury Gray" , Price : "$699.00" , ListPrice : "$999.99" , ImageUrl : "https:\/\/m.media-amazon.com\/images\/I\/41rfah1ReYL._SL160_.jpg" , DetailPageURL : "https:\/\/www.amazon.com\/dp\/B088TDDNGT" , Rating : "4.2" , TotalReviews : "325" , Subtitle : "" , IsPrimeEligible : "1" } , { ASIN : "B083ZB9YQ6" , Title : "ASUS Chromebook Flip C436 2-in-1 Laptop, 14\" Touchscreen FHD 4-Way NanoEdge, Intel Core i3-10110U, 128GB PCIe SSD, Fingerprint, Backlit KB, Wi-Fi 6, Chrome OS, C436FA-DS388T, Magnesium-Alloy, Silver" , Price : "$769.12" , ListPrice : "" , ImageUrl : "https:\/\/m.media-amazon.com\/images\/I\/41rNEsmix4L._SL160_.jpg" , DetailPageURL : "https:\/\/www.amazon.com\/dp\/B083ZB9YQ6" , Rating : "4.4" , TotalReviews : "398" , Subtitle : "" , IsPrimeEligible : "1" } , { ASIN : "B096VBL99G" , Title : "ASUS Chromebook Flip CM5, 15.6\" Touchscreen Full HD NanoEdge Display, AMD Ryzen 3 3250C Processor, 64GB eMMC, 4GB RAM, Backlit Keyboard, Wi-Fi 6, Chrome OS, Aluminum, Mineral Gray, CM5500FDA-DS344T" , Price : "$499.99" , ListPrice : "" , ImageUrl : "https:\/\/m.media-amazon.com\/images\/I\/41-Pcz4KRyS._SL160_.jpg" , DetailPageURL : "https:\/\/www.amazon.com\/dp\/B096VBL99G" , Rating : "4.5" , TotalReviews : "50" , Subtitle : "" , IsPrimeEligible : "1" } ], MarketPlace: "US", InstanceId: "0"})  
JSONP;
        $_aJSON = AmazonAutoLinks_AdWidgetAPI_Base::getJSONFromJSONP( $_sJSONP );
        $this->_outputDetails( 'Converted JSON', $_aJSON );
        $this->_assertTrue( is_array( $_aJSON ) );
        $this->_assertEqual( 10, count( $_aJSON[ 'results' ] ) );
        $this->_assertEqual( 'US', $_aJSON[ 'MarketPlace' ] );
        $this->_assertEqual( '0', $_aJSON[ 'InstanceId' ] );
    }
}
