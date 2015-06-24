<?php
/**
 * Creates links for the user.
 * 
 * @package     Amazon Auto Links
 * @copyright   Copyright (c) 2013, Michael Uno
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since        2.0.0
 * @since        2.0.5            Fixed a bug that a first item always get removed.
*/
abstract class AmazonAutoLinks_UserAds_ {

    // Properties        
    // URLs by locale
    protected $arrURLFeedText = array(
        'en' => array( 'http://feeds.feedburner.com/GANLinkTextRandom40' ),
        'ja' => array( 'http://feeds.feedburner.com/MiunosoftTextLinks-Jp' ),
    );    
    protected $arrURLFeed160xNTopRight = array(
        'en' => array( 'http://feeds.feedburner.com/Miunosoft-160xnTopRight' ),
        'ja' => array( 'http://feeds.feedburner.com/Miunosoft-160xnTopRight-Jp' ),
    );    
    protected $arrURLFeed250xNTopRight = array(
        'en' => array( 'http://feeds.feedburner.com/Miunosoft-TopRightImages250Width' ),
        'ja' => array( 'http://feeds.feedburner.com/Miunosoft-TopRightImages250Width' ),
    );    
    protected $arrURLFeed160xN = array(
        'en' => array( 'http://feeds.feedburner.com/Miunosoft-160xnImageLinks' ),
        'ja' => array( 'http://feeds.feedburner.com/Miunosoft-160xnImageLinks-Jp' ),
    );    
    protected $arrURLFeed250xN = array(
        'en' => array( 'http://feeds.feedburner.com/Miunosoft-250xnImageLinks' ),
        'ja' => array( 'http://feeds.feedburner.com/Miunosoft-250xnImageLinks' ),
    );    
    protected $arrURLFeed160x600 = array(
        'en' => array(
            'http://feeds.feedburner.com/GANLinkBanner160x600Random40',
            'http://feeds.feedburner.com/RawBanner160x600',    
        ),
        'ja' => array( 'http://feeds.feedburner.com/Miunosoft-160x600ImageBanners-Jp' ),
    );
    protected $arrURLFeed468x60 = array(
        'en' => array(
            'http://feeds.feedburner.com/GANBanner60x468',
            'http://feeds.feedburner.com/RawBanner468x60'        
        ),
        'ja' => array( 'http://feeds.feedburner.com/Miunosoft-468x60ImageBanners-Jp' ),
    );    
    protected $arrURLFeed728x90 = array(
        'en' => array( 
            'http://feeds.feedburner.com/RawBanner728x90',
            'http://feeds.feedburner.com/CustomBanner728x90',
        ),
        'ja' => array( 'http://feeds.feedburner.com/Miunosoft-728x90ImageBanners-Jp' ),
    );    
    
    // Container arrays
    protected $arrFeedItems = array();    // stores fetched feed items.
    
    // Objects
    protected $oFeed;    // stores the feed object. 
    
    // Properties
    protected $strTransientPrefix = '';
        
    function __construct() {
        
        $this->strTransientPrefix = AmazonAutoLinks_Commons::TransientPrefix . 'ADS_';
        $strLangKey = defined( 'WPLANG' ) ? WPLANG : 'en';
        $this->arrURLFeedText = isset( $this->arrURLFeedText[ $strLangKey ] ) ? $this->arrURLFeedText[ $strLangKey ] : $this->arrURLFeedText['en'];
        $this->arrURLFeed160xNTopRight = isset( $this->arrURLFeed160xNTopRight[ $strLangKey ] ) ? $this->arrURLFeed160xNTopRight[ $strLangKey ] : $this->arrURLFeed160xNTopRight['en'];
        $this->arrURLFeed250xNTopRight = isset( $this->arrURLFeed250xNTopRight[ $strLangKey ] ) ? $this->arrURLFeed250xNTopRight[ $strLangKey ] : $this->arrURLFeed250xNTopRight['en'];
        $this->arrURLFeed160xN = isset( $this->arrURLFeed160xN[ $strLangKey ] ) ? $this->arrURLFeed160xN[ $strLangKey ] : $this->arrURLFeed160xN['en'];
        $this->arrURLFeed250xN = isset( $this->arrURLFeed250xN[ $strLangKey ] ) ? $this->arrURLFeed250xN[ $strLangKey ] : $this->arrURLFeed250xN['en'];
        $this->arrURLFeed160x600 = isset( $this->arrURLFeed160x600[ $strLangKey ] ) ? $this->arrURLFeed160x600[ $strLangKey ] : $this->arrURLFeed160x600['en'];
        $this->arrURLFeed468x60 = isset( $this->arrURLFeed468x60[ $strLangKey ] ) ? $this->arrURLFeed468x60[ $strLangKey ] : $this->arrURLFeed468x60['en'];
        $this->arrURLFeed728x90 = isset( $this->arrURLFeed728x90[ $strLangKey ] ) ? $this->arrURLFeed728x90[ $strLangKey ] : $this->arrURLFeed728x90['en'];

    }

    protected function fetchItems( $arrURLs, $numItems=1 ) {
        
        $strURLID = md5( serialize( is_string( $arrURLs ) ? array( $arrURLs ) : $arrURLs ) );
        
        if ( ! isset( $this->arrFeedItems[ $strURLID ] ) ) {
            $this->arrFeedItems[ $strURLID ] = ( array ) AmazonAutoLinks_WPUtilities::getTransient( $this->strTransientPrefix . $strURLID );
            $this->arrFeedItems[ $strURLID ] = array_filter( $this->arrFeedItems[ $strURLID ] );    // casting array causes the 0 key
        }
            
        // If it's out of stock, fill the array by fetching the feed.
        shuffle( $this->arrFeedItems[ $strURLID ] );
        $this->arrFeedItems[ $strURLID ] = array_unique( $this->arrFeedItems[ $strURLID ] );
        if ( count( $this->arrFeedItems[ $strURLID ] ) < $numItems ) {    
            
            $oReplace = new AmazonAutoLinks_HTMLElementReplacer( get_bloginfo( 'charset' ) );
            
            // When an array of urls is passed to the Simple Pie's set_feed_url() method, the memory usage increases largely.
            // So fetch the feeds one by one per url and store the output into an array.
            foreach( $arrURLs as $strURL ) {
                                
                $oFeed = $this->getFeedObj( $strURL, $numItems * 10 );    // multiplied by three to store items more than enough for next calls.
                foreach ( $oFeed->get_items() as $item )     // foreach ( $oFeed->get_items( 0, $numItems * 3 ) as $item ) does not change the memory usage
                    $this->arrFeedItems[ $strURLID ][] = $oReplace->Perform( $item->get_content() );
                
                // For PHP below 5.3 to release the memory.
                $oFeed->__destruct(); // Do what PHP should be doing on it's own.
                unset( $oFeed ); 
                
            }
            unset( $oReplace );
            
            // This life span should be little longer than the feed cache life span, which is 1700.
            AmazonAutoLinks_WPUtilities::setTransient( $this->strTransientPrefix . $strURLID, $this->arrFeedItems[ $strURLID ], 1800 );    // 30 minutes    
            
        }
        
        $this->arrFeedItems[ $strURLID ] = array_unique( $this->arrFeedItems[ $strURLID ] );
        shuffle( $this->arrFeedItems[ $strURLID ] );

        $strOut = '';
        for ( $i = 1; $i <= $numItems; $i++ ) 
            $strOut .= array_pop( $this->arrFeedItems[ $strURLID ] );        
        return $strOut;         
        
    }
    public function get160xNTopRight( $numItems=1 ) {        
        return '<div style="float: right; margin-left: 20px; margin-right: auto; width: 160px; clear:right;">' 
            . $this->fetchItems( $this->arrURLFeed160xNTopRight, $numItems )
            . "</div>";            
    }
    public function get250xNTopRight( $numItems=1 ) {        
        return '<div style="margin: 10px auto 20px auto; width: 250px; clear:right;">' 
            . $this->fetchItems( $this->arrURLFeed250xNTopRight, $numItems )
            . "</div>";            
    }
    public function get250xN( $numItems=2 ) {
        return '<div style="margin: 20px auto 20px auto; width: 250px; clear:right;">' 
            . $this->fetchItems( $this->arrURLFeed250xN, $numItems )
            . "</div>";                
    }
    public function get160xN( $numItems=10 ) {
        return '<div style="float: right; margin-left: 20px; margin-right: auto; width: 160px; clear:right;">' 
            . $this->fetchItems( $this->arrURLFeed160xN, $numItems )
            . "</div>";                    
    }    
    public function getBottomBanner( $numItems=1 ) {
                    
        return '<div style="margin: 20px 0 8px; width: 728px;">' 
            . $this->fetchItems( $this->arrURLFeed728x90, $numItems )
            . "</div>";
        
    }    
    public function getSkyscraper( $numItems=2 ) {
        
        return '<div style="float:right; padding: 0 0 0 20px; width: 160px;">' 
                . $this->fetchItems( $this->arrURLFeed160x600, $numItems )
            . "</div>";
        
    }        
    public function getTopBanner( $numItems=1 ) {
        
        return '<div style="float:right; margin:0; padding:0; width: 468px; height: 60px;">' 
                . $this->fetchItems( $this->arrURLFeed468x60, $numItems )
            . "</div>";
        
    }    
    public function getTextAd( $numItems=1 ) { 
    
        return '<div align="left" style="padding: 8px 20px 4px 0px;">' 
                . $this->fetchItems( $this->arrURLFeedText, $numItems )
            . "</div>"; 

    }
    
    function initializeFeeds() {

        // This is used to create transients to prevent delays in page load.
    
        $arrAllURLs = array_merge( 
            $this->arrURLFeedText,  
            // $this->arrURLFeed160x600,
            $this->arrURLFeed468x60,
            $this->arrURLFeed728x90,
            $this->arrURLFeed160xNTopRight,
            $this->arrURLFeed160xN
        );
        
        foreach( $arrAllURLs as $strURL ) {
            
            // Passing 0 to the third parameter renews the cache.
            $oTextFeed = $this->getFeedObj( $strURL, 1, 0 );    
            
            // For PHP below 5.3 to release the memory ( SimplePie specific method ).
            $oTextFeed->__destruct(); // Do what PHP should be doing on it's own.
            unset( $oTextFeed );             
            
        }    
    }

    function getFeedObj( $arrUrls, $numItem=1, $numCacheDuration=36000 ) {    // 60 seconds * 60 = 1 hour, 1800 = 30 minutes
        
        // Reuse the object that already exists. This conserves the memory usage.
        $this->oFeed = isset( $this->oFeed ) ? $this->oFeed : new AmazonAutoLinks_SimplePie();
        $oFeed = $this->oFeed; // $oFeed = new AmazonAutoLinks_SimplePie();
        
        // Set sort type.
        $oFeed->set_sortorder( 'random' );

        // Set urls
        $oFeed->set_feed_url( $arrUrls );    
        $oFeed->set_item_limit( $numItem );    
        
        // This should be set after defining $urls
        $oFeed->set_cache_duration( $numCacheDuration );    
        
        $oFeed->set_stupidly_fast( true );
        
        // If the cache lifetime is explicitly set to 0, do not trigger the background renewal cache event
        if ( $numCacheDuration == 0 )
            $oFeed->setBackground( true );    // setting it true will be considered the background process; thus, it won't trigger the renewal event.
        
        // set_stupidly_fast() disables this internally so turn it on manually because it will trigger the custom sort method
        $oFeed->enable_order_by_date( true );    
        $oFeed->init();            
        return $oFeed;
        
    }    
    
    function setupTransients() {
        
        $this->initializeFeeds();
        $this->getTopBanner();
        // $this->getSkyscraper();
        // $this->getBottomBanner();
        $this->getTextAd();
        $this->get160xNTopRight();
        $this->get250xNTopRight();
        $this->get160xN();
        $this->get250xN();
        
    }
}