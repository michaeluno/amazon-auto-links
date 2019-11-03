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
    /**
     * @var array
     * @since   2?
     * @since   3.8.12      Changed the scheme to https from http.
     * @deprecated   3.9.1
     */
    static public $aCategoryRootURLs = array(
        'CA'    => 'https://www.amazon.ca/gp/bestsellers/',
        'CN'    => 'https://www.amazon.cn/gp/bestsellers/',
        'FR'    => 'https://www.amazon.fr/gp/bestsellers/',
        'DE'    => 'https://www.amazon.de/gp/bestsellers/',
        'IT'    => 'https://www.amazon.it/gp/bestsellers/',
        'JP'    => 'https://www.amazon.co.jp/gp/bestsellers/',
        'UK'    => 'https://www.amazon.co.uk/gp/bestsellers/',
        'ES'    => 'https://www.amazon.es/gp/bestsellers/',
        'US'    => 'https://www.amazon.com/gp/bestsellers/',
        'IN'    => 'https://www.amazon.in/gp/bestsellers/',
        'BR'    => 'https://www.amazon.com.br/gp/bestsellers/',
        'MX'    => 'https://www.amazon.com.mx/gp/bestsellers/',
        'AU'    => 'https://www.amazon.com.au/gp/bestsellers/',
    );

    /**
     * @var array 
     * @since   3.8.12
     * @deprecated  3.9.1
     */
    static public $aStoreDomains = array(
        'CA'    => 'www.amazon.ca',
        'CN'    => 'www.amazon.cn',
        'FR'    => 'www.amazon.fr',
        'DE'    => 'www.amazon.de',
        'IT'    => 'www.amazon.it',
        'JP'    => 'www.amazon.co.jp',
        'UK'    => 'www.amazon.co.uk',
        'ES'    => 'www.amazon.es',
        'US'    => 'www.amazon.com',
        'IN'    => 'www.amazon.in',
        'BR'    => 'www.amazon.com.br',
        'MX'    => 'www.amazon.com.mx',
        'AU'    => 'www.amazon.com.au',
    );    
    /**
     * Returns the market place domain url by the given locale.
     * 
     * @since       3.8.12
     * @return  string the store domain including the URL scheme (https://).
     * @deprecated  3.9.1
     */
/*    static public function getStoreDomainByLocale( $sLocale, $bPrefixScheme=true ) {
        $_sLocale = strtoupper( $sLocale );
        $_sScheme = $bPrefixScheme ? 'https://' : '';
        return isset( self::$aStoreDomains[ $_sLocale ] )
            ? $_sScheme . self::$aStoreDomains[ $_sLocale ]
            : $_sScheme . self::$aStoreDomains[ 'US' ];    // default
    }    
    */
    /**
     * @var array
     */
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
     * @since   unknown
     * @since   3.9.0   Made it compatible with PA-API 5
     * @see     https://webservices.amazon.com/paapi5/documentation/locale-reference.html
     */
    static public function getSearchIndexByLocale( $sLocale ) {

        switch ( strtoupper( $sLocale ) ) {
            case 'AU':
                return array(
                    'All' => __( 'All Departments', 'amazon-auto-links' ),
                    'Automotive' => __( 'Automotive', 'amazon-auto-links' ),
                    'Baby' => __( 'Baby', 'amazon-auto-links' ),
                    'Beauty' => __( 'Beauty', 'amazon-auto-links' ),
                    'Books' => __( 'Books', 'amazon-auto-links' ),
                    'Computers' => __( 'Computers', 'amazon-auto-links' ),
                    'Electronics' => __( 'Electronics', 'amazon-auto-links' ),
                    'EverythingElse' => __( 'Everything Else', 'amazon-auto-links' ),
                    'Fashion' => __( 'Clothing & Shoes', 'amazon-auto-links' ), 
                    'GiftCards' => __( 'Gift Cards', 'amazon-auto-links' ), 
                    'HealthPersonalCare' =>	__( 'Health,  Household & Personal Care', 'amazon-auto-links' ),
                    'HomeAndKitchen' => __( 'Home & Kitchen', 'amazon-auto-links' ), 
                    'KindleStore' => __( 'Kindle Store', 'amazon-auto-links' ), 
                    'Lighting' => __( 'Lighting', 'amazon-auto-links' ), 
                    'Luggage' => __( 'Luggage & Travel Gear', 'amazon-auto-links' ), 
                    'MobileApps' => __( 'Apps & Games', 'amazon-auto-links' ), 
                    'MoviesAndTV' => __( 'Movies & TV', 'amazon-auto-links' ), 
                    'Music' => __( 'CDs & Vinyl', 'amazon-auto-links' ), 
                    'OfficeProducts' => __( 'Stationery & Office Products', 'amazon-auto-links' ), 
                    'PetSupplies' => __( 'Pet Supplies', 'amazon-auto-links' ), 
                    'Software' => __( 'Software', 'amazon-auto-links' ), 
                    'SportsAndOutdoors' => __( 'Sports,  Fitness & Outdoors', 'amazon-auto-links' ), 
                    'ToolsAndHomeImprovement' => __( 'Home Improvement', 'amazon-auto-links' ), 
                    'ToysAndGames' => __( 'Toys & Games', 'amazon-auto-links' ), 
                    'VideoGames' => __( 'Video Games', 'amazon-auto-links' ), 
                );
            case 'BR':
                return array(
                    'All' => __( 'Todos os departamentos', 'amazon-auto-links' ), 
                    'Books' => __( 'Livros', 'amazon-auto-links' ), 
                    'Computers' => __( 'Computadores e Informática', 'amazon-auto-links' ), 
                    'Electronics' => __( 'Eletrônicos', 'amazon-auto-links' ), 
                    'HomeAndKitchen' => __( 'Casa e Cozinha', 'amazon-auto-links' ), 
                    'KindleStore' => __( 'Loja Kindle', 'amazon-auto-links' ), 
                    'MobileApps' => __( 'Apps e Jogos', 'amazon-auto-links' ), 
                    'OfficeProducts' => __( 'Material para Escritório e Papelaria', 'amazon-auto-links' ), 
                    'ToolsAndHomeImprovement' => __( 'Ferramentas e Materiais de Construção', 'amazon-auto-links' ), 
                    'VideoGames' => __( 'Games', 'amazon-auto-links' ), 
                );
            case 'CA':
                // @see https://webservices.amazon.com/paapi5/documentation/locale-reference/canada.html
                return array(
                    'All' => __( 'All Department', 'amazon-auto-links' ), 
                    'Apparel' => __( 'Clothing & Accessories', 'amazon-auto-links' ), 
                    'Automotive' => __( 'Automotive', 'amazon-auto-links' ), 
                    'Baby' => __( 'Baby', 'amazon-auto-links' ), 
                    'Beauty' => __( 'Beauty', 'amazon-auto-links' ), 
                    'Books' => __( 'Books', 'amazon-auto-links' ), 
                    'Classical' => __( 'Classical Music', 'amazon-auto-links' ), 
                    'Electronics' => __( 'Electronics', 'amazon-auto-links' ), 
                    'EverythingElse' => __( 'Everything Else', 'amazon-auto-links' ), 
                    'ForeignBooks' => __( 'English Books', 'amazon-auto-links' ), 
                    'GardenAndOutdoor' => __( 'Patio, Lawn & Garden', 'amazon-auto-links' ), 
                    'GiftCards' => __( 'Gift Cards', 'amazon-auto-links' ), 
                    'GroceryAndGourmetFood' => __( 'Grocery & Gourmet Food', 'amazon-auto-links' ), 
                    'Handmade' => __( 'Handmade', 'amazon-auto-links' ), 
                    'HealthPersonalCare' => __( 'Health & Personal Care', 'amazon-auto-links' ), 
                    'HomeAndKitchen' => __( 'Home & Kitchen', 'amazon-auto-links' ), 
                    'Industrial' => __( 'Industrial & Scientific', 'amazon-auto-links' ), 
                    'Jewelry' => __( 'Jewelry', 'amazon-auto-links' ), 
                    'KindleStore' => __( 'Kindle Store', 'amazon-auto-links' ), 
                    'Luggage' => __( 'Luggage & Bags', 'amazon-auto-links' ), 
                    'LuxuryBeauty' => __( 'Luxury Beauty', 'amazon-auto-links' ), 
                    'MobileApps' => __( 'Apps & Games', 'amazon-auto-links' ), 
                    'MoviesAndTV' => __( 'Movies & TV', 'amazon-auto-links' ), 
                    'Music' => __( 'Music', 'amazon-auto-links' ), 
                    'MusicalInstruments' => __( 'Musical Instruments, Stage & Studio', 'amazon-auto-links' ), 
                    'OfficeProducts' => __( 'Office Products', 'amazon-auto-links' ), 
                    'PetSupplies' => __( 'Pet Supplies', 'amazon-auto-links' ), 
                    'Shoes' => __( 'Shoes & Handbags', 'amazon-auto-links' ), 
                    'Software' => __( 'Software', 'amazon-auto-links' ), 
                    'SportsAndOutdoors' => __( 'Sports & Outdoors', 'amazon-auto-links' ), 
                    'ToolsAndHomeImprovement' => __( 'Tools & Home Improvement', 'amazon-auto-links' ), 
                    'ToysAndGames' => __( 'Toys & Games', 'amazon-auto-links' ), 
                    'VHS' => __( 'VHS', 'amazon-auto-links' ), 
                    'VideoGames' => __( 'Video Games', 'amazon-auto-links' ), 
                    'Watches' => __( 'Watches', 'amazon-auto-links' ), 
                );
            case 'FR':
                // @see https://webservices.amazon.com/paapi5/documentation/locale-reference/france.html
                return array(
                    'All' => __( 'Toutes nos catégories', 'amazon-auto-links' ), 
                    'Apparel' => __( 'Vêtements et accessoires', 'amazon-auto-links' ), 
                    'Appliances' => __( 'Gros électroménager', 'amazon-auto-links' ), 
                    'Automotive' => __( 'Auto et Moto', 'amazon-auto-links' ), 
                    'Baby' => __( 'Bébés & Puériculture', 'amazon-auto-links' ), 
                    'Beauty' => __( 'Beauté et Parfum', 'amazon-auto-links' ), 
                    'Books' => __( 'Livres en français', 'amazon-auto-links' ), 
                    'Computers' => __( 'Informatique', 'amazon-auto-links' ), 
                    'DigitalMusic' => __( 'Téléchargement de musique', 'amazon-auto-links' ), 
                    'Electronics' => __( 'High-Tech', 'amazon-auto-links' ), 
                    'EverythingElse' => __( 'Autres', 'amazon-auto-links' ), 
                    'Fashion' => __( 'Mode', 'amazon-auto-links' ), 
                    'ForeignBooks' => __( 'Livres anglais et étrangers', 'amazon-auto-links' ), 
                    'GardenAndOutdoor' => __( 'Jardin', 'amazon-auto-links' ), 
                    'GiftCards' => __( 'Boutique chèques-cadeaux', 'amazon-auto-links' ), 
                    'GroceryAndGourmetFood' => __( 'Epicerie', 'amazon-auto-links' ), 
                    'Handmade' => __( 'Handmade', 'amazon-auto-links' ), 
                    'HealthPersonalCare' => __( 'Hygiène et Santé', 'amazon-auto-links' ), 
                    'HomeAndKitchen' => __( 'Cuisine & Maison', 'amazon-auto-links' ), 
                    'Industrial' => __( 'Secteur industriel & scientifique', 'amazon-auto-links' ), 
                    'Jewelry' => __( 'Bijoux', 'amazon-auto-links' ), 
                    'KindleStore' => __( 'Boutique Kindle', 'amazon-auto-links' ), 
                    'Lighting' => __( 'Luminaires et Eclairage', 'amazon-auto-links' ), 
                    'Luggage' => __( 'Bagages', 'amazon-auto-links' ), 
                    'LuxuryBeauty' => __( 'Beauté Prestige', 'amazon-auto-links' ), 
                    'MobileApps' => __( 'Applis & Jeux', 'amazon-auto-links' ), 
                    'MoviesAndTV' => __( 'DVD & Blu-ray', 'amazon-auto-links' ), 
                    'Music' => __( 'Musique : CD & Vinyles', 'amazon-auto-links' ), 
                    'MusicalInstruments' => __( 'Instruments de musique & Sono', 'amazon-auto-links' ), 
                    'OfficeProducts' => __( 'Fournitures de bureau', 'amazon-auto-links' ), 
                    'PetSupplies' => __( 'Animalerie', 'amazon-auto-links' ), 
                    'Shoes' => __( 'Chaussures et Sacs', 'amazon-auto-links' ), 
                    'Software' => __( 'Logiciels', 'amazon-auto-links' ), 
                    'SportsAndOutdoors' => __( 'Sports et Loisirs', 'amazon-auto-links' ), 
                    'ToolsAndHomeImprovement' => __( 'Bricolage', 'amazon-auto-links' ), 
                    'ToysAndGames' => __( 'Jeux et Jouets', 'amazon-auto-links' ), 
                    'VHS' => __( 'VHS', 'amazon-auto-links' ), 
                    'VideoGames' => __( 'Jeux vidéo', 'amazon-auto-links' ), 
                    'Watches' => __( 'Montres', 'amazon-auto-links' ), 
                );
            case 'DE':
                // @see https://webservices.amazon.com/paapi5/documentation/locale-reference/germany.html
                return array(
                    'All' => __( 'Alle Kategorien', 'amazon-auto-links' ), 
                    'AmazonVideo' => __( 'Prime Video', 'amazon-auto-links' ), 
                    'Apparel' => __( 'Bekleidung', 'amazon-auto-links' ), 
                    'Appliances' => __( 'Elektro-Großgeräte', 'amazon-auto-links' ), 
                    'Automotive' => __( 'Auto & Motorrad', 'amazon-auto-links' ), 
                    'Baby' => __( 'Baby', 'amazon-auto-links' ), 
                    'Beauty' => __( 'Beauty', 'amazon-auto-links' ), 
                    'Books' => __( 'Bücher', 'amazon-auto-links' ), 
                    'Classical' => __( 'Klassik', 'amazon-auto-links' ), 
                    'Computers' => __( 'Computer & Zubehör', 'amazon-auto-links' ), 
                    'DigitalMusic' => __( 'Musik-Downloads', 'amazon-auto-links' ), 
                    'Electronics' => __( 'Elektronik & Foto', 'amazon-auto-links' ), 
                    'EverythingElse' => __( 'Sonstiges', 'amazon-auto-links' ), 
                    'Fashion' => __( 'Fashion', 'amazon-auto-links' ), 
                    'ForeignBooks' => __( 'Bücher (Fremdsprachig)', 'amazon-auto-links' ), 
                    'GardenAndOutdoor' => __( 'Garten', 'amazon-auto-links' ), 
                    'GiftCards' => __( 'Geschenkgutscheine', 'amazon-auto-links' ), 
                    'GroceryAndGourmetFood' => __( 'Lebensmittel & Getränke', 'amazon-auto-links' ), 
                    'Handmade' => __( 'Handmade', 'amazon-auto-links' ), 
                    'HealthPersonalCare' => __( 'Drogerie & Körperpflege', 'amazon-auto-links' ), 
                    'HomeAndKitchen' => __( 'Küche, Haushalt & Wohnen', 'amazon-auto-links' ), 
                    'Industrial' => __( 'Gewerbe, Industrie & Wissenschaft', 'amazon-auto-links' ), 
                    'Jewelry' => __( 'Schmuck', 'amazon-auto-links' ), 
                    'KindleStore' => __( 'Kindle-Shop', 'amazon-auto-links' ), 
                    'Lighting' => __( 'Beleuchtung', 'amazon-auto-links' ), 
                    'Luggage' => __( 'Koffer, Rucksäcke & Taschen', 'amazon-auto-links' ), 
                    'LuxuryBeauty' => __( 'Luxury Beauty', 'amazon-auto-links' ), 
                    'Magazines' => __( 'Zeitschriften', 'amazon-auto-links' ), 
                    'MobileApps' => __( 'Apps & Spiele', 'amazon-auto-links' ), 
                    'MoviesAndTV' => __( 'DVD & Blu-ray', 'amazon-auto-links' ), 
                    'Music' => __( 'Musik-CDs & Vinyl', 'amazon-auto-links' ), 
                    'MusicalInstruments' => __( 'Musikinstrumente & DJ-Equipment', 'amazon-auto-links' ), 
                    'OfficeProducts' => __( 'Bürobedarf & Schreibwaren', 'amazon-auto-links' ), 
                    'PetSupplies' => __( 'Haustier', 'amazon-auto-links' ), 
                    'Photo' => __( 'Kamera & Foto', 'amazon-auto-links' ), 
                    'Shoes' => __( 'Schuhe & Handtaschen', 'amazon-auto-links' ), 
                    'Software' => __( 'Software', 'amazon-auto-links' ), 
                    'SportsAndOutdoors' => __( 'Sport & Freizeit', 'amazon-auto-links' ), 
                    'ToolsAndHomeImprovement' => __( 'Baumarkt', 'amazon-auto-links' ), 
                    'ToysAndGames' => __( 'Spielzeug', 'amazon-auto-links' ), 
                    'VHS' => __( 'VHS', 'amazon-auto-links' ), 
                    'VideoGames' => __( 'Games', 'amazon-auto-links' ), 
                    'Watches' => __( 'Uhren', 'amazon-auto-links' ), 
                );
            case 'IN':
                // @see https://webservices.amazon.com/paapi5/documentation/locale-reference/india.html
                return array(
                    'All' => __( 'All Categories', 'amazon-auto-links' ), 
                    'Apparel' => __( 'Clothing & Accessories', 'amazon-auto-links' ), 
                    'Appliances' => __( 'Appliances', 'amazon-auto-links' ), 
                    'Automotive' => __( 'Car & Motorbike', 'amazon-auto-links' ), 
                    'Baby' => __( 'Baby', 'amazon-auto-links' ), 
                    'Beauty' => __( 'Beauty', 'amazon-auto-links' ), 
                    'Books' => __( 'Books', 'amazon-auto-links' ), 
                    'Collectibles' => __( 'Collectibles', 'amazon-auto-links' ), 
                    'Computers' => __( 'Computers & Accessories', 'amazon-auto-links' ), 
                    'Electronics' => __( 'Electronics', 'amazon-auto-links' ), 
                    'EverythingElse' => __( 'Everything Else', 'amazon-auto-links' ), 
                    'Fashion' => __( 'Amazon Fashion', 'amazon-auto-links' ), 
                    'Furniture' => __( 'Furniture', 'amazon-auto-links' ), 
                    'GardenAndOutdoor' => __( 'Garden & Outdoors', 'amazon-auto-links' ), 
                    'GiftCards' => __( 'Gift Cards', 'amazon-auto-links' ), 
                    'GroceryAndGourmetFood' => __( 'Grocery & Gourmet Foods', 'amazon-auto-links' ), 
                    'HealthPersonalCare' => __( 'Health & Personal Care', 'amazon-auto-links' ), 
                    'HomeAndKitchen' => __( 'Home & Kitchen', 'amazon-auto-links' ), 
                    'Industrial' => __( 'Industrial & Scientific', 'amazon-auto-links' ), 
                    'Jewelry' => __( 'Jewellery', 'amazon-auto-links' ), 
                    'KindleStore' => __( 'Kindle Store', 'amazon-auto-links' ), 
                    'Luggage' => __( 'Luggage & Bags', 'amazon-auto-links' ), 
                    'LuxuryBeauty' => __( 'Luxury Beauty', 'amazon-auto-links' ), 
                    'MobileApps' => __( 'Apps & Games', 'amazon-auto-links' ), 
                    'MoviesAndTV' => __( 'Movies & TV Shows', 'amazon-auto-links' ), 
                    'Music' => __( 'Music', 'amazon-auto-links' ), 
                    'MusicalInstruments' => __( 'Musical Instruments', 'amazon-auto-links' ), 
                    'OfficeProducts' => __( 'Office Products', 'amazon-auto-links' ), 
                    'PetSupplies' => __( 'Pet Supplies', 'amazon-auto-links' ), 
                    'Shoes' => __( 'Shoes & Handbags', 'amazon-auto-links' ), 
                    'Software' => __( 'Software', 'amazon-auto-links' ), 
                    'SportsAndOutdoors' => __( 'Sports, Fitness & Outdoors', 'amazon-auto-links' ), 
                    'ToolsAndHomeImprovement' => __( 'Tools & Home Improvement', 'amazon-auto-links' ), 
                    'ToysAndGames' => __( 'Toys & Games', 'amazon-auto-links' ), 
                    'VideoGames' => __( 'Video Games', 'amazon-auto-links' ),
                    'Watches' => __( 'Watches', 'amazon-auto-links' ),
                );
            case 'IT':
                // @see https://webservices.amazon.com/paapi5/documentation/locale-reference/italy.html
                return array(
                    'All' => __( 'Tutte le categorie', 'amazon-auto-links' ), 
                    'Apparel' => __( 'Abbigliamento', 'amazon-auto-links' ), 
                    'Appliances' => __( 'Grandi elettrodomestici', 'amazon-auto-links' ), 
                    'Automotive' => __( 'Auto e Moto', 'amazon-auto-links' ), 
                    'Baby' => __( 'Prima infanzia', 'amazon-auto-links' ), 
                    'Beauty' => __( 'Bellezza', 'amazon-auto-links' ), 
                    'Books' => __( 'Libri', 'amazon-auto-links' ), 
                    'Computers' => __( 'Informatica', 'amazon-auto-links' ), 
                    'DigitalMusic' => __( 'Musica Digitale', 'amazon-auto-links' ), 
                    'Electronics' => __( 'Elettronica', 'amazon-auto-links' ), 
                    'EverythingElse' => __( 'Altro', 'amazon-auto-links' ), 
                    'Fashion' => __( 'Moda', 'amazon-auto-links' ), 
                    'ForeignBooks' => __( 'Libri in altre lingue', 'amazon-auto-links' ), 
                    'GardenAndOutdoor' => __( 'Giardino e giardinaggio', 'amazon-auto-links' ), 
                    'GiftCards' => __( 'Buoni Regalo', 'amazon-auto-links' ), 
                    'GroceryAndGourmetFood' => __( 'Alimentari e cura della casa', 'amazon-auto-links' ), 
                    'Handmade' => __( 'Handmade', 'amazon-auto-links' ), 
                    'HealthPersonalCare' => __( 'Salute e cura della persona', 'amazon-auto-links' ), 
                    'HomeAndKitchen' => __( 'Casa e cucina', 'amazon-auto-links' ), 
                    'Industrial' => __( 'Industria e Scienza', 'amazon-auto-links' ), 
                    'Jewelry' => __( 'Gioielli', 'amazon-auto-links' ), 
                    'KindleStore' => __( 'Kindle Store', 'amazon-auto-links' ), 
                    'Lighting' => __( 'Illuminazione', 'amazon-auto-links' ), 
                    'Luggage' => __( 'Valigeria', 'amazon-auto-links' ), 
                    'MobileApps' => __( 'App e Giochi', 'amazon-auto-links' ), 
                    'MoviesAndTV' => __( 'Film e TV', 'amazon-auto-links' ), 
                    'Music' => __( 'CD e Vinili', 'amazon-auto-links' ), 
                    'MusicalInstruments' => __( 'Strumenti musicali e DJ', 'amazon-auto-links' ), 
                    'OfficeProducts' => __( 'Cancelleria e prodotti per ufficio', 'amazon-auto-links' ), 
                    'PetSupplies' => __( 'Prodotti per animali domestici', 'amazon-auto-links' ), 
                    'Shoes' => __( 'Scarpe e borse', 'amazon-auto-links' ), 
                    'Software' => __( 'Software', 'amazon-auto-links' ), 
                    'SportsAndOutdoors' => __( 'Sport e tempo libero', 'amazon-auto-links' ), 
                    'ToolsAndHomeImprovement' => __( 'Fai da te', 'amazon-auto-links' ), 
                    'ToysAndGames' => __( 'Giochi e giocattoli', 'amazon-auto-links' ), 
                    'VideoGames' => __( 'Videogiochi', 'amazon-auto-links' ), 
                    'Watches' => __( 'Orologi', 'amazon-auto-links' ),
                );
            case 'JP':
                // @see https://webservices.amazon.com/paapi5/documentation/locale-reference/japan.html
                return array(
                    'All' => __( 'All Departments', 'amazon-auto-links' ), 
                    'AmazonVideo' => __( 'Prime Video', 'amazon-auto-links' ), 
                    'Apparel' => __( 'Clothing & Accessories', 'amazon-auto-links' ), 
                    'Appliances' => __( 'Large Appliances', 'amazon-auto-links' ), 
                    'Automotive' => __( 'Car & Bike Products', 'amazon-auto-links' ), 
                    'Baby' => __( 'Baby & Maternity', 'amazon-auto-links' ), 
                    'Beauty' => __( 'Beauty', 'amazon-auto-links' ), 
                    'Books' => __( 'Japanese Books', 'amazon-auto-links' ), 
                    'Classical' => __( 'Classical', 'amazon-auto-links' ), 
                    'Computers' => __( 'Computers & Accessories', 'amazon-auto-links' ), 
                    'CreditCards' => __( 'Credit Cards', 'amazon-auto-links' ), 
                    'DigitalMusic' => __( 'Digital Music', 'amazon-auto-links' ), 
                    'Electronics' => __( 'Electronics & Cameras', 'amazon-auto-links' ), 
                    'EverythingElse' => __( 'Everything Else', 'amazon-auto-links' ), 
                    'Fashion' => __( 'Fashion', 'amazon-auto-links' ), 
                    'FashionBaby' => __( 'Kids & Baby', 'amazon-auto-links' ), 
                    'FashionMen' => __( 'Men', 'amazon-auto-links' ), 
                    'FashionWomen' => __( 'Women', 'amazon-auto-links' ), 
                    'ForeignBooks' => __( 'English Books', 'amazon-auto-links' ), 
                    'GiftCards' => __( 'Gift Cards', 'amazon-auto-links' ), 
                    'GroceryAndGourmetFood' => __( 'Food & Beverage', 'amazon-auto-links' ), 
                    'HealthPersonalCare' => __( 'Health & Personal Care', 'amazon-auto-links' ), 
                    'Hobbies' => __( 'Hobby', 'amazon-auto-links' ), 
                    'HomeAndKitchen' => __( 'Kitchen & Housewares', 'amazon-auto-links' ), 
                    'Industrial' => __( 'Industrial & Scientific', 'amazon-auto-links' ), 
                    'Jewelry' => __( 'Jewelry', 'amazon-auto-links' ), 
                    'KindleStore' => __( 'Kindle Store', 'amazon-auto-links' ), 
                    'MobileApps' => __( 'Apps & Games', 'amazon-auto-links' ), 
                    'MoviesAndTV' => __( 'Movies & TV', 'amazon-auto-links' ), 
                    'Music' => __( 'Music', 'amazon-auto-links' ), 
                    'MusicalInstruments' => __( 'Musical Instruments', 'amazon-auto-links' ), 
                    'OfficeProducts' => __( 'Stationery and Office Products', 'amazon-auto-links' ), 
                    'PetSupplies' => __( 'Pet Supplies', 'amazon-auto-links' ), 
                    'Shoes' => __( 'Shoes & Bags', 'amazon-auto-links' ), 
                    'Software' => __( 'Software', 'amazon-auto-links' ), 
                    'SportsAndOutdoors' => __( 'Sports', 'amazon-auto-links' ), 
                    'ToolsAndHomeImprovement' => __( 'DIY, Tools & Garden', 'amazon-auto-links' ), 
                    'Toys' => __( 'Toys', 'amazon-auto-links' ), 
                    'VideoGames' => __( 'Computer & Video Games', 'amazon-auto-links' ), 
                    'Watches' => __( 'Watches', 'amazon-auto-links' ),
                );
            case 'MX':
                // @see https://webservices.amazon.com/paapi5/documentation/locale-reference/mexico.html
                return array(
                    'All' => __( 'Todos los departamentos', 'amazon-auto-links' ), 
                    'Automotive' => __( 'Auto', 'amazon-auto-links' ), 
                    'Baby' => __( 'Bebé', 'amazon-auto-links' ), 
                    'Books' => __( 'Libros', 'amazon-auto-links' ), 
                    'Electronics' => __( 'Electrónicos', 'amazon-auto-links' ), 
                    'Fashion' => __( 'Ropa, Zapatos y Accesorios', 'amazon-auto-links' ), 
                    'FashionBaby' => __( 'Ropa, Zapatos y Accesorios Bebé', 'amazon-auto-links' ), 
                    'FashionBoys' => __( 'Ropa, Zapatos y Accesorios Niños', 'amazon-auto-links' ), 
                    'FashionGirls' => __( 'Ropa, Zapatos y Accesorios Niñas', 'amazon-auto-links' ), 
                    'FashionMen' => __( 'Ropa, Zapatos y Accesorios Hombres', 'amazon-auto-links' ), 
                    'FashionWomen' => __( 'Ropa, Zapatos y Accesorios Mujeres', 'amazon-auto-links' ), 
                    'GroceryAndGourmetFood' => __( 'Alimentos y Bebidas', 'amazon-auto-links' ), 
                    'Handmade' => __( 'Productos Handmade', 'amazon-auto-links' ), 
                    'HealthPersonalCare' => __( 'Salud, Belleza y Cuidado Personal', 'amazon-auto-links' ), 
                    'HomeAndKitchen' => __( 'Hogar y Cocina', 'amazon-auto-links' ), 
                    'IndustrialAndScientific' => __( 'Industria y ciencia', 'amazon-auto-links' ), 
                    'KindleStore' => __( 'Tienda Kindle', 'amazon-auto-links' ), 
                    'MoviesAndTV' => __( 'Películas y Series de TV', 'amazon-auto-links' ), 
                    'Music' => __( 'Música', 'amazon-auto-links' ), 
                    'MusicalInstruments' => __( 'Instrumentos musicales', 'amazon-auto-links' ), 
                    'OfficeProducts' => __( 'Oficina y Papelería', 'amazon-auto-links' ), 
                    'PetSupplies' => __( 'Mascotas', 'amazon-auto-links' ), 
                    'Software' => __( 'Software', 'amazon-auto-links' ), 
                    'SportsAndOutdoors' => __( 'Deportes y Aire Libre', 'amazon-auto-links' ), 
                    'ToolsAndHomeImprovement' => __( 'Herramientas y Mejoras del Hogar', 'amazon-auto-links' ), 
                    'ToysAndGames' => __( 'Juegos y juguetes', 'amazon-auto-links' ), 
                    'VideoGames' => __( 'Videojuegos', 'amazon-auto-links' ), 
                    'Watches' => __( 'Relojes', 'amazon-auto-links' ),
                );
            case 'ES':
                // @see https://webservices.amazon.com/paapi5/documentation/locale-reference/spain.html
                return array(
                    'All' => __( 'Todos los departamentos', 'amazon-auto-links' ), 
                    'Apparel' => __( 'Ropa y accesorios', 'amazon-auto-links' ), 
                    'Appliances' => __( 'Grandes electrodomésticos', 'amazon-auto-links' ), 
                    'Automotive' => __( 'Coche y moto', 'amazon-auto-links' ), 
                    'Baby' => __( 'Bebé', 'amazon-auto-links' ), 
                    'Beauty' => __( 'Belleza', 'amazon-auto-links' ), 
                    'Books' => __( 'Libros', 'amazon-auto-links' ), 
                    'Computers' => __( 'Informática', 'amazon-auto-links' ), 
                    'DigitalMusic' => __( 'Música Digital', 'amazon-auto-links' ), 
                    'Electronics' => __( 'Electrónica', 'amazon-auto-links' ), 
                    'EverythingElse' => __( 'Otros Productos', 'amazon-auto-links' ), 
                    'Fashion' => __( 'Moda', 'amazon-auto-links' ), 
                    'ForeignBooks' => __( 'Libros en idiomas extranjeros', 'amazon-auto-links' ), 
                    'GardenAndOutdoor' => __( 'Jardín', 'amazon-auto-links' ), 
                    'GiftCards' => __( 'Cheques regalo', 'amazon-auto-links' ), 
                    'GroceryAndGourmetFood' => __( 'Alimentación y bebidas', 'amazon-auto-links' ), 
                    'Handmade' => __( 'Handmade', 'amazon-auto-links' ), 
                    'HealthPersonalCare' => __( 'Salud y cuidado personal', 'amazon-auto-links' ), 
                    'HomeAndKitchen' => __( 'Hogar y cocina', 'amazon-auto-links' ), 
                    'Industrial' => __( 'Industria y ciencia', 'amazon-auto-links' ), 
                    'Jewelry' => __( 'Joyería', 'amazon-auto-links' ), 
                    'KindleStore' => __( 'Tienda Kindle', 'amazon-auto-links' ), 
                    'Lighting' => __( 'Iluminación', 'amazon-auto-links' ), 
                    'Luggage' => __( 'Equipaje', 'amazon-auto-links' ), 
                    'MobileApps' => __( 'Appstore para Android', 'amazon-auto-links' ), 
                    'MoviesAndTV' => __( 'Películas y TV', 'amazon-auto-links' ), 
                    'Music' => __( 'Música: CDs y vinilos', 'amazon-auto-links' ),
                    'MusicalInstruments' => __( 'Instrumentos musicales', 'amazon-auto-links' ),
                    'OfficeProducts' => __( 'Oficina y papelería', 'amazon-auto-links' ), 
                    'PetSupplies' => __( 'Productos para mascotas', 'amazon-auto-links' ), 
                    'Shoes' => __( 'Zapatos y complementos', 'amazon-auto-links' ), 
                    'Software' => __( 'Software', 'amazon-auto-links' ), 
                    'SportsAndOutdoors' => __( 'Deportes y aire libre', 'amazon-auto-links' ), 
                    'ToolsAndHomeImprovement' => __( 'Bricolaje y herramientas', 'amazon-auto-links' ), 
                    'ToysAndGames' => __( 'Juguetes y juegos', 'amazon-auto-links' ), 
                    'Vehicles' => __( 'Coche - renting', 'amazon-auto-links' ), 
                    'VideoGames' => __( 'Videojuegos', 'amazon-auto-links' ), 
                    'Watches' => __( 'Relojes', 'amazon-auto-links' ), 
                );
            case 'TR':
                // @see https://webservices.amazon.com/paapi5/documentation/locale-reference/turkey.html
                return array(
                    'All' => __( 'Tüm Kategoriler', 'amazon-auto-links' ), 
                    'Baby' => __( 'Bebek', 'amazon-auto-links' ), 
                    'Books' => __( 'Kitaplar', 'amazon-auto-links' ), 
                    'Computers' => __( 'Bilgisayarlar', 'amazon-auto-links' ), 
                    'Electronics' => __( 'Elektronik', 'amazon-auto-links' ), 
                    'EverythingElse' => __( 'Diğer Her Şey', 'amazon-auto-links' ), 
                    'Fashion' => __( 'Moda', 'amazon-auto-links' ), 
                    'HomeAndKitchen' => __( 'Ev ve Mutfak', 'amazon-auto-links' ), 
                    'OfficeProducts' => __( 'Ofis Ürünleri', 'amazon-auto-links' ), 
                    'SportsAndOutdoors' => __( 'Spor', 'amazon-auto-links' ), 
                    'ToolsAndHomeImprovement' => __( 'Yapı Market', 'amazon-auto-links' ), 
                    'ToysAndGames' => __( 'Oyuncaklar ve Oyunlar', 'amazon-auto-links' ), 
                    'VideoGames' => __( 'PC ve Video Oyunları', 'amazon-auto-links' ), 
                );
            case 'AE':
                // @see https://webservices.amazon.com/paapi5/documentation/locale-reference/united-arab-emirates.html
                return array(
                    'All' => __( 'All Departments', 'amazon-auto-links' ), 
                    'Automotive' => __( 'Automotive Parts & Accessories', 'amazon-auto-links' ), 
                    'Baby' => __( 'Baby', 'amazon-auto-links' ), 
                    'Beauty' => __( 'Beauty & Personal Care', 'amazon-auto-links' ), 
                    'Books' => __( 'Books', 'amazon-auto-links' ), 
                    'Computers' => __( 'Computers', 'amazon-auto-links' ), 
                    'Electronics' => __( 'Electronics', 'amazon-auto-links' ), 
                    'EverythingElse' => __( 'Everything Else', 'amazon-auto-links' ), 
                    'Fashion' => __( 'Clothing, Shoes & Jewelry', 'amazon-auto-links' ), 
                    'HomeAndKitchen' => __( 'Home & Kitchen', 'amazon-auto-links' ), 
                    'Lighting' => __( 'Lighting', 'amazon-auto-links' ), 
                    'ToysAndGames' => __( 'Toys & Games', 'amazon-auto-links' ), 
                    'VideoGames' => __( 'Video Games', 'amazon-auto-links' ), 
                );
            case 'UK':
                // @see https://webservices.amazon.com/paapi5/documentation/locale-reference/united-kingdom.html
                return array(
                    'All' => __( 'All Departments', 'amazon-auto-links' ), 
                    'AmazonVideo' => __( 'Amazon Video', 'amazon-auto-links' ), 
                    'Apparel' => __( 'Clothing', 'amazon-auto-links' ), 
                    'Appliances' => __( 'Large Appliances', 'amazon-auto-links' ), 
                    'Automotive' => __( 'Car & Motorbike', 'amazon-auto-links' ), 
                    'Baby' => __( 'Baby', 'amazon-auto-links' ), 
                    'Beauty' => __( 'Beauty', 'amazon-auto-links' ), 
                    'Books' => __( 'Books', 'amazon-auto-links' ), 
                    'Classical' => __( 'Classical Music', 'amazon-auto-links' ), 
                    'Computers' => __( 'Computers & Accessories', 'amazon-auto-links' ), 
                    'DigitalMusic' => __( 'Digital Music', 'amazon-auto-links' ), 
                    'Electronics' => __( 'Electronics & Photo', 'amazon-auto-links' ), 
                    'EverythingElse' => __( 'Everything Else', 'amazon-auto-links' ), 
                    'Fashion' => __( 'Fashion', 'amazon-auto-links' ), 
                    'GardenAndOutdoor' => __( 'Garden & Outdoors', 'amazon-auto-links' ), 
                    'GiftCards' => __( 'Gift Cards', 'amazon-auto-links' ), 
                    'GroceryAndGourmetFood' => __( 'Grocery', 'amazon-auto-links' ), 
                    'Handmade' => __( 'Handmade', 'amazon-auto-links' ), 
                    'HealthPersonalCare' => __( 'Health & Personal Care', 'amazon-auto-links' ), 
                    'HomeAndKitchen' => __( 'Home & Kitchen', 'amazon-auto-links' ), 
                    'Industrial' => __( 'Industrial & Scientific', 'amazon-auto-links' ), 
                    'Jewelry' => __( 'Jewellery', 'amazon-auto-links' ), 
                    'KindleStore' => __( 'Kindle Store', 'amazon-auto-links' ), 
                    'Lighting' => __( 'Lighting', 'amazon-auto-links' ), 
                    'LuxuryBeauty' => __( 'Luxury Beauty', 'amazon-auto-links' ), 
                    'MobileApps' => __( 'Apps & Games', 'amazon-auto-links' ), 
                    'MoviesAndTV' => __( 'DVD & Blu-ray', 'amazon-auto-links' ), 
                    'Music' => __( 'CDs & Vinyl', 'amazon-auto-links' ), 
                    'MusicalInstruments' => __( 'Musical Instruments & DJ', 'amazon-auto-links' ), 
                    'OfficeProducts' => __( 'Stationery & Office Supplies', 'amazon-auto-links' ), 
                    'PetSupplies' => __( 'Pet Supplies', 'amazon-auto-links' ), 
                    'Shoes' => __( 'Shoes & Bags', 'amazon-auto-links' ), 
                    'Software' => __( 'Software', 'amazon-auto-links' ), 
                    'SportsAndOutdoors' => __( 'Sports & Outdoors', 'amazon-auto-links' ), 
                    'ToolsAndHomeImprovement' => __( 'DIY & Tools', 'amazon-auto-links' ), 
                    'ToysAndGames' => __( 'Toys & Games', 'amazon-auto-links' ), 
                    'VHS' => __( 'VHS', 'amazon-auto-links' ), 
                    'VideoGames' => __( 'PC & Video Games', 'amazon-auto-links' ), 
                    'Watches' => __( 'Watches', 'amazon-auto-links' ), 
                );
            case 'CN':
                // @see https://docs.aws.amazon.com/AWSECommerceService/latest/DG/LocaleCN.html
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
            default:
            case 'US':
                // @see https://webservices.amazon.com/paapi5/documentation/locale-reference/united-states.html
                return array(
                    'All' => __( 'All Departments', 'amazon-auto-links' ), 
                    'AmazonVideo' => __( 'Prime Video', 'amazon-auto-links' ), 
                    'Apparel' => __( 'Clothing & Accessories', 'amazon-auto-links' ), 
                    'Appliances' => __( 'Appliances', 'amazon-auto-links' ), 
                    'ArtsAndCrafts' => __( 'Arts, Crafts & Sewing', 'amazon-auto-links' ), 
                    'Automotive' => __( 'Automotive Parts & Accessories', 'amazon-auto-links' ), 
                    'Baby' => __( 'Baby', 'amazon-auto-links' ), 
                    'Beauty' => __( 'Beauty & Personal Care', 'amazon-auto-links' ), 
                    'Books' => __( 'Books', 'amazon-auto-links' ), 
                    'Classical' => __( 'Classical', 'amazon-auto-links' ), 
                    'Collectibles' => __( 'Collectibles & Fine Art', 'amazon-auto-links' ), 
                    'Computers' => __( 'Computers', 'amazon-auto-links' ), 
                    'DigitalMusic' => __( 'Digital Music', 'amazon-auto-links' ), 
                    'Electronics' => __( 'Electronics', 'amazon-auto-links' ), 
                    'EverythingElse' => __( 'Everything Else', 'amazon-auto-links' ), 
                    'Fashion' => __( 'Clothing, Shoes & Jewelry', 'amazon-auto-links' ), 
                    'FashionBaby' => __( 'Clothing, Shoes & Jewelry Baby', 'amazon-auto-links' ), 
                    'FashionBoys' => __( 'Clothing, Shoes & Jewelry Boys', 'amazon-auto-links' ), 
                    'FashionGirls' => __( 'Clothing, Shoes & Jewelry Girls', 'amazon-auto-links' ), 
                    'FashionMen' => __( 'Clothing, Shoes & Jewelry Men', 'amazon-auto-links' ), 
                    'FashionWomen' => __( 'Clothing, Shoes & Jewelry Women', 'amazon-auto-links' ), 
                    'GardenAndOutdoor' => __( 'Garden & Outdoor', 'amazon-auto-links' ), 
                    'GiftCards' => __( 'Gift Cards', 'amazon-auto-links' ), 
                    'GroceryAndGourmetFood' => __( 'Grocery & Gourmet Food', 'amazon-auto-links' ), 
                    'Handmade' => __( 'Handmade', 'amazon-auto-links' ), 
                    'HealthPersonalCare' => __( 'Health, Household & Baby Care', 'amazon-auto-links' ), 
                    'HomeAndKitchen' => __( 'Home & Kitchen', 'amazon-auto-links' ), 
                    'Industrial' => __( 'Industrial & Scientific', 'amazon-auto-links' ), 
                    'Jewelry' => __( 'Jewelry', 'amazon-auto-links' ), 
                    'KindleStore' => __( 'Kindle Store', 'amazon-auto-links' ), 
                    'LocalServices' => __( 'Home & Business Services', 'amazon-auto-links' ), 
                    'Luggage' => __( 'Luggage & Travel Gear', 'amazon-auto-links' ), 
                    'LuxuryBeauty' => __( 'Luxury Beauty', 'amazon-auto-links' ), 
                    'Magazines' => __( 'Magazine Subscriptions', 'amazon-auto-links' ), 
                    'MobileAndAccessories' => __( 'Cell Phones & Accessories', 'amazon-auto-links' ), 
                    'MobileApps' => __( 'Apps & Games', 'amazon-auto-links' ), 
                    'MoviesAndTV' => __( 'Movies & TV', 'amazon-auto-links' ), 
                    'Music' => __( 'CDs & Vinyl', 'amazon-auto-links' ), 
                    'MusicalInstruments' => __( 'Musical Instruments', 'amazon-auto-links' ), 
                    'OfficeProducts' => __( 'Office Products', 'amazon-auto-links' ), 
                    'PetSupplies' => __( 'Pet Supplies', 'amazon-auto-links' ), 
                    'Photo' => __( 'Camera & Photo', 'amazon-auto-links' ), 
                    'Shoes' => __( 'Shoes', 'amazon-auto-links' ), 
                    'Software' => __( 'Software', 'amazon-auto-links' ), 
                    'SportsAndOutdoors' => __( 'Sports & Outdoors', 'amazon-auto-links' ), 
                    'ToolsAndHomeImprovement' => __( 'Tools & Home Improvement', 'amazon-auto-links' ), 
                    'ToysAndGames' => __( 'Toys & Games', 'amazon-auto-links' ), 
                    'VHS' => __( 'VHS', 'amazon-auto-links' ), 
                    'VideoGames' => __( 'Video Games', 'amazon-auto-links' ), 
                    'Watches' => __( 'Watches', 'amazon-auto-links' ), 
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
    static public function getRootNodes( $sLocale ) {
        
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