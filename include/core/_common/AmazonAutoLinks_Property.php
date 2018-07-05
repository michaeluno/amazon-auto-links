<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2018 Michael Uno
 */

/**
 * Properties of Amazon store info.
 * 
 * @package     Amazon Auto Links
 * @since       2.0.0
 * @since       3       Changed the name from `AmazonAutoLinks_Properties`.
*/
final class AmazonAutoLinks_Property {
    
    /**
     * 
     * @see                http://php.net/manual/en/function.mb-language.php
     */
    static public $aCategoryPageMBLanguages = array(
        'CA'    => 'uni',
        'CN'    => 'uni',
        'FR'    => 'uni',
        'DE'    => 'uni',
        'IT'    => 'uni',
        'JP'    => 'ja',
        'UK'    => 'en',
        'ES'    => 'uni',
        'US'    => 'en',    
        'IN'    => 'uni',    
        'BR'    => 'uni',
        'MX'    => 'uni',
        'AU'    => 'uni',   // 3.5.5+
    );
    static public $aCategoryRootURLs = array(
        'CA'    => 'http://www.amazon.ca/gp/bestsellers/',
        'CN'    => 'http://www.amazon.cn/gp/bestsellers/',
        'FR'    => 'http://www.amazon.fr/gp/bestsellers/',
        'DE'    => 'http://www.amazon.de/gp/bestsellers/',
        'IT'    => 'http://www.amazon.it/gp/bestsellers/',
        'JP'    => 'http://www.amazon.co.jp/gp/bestsellers/',
        'UK'    => 'http://www.amazon.co.uk/gp/bestsellers/',
        'ES'    => 'http://www.amazon.es/gp/bestsellers/',
        'US'    => 'http://www.amazon.com/gp/bestsellers/',
        'IN'    => 'http://www.amazon.in/gp/bestsellers/',
        'BR'    => 'http://www.amazon.com.br/gp/bestsellers/',
        'MX'    => 'http://www.amazon.com.mx/gp/bestsellers/',
        'AU'    => 'http://www.amazon.com.au/gp/bestsellers/',
    );
    static public $aCategoryBlackCurtainURLs = array(
        'CA'    => 'https://www.amazon.ca/gp/product/black-curtain-redirect.html',
        'CN'    => 'https://www.amazon.cn/gp/product/black-curtain-redirect.html',
        'FR'    => 'https://www.amazon.fr/gp/product/black-curtain-redirect.html',
        'DE'    => 'https://www.amazon.de/gp/product/black-curtain-redirect.html',
        'IT'    => 'https://www.amazon.it/gp/product/black-curtain-redirect.html',
        'JP'    => 'https://www.amazon.co.jp/gp/product/black-curtain-redirect.html',
        'UK'    => 'https://www.amazon.co.uk/gp/product/black-curtain-redirect.html',
        'ES'    => 'https://www.amazon.es/gp/product/black-curtain-redirect.html',
        'US'    => 'https://www.amazon.com/gp/product/black-curtain-redirect.html',
        'IN'    => 'https://www.amazon.in/gp/product/black-curtain-redirect.html',
        'BR'    => 'https://www.amazon.com.br/gp/product/black-curtain-redirect.html',
        'MX'    => 'https://www.amazon.com.mx/gp/product/black-curtain-redirect.html',
        'AU'    => 'https://www.amazon.com.au/gp/product/black-curtain-redirect.html',   // 3.5.5+
    );

    static public $aNoImageAvailable = array(    // the domain can be g-ecx.images-amazon.com
        'CA'    => 'http://g-images.amazon.com/images/G/01/x-site/icons/no-img-sm.gif',
        'CN'    => 'http://g-images.amazon.com/images/G/28/x-site/icons/no-img-sm.gif',
        'FR'    => 'http://g-images.amazon.com/images/G/08/x-site/icons/no-img-sm.gif',
        'DE'    => 'http://g-images.amazon.com/images/G/03/x-site/icons/no-img-sm.gif',
        'IT'    => 'http://g-images.amazon.com/images/G/29/x-site/icons/no-img-sm.gif',
        'JP'    => 'http://g-images.amazon.com/images/G/09/x-site/icons/no-img-sm.gif',
        'UK'    => 'http://g-images.amazon.com/images/G/01/x-site/icons/no-img-sm.gif',
        'ES'    => 'http://g-images.amazon.com/images/G/30/x-site/icons/no-img-sm.gif',
        'US'    => 'http://g-images.amazon.com/images/G/01/x-site/icons/no-img-sm.gif',
        'IN'    => 'http://g-images.amazon.com/images/G/01/x-site/icons/no-img-sm.gif',
        'BR'    => 'http://g-images.amazon.com/images/G/01/x-site/icons/no-img-sm.gif',    // should be Portuguese but could not find
        'MX'    => 'http://g-images.amazon.com/images/G/30/x-site/icons/no-img-sm.gif',    // Spanish
        'AU'    => 'http://g-images.amazon.com/images/G/01/x-site/icons/no-img-sm.gif',    // 3.5.5+
    );

    /**
     * @since       3.1.0
     * @see         http://docs.aws.amazon.com/AWSECommerceService/latest/DG/AddToCartForm.html
     */
    static public $aAddToCartURLs = array(
        'CA' => 'www.amazon.ca/gp/aws/cart/add.html',
            'CN' => 'www.amazon.cn/gp/aws/cart/add.html',
        'FR' => 'www.amazon.fr/gp/aws/cart/add.html',
        'DE' => 'www.amazon.de/gp/aws/cart/add.html',
            'IT' => 'www.amazon.it/gp/aws/cart/add.html',
        'JP' => 'www.amazon.co.jp/gp/aws/cart/add.html',
        'UK' => 'www.amazon.co.uk/gp/aws/cart/add.html',
            'ES' => 'www.amazon.es/gp/aws/cart/add.html',
        'US' => 'www.amazon.com/gp/aws/cart/add.html',
        'US' => 'www.amazon.com/gp/aws/cart/add.html',
            'IN' => 'www.amazon.in/gp/aws/cart/add.html',
            'BR'    => 'www.amazon.com.br/gp/aws/cart/add.html',
            'MX'    => 'www.amazon.com.mx/gp/aws/cart/add.html',
        'AU' => 'www.amazon.com.au/gp/aws/cart/add.html',   // 3.5.5+
    );

    static public $aTokens = array(
        'CA' => 'bWl1bm9zb2Z0Y2EtMjA=',
        'CN' => 'bWl1bm9zb2Z0LTIz',
        'FR' => 'bWl1bm9zb2Z0ZnItMjE=',
        'DE' => 'bWl1bm9zb2Z0ZGUtMjE=',
        'IT' => 'bWl1bm9zb2Z0LTIx',
        'JP' => 'bWl1bm9zb2Z0LTIy',
        'UK' => 'bWl1bm9zb2Z0dWstMjE=',
        'ES' => 'bWl1bm9zb2Z0ZXMtMjE=',
        'US' => 'bWl1bm9zb2Z0LTIw',
        'MX' => 'bWl1bm9zb2Z0LTIw', // 3.5.5+
        'AU' => 'bWl1bm9zb2Z0LTIw', // 3.5.5+
    );

    /**
     * Returns an array of search index of the specified locale.
     *
     * @see                http://docs.aws.amazon.com/AWSECommerceService/latest/DG/APPNDX_SearchIndexValues.html
     * @remark             The above link is no longer available.
     * @see                https://docs.aws.amazon.com/AWSECommerceService/latest/DG/localevalues.html
     * @remark             The `AU` locale is missing in the AWS documentation.
     */
    static public function getSearchIndexByLocale( $sLocale ) {

        switch ( strtoupper( $sLocale ) ) {
            // @see https://docs.aws.amazon.com/AWSECommerceService/latest/DG/LocaleCA.html
            case 'CA':
                return array(
                    'All'                   => __( 'All', 'amazon-auto-links' ),
                    'Apparel'               => __( 'Clothing & Accessories', 'amazon-auto-links' ),
                    'Automotive'            => __( 'Automotive', 'amazon-auto-links' ),
                    'Baby'                  => __( 'Baby', 'amazon-auto-links' ),
                    'Beauty'                => __( 'Beauty', 'amazon-auto-links' ),
                    'Blended'               => __( 'Blended', 'amazon-auto-links' ),
                    'Books'                 => __( 'Books', 'amazon-auto-links' ),
                    'Classical'             => __( 'Classical', 'amazon-auto-links' ),
                    'DVD'                   => __( 'DVD', 'amazon-auto-links' ),
                    'Electronics'           => __( 'Electronics', 'amazon-auto-links' ),
                    'GiftCards'             => __( 'Gift Cards', 'amazon-auto-links' ),
                    'Grocery'               => __( 'Grocery & Gourmet Food', 'amazon-auto-links' ),
                    'ForeignBooks'          => __( 'Foreign Books', 'amazon-auto-links' ),
                    'HealthPersonalCare'    => __( 'Health Personal Care', 'amazon-auto-links' ),
                    'Industrial'            => __( 'Industrial & Scientific', 'amazon-auto-links' ),
                    'Jewelry'               => __( 'Jewelry', 'amazon-auto-links' ),
                    'KindleStore'           => __( 'Kindle Store', 'amazon-auto-links' ),
                    'Kitchen'               => __( 'Home & Kitchen', 'amazon-auto-links' ),
                    'LawnAndGarden'         => __( 'Lawn and Garden', 'amazon-auto-links' ),
                    'Luggage'               => __( 'Luggage & Bags', 'amazon-auto-links' ),    // 3.5.5+
                    'MobileApps'            => __( 'Apps & Games', 'amazon-auto-links' ),      // 3.5.5+
                    'Music'                 => __( 'Music', 'amazon-auto-links' ),
                    'MusicalInstruments'    => __( 'Musical Instruments, Stage & Studio', 'amazon-auto-links' ), // 3.5.5+
                    'OfficeProducts'        => __( 'Office Products', 'amazon-auto-links' ), // 3.5.5+
                    'PetSupplies'           => __( 'Pet Supplies', 'amazon-auto-links' ),   // 2.1.0+
                    'Shoes'                 => __( 'Shoes & Handbags', 'amazon-auto-links' ),   // 3.5.5+
                    'Software'              => __( 'Software', 'amazon-auto-links' ),
                    'SoftwareVideoGames'    => __( 'Software Video Games', 'amazon-auto-links' ),
                    'SportingGoods'         => __( 'Sports & Outdoors', 'amazon-auto-links' ),  // 3.5.5+
                    'Tools'                 => __( 'Tools & Home ImprovementToys', 'amazon-auto-links' ),   // 3.5.5+
                    'Toys'                  => __( 'Toys & Games', 'amazon-auto-links' ),   // 2.1.0+
                    'VHS'                   => __( 'VHS', 'amazon-auto-links' ),
                    'Video'                 => __( 'Video', 'amazon-auto-links' ),
                    'VideoGames'            => __( 'Video Games', 'amazon-auto-links' ),
                    'Watches'               => __( 'Watches', 'amazon-auto-links' ), // 3.5.5+
                );
            // @see https://docs.aws.amazon.com/AWSECommerceService/latest/DG/LocaleCN.html
            case 'CN':
                return array(
                    'All'                   => __( 'All', 'amazon-auto-links' ),
                    'Apparel'               => __( 'Apparel', 'amazon-auto-links' ),
                    'Appliances'            => __( 'Appliances', 'amazon-auto-links' ),
                    'Automotive'            => __( 'Automotive', 'amazon-auto-links' ),
                    'Baby'                  => __( 'Baby', 'amazon-auto-links' ),
                    'Beauty'                => __( 'Beauty', 'amazon-auto-links' ),
                    'Books'                 => __( 'Books', 'amazon-auto-links' ),
                    'Electronics'           => __( 'Electronics', 'amazon-auto-links' ),
                    'Grocery'               => __( 'Grocery', 'amazon-auto-links' ),
                    'HealthPersonalCare'    => __( 'Health Personal Care', 'amazon-auto-links' ),
                    'Home'                  => __( 'Home', 'amazon-auto-links' ),
                    'HomeImprovement'       => __( 'Home Improvement', 'amazon-auto-links' ),
                    'Jewelry'               => __( 'Jewelry', 'amazon-auto-links' ),
                    'KindleStore'           => __( 'Kindle Store', 'amazon-auto-links' ),
                    'Miscellaneous'         => __( 'Miscellaneous', 'amazon-auto-links' ),   // missing in recent documentation
                    'Kitchen'               => __( 'Kitchen', 'amazon-auto-links' ),    // 3.5.5+
                    'MobileApps'            => __( 'Mobile Apps', 'amazon-auto-links' ),
                    'Music'                 => __( 'Music', 'amazon-auto-links' ),    // 3.5.5+
                    'MusicalInstruments'    => __( 'MusicalInstruments', 'amazon-auto-links' ),    // 2.1.0+
                    'OfficeProducts'        => __( 'Office Products', 'amazon-auto-links' ),
                    'PCHardware'            => __( 'PCHardware', 'amazon-auto-links' ),    // 3.5.5+
                    'PetSupplies'           => __( 'Pet Supplies', 'amazon-auto-links' ),
                    'Photo'                 => __( 'Photo', 'amazon-auto-links' ),
                    'Shoes'                 => __( 'Shoes', 'amazon-auto-links' ),
                    'Software'              => __( 'Software', 'amazon-auto-links' ),
                    'SportingGoods'         => __( 'Sporting Goods', 'amazon-auto-links' ),
                        'Toys'                  => __( 'Toys', 'amazon-auto-links' ),   // missing in recent documentation
                    'Video'                 => __( 'Video', 'amazon-auto-links' ),
                    'VideoGames'            => __( 'Video Games', 'amazon-auto-links' ),
                    'Watches'               => __( 'Watches', 'amazon-auto-links' ),
                );
            // @see https://docs.aws.amazon.com/AWSECommerceService/latest/DG/LocaleDE.html
            case 'DE':
                return array(
                    'All'                   => __( 'All', 'amazon-auto-links' ),
                    'Apparel'               => __( 'Apparel', 'amazon-auto-links' ),
                    'Appliances'            => __( 'Appliances', 'amazon-auto-links' ), // 3.5.5+
                    'Automotive'            => __( 'Automotive', 'amazon-auto-links' ),
                    'Baby'                  => __( 'Baby', 'amazon-auto-links' ),
                    'Beauty'                => __( 'Beauty', 'amazon-auto-links' ),
                    'Blended'               => __( 'Blended', 'amazon-auto-links' ),
                    'Books'                 => __( 'Books', 'amazon-auto-links' ),
                    'Classical'             => __( 'Classical', 'amazon-auto-links' ),
                    'DVD'                   => __( 'DVD', 'amazon-auto-links' ),
                    'Electronics'           => __( 'Electronics', 'amazon-auto-links' ),
                    'ForeignBooks'          => __( 'Foreign Books', 'amazon-auto-links' ),
                    'Grocery'               => __( 'Grocery', 'amazon-auto-links' ),
                    'Handmade'              => __( 'Handmade', 'amazon-auto-links' ), // 3.5.5+
                    'HealthPersonalCare'    => __( 'Health Personal Care', 'amazon-auto-links' ),
                    'HomeGarden'            => __( 'Home Garden', 'amazon-auto-links' ),
                        'HomeImprovement'       => __( 'Home Improvement', 'amazon-auto-links' ), // missing in recent documentation
                    'Industrial'            => __( 'Industrial', 'amazon-auto-links' ),
                    'Jewelry'               => __( 'Jewelry', 'amazon-auto-links' ),
                    'KindleStore'           => __( 'Kindle Store', 'amazon-auto-links' ),
                    'Kitchen'               => __( 'Kitchen', 'amazon-auto-links' ),
                    'Lighting'              => __( 'Lighting', 'amazon-auto-links' ),
                    'Luggage'               => __( 'Luggage', 'amazon-auto-links' ),      // 2.1.0+
                    'Magazines'             => __( 'Magazines', 'amazon-auto-links' ),
                    'Marketplace'           => __( 'Marketplace', 'amazon-auto-links' ),
                    'MobileApps'            => __( 'Mobile Apps', 'amazon-auto-links' ),   // 2.1.0+
                    'MP3Downloads'          => __( 'MP3 Downloads', 'amazon-auto-links' ),
                    'Music'                 => __( 'Music', 'amazon-auto-links' ),
                    'MusicalInstruments'    => __( 'Musical Instruments', 'amazon-auto-links' ),
                        'MusicTracks'           => __( 'Music Tracks', 'amazon-auto-links' ), // missing in recent documentation
                    'OfficeProducts'        => __( 'Office Products', 'amazon-auto-links' ),
                        'OutdoorLiving'         => __( 'Outdoor Living', 'amazon-auto-links' ), // missing in recent documentation
                        'Outlet'                => __( 'Outlet', 'amazon-auto-links' ), // missing in recent documentation
                    'Pantry'                => __( 'Pantry', 'amazon-auto-links' ), // 3.5.5+
                    'PCHardware'            => __( 'PC Hardware', 'amazon-auto-links' ),
                    'PetSupplies'            => __( 'PetSupplies', 'amazon-auto-links' ),   // 3.5.5+
                    'Photo'                 => __( 'Photo', 'amazon-auto-links' ),
                    'Shoes'                 => __( 'Shoes', 'amazon-auto-links' ),
                    'Software'              => __( 'Software', 'amazon-auto-links' ),
                        'SoftwareVideoGames'    => __( 'Software Video Games', 'amazon-auto-links' ),   // missing in recent documentation
                    'SportingGoods'         => __( 'Sporting Goods', 'amazon-auto-links' ),
                    'Tools'                 => __( 'Tools', 'amazon-auto-links' ),
                    'Toys'                  => __( 'Toys', 'amazon-auto-links' ),
                    'UnboxVideo'            => __( 'Unbox Video', 'amazon-auto-links' ), // 3.5.5+
                        'VHS'                   => __( 'VHS', 'amazon-auto-links' ),    // missing in recent documentation
                        'Video'                 => __( 'Video', 'amazon-auto-links' ),  // missing in recent documentation
                    'VideoGames'            => __( 'Video Games', 'amazon-auto-links' ),
                    'Watches'               => __( 'Watches', 'amazon-auto-links' ),
                );
            // @see https://docs.aws.amazon.com/AWSECommerceService/latest/DG/LocaleES.html
            case 'ES':
                return array(
                    'All'                   => __( 'All', 'amazon-auto-links' ),
                    'Apparel'               => __( 'Apparel', 'amazon-auto-links' ),    // 3.5.5+
                    'Automotive'            => __( 'Automotive', 'amazon-auto-links' ),
                    'Baby'                  => __( 'Baby', 'amazon-auto-links' ),
                    'Beauty'                => __( 'Beauty', 'amazon-auto-links' ), // 3.5.5+
                    'Books'                 => __( 'Books', 'amazon-auto-links' ),
                    'DVD'                   => __( 'DVD', 'amazon-auto-links' ),
                    'Electronics'           => __( 'Electronics', 'amazon-auto-links' ),
                    'ForeignBooks'          => __( 'Foreign Books', 'amazon-auto-links' ),
                    'GiftCards'             => __( 'Gift Cards', 'amazon-auto-links' ),    // 3.5.5+
                    'Grocery'               => __( 'Grocery', 'amazon-auto-links' ),    // 3.5.5+
                    'Handmade'              => __( 'Handmade', 'amazon-auto-links' ),    // 3.5.5+
                    'HealthPersonalCare'    => __( 'HealthPersonalCare', 'amazon-auto-links' ),    // 3.5.5+
                    'Industrial'            => __( 'Industrial', 'amazon-auto-links' ),    // 3.5.5+
                    'Jewelry'               => __( 'Jewelry', 'amazon-auto-links' ),    // 3.5.5+
                    'KindleStore'           => __( 'Kindle Store', 'amazon-auto-links' ),
                    'Kitchen'               => __( 'Kitchen', 'amazon-auto-links' ),
                    'LawnAndGarden'         => __( 'Lawn And Garden', 'amazon-auto-links' ), // 3.5.5+
                    'Lighting'              => __( 'Lighting', 'amazon-auto-links' ), // 3.5.5+
                    'Luggage'               => __( 'Luggage', 'amazon-auto-links' ), // 3.5.5+
                    'MobileApps'            => __( 'Mobile Apps', 'amazon-auto-links' ),   // 2.1.0+
                    'MP3Downloads'          => __( 'MP3 Downloads', 'amazon-auto-links' ),
                    'Music'                 => __( 'Music', 'amazon-auto-links' ),
                    'MusicalInstruments'    => __( 'Musical Instruments', 'amazon-auto-links' ), // 3.5.5+
                    'OfficeProducts'        => __( 'OfficeProducts', 'amazon-auto-links' ), // 3.5.5+
                    'PCHardware'            => __( 'PCHardware', 'amazon-auto-links' ), // 3.5.5+
                    'Shoes'                 => __( 'Shoes', 'amazon-auto-links' ),
                    'Software'              => __( 'Software', 'amazon-auto-links' ),
                    'SportingGoods'         => __( 'Sporting Goods', 'amazon-auto-links' ), // 2.1.0+
                    'Tools'                 => __( 'Tools', 'amazon-auto-links' ), // 3.5.5+
                    'Toys'                  => __( 'Toys', 'amazon-auto-links' ),
                    'VideoGames'            => __( 'Video Games', 'amazon-auto-links' ),
                    'Watches'               => __( 'Watches', 'amazon-auto-links' ),
                );
            // @see https://docs.aws.amazon.com/AWSECommerceService/latest/DG/LocaleFR.html
            case 'FR':
                return array(
                    'All'                   => __( 'All', 'amazon-auto-links' ),
                    'Apparel'               => __( 'Apparel', 'amazon-auto-links' ),
                        'Automotive'            => __( 'Automotive', 'amazon-auto-links' ), // missing in recent documentation
                    'Appliances'            => __( 'Appliances', 'amazon-auto-links' ), // 3.5.5+
                    'Baby'                  => __( 'Baby', 'amazon-auto-links' ),
                    'Beauty'                => __( 'Beauty', 'amazon-auto-links' ),
                    'Blended'               => __( 'Blended', 'amazon-auto-links' ),
                    'Books'                 => __( 'Books', 'amazon-auto-links' ),
                    'Classical'             => __( 'Classical', 'amazon-auto-links' ),
                    'DVD'                   => __( 'DVD', 'amazon-auto-links' ),
                    'Electronics'           => __( 'Electronics', 'amazon-auto-links' ),
                    'ForeignBooks'          => __( 'Foreign Books', 'amazon-auto-links' ),
                    'GiftCards'             => __( 'GiftCards', 'amazon-auto-links' ),  // 3.5.5+
                    'Grocery'               => __( 'Grocery', 'amazon-auto-links' ),  // 3.5.5+
                    'Handmade'              => __( 'Handmade', 'amazon-auto-links' ),  // 3.5.5+
                    'HealthPersonalCare'    => __( 'Health Personal Care', 'amazon-auto-links' ),
                    'HomeImprovement'       => __( 'Home Improvement', 'amazon-auto-links' ),
                    'Industrial'            => __( 'Industrial', 'amazon-auto-links' ), // 3.5.5+
                    'Jewelry'               => __( 'Jewelry', 'amazon-auto-links' ),
                    'KindleStore'           => __( 'Kindle Store', 'amazon-auto-links' ),
                    'Kitchen'               => __( 'Kitchen', 'amazon-auto-links' ),
                    'LawnAndGarden'         => __( 'LawnAndGarden', 'amazon-auto-links' ),  // 3.5.5+
                    'Lighting'              => __( 'Lighting', 'amazon-auto-links' ),
                    'Luggage'               => __( 'Luggage', 'amazon-auto-links' ),      // 2.1.0+
                    'Marketplace'           => __( 'Marketplace', 'amazon-auto-links' ),      // 3.5.5+
                    'MobileApps'            => __( 'Mobile Apps', 'amazon-auto-links' ),      // 2.1.0+
                    'MP3Downloads'          => __( 'MP3 Downloads', 'amazon-auto-links' ),
                    'Music'                 => __( 'Music', 'amazon-auto-links' ),
                    'MusicalInstruments'    => __( 'Musical Instruments', 'amazon-auto-links' ),
                        'MusicTracks'           => __( 'Music Tracks', 'amazon-auto-links' ),  // missing in recent documentation
                    'OfficeProducts'        => __( 'Office Products', 'amazon-auto-links' ),
                    'PCHardware'            => __( 'PC Hardware', 'amazon-auto-links' ),
                    'PetSupplies'           => __( 'Pet Supplies', 'amazon-auto-links' ),
                    'Shoes'                 => __( 'Shoes', 'amazon-auto-links' ),
                    'Software'              => __( 'Software', 'amazon-auto-links' ),
                        'SoftwareVideoGames'    => __( 'Software Video Games', 'amazon-auto-links' ),   // missing in recent documentation
                    'SportingGoods'         => __( 'Sporting Goods', 'amazon-auto-links' ),
                    'Toys'                  => __( 'Toys', 'amazon-auto-links' ),
                        'VHS'                   => __( 'VHS', 'amazon-auto-links' ),    // missing in recent documentation
                        'Video'                 => __( 'Video', 'amazon-auto-links' ),  // missing in recent documentation
                    'VideoGames'            => __( 'Video Games', 'amazon-auto-links' ),
                    'Watches'               => __( 'Watches', 'amazon-auto-links' ),
                );
            // 2.1.0+ updated the list to the Amazon API Version 2013-08-01 from 2011-08-01
            // @see https://docs.aws.amazon.com/AWSECommerceService/latest/DG/LocaleIN.html
            case 'IN':
                return array(
                    'All'                   => __( 'All', 'amazon-auto-links' ),
                    'Apparel'               => __( 'Apparel', 'amazon-auto-links' ),    // 3.5.5+
                    'Appliances'            => __( 'Appliances', 'amazon-auto-links' ),    // 3.5.5+
                    'Automotive'            => __( 'Automotive', 'amazon-auto-links' ),    // 3.5.5+
                    'Baby'                  => __( 'Baby', 'amazon-auto-links' ),    // 3.5.5+
                    'Beauty'                => __( 'Beauty', 'amazon-auto-links' ),    // 3.5.5+
                    'Books'                 => __( 'Books', 'amazon-auto-links' ),
                    'DVD'                   => __( 'DVD', 'amazon-auto-links' ),
                    // 'Marketplace'           => __( 'Marketplace', 'amazon-auto-links' ), // Does not seem to be supported by Amazon API although the documentation indicates a check. // Missing in recent documentation
                    'Electronics'           => __( 'Electronics', 'amazon-auto-links' ),
                    'Furniture'             => __( 'Furniture', 'amazon-auto-links' ),    // 3.5.5+
                    'GiftCards'             => __( 'Gift Cards', 'amazon-auto-links' ),    // 3.5.5+
                    'Grocery'               => __( 'Grocery', 'amazon-auto-links' ),    // 3.5.5+
                    'HealthPersonalCare'    => __( 'Health Personal Care', 'amazon-auto-links' ),    // 3.5.5+
                    'HomeGarden'            => __( 'Home Garden', 'amazon-auto-links' ),    // 3.5.5+
                    'Industrial'            => __( 'Industrial', 'amazon-auto-links' ),    // 3.5.5+
                    'Jewelry'               => __( 'Jewelry', 'amazon-auto-links' ),    // 3.5.5+
                    'KindleStore'           => __( 'Kindle Store', 'amazon-auto-links' ),    // 3.5.5+
                    'LawnAndGarden'         => __( 'Lawn and Garden', 'amazon-auto-links' ),    // 3.5.5+
                    'Luggage'               => __( 'Luggage', 'amazon-auto-links' ),    // 3.5.5+
                    'LuxuryBeauty'          => __( 'Luxury Beauty', 'amazon-auto-links' ),    // 3.5.5+
                    'Marketplace'           => __( 'Marketplace', 'amazon-auto-links' ),    // 3.5.5+
                    'Music'                 => __( 'Music', 'amazon-auto-links' ),    // 3.5.5+
                    'MusicalInstruments'    => __( 'Musical Instruments', 'amazon-auto-links' ),    // 3.5.5+
                    'OfficeProducts'        => __( 'Office Products', 'amazon-auto-links' ),    // 3.5.5+
                    'Pantry'                => __( 'Pantry', 'amazon-auto-links' ),    // 3.5.5+
                    'PCHardware'            => __( 'PC Hardware', 'amazon-auto-links' ),    // 3.5.5+
                    'PetSupplies'           => __( 'Pet Supplies', 'amazon-auto-links' ),    // 3.5.5+
                    'Shoes'                 => __( 'Shoes', 'amazon-auto-links' ),    // 3.5.5+
                    'Software'              => __( 'Software', 'amazon-auto-links' ),    // 3.5.5+
                    'SportingGoods'         => __( 'Sporting Goods', 'amazon-auto-links' ),    // 3.5.5+
                    'Toys'                  => __( 'Toys', 'amazon-auto-links' ),    // 3.5.5+
                    'VideoGames'            => __( 'Video Games', 'amazon-auto-links' ),    // 3.5.5+
                    'Watches'               => __( 'Watches', 'amazon-auto-links' ),    // 3.5.5+
                );
            // @see https://docs.aws.amazon.com/AWSECommerceService/latest/DG/LocaleIT.html
            case 'IT':
                return array(
                    'All'                   => __( 'All', 'amazon-auto-links' ),
                    'Apparel'               => __( 'Apparel', 'amazon-auto-links' ),    // 3.5.5+
                    'Automotive'            => __( 'Automotive', 'amazon-auto-links' ),
                    'Baby'                  => __( 'Baby', 'amazon-auto-links' ),
                    'Beauty'                => __( 'Beauty', 'amazon-auto-links' ), // 3.5.5+
                    'Books'                 => __( 'Books', 'amazon-auto-links' ),
                    'DVD'                   => __( 'DVD', 'amazon-auto-links' ),
                    'Electronics'           => __( 'Electronics', 'amazon-auto-links' ),
                    'ForeignBooks'          => __( 'Foreign Books', 'amazon-auto-links' ),
                    'Garden'                => __( 'Garden', 'amazon-auto-links' ),
                    'GiftCards'             => __( 'Gift Cards', 'amazon-auto-links' ), // 3.5.5+
                    'Grocery'               => __( 'Grocery', 'amazon-auto-links' ), // 3.5.5+
                    'Handmade'              => __( 'Handmade', 'amazon-auto-links' ), // 3.5.5+
                    'HealthPersonalCare'    => __( 'Health Personal Care', 'amazon-auto-links' ), // 3.5.5+
                    'Industrial'            => __( 'Industrial', 'amazon-auto-links' ), // 3.5.5+
                    'Jewelry'               => __( 'Jewelry', 'amazon-auto-links' ), // 3.5.5+
                    'KindleStore'           => __( 'Kindle Store', 'amazon-auto-links' ),
                    'Kitchen'               => __( 'Kitchen', 'amazon-auto-links' ),
                    'Lighting'              => __( 'Lighting', 'amazon-auto-links' ),
                    'Luggage'               => __( 'Luggage', 'amazon-auto-links' ),      // 3.1.0+
                    'MobileApps'            => __( 'Mobile Apps', 'amazon-auto-links' ),   // 3.1.0+
                    'MP3Downloads'          => __( 'MP3 Downloads', 'amazon-auto-links' ),
                    'Music'                 => __( 'Music', 'amazon-auto-links' ),
                    'MusicalInstruments'    => __( 'Musical Instruments', 'amazon-auto-links' ),    // 3.5.5+
                    'OfficeProducts'        => __( 'Office Products', 'amazon-auto-links' ),    // 3.5.5+
                    'PCHardware'            => __( 'PC Hardware', 'amazon-auto-links' ),    // 3.5.5+
                    'Shoes'                 => __( 'Shoes', 'amazon-auto-links' ),
                    'Software'              => __( 'Software', 'amazon-auto-links' ),
                    'SportingGoods'         => __( 'SportingGoods', 'amazon-auto-links' ),  // 3.5.5+
                    'Tools'                 => __( 'Tools', 'amazon-auto-links' ),  // 3.5.5+
                    'Toys'                  => __( 'Toys', 'amazon-auto-links' ),
                    'VideoGames'            => __( 'Video Games', 'amazon-auto-links' ),
                    'Watches'               => __( 'Watches', 'amazon-auto-links' ),
                );
            // @see https://docs.aws.amazon.com/AWSECommerceService/latest/DG/LocaleJP.html
            case 'JP':
                return array(
                    'All'                   => __( 'All', 'amazon-auto-links' ),
                    'Apparel'               => __( 'Apparel', 'amazon-auto-links' ),
                    'Appliances'            => __( 'Appliances', 'amazon-auto-links' ),
                    'Automotive'            => __( 'Automotive', 'amazon-auto-links' ),
                    'Baby'                  => __( 'Baby', 'amazon-auto-links' ),
                    'Beauty'                => __( 'Beauty', 'amazon-auto-links' ),
                    'Blended'               => __( 'Blended', 'amazon-auto-links' ),
                    'Books'                 => __( 'Books', 'amazon-auto-links' ),
                    'Classical'             => __( 'Classical', 'amazon-auto-links' ),
                    'DVD'                   => __( 'DVD', 'amazon-auto-links' ),
                    'Electronics'           => __( 'Electronics', 'amazon-auto-links' ),
                    'ForeignBooks'          => __( 'Foreign Books', 'amazon-auto-links' ),
                    'GiftCards'             => __( 'Gift Cards', 'amazon-auto-links' ), // 3.5.5+
                    'Grocery'               => __( 'Grocery', 'amazon-auto-links' ),
                    'HealthPersonalCare'    => __( 'Health Personal Care', 'amazon-auto-links' ),
                    'Hobbies'               => __( 'Hobbies', 'amazon-auto-links' ),
                    'HomeImprovement'       => __( 'Home Improvement', 'amazon-auto-links' ),
                    'Industrial'            => __( 'Industrial', 'amazon-auto-links' ), // 3.5.5+
                    'Jewelry'               => __( 'Jewelry', 'amazon-auto-links' ),
                    'KindleStore'           => __( 'Kindle Store', 'amazon-auto-links' ),
                    'Kitchen'               => __( 'Kitchen', 'amazon-auto-links' ),
                    'Marketplace'           => __( 'Marketplace', 'amazon-auto-links' ),
                    'MobileApps'            => __( 'Mobile Apps', 'amazon-auto-links' ),
                    'MP3Downloads'          => __( 'MP3 Downloads', 'amazon-auto-links' ),
                    'Music'                 => __( 'Music', 'amazon-auto-links' ),
                    'MusicalInstruments'    => __( 'Musical Instruments', 'amazon-auto-links' ),
                        'MusicTracks'           => __( 'Music Tracks', 'amazon-auto-links' ),   // missing in recent documentation
                    'OfficeProducts'        => __( 'Office Products', 'amazon-auto-links' ),
                    'PCHardware'            => __( 'PC Hardware', 'amazon-auto-links' ),    // 3.5.5+
                    'PetSupplies'           => __( 'Pet Supplies', 'amazon-auto-links' ),    // 3.5.5+
                    'Shoes'                 => __( 'Shoes', 'amazon-auto-links' ),
                    'Software'              => __( 'Software', 'amazon-auto-links' ),
                    'SportingGoods'         => __( 'Sporting Goods', 'amazon-auto-links' ),
                    'Toys'                  => __( 'Toys', 'amazon-auto-links' ),
                        'VHS'                   => __( 'VHS', 'amazon-auto-links' ),    // missing in recent documentation
                    'Video'                 => __( 'Video', 'amazon-auto-links' ),
                    'VideoDownload'         => __( 'Video Download', 'amazon-auto-links' ), // 3.5.5+
                    'VideoGames'            => __( 'Video Games', 'amazon-auto-links' ),
                    'Watches'               => __( 'Watches', 'amazon-auto-links' ),    // 3.5.5+
                );
            // @since   3.5.5
            // @see https://docs.aws.amazon.com/AWSECommerceService/latest/DG/LocaleMX.html
            case "MX":
                return array(
                    'All'                   => __( 'All', 'amazon-auto-links' ),
                    'Baby'                  => __( 'Baby', 'amazon-auto-links' ),
                    'Books'                 => __( 'Books', 'amazon-auto-links' ),
                    'Electronics'           => __( 'Electronics', 'amazon-auto-links' ),
                    'HealthPersonalCare'    => __( 'Health Personal Care', 'amazon-auto-links' ),
                    'HomeImprovement'       => __( 'Home Improvement', 'amazon-auto-links' ),
                    'KindleStore'           => __( 'Kindle Store', 'amazon-auto-links' ),
                    'Kitchen'               => __( 'Kitchen', 'amazon-auto-links' ),
                    'Music'                 => __( 'Music', 'amazon-auto-links' ),
                    'OfficeProducts'        => __( 'Office Products', 'amazon-auto-links' ),
                    'Software'              => __( 'Software', 'amazon-auto-links' ),
                    'SportingGoods'         => __( 'Sporting Goods', 'amazon-auto-links' ),
                    'VideoGames'            => __( 'Video Games', 'amazon-auto-links' ),
                    'Watches'               => __( 'Watches', 'amazon-auto-links' ),
                );
            // @see https://docs.aws.amazon.com/AWSECommerceService/latest/DG/LocaleUK.html
            case 'UK':
                return array(
                    'All'                   => __( 'All', 'amazon-auto-links' ),
                    'Apparel'               => __( 'Apparel', 'amazon-auto-links' ),
                    'Appliances'            => __( 'Appliances', 'amazon-auto-links' ),
                    'Automotive'            => __( 'Automotive', 'amazon-auto-links' ),
                    'Baby'                  => __( 'Baby', 'amazon-auto-links' ),
                    'Beauty'                => __( 'Beauty', 'amazon-auto-links' ),
                    'Blended'               => __( 'Blended', 'amazon-auto-links' ),
                    'Books'                 => __( 'Books', 'amazon-auto-links' ),
                    'Classical'             => __( 'Classical', 'amazon-auto-links' ),
                    'DVD'                   => __( 'DVD', 'amazon-auto-links' ),
                    'Electronics'           => __( 'Electronics', 'amazon-auto-links' ),
                    'GiftCards'             => __( 'Gift Cards', 'amazon-auto-links' ), // 3.5.5+
                    'Grocery'               => __( 'Grocery', 'amazon-auto-links' ),
                    'Handmade'              => __( 'Handmade', 'amazon-auto-links' ),   // 3.5.5+
                    'HealthPersonalCare'    => __( 'Health Personal Care', 'amazon-auto-links' ),
                    'HomeGarden'            => __( 'Home Garden', 'amazon-auto-links' ),
                        'HomeImprovement'       => __( 'Home Improvement', 'amazon-auto-links' ),   // missing in recent documentation
                    'Industrial'            => __( 'Industrial', 'amazon-auto-links' ), // 3.5.5+
                    'Jewelry'               => __( 'Jewelry', 'amazon-auto-links' ),
                    'KindleStore'           => __( 'Kindle Store', 'amazon-auto-links' ),
                    'Kitchen'               => __( 'Kitchen', 'amazon-auto-links' ),
                    'Lighting'              => __( 'Lighting', 'amazon-auto-links' ),
                    'Luggage'               => __( 'Luggage', 'amazon-auto-links' ),      // 2.1.0+
                    'Marketplace'           => __( 'Marketplace', 'amazon-auto-links' ),
                    'MobileApps'            => __( 'Mobile Apps', 'amazon-auto-links' ),       // 2.1.0+
                    'MP3Downloads'          => __( 'MP3 Downloads', 'amazon-auto-links' ),
                    'Music'                 => __( 'Music', 'amazon-auto-links' ),
                    'MusicalInstruments'    => __( 'Musical Instruments', 'amazon-auto-links' ),
                        'MusicTracks'           => __( 'Music Tracks', 'amazon-auto-links' ),   // missing in recent documentation
                    'OfficeProducts'        => __( 'Office Products', 'amazon-auto-links' ),
                    'Pantry'                => __( 'Pantry', 'amazon-auto-links' ), // 3.5.5+
                        'OutdoorLiving'         => __( 'Outdoor Living', 'amazon-auto-links' ), // missing in recent documentation
                        'Outlet'                => __( 'Outlet', 'amazon-auto-links' ), // missing in recent documentation
                    'PCHardware'            => __( 'PC Hardware', 'amazon-auto-links' ),
                    'Shoes'                 => __( 'Shoes', 'amazon-auto-links' ),
                    'Software'              => __( 'Software', 'amazon-auto-links' ),
                        'SoftwareVideoGames'    => __( 'Software Video Games', 'amazon-auto-links' ),   // missing in recent documentation
                    'SportingGoods'         => __( 'Sporting Goods', 'amazon-auto-links' ),
                    'Tools'                 => __( 'Tools', 'amazon-auto-links' ),
                    'Toys'                  => __( 'Toys', 'amazon-auto-links' ),
                    'UnboxVideo'            => __( 'Unbox Video', 'amazon-auto-links' ),    // 3.5.5+
                    'VHS'                   => __( 'VHS', 'amazon-auto-links' ),
                    'Video'                 => __( 'Video', 'amazon-auto-links' ),
                    'VideoGames'            => __( 'Video Games', 'amazon-auto-links' ),
                    'Watches'               => __( 'Watches', 'amazon-auto-links' ),
                );
            // @since   3.5.5
            // @remark  The `AU` locale is missing in the AWS documentation. So the categories are extracted from the HTML source code of ScratchPad SearchIndex parameter.
            // @see     http://webservices.amazon.com.au/scratchpad/index.html
            case 'AU':
                return array(
                    'All'                   => __( 'All', 'amazon-auto-links' ),
                    'Baby'                  => __( 'Baby', 'amazon-auto-links' ),
                    'Beauty'                => __( 'Beauty', 'amazon-auto-links' ),
                    'Books'                 => __( 'Books', 'amazon-auto-links' ),
                    'Electronics'           => __( 'Electronics', 'amazon-auto-links' ),
                    'Fashion'               => __( 'Fashion', 'amazon-auto-links' ),
                    'HealthPersonalCare'    => __( 'HealthPersonalCare', 'amazon-auto-links' ),
                    'KindleStore'           => __( 'KindleStore', 'amazon-auto-links' ),
                    'MobileApps'            => __( 'MobileApps', 'amazon-auto-links' ),
                    'Movies'                => __( 'Movies', 'amazon-auto-links' ),
                    'Music'                 => __( 'Music', 'amazon-auto-links' ),
                    'OfficeProducts'        => __( 'OfficeProducts', 'amazon-auto-links' ),
                    'PCHardware'            => __( 'PCHardware', 'amazon-auto-links' ),
                    'Software'              => __( 'Software', 'amazon-auto-links' ),
                    'SportingGoods'         => __( 'SportingGoods', 'amazon-auto-links' ),
                    'Toys'                  => __( 'Toys', 'amazon-auto-links' ),
                    'VideoGames'            => __( 'VideoGames', 'amazon-auto-links' ),
                );
            // @since   3.5.5
            // @see     https://docs.aws.amazon.com/AWSECommerceService/latest/DG/LocaleBR.html
            case 'BR':
                return array(
                    'All'                   => __( 'All', 'amazon-auto-links' ),
                    'Books'                 => __( 'Livros', 'amazon-auto-links' ),
                    'KindleStore'           => __( 'Loja Kindle', 'amazon-auto-links' ),
                    'MobileApps'            => __( 'Apps e Jogos', 'amazon-auto-links' ),
                );
            // @see https://docs.aws.amazon.com/AWSECommerceService/latest/DG/LocaleUS.html
            default:
            case 'US':
                return array(
                    'All'                   => __( 'All', 'amazon-auto-links' ),
                        'Apparel'               => __( 'Apparel', 'amazon-auto-links' ),    // missing in recent documentation
                    'Appliances'            => __( 'Appliances', 'amazon-auto-links' ),
                    'ArtsAndCrafts'         => __( 'Arts And Crafts', 'amazon-auto-links' ),
                    'Automotive'            => __( 'Automotive', 'amazon-auto-links' ),
                    'Baby'                  => __( 'Baby', 'amazon-auto-links' ),
                    'Beauty'                => __( 'Beauty', 'amazon-auto-links' ),
                    'Blended'               => __( 'Blended', 'amazon-auto-links' ),
                    'Books'                 => __( 'Books', 'amazon-auto-links' ),
                        'Classical'             => __( 'Classical', 'amazon-auto-links' ),  // missing in recent documentation
                    'Collectibles'          => __( 'Collectibles', 'amazon-auto-links' ),
                        'DigitalMusic'          => __( 'Digital Music', 'amazon-auto-links' ),  // missing in recent documentation
                        'DVD'                   => __( 'DVD', 'amazon-auto-links' ),    // missing in recent documentation
                    'Electronics'           => __( 'Electronics', 'amazon-auto-links' ),
                        'GourmetFood'           => __( 'Gourmet Food', 'amazon-auto-links' ),   // missing in recent documentation
                    'Fashion'               => __( 'Fashion', 'amazon-auto-links' ), // 3.5.5+
                    'FashionBaby'           => __( 'Fashion Baby', 'amazon-auto-links' ), // 3.5.5+
                    'FashionBoys'           => __( 'Fashion Boys', 'amazon-auto-links' ), // 3.5.5+
                    'FashionGirls'          => __( 'Fashion Girls', 'amazon-auto-links' ), // 3.5.5+
                    'FashionMen'            => __( 'Fashion Men', 'amazon-auto-links' ), // 3.5.5+
                    'FashionWomen'          => __( 'Fashion Women', 'amazon-auto-links' ), // 3.5.5+
                    'GiftCards'             => __( 'Gift Cards', 'amazon-auto-links' ), // 3.5.5+
                    'Grocery'               => __( 'Grocery', 'amazon-auto-links' ),
                    'Handmade'              => __( 'Handmade', 'amazon-auto-links' ),   // 3.5.5+
                    'HealthPersonalCare'    => __( 'Health Personal Care', 'amazon-auto-links' ),
                    'HomeGarden'            => __( 'Home Garden', 'amazon-auto-links' ),
                    'Industrial'            => __( 'Industrial', 'amazon-auto-links' ),
                        'Jewelry'               => __( 'Jewelry', 'amazon-auto-links' ),    // missing in recent documentation
                    'KindleStore'           => __( 'Kindle Store', 'amazon-auto-links' ),
                        'Kitchen'               => __( 'Kitchen', 'amazon-auto-links' ),    // missing in recent documentation
                    'LawnAndGarden'         => __( 'Lawn and Garden', 'amazon-auto-links' ),
                    'Luggage'               => __( 'Luggage & Travel Gear', 'amazon-auto-links' ),        // 3.5.5+
                    'Magazines'             => __( 'Magazines', 'amazon-auto-links' ),
                    'Marketplace'           => __( 'Marketplace', 'amazon-auto-links' ),
                        'Miscellaneous'         => __( 'Miscellaneous', 'amazon-auto-links' ),  // missing in recent documentation
                    'Merchants'             => __( 'Merchants', 'amazon-auto-links' ),  // 3.5.5+
                    'MobileApps'            => __( 'Mobile Apps', 'amazon-auto-links' ),
                    'Movies'                => __( 'Movies', 'amazon-auto-links' ), // 3.5.5+
                    'MP3Downloads'          => __( 'MP3 Downloads', 'amazon-auto-links' ),
                    'Music'                 => __( 'Music', 'amazon-auto-links' ),
                    'MusicalInstruments'    => __( 'Musical Instruments', 'amazon-auto-links' ),
                        'MusicTracks'           => __( 'Music Tracks', 'amazon-auto-links' ),   // missing in recent documentation
                    'OfficeProducts'        => __( 'Office Products', 'amazon-auto-links' ),
                        'OutdoorLiving'         => __( 'Outdoor Living', 'amazon-auto-links' ), // missing in recent documentation
                    'Pantry'                => __( 'Pantry', 'amazon-auto-links' ),
                    'PCHardware'            => __( 'PC Hardware', 'amazon-auto-links' ),
                    'PetSupplies'           => __( 'Pet Supplies', 'amazon-auto-links' ),
                    'Photo'                 => __( 'Photo', 'amazon-auto-links' ),
                       'Shoes'                 => __( 'Shoes', 'amazon-auto-links' ),   // missing in recent documentation
                    'Software'              => __( 'Software', 'amazon-auto-links' ),
                    'SportingGoods'         => __( 'Sporting Goods', 'amazon-auto-links' ),
                    'Tools'                 => __( 'Tools', 'amazon-auto-links' ),
                    'Toys'                  => __( 'Toys', 'amazon-auto-links' ),
                    'UnboxVideo'            => __( 'Unbox Video', 'amazon-auto-links' ),
                        'VHS'                   => __( 'VHS', 'amazon-auto-links' ),    // missing in recent documentation
                        'Video'                 => __( 'Video', 'amazon-auto-links' ),  // missing in recent documentation
                    'Vehicles'              => __( 'Vehicles', 'amazon-auto-links' ),   // 3.5.5+
                    'VideoGames'            => __( 'Video Games', 'amazon-auto-links' ),
                        'Watches'               => __( 'Watches', 'amazon-auto-links' ),    // missing in recent documentation
                    'Wine'                  => __( 'Wine', 'amazon-auto-links' ),
                    'Wireless'              => __( 'Wireless', 'amazon-auto-links' ),
                        'WirelessAccessories'   => __( 'Wireless Accessories', 'amazon-auto-links' ),   // missing in recent documenattion
                );
        }

    }

    /**
     * @todo    Confirm if this property is being used.
     * @remark  Seems not used. And the documentation of the below url does not include the `AU` locale.
     * @see     https://docs.aws.amazon.com/AWSECommerceService/latest/DG/localevalues.html
     */
    static public $aSearchIndex = array(
        'CA' => array(
            "All","Baby","Beauty","Blended","Books","Classical","DVD","Electronics","ForeignBooks",
            "HealthPersonalCare","KindleStore","LawnAndGarden","Music","PetSupplies","Software",
            "SoftwareVideoGames","VHS","Video","VideoGames"
        ),
        'CN' => array(
            "All","Apparel","Appliances","Automotive","Baby","Beauty","Books","Electronics","Grocery",
            "HealthPersonalCare","Home","HomeImprovement","Jewelry","KindleStore","Miscellaneous","Music",
            "OfficeProducts","PetSupplies","Photo","Shoes","Software","SportingGoods","Toys","Video","VideoGames",
            "Watches"
        ),
        'DE' => array(
            "All","Apparel","Automotive","Baby","Beauty","Blended","Books","Classical","DVD","Electronics",
            "ForeignBooks","Grocery","HealthPersonalCare","HomeGarden","HomeImprovement","Jewelry","KindleStore",
            "Kitchen","Lighting","Magazines","Marketplace","MP3Downloads","Music","MusicalInstruments",
            "MusicTracks","OfficeProducts","OutdoorLiving","Outlet","PCHardware","Photo","Shoes","Software",
            "SoftwareVideoGames","SportingGoods","Tools","Toys","VHS","Video","VideoGames","Watches"
        ),
        'ES' => array(
            "All","Automotive","Baby","Books","DVD","Electronics","ForeignBooks","KindleStore","Kitchen",
            "MP3Downloads","Music","Shoes","Software","Toys","VideoGames","Watches"
        ),
        'FR' => array(
            "All","Apparel","Automotive","Baby","Beauty","Blended","Books","Classical","DVD","Electronics",
            "ForeignBooks","HealthPersonalCare","Jewelry","KindleStore","Kitchen","Lighting","MP3Downloads",
            "Music","MusicalInstruments","MusicTracks","OfficeProducts","PCHardware","PetSupplies","Shoes","Software",
            "SoftwareVideoGames","SportingGoods","Toys","VHS","Video","VideoGames","Watches"
        ),
        'IN' => array( "All", "Books","DVD"    ),
        'IT' => array(
            "All","Automotive","Baby","Books","DVD","Electronics","ForeignBooks","Garden","KindleStore","Kitchen",
            "Lighting","MP3Downloads","Music","Shoes","Software","Toys","VideoGames","Watches"
        ),
        'JP' => array(
            "All","Apparel","Appliances","Automotive","Baby","Beauty","Blended","Books","Classical","DVD",
            "Electronics","ForeignBooks","Grocery","HealthPersonalCare","Hobbies","HomeImprovement","Jewelry",
            "KindleStore","Kitchen","Marketplace","MobileApps","MP3Downloads","Music","MusicalInstruments",
            "MusicTracks","OfficeProducts","Shoes","Software","SportingGoods","Toys","VHS","Video","VideoGames"
        ),
        'UK' => array(
            "All","Apparel","Automotive","Baby","Beauty","Blended","Books","Classical","DVD","Electronics",
            "Grocery","HealthPersonalCare","HomeGarden","HomeImprovement","Jewelry","KindleStore","Kitchen",
            "Lighting","Marketplace","MP3Downloads","Music","MusicalInstruments","MusicTracks","OfficeProducts",
            "OutdoorLiving","Outlet","PCHardware","Shoes","Software","SoftwareVideoGames","SportingGoods","Tools",
            "Toys","VHS","Video","VideoGames","Watches"
        ),
        'US' => array(
            "All","Apparel","Appliances","ArtsAndCrafts","Automotive","Baby","Beauty","Blended","Books","Classical",
            "Collectibles","DigitalMusic","DVD","Electronics","GourmetFood","Grocery","HealthPersonalCare",
            "HomeGarden","Industrial","Jewelry","KindleStore","Kitchen","LawnAndGarden","Magazines","Marketplace",
            "Miscellaneous","MobileApps","MP3Downloads","Music","MusicalInstruments","MusicTracks","OfficeProducts",
            "OutdoorLiving","PCHardware","PetSupplies","Photo","Shoes","Software","SportingGoods","Tools","Toys",
            "UnboxVideo","VHS","Video","VideoGames","Watches","Wireless","WirelessAccessories"
        ),
        // 3.5.5+
        'MX' => array(),
        'AU' => array(),

    );

    /**
     * 
     * @remark          These IDs were valid as of the publication date of this guide. API Version 2011-08-01
     * @see             http://docs.aws.amazon.com/AWSECommerceService/latest/DG/BrowseNodeIDs.html
     * @todo            Confirm if this property is used or not. The caller method may not be used.
     */
    static public $aRootNodes = array(
        'CA' => array(
            3561346011,6205124011,927726,962454,14113311,677211011,927726,6205177011,2972705011,2206275011,
            6205499011,962454,6205514011,3234171,3323751,962072,962454,110218011,
        ),
        'CN' => array(
            2016156051,80207071,1947899051,746776051,658390051,2016116051,2127215051,852803051,2016126051,
            1952920051,816482051,116087071,899280051,754386051,2127221051,118863071 ,755653051,2029189051,
            863872051,836312051,647070051,2016136051,897415051,1953164051,
        ),
        'DE' => array(
            78689031,78191031,357577011,64257031,541686,542676,547664,569604,54071011,340846031,64257031,
            10925241,327473011,530484031,3169011,213083031,1161658,77195031,542676,340849031,192416031,10925051,
            569604,569604,542064,541708,16435121,12950661,547082,547664,541708,193708031,
        ),
        'ES' => array(
            1951051031,1703495031,599364031,599379031,667049031,599367031,530484031,599391031,1748200031,
            599373031,1571262031,599376031,599385031,599382031,599388031,        
        ),
        'FR' => array(
            340855031,1571265031,206617031,197858031,468256,537366,578608,1058082,69633011,197861031,590748031,
            193711031,818936031,57686031,213080031,206442031,537366,340862031,192420031,1571268031,215934031,
            548012,548014,548014,578610,578608,548014,60937031,
        ),
        // 'IN' => array( 976389031, 976416031 ), 
        // 2.1.0+ Updated the list to the API Version 2013-08-01) from API Version 2011-08-01
        'IN' => array( 976389031, 976416031, 976419031, 976442031, 1951048031, 976392031, 1350380031, 1350387031 ),
        'IT' => array(
            1571280031,1571286031,411663031,412606031,412609031,433842031,635016031,818937031,524015031,
            1571292031,1748203031,412600031,524006031,412612031,523997031,412603031,524009031,
        ),
        'JP' => array(
            361299011,2277724051,2017304051,13331821,52391051,465610,562032,562002,3210991,388316011,57239051,
            161669011,13331821,85896051,2250738051,3839151,2381130051,2128134051,562032,2123629051,2016926051,
            637630,14304371,13331821,2130989051,561972,637872,324025011
        ),
        'UK' => array(
            83451031,248877031,60032031,66280031,1025612,505510,283926,560800,340834031,66280031,11052591,
            2016929051,193717031,341677031,11052591,213077031,77198031,505510,340837031,560800,11052591,1025614,
            1025616,319530011,11052591,712832,283926,283926,1025616,595312
        ),
        'US' => array(
            1036592, 2619525011, 2617941011, 15690151, 165796011, 11055981, 1000,301668, 4991425011, 
            2625373011, 493964, 16310101,3760931, 228239, 3880591, 133141011, 1063498, 
            2972638011, 599872, 10304191, 2350149011, 301668, 11091801, 1084128, 1063498, 493964, 
            1063498, 493964, 409488, 3375251, 468240, 493964, 130, 493964, 377110011, 13900851        
        ),    // caused error: 195208011, 3580501, 285080, 195211011, 404272, 508494
        'MX' => array(),
        'AU' => array(),
    );
    
    /**
     * Returns an array of root node IDs of the specified locale.
     * 
     * The nodes are divided up to 10 elements for the API request.
     * @remark      Not used at the moment.
     */
    static public function getRootNoeds( $sLocale ) {
        
        if ( ! isset( self::$aRootNodes[ strtoupper( $sLocale ) ] ) ) {
            return array();
        }
        return array_chunk( 
            self::$aRootNodes[ strtoupper( $sLocale ) ], 
            10 
        );
        
    }
    
    /**
     * The list of marketplace domains.
     * 
     * This is used when the search API request has the category of 'Marketplace', the domain needs to be specified.
     * @since       2.1.0
     * @see         http://docs.aws.amazon.com/AWSECommerceService/latest/DG/MarketplaceDomainParameter.html
     */
    static public $aMarketplaceDomain = array(
        'DE' => 'www.javari.de',
        'JP' => 'www.javari.jp',
        'UK' => 'www.javari.co.uk',
        'US' => 'www.amazonsupply.com',
    );
    
    /**
     * Returns the market place domain url by the given locale.
     * 
     * @since       2.1.0
     */
    static public function getMarketplaceDomainByLocale( $sLocale ) {
        
        return isset( self::$aMarketplaceDomain[ $sLocale ]  )
            ? self::$aMarketplaceDomain[ $sLocale ]
            : self::$aMarketplaceDomain[ 'US' ];    // default
        
    }
    
    /**
     * Returns the JavaScript script of the impression counter.
     *
     * @since       3.1.0
     * @since       3.5.6       Supported SSL.
     * @return      string
     * @rmark       Some locales are not available.
     */
    static public function getImpressionCounterScript( $sLocale ) {
        $_sScript = isset( self::$aImpressionCounterScripts[ $sLocale ] )
            ? self::$aImpressionCounterScripts[ $sLocale ]
            : self::$aImpressionCounterScripts[ 'US' ]; // default
        return is_ssl()
            ? str_replace( 'http://', 'https://', $_sScript )
            : $_sScript;
    }
        /**
         * 
         * @remark      %ASSOCIATE_TAG% is a dummy associate id.
         * @since       3.1.0
         */
        static public $aImpressionCounterScripts = array(

            // https://associates.amazon.ca/gp/associates/tips/impressions.html?ie=UTF8&pf_rd_i=assoc_help_t20_a2&pf_rd_m=A3DWYIK6Y9EEQB&pf_rd_p=&pf_rd_r=&pf_rd_s=assoc-center-1&pf_rd_t=501&ref_=amb_link_10060771_2&rw_useCurrentProtocol=1
            'CA'    => '<script class="amazon_auto_links_impression_counter_ca" type="text/javascript" src="http://ir-ca.amazon-adsystem.com/s/impression-counter?tag=%ASSOCIATE_TAG%&o=15"></script><noscript><img class="amazon_auto_links_impression_counter_ca" src="http://ir-ca.amazon-adsystem.com/s/noscript?tag=%ASSOCIATE_TAG%" alt="" /></noscript>',
            
            // https://associates.amazon.cn/gp/associates/tips/impressions.html?ie=UTF8&%20=&pf_rd_i=assoc_help_t20_a2&pf_rd_m=A1AJ19PSB66TGU&pf_rd_p=&pf_rd_r=&pf_rd_s=assoc-center-1&pf_rd_t=501&ref_=amb_link_3141918_2&rw_useCurrentProtocol=1
            // @remark      seems not available now at the date of 05/14/2018ir-in.amazon-adsystem.com/s/impression-counter?tag=%ASSOCIATE_TAG%&o=31
            'CN'    => '<script class="amazon_auto_links_impression_counter_cn" type="text/javascript" src="http://ir-cn.amazon-adsystem.com/s/impression-counter?tag=%ASSOCIATE_TAG%&o=28"></script><noscript><img class="amazon_auto_links_impression_counter_cn" src="http://ir-cn.amazon-adsystem.com/s/noscript?tag=%ASSOCIATE_TAG%" alt="" /></noscript>',
           
            // https://partnernet.amazon.de/gp/associates/tips/impressions.html?ie=UTF8&pf_rd_i=assoc_help_t20_a2&pf_rd_s=assoc-center-1&pf_rd_t=501
            'DE'    => '<script class="amazon_auto_links_impression_counter_de" type="text/javascript" src="http://ir-de.amazon-adsystem.com/s/impression-counter?tag=%ASSOCIATE_TAG%&o=3"></script><noscript><img class="amazon_auto_links_impression_counter_de" src="http://ir-de.amazon-adsystem.com/s/noscript?tag=%ASSOCIATE_TAG%" alt="" /></noscript>',            
            
            // https://affiliate.amazon.co.jp/gp/associates/tips/impressions.html?ie=UTF8&pf_rd_i=assoc_help_t16_a8&pf_rd_m=AN1VRQENFRJN5&pf_rd_p=&pf_rd_r=&pf_rd_s=center-1&pf_rd_t=501&ref_=amb_link_10038521_1&rw_useCurrentProtocol=1
            'JP'    => '<script class="amazon_auto_links_impression_counter_jp" type="text/javascript" src="http://ir-jp.amazon-adsystem.com/s/impression-counter?tag=%ASSOCIATE_TAG%&o=9"></script><noscript><img class="amazon_auto_links_impression_counter_jp" src="http://ir-jp.amazon-adsystem.com/s/noscript?tag=%ASSOCIATE_TAG%" alt="" /></noscript>',
            
            // https://affiliate-program.amazon.co.uk/gp/associates/tips/impressions.html?ie=UTF8&pf_rd_i=assoc_help_t20_a2&pf_rd_s=assoc-center-1&pf_rd_t=501
            'UK'    => '<script class="amazon_auto_links_impression_counter_uk" type="text/javascript" src="http://ir-uk.amazon-adsystem.com/s/impression-counter?tag=%ASSOCIATE_TAG%&o=2"></script><noscript><img class="amazon_auto_links_impression_counter_uk" src="http://ir-uk.amazon-adsystem.com/s/noscript?tag=%ASSOCIATE_TAG%" alt="" /></noscript>',
            
            // https://affiliate-program.amazon.co.uk/gp/associates/tips/impressions.html?ie=UTF8&pf_rd_i=assoc_help_t20_a2&pf_rd_s=assoc-center-1&pf_rd_t=501
            'US'    => '<script class="amazon_auto_links_impression_counter_us" type="text/javascript" src="http://ir-na.amazon-adsystem.com/s/impression-counter?tag=%ASSOCIATE_TAG%&o=1"></script><noscript><img class="amazon_auto_links_impression_counter_us" src="http://ir-na.amazon-adsystem.com/s/noscript?tag=%ASSOCIATE_TAG%" alt="" /></noscript>',

            // https://associados.amazon.com.br/gp/associates/tips/impressions.html?ie=UTF8&pf_rd_i=assoc_help_t20_a2&pf_rd_m=A1ZZFT5FULY4LN&pf_rd_p=&pf_rd_r=&pf_rd_s=assoc-center-1&pf_rd_t=501&ref_=amb_link_395484562_2&rw_useCurrentProtocol=1
            // @remark      seems not available at the date of 05/14/2018ir-in.amazon-adsystem.com/s/impression-counter?tag=%ASSOCIATE_TAG%&o=31
            'BR'    => '<script class="amazon_auto_links_impression_counter_br" type="text/javascript" src="http://ir-br.amazon-adsystem.com/s/impression-counter?tag=%ASSOCIATE_TAG%&o=33"></script><noscript><img class="amazon_auto_links_impression_counter_br" src="http://ir-br.amazon-adsystem.com/s/noscript?tag=%ASSOCIATE_TAG%" alt="" /></noscript>',
            
            // https://affiliate-program.amazon.in/gp/associates/tips/impressions.html?ie=UTF8&pf_rd_i=assoc_help_t20_a2&pf_rd_m=A1VBAL9TL5WCBF&pf_rd_p=&pf_rd_r=&pf_rd_s=assoc-center-1&pf_rd_t=501&ref_=amb_link_162366867_2&rw_useCurrentProtocol=1
            // @remark      seems not available at the date of 05/14/2018ir-in.amazon-adsystem.com/s/impression-counter?tag=%ASSOCIATE_TAG%&o=31
            'IN'    => '<script class="amazon_auto_links_impression_counter_in" type="text/javascript" src="http://ir-in.amazon-adsystem.com/s/impression-counter?tag=%ASSOCIATE_TAG%&o=31"></script><noscript><img class="amazon_auto_links_impression_counter_in" src="http://ir-in.amazon-adsystem.com/s/noscript?tag=%ASSOCIATE_TAG%" alt="" /></noscript>',

            // @since   3.5.6   Checked manually by changing the `o` url query parameter.
            'FR'    => '<script class="amazon_auto_links_impression_counter_fr" type="text/javascript" src="http://ir-na.amazon-adsystem.com/s/impression-counter?tag=%ASSOCIATE_TAG%&o=8"></script><noscript><img class="amazon_auto_links_impression_counter_us" src="http://ir-fr.amazon-adsystem.com/s/noscript?tag=%ASSOCIATE_TAG%" alt="" /></noscript>',

            // Not available
            // 'IT'    => '',
            // 'ES'    => '',
            // 'MX'    => '',
            // 'AU'    => '',
            
        );
}