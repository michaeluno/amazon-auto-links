<?php
/**
 * Properties of Amazon store info.
 * 
 * 
 * @package     Amazon Auto Links
 * @copyright   Copyright (c) 2013, Michael Uno
 * @authorurl    http://michaeluno.jp
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since        2.0.0
*/

final class AmazonAutoLinks_Properties {
    
    /**
     * 
     * @see                http://php.net/manual/en/function.mb-language.php
     */
    public static $arrCategoryPageMBLanguages = array(
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
    );        
    public static $arrCategoryRootURLs = array(
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
    );    
    public static $arrCategoryBlackCurtainURLs = array(
        'CA'    => 'http://www.amazon.ca/gp/product/black-curtain-redirect.html',
        'CN'    => 'http://www.amazon.cn/gp/product/black-curtain-redirect.html',
        'FR'    => 'http://www.amazon.fr/gp/product/black-curtain-redirect.html',
        'DE'    => 'http://www.amazon.de/gp/product/black-curtain-redirect.html',
        'IT'    => 'http://www.amazon.it/gp/product/black-curtain-redirect.html',
        'JP'    => 'http://www.amazon.co.jp/gp/product/black-curtain-redirect.html',
        'UK'    => 'http://www.amazon.co.uk/gp/product/black-curtain-redirect.html',
        'ES'    => 'http://www.amazon.es/gp/product/black-curtain-redirect.html',
        'US'    => 'http://www.amazon.com/gp/product/black-curtain-redirect.html',    
        'IN'    => 'http://www.amazon.in/gp/product/black-curtain-redirect.html',    
        'BR'    => 'http://www.amazon.com.br/gp/product/black-curtain-redirect.html',
        'MX'    => 'http://www.amazon.com.mx/gp/product/black-curtain-redirect.html',
    );

    public static $aNoImageAvailable = array(    // the domain can be g-ecx.images-amazon.com
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
    );
    
    public static $arrTokens = array(
        'CA' => 'bWl1bm9zb2Z0Y2EtMjA=',
        'CN' => 'bWl1bm9zb2Z0LTIz',
        'FR' => 'bWl1bm9zb2Z0ZnItMjE=',
        'DE' => 'bWl1bm9zb2Z0ZGUtMjE=',
        'IT' => 'bWl1bm9zb2Z0LTIx',
        'JP' => 'bWl1bm9zb2Z0LTIy',
        'UK' => 'bWl1bm9zb2Z0dWstMjE=',
        'ES' => 'bWl1bm9zb2Z0ZXMtMjE=',
        'US' => 'bWl1bm9zb2Z0LTIw',
    );    
    
    /**
     * Returns an array of search index of the specified locale.
     * 
     * @see                http://docs.aws.amazon.com/AWSECommerceService/latest/DG/APPNDX_SearchIndexValues.html
     */
    public static function getSearchIndexByLocale( $strLocale ) {
        
        switch ( strtoupper( $strLocale ) ) {
            case "CA":
                return array(                
                    'All'                   => __( 'All', 'amazon-auto-links' ),
                    'Baby'                  => __( 'Baby', 'amazon-auto-links' ),
                    'Beauty'                => __( 'Beauty', 'amazon-auto-links' ),
                    'Blended'               => __( 'Blended', 'amazon-auto-links' ),
                    'Books'                 => __( 'Books', 'amazon-auto-links' ),
                    'Classical'             => __( 'Classical', 'amazon-auto-links' ),
                    'DVD'                   => __( 'DVD', 'amazon-auto-links' ),
                    'Electronics'           => __( 'Electronics', 'amazon-auto-links' ),
                    'ForeignBooks'          => __( 'Foreign Books', 'amazon-auto-links' ),
                    'HealthPersonalCare'    => __( 'Health Personal Care', 'amazon-auto-links' ),
                    'KindleStore'           => __( 'Kindle Store', 'amazon-auto-links' ),
                    'LawnAndGarden'         => __( 'Lawn and Garden', 'amazon-auto-links' ),
                    'Luggage'               => __( 'Luggage', 'amazon-auto-links' ),    // 2.1.0+
                    'Music'                 => __( 'Music', 'amazon-auto-links' ),
                    'PetSupplies'           => __( 'Pet Supplies', 'amazon-auto-links' ),   // 2.1.0+
                    'Software'              => __( 'Software', 'amazon-auto-links' ),
                    'SoftwareVideoGames'    => __( 'Software Video Games', 'amazon-auto-links' ),
                    'Toys'                  => __( 'Toys', 'amazon-auto-links' ),   // 2.1.0+
                    'VHS'                   => __( 'VHS', 'amazon-auto-links' ),
                    'Video'                 => __( 'Video', 'amazon-auto-links' ),
                    'VideoGames'            => __( 'Video Games', 'amazon-auto-links' ),      
                );
            case "CN":
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
                    'Miscellaneous'         => __( 'Miscellaneous', 'amazon-auto-links' ),
                    'Music'                 => __( 'Music', 'amazon-auto-links' ),
                    'MusicalInstruments'    => __( 'MusicalInstruments', 'amazon-auto-links' ),    // 2.1.0+
                    'OfficeProducts'        => __( 'Office Products', 'amazon-auto-links' ),
                    'PetSupplies'           => __( 'Pet Supplies', 'amazon-auto-links' ),
                    'Photo'                 => __( 'Photo', 'amazon-auto-links' ),
                    'Shoes'                 => __( 'Shoes', 'amazon-auto-links' ),
                    'Software'              => __( 'Software', 'amazon-auto-links' ),
                    'SportingGoods'         => __( 'Sporting Goods', 'amazon-auto-links' ),
                    'Toys'                  => __( 'Toys', 'amazon-auto-links' ),
                    'Video'                 => __( 'Video', 'amazon-auto-links' ),
                    'VideoGames'            => __( 'Video Games', 'amazon-auto-links' ),
                    'Watches'               => __( 'Watches', 'amazon-auto-links' ),                    
                );
            case "DE":
                return array(
                    'All'                   => __( 'All', 'amazon-auto-links' ),
                    'Apparel'               => __( 'Apparel', 'amazon-auto-links' ),
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
                    'HealthPersonalCare'    => __( 'Health Personal Care', 'amazon-auto-links' ),
                    'HomeGarden'            => __( 'Home Garden', 'amazon-auto-links' ),
                    'HomeImprovement'       => __( 'Home Improvement', 'amazon-auto-links' ),
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
                    'MusicTracks'           => __( 'Music Tracks', 'amazon-auto-links' ),
                    'OfficeProducts'        => __( 'Office Products', 'amazon-auto-links' ),
                    'OutdoorLiving'         => __( 'Outdoor Living', 'amazon-auto-links' ),
                    'Outlet'                => __( 'Outlet', 'amazon-auto-links' ),
                    'PCHardware'            => __( 'PC Hardware', 'amazon-auto-links' ),
                    'Photo'                 => __( 'Photo', 'amazon-auto-links' ),
                    'Shoes'                 => __( 'Shoes', 'amazon-auto-links' ),
                    'Software'              => __( 'Software', 'amazon-auto-links' ),
                    'SoftwareVideoGames'    => __( 'Software Video Games', 'amazon-auto-links' ),
                    'SportingGoods'         => __( 'Sporting Goods', 'amazon-auto-links' ),
                    'Tools'                 => __( 'Tools', 'amazon-auto-links' ),
                    'Toys'                  => __( 'Toys', 'amazon-auto-links' ),
                    'VHS'                   => __( 'VHS', 'amazon-auto-links' ),
                    'Video'                 => __( 'Video', 'amazon-auto-links' ),
                    'VideoGames'            => __( 'Video Games', 'amazon-auto-links' ),
                    'Watches'               => __( 'Watches', 'amazon-auto-links' ),            
                );
            case "ES":
                return array(
                    'All'                   => __( 'All', 'amazon-auto-links' ),
                    'Automotive'            => __( 'Automotive', 'amazon-auto-links' ),
                    'Baby'                  => __( 'Baby', 'amazon-auto-links' ),
                    'Books'                 => __( 'Books', 'amazon-auto-links' ),
                    'DVD'                   => __( 'DVD', 'amazon-auto-links' ),
                    'Electronics'           => __( 'Electronics', 'amazon-auto-links' ),
                    'ForeignBooks'          => __( 'Foreign Books', 'amazon-auto-links' ),
                    'KindleStore'           => __( 'Kindle Store', 'amazon-auto-links' ),
                    'Kitchen'               => __( 'Kitchen', 'amazon-auto-links' ),
                    'MobileApps'            => __( 'Mobile Apps', 'amazon-auto-links' ),   // 2.1.0+
                    'MP3Downloads'          => __( 'MP3 Downloads', 'amazon-auto-links' ),
                    'Music'                 => __( 'Music', 'amazon-auto-links' ),
                    'Shoes'                 => __( 'Shoes', 'amazon-auto-links' ),
                    'Software'              => __( 'Software', 'amazon-auto-links' ),
                    'SportingGoods'         => __( 'Sporting Goods', 'amazon-auto-links' ), // 2.1.0+
                    'Toys'                  => __( 'Toys', 'amazon-auto-links' ),
                    'VideoGames'            => __( 'Video Games', 'amazon-auto-links' ),
                    'Watches'               => __( 'Watches', 'amazon-auto-links' ),      
                );
            case "FR":
                return array(
                    'All'                   => __( 'All', 'amazon-auto-links' ),
                    'Apparel'               => __( 'Apparel', 'amazon-auto-links' ),
                    'Automotive'            => __( 'Automotive', 'amazon-auto-links' ),
                    'Baby'                  => __( 'Baby', 'amazon-auto-links' ),
                    'Beauty'                => __( 'Beauty', 'amazon-auto-links' ),
                    'Blended'               => __( 'Blended', 'amazon-auto-links' ),
                    'Books'                 => __( 'Books', 'amazon-auto-links' ),
                    'Classical'             => __( 'Classical', 'amazon-auto-links' ),
                    'DVD'                   => __( 'DVD', 'amazon-auto-links' ),
                    'Electronics'           => __( 'Electronics', 'amazon-auto-links' ),
                    'ForeignBooks'          => __( 'Foreign Books', 'amazon-auto-links' ),
                    'HealthPersonalCare'    => __( 'Health Personal Care', 'amazon-auto-links' ),
                    'Jewelry'               => __( 'Jewelry', 'amazon-auto-links' ),
                    'KindleStore'           => __( 'Kindle Store', 'amazon-auto-links' ),
                    'Kitchen'               => __( 'Kitchen', 'amazon-auto-links' ),
                    'Lighting'              => __( 'Lighting', 'amazon-auto-links' ),
                    'Luggage'               => __( 'Luggage', 'amazon-auto-links' ),      // 2.1.0+
                    'MobileApps'            => __( 'Mobile Apps', 'amazon-auto-links' ),      // 2.1.0+
                    'MP3Downloads'          => __( 'MP3 Downloads', 'amazon-auto-links' ),
                    'Music'                 => __( 'Music', 'amazon-auto-links' ),
                    'MusicalInstruments'    => __( 'Musical Instruments', 'amazon-auto-links' ),
                    'MusicTracks'           => __( 'Music Tracks', 'amazon-auto-links' ),
                    'OfficeProducts'        => __( 'Office Products', 'amazon-auto-links' ),
                    'PCHardware'            => __( 'PC Hardware', 'amazon-auto-links' ),
                    'PetSupplies'           => __( 'Pet Supplies', 'amazon-auto-links' ),
                    'Shoes'                 => __( 'Shoes', 'amazon-auto-links' ),
                    'Software'              => __( 'Software', 'amazon-auto-links' ),
                    'SoftwareVideoGames'    => __( 'Software Video Games', 'amazon-auto-links' ),
                    'SportingGoods'         => __( 'Sporting Goods', 'amazon-auto-links' ),
                    'Toys'                  => __( 'Toys', 'amazon-auto-links' ),
                    'VHS'                   => __( 'VHS', 'amazon-auto-links' ),    
                    'Video'                 => __( 'Video', 'amazon-auto-links' ),
                    'VideoGames'            => __( 'Video Games', 'amazon-auto-links' ),
                    'Watches'               => __( 'Watches', 'amazon-auto-links' ),             
                );            
            case "IN":  // 2.1.0+ updated the list to the Amazon API Version 2013-08-01 from 2011-08-01
                return array(
                    'All'                   => __( 'All', 'amazon-auto-links' ),
                    'Books'                 => __( 'Books', 'amazon-auto-links' ),
                    'Electronics'           => __( 'Electronics', 'amazon-auto-links' ),
                    // 'Marketplace'           => __( 'Marketplace', 'amazon-auto-links' ), // Does not seem to be supported by Amazon API although the documentation indicates a check. 
                    'DVD'                   => __( 'DVD', 'amazon-auto-links' ),                
                );
            case "IT":
                return array(
                    'All'                   => __( 'All', 'amazon-auto-links' ),
                    'Automotive'            => __( 'Automotive', 'amazon-auto-links' ),
                    'Baby'                  => __( 'Baby', 'amazon-auto-links' ),
                    'Books'                 => __( 'Books', 'amazon-auto-links' ),
                    'DVD'                   => __( 'DVD', 'amazon-auto-links' ),
                    'Electronics'           => __( 'Electronics', 'amazon-auto-links' ),
                    'ForeignBooks'          => __( 'Foreign Books', 'amazon-auto-links' ),
                    'Garden'                => __( 'Garden', 'amazon-auto-links' ),
                    'KindleStore'           => __( 'Kindle Store', 'amazon-auto-links' ),
                    'Kitchen'               => __( 'Kitchen', 'amazon-auto-links' ),
                    'Lighting'              => __( 'Lighting', 'amazon-auto-links' ),
                    'Luggage'               => __( 'Luggage', 'amazon-auto-links' ),      // 3.1.0+
                    'MobileApps'            => __( 'Mobile Apps', 'amazon-auto-links' ),   // 3.1.0+
                    'MP3Downloads'          => __( 'MP3 Downloads', 'amazon-auto-links' ),
                    'Music'                 => __( 'Music', 'amazon-auto-links' ),
                    'Shoes'                 => __( 'Shoes', 'amazon-auto-links' ),
                    'Software'              => __( 'Software', 'amazon-auto-links' ),
                    'Toys'                  => __( 'Toys', 'amazon-auto-links' ),
                    'VideoGames'            => __( 'Video Games', 'amazon-auto-links' ),
                    'Watches'               => __( 'Watches', 'amazon-auto-links' ),              
                );
            case "JP":
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
                    'Grocery'               => __( 'Grocery', 'amazon-auto-links' ),
                    'HealthPersonalCare'    => __( 'Health Personal Care', 'amazon-auto-links' ),
                    'Hobbies'               => __( 'Hobbies', 'amazon-auto-links' ),
                    'HomeImprovement'       => __( 'Home Improvement', 'amazon-auto-links' ),
                    'Jewelry'               => __( 'Jewelry', 'amazon-auto-links' ),
                    'KindleStore'           => __( 'Kindle Store', 'amazon-auto-links' ),
                    'Kitchen'               => __( 'Kitchen', 'amazon-auto-links' ),
                    'Marketplace'           => __( 'Marketplace', 'amazon-auto-links' ),
                    'MobileApps'            => __( 'Mobile Apps', 'amazon-auto-links' ),
                    'MP3Downloads'          => __( 'MP3 Downloads', 'amazon-auto-links' ),
                    'Music'                 => __( 'Music', 'amazon-auto-links' ),
                    'MusicalInstruments'    => __( 'Musical Instruments', 'amazon-auto-links' ),
                    'MusicTracks'           => __( 'Music Tracks', 'amazon-auto-links' ),
                    'OfficeProducts'        => __( 'Office Products', 'amazon-auto-links' ),
                    'Shoes'                 => __( 'Shoes', 'amazon-auto-links' ),
                    'Software'              => __( 'Software', 'amazon-auto-links' ),
                    'SportingGoods'         => __( 'Sporting Goods', 'amazon-auto-links' ),
                    'Toys'                  => __( 'Toys', 'amazon-auto-links' ),
                    'VHS'                   => __( 'VHS', 'amazon-auto-links' ),
                    'Video'                 => __( 'Video', 'amazon-auto-links' ),
                    'VideoGames'            => __( 'Video Games', 'amazon-auto-links' ),  
                );
            case "UK":
                return array(
                    'All'                   => __( 'All', 'amazon-auto-links' ),
                    'Apparel'               => __( 'Apparel', 'amazon-auto-links' ),
                    'Automotive'            => __( 'Automotive', 'amazon-auto-links' ),
                    'Baby'                  => __( 'Baby', 'amazon-auto-links' ),
                    'Beauty'                => __( 'Beauty', 'amazon-auto-links' ),
                    'Blended'               => __( 'Blended', 'amazon-auto-links' ),
                    'Books'                 => __( 'Books', 'amazon-auto-links' ),
                    'Classical'             => __( 'Classical', 'amazon-auto-links' ),
                    'DVD'                   => __( 'DVD', 'amazon-auto-links' ),
                    'Electronics'           => __( 'Electronics', 'amazon-auto-links' ),
                    'Grocery'               => __( 'Grocery', 'amazon-auto-links' ),
                    'HealthPersonalCare'    => __( 'Health Personal Care', 'amazon-auto-links' ),
                    'HomeGarden'            => __( 'Home Garden', 'amazon-auto-links' ),
                    'HomeImprovement'       => __( 'Home Improvement', 'amazon-auto-links' ),
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
                    'MusicTracks'           => __( 'Music Tracks', 'amazon-auto-links' ),
                    'OfficeProducts'        => __( 'Office Products', 'amazon-auto-links' ),
                    'OutdoorLiving'         => __( 'Outdoor Living', 'amazon-auto-links' ),
                    'Outlet'                => __( 'Outlet', 'amazon-auto-links' ),
                    'PCHardware'            => __( 'PC Hardware', 'amazon-auto-links' ),
                    'Shoes'                 => __( 'Shoes', 'amazon-auto-links' ),
                    'Software'              => __( 'Software', 'amazon-auto-links' ),
                    'SoftwareVideoGames'    => __( 'Software Video Games', 'amazon-auto-links' ),
                    'SportingGoods'         => __( 'Sporting Goods', 'amazon-auto-links' ),
                    'Tools'                 => __( 'Tools', 'amazon-auto-links' ),
                    'Toys'                  => __( 'Toys', 'amazon-auto-links' ),
                    'VHS'                   => __( 'VHS', 'amazon-auto-links' ),
                    'Video'                 => __( 'Video', 'amazon-auto-links' ),
                    'VideoGames'            => __( 'Video Games', 'amazon-auto-links' ),
                    'Watches'               => __( 'Watches', 'amazon-auto-links' ),          
                );
            default:
            case "US":            
                return array(
                    'All'                   => __( 'All', 'amazon-auto-links' ),
                    'Apparel'               => __( 'Apparel', 'amazon-auto-links' ),
                    'Appliances'            => __( 'Appliances', 'amazon-auto-links' ),
                    'ArtsAndCrafts'         => __( 'Arts And Crafts', 'amazon-auto-links' ),
                    'Automotive'            => __( 'Automotive', 'amazon-auto-links' ),
                    'Baby'                  => __( 'Baby', 'amazon-auto-links' ),
                    'Beauty'                => __( 'Beauty', 'amazon-auto-links' ),
                    'Blended'               => __( 'Blended', 'amazon-auto-links' ),
                    'Books'                 => __( 'Books', 'amazon-auto-links' ),
                    'Classical'             => __( 'Classical', 'amazon-auto-links' ),
                    'Collectibles'          => __( 'Collectibles', 'amazon-auto-links' ),
                    'DigitalMusic'          => __( 'Digital Music', 'amazon-auto-links' ),
                    'DVD'                   => __( 'DVD', 'amazon-auto-links' ),
                    'Electronics'           => __( 'Electronics', 'amazon-auto-links' ),
                    'GourmetFood'           => __( 'Gourmet Food', 'amazon-auto-links' ),
                    'Grocery'               => __( 'Grocery', 'amazon-auto-links' ),
                    'HealthPersonalCare'    => __( 'Health Personal Care', 'amazon-auto-links' ),
                    'HomeGarden'            => __( 'Home Garden', 'amazon-auto-links' ),
                    'Industrial'            => __( 'Industrial', 'amazon-auto-links' ),
                    'Jewelry'               => __( 'Jewelry', 'amazon-auto-links' ),
                    'KindleStore'           => __( 'Kindle Store', 'amazon-auto-links' ),
                    'Kitchen'               => __( 'Kitchen', 'amazon-auto-links' ),
                    'LawnAndGarden'         => __( 'Lawn and Garden', 'amazon-auto-links' ),
                    'Magazines'             => __( 'Magazines', 'amazon-auto-links' ),
                    'Marketplace'           => __( 'Marketplace', 'amazon-auto-links' ),
                    'Miscellaneous'         => __( 'Miscellaneous', 'amazon-auto-links' ),
                    'MobileApps'            => __( 'Mobile Apps', 'amazon-auto-links' ),
                    'MP3Downloads'          => __( 'MP3 Downloads', 'amazon-auto-links' ),
                    'Music'                 => __( 'Music', 'amazon-auto-links' ),
                    'MusicalInstruments'    => __( 'Musical Instruments', 'amazon-auto-links' ),
                    'MusicTracks'           => __( 'Music Tracks', 'amazon-auto-links' ),
                    'OfficeProducts'        => __( 'Office Products', 'amazon-auto-links' ),
                    'OutdoorLiving'         => __( 'Outdoor Living', 'amazon-auto-links' ),
                    'PCHardware'            => __( 'PC Hardware', 'amazon-auto-links' ),
                    'PetSupplies'           => __( 'Pet Supplies', 'amazon-auto-links' ),
                    'Photo'                 => __( 'Photo', 'amazon-auto-links' ),
                    'Shoes'                 => __( 'Shoes', 'amazon-auto-links' ),
                    'Software'              => __( 'Software', 'amazon-auto-links' ),
                    'SportingGoods'         => __( 'Sporting Goods', 'amazon-auto-links' ),
                    'Tools'                 => __( 'Tools', 'amazon-auto-links' ),
                    'Toys'                  => __( 'Toys', 'amazon-auto-links' ),
                    'UnboxVideo'            => __( 'Unbox Video', 'amazon-auto-links' ),
                    'VHS'                   => __( 'VHS', 'amazon-auto-links' ),
                    'Video'                 => __( 'Video', 'amazon-auto-links' ),
                    'VideoGames'            => __( 'Video Games', 'amazon-auto-links' ),
                    'Watches'               => __( 'Watches', 'amazon-auto-links' ),
                    'Wireless'              => __( 'Wireless', 'amazon-auto-links' ),
                    'WirelessAccessories'   => __( 'Wireless Accessories', 'amazon-auto-links' ),
                );
        }
        
    }    
    
    /**
     * @todo    Confirm if this property is being used. 
     */
    public static $arrSearchIndex = array(
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
    
    
    );
    
    /**
     * 
     * @remark            These IDs were valid as of the publication date of this guide. API Version 2011-08-01
     * @see                http://docs.aws.amazon.com/AWSECommerceService/latest/DG/BrowseNodeIDs.html
     * @todo            Confirm if this property is used or not. The caller method may not be used.
     */
    public static $arrRootNodes = array(
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
    );
    
    /**
     * Returns an array of root node IDs of the specified locale.
     * 
     * The nodes are divided up to 10 elements for the API request.
     */
    public static function getRootNoeds( $strLocale ) {
        
        if ( ! isset( self::$arrRootNodes[ strtoupper( $strLocale ) ] ) ) return array();
        
        return array_chunk( self::$arrRootNodes[ strtoupper( $strLocale ) ], 10 );
        
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
}