<?php
/**
    Event handler.
    
 * @package     Amazon Auto Links
 * @copyright   Copyright (c) 2013, Michael Uno
 * @authorurl    http://michaeluno.jp
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since        2.0.0
 * @action        aal_action_setup_transients
 * @action        aal_action_simplepie_renew_cache
 * @action        aal_action_api_transient_renewal    
 * @filter        aal_filter_store_redirect_url - [2.0.5+] receives the redirecting url of the Amazon store
 */
abstract class AmazonAutoLinks_Event_ {

    public function __construct() {

        // For SimplePie cache renewal events 
        add_action( 'aal_action_simplepie_renew_cache', array( $this, '_replyToRenewSimplePieCaches' ) );
    
        // For API transient (cache) renewal events
        add_action( 'aal_action_api_transient_renewal', array( $this, '_replyToRenewAPITransients' ) );
        
        // This must be called after the above action hooks are added.
        if ( 'intense' == $GLOBALS['oAmazonAutoLinks_Option']->arrOptions['aal_settings']['cache']['chaching_mode'] ) {    
            new AmazonAutoLinks_Shadow(    // defined in the parent class.             
                array(
                    'aal_action_simplepie_renew_cache',
                    'aal_action_api_transient_renewal',
                ) 
            );    
        } else {
            if ( AmazonAutoLinks_Shadow::isBackground() ) {
                exit;
            }
        }
            
        // User ads redirects
        if ( isset( $_GET['amazon_auto_links_link'] ) && $_GET['amazon_auto_links_link'] ) {            
            $_oRedirect = new AmazonAutoLinks_Redirects;
            $_oRedirect->go( $_GET['amazon_auto_links_link'] );    // will exit there.
        }
        
        // Draw cached image.
        if ( isset( $_GET['amazon_auto_links_image'] ) && $_GET['amazon_auto_links_image'] && is_user_logged_in() ) {
            
            $_oImageLoader = new AmazonAutoLinks_ImageHandler( AmazonAutoLinks_Commons::TransientPrefix );
            $_oImageLoader->draw( $_GET['amazon_auto_links_image'] );
            exit;
            
        }            
        
        // For the activation hook
        add_action( 'aal_action_setup_transients', array( $this, '_replyToSetUpTransients' ) );
        
        // Load styles of templates
        if ( isset( $_GET['amazon_auto_links_style'] ) ) {
            $GLOBALS['oAmazonAutoLinks_Templates']->loadStyle( $_GET['amazon_auto_links_style'] );
        }
            
        // URL Cloak
        $_sQueryKey = $GLOBALS['oAmazonAutoLinks_Option']->arrOptions['aal_settings']['query']['cloak'];
        if ( isset( $_GET[ $_sQueryKey ] ) ) {
            $this->_redirect( $_GET[ $_sQueryKey ] );
        }

    }
        /**
         * @since       3.0.5
         */
        private function _redirect( $sQueryValue ) {
            
            if ( 'vendor' === $sQueryValue ) {
                exit( 
                    wp_redirect( AmazonAutoLinks_Commons::URI ) 
                );
            }
            
            $this->goToStore( $sQueryValue, $_GET );
            
            exit();
        }

    public function _replyToRenewAPITransients( $arrRequestInfo ) {

        $strLocale = $arrRequestInfo['locale'];
        $arrParams = $arrRequestInfo['parameters'];
        $oAmazonAPI = new AmazonAutoLinks_ProductAdvertisingAPI( 
            $strLocale, 
            $GLOBALS['oAmazonAutoLinks_Option']->getAccessPublicKey(), 
            $GLOBALS['oAmazonAutoLinks_Option']->getAccessPrivateKey()
        );
        $oAmazonAPI->request( $arrParams, $strLocale, null );    // passing null will fetch the data right away and sets the cache.
        
    }
    
    public function _replyToSetUpTransients() {
        
        $oUA = new AmazonAutoLinks_UserAds();
        $oUA->setupTransients();        
        
    }
    
    public function _replyToRenewSimplePieCaches( $vURLs ) {
        
        // Setup Caches
        $oFeed = new AmazonAutoLinks_SimplePie();

        // Set urls
        $oFeed->set_feed_url( $vURLs );    

        // this should be set after defining $vURLs
        $oFeed->set_cache_duration( 0 );    // 0 seconds, means renew the cache right away.
    
        // Set the background flag to True so that it won't trigger the event action recursively.
        $oFeed->setBackground( true );
        $oFeed->init();    
        
    }
    
    /**
     * 
     * For URL cloaking redirects.
     */
    protected function goToStore( $sASIN, $aArgs ) {
        
        $aArgs = $aArgs + array(
            'locale' => null,
            'tag'    => null,
            'ref'    => null,
        );
        
        // http://www.amazon.[domain-suffix]/dp/ASIN/[asin]/ref=[...]?tag=[associate-id]
        $_sURL = isset( AmazonAutoLinks_Properties::$arrCategoryRootURLs[ strtoupper( $aArgs['locale'] ) ] )
            ? AmazonAutoLinks_Properties::$arrCategoryRootURLs[ strtoupper( $aArgs['locale'] ) ]
            : AmazonAutoLinks_Properties::$arrCategoryRootURLs['US'];
        
        $_aURLelem  = parse_url( $_sURL );
        $_sStoreURL = $_aURLelem['scheme'] . '://' . $_aURLelem['host'] 
            . '/dp/ASIN/' . $sASIN . '/' 
            . ( empty( $aArgs['ref'] ) ? '' : 'ref=nosim' )
            . "?tag={$aArgs['tag']}";
        exit( 
            wp_redirect( 
                apply_filters( 'aal_filter_store_redirect_url', $_sStoreURL ) 
            ) 
        );
                
    }
    
    
}