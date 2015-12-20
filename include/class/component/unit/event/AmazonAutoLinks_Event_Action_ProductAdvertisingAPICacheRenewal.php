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
 * @action       aal_action_api_transient_renewal
 */
class AmazonAutoLinks_Event_Action_ProductAdvertisingAPICacheRenewal extends AmazonAutoLinks_Event_Action_Base {    
    
    /**
     * 
     * @callback        action        aal_action_api_transient_renewal
     */
    public function doAction( /* $aRequestInfo */ ) {
        
        $_oOption       = AmazonAutoLinks_Option::getInstance();
        if ( ! $_oOption->isAPIConnected() ) {
            return;
        }
        
        $_aParams       = func_get_args() + array( null );
        $aRequestInfo   = $_aParams[ 0 ];

        $_sLocale       = $aRequestInfo[ 'locale' ];
        $_aParameters   = $aRequestInfo[ 'parameters' ];
        $_oAmazonAPI    = new AmazonAutoLinks_ProductAdvertisingAPI(
            $_sLocale, 
            $_oOption->get( array( 'authentication_keys', 'access_key' ), '' ), 
            $_oOption->get( array( 'authentication_keys', 'access_key_secret' ), '' )
        );
        $_oAmazonAPI->request( 
            $_aParameters, 
            $_sLocale, 
            null // passing null will fetch the data right away and sets the cache.
        );    
        
    }
    
}