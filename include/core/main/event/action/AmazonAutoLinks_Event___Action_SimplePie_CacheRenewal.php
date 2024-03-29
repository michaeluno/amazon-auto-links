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
 * Handles  SimplePie cache renewal events.
 *
 * @since        3
 * @tood         Removed this class and related lines and files.
 * @deprecated   4.3.4
 */
class AmazonAutoLinks_Event___Action_SimplePie_CacheRenewal extends AmazonAutoLinks_Event___Action_Base {

    protected $_sActionHookName = 'aal_action_simplepie_renew_cache';

    /**
     *
     */
    protected function _doAction( /* $asURLs */ ) {
        
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