<?php
/**
 * Amazon Auto Links
 * 
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2015 Michael Uno
 * 
 */

/**
 * Handles  SimplePie cache renewal events.
 * @package      Amazon Auto Links
 * @since        3
 * @action       aal_action_simplepie_renew_cache
 */
class AmazonAutoLinks_Event_Action_SimplePie_CacheRenewal extends AmazonAutoLinks_Event_Action_Base {
        
    /**
     * 
     * @callback        action        aal_action_simplepie_renew_cache
     */
    public function doAction( /* $asURLs */ ) {
        
        $_aParams = func_get_args() + array( null );
        $asURLs   = $_aParams[ 0 ];
        
        // Set up Caches
        $_oFeed = new AmazonAutoLinks_SimplePie();

        // Set urls
        $_oFeed->set_feed_url( $asURLs );    

        // this should be set after defining $asURLs
        // 0 seconds, means renew the cache right away.
        $_oFeed->set_cache_duration( 0 );    
    
        // Set the background flag to True so that it won't trigger the event action recursively.
        $_oFeed->setBackground( true );
        $_oFeed->init();    

        
    }   
    
}