<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2020 Michael Uno
 */

/**
 * Handles  SimplePie cache renewal events.
 * @package      Amazon Auto Links
 * @since        3
 * @since        3.5.0      Renamed from `AmazonAutoLinks_Event_Action_ProductAdvertisingAPICacheRenewal`.
 * @deprecated   3.9.0      No longer used as prefetch for searching products is now removed.
 */
class AmazonAutoLinks_Event___Action_ProductAdvertisingAPICacheRenewal extends AmazonAutoLinks_Event___Action_Base {

    protected $_sActionHookName = 'aal_action_api_transient_renewal';

    /**
     * @todo    Support the currency and the language.
     */
    protected function _doAction( /* $aRequestInfo */ ) {
        
        $_oOption        = AmazonAutoLinks_Option::getInstance();
        if ( ! $_oOption->isAPIConnected() ) {
            return;
        }
        
        $_aParams        = func_get_args() + array( null );
        $aRequestInfo    = $_aParams[ 0 ];

        $_sLocale        = $aRequestInfo[ 'locale' ];
        $_aParameters    = $aRequestInfo[ 'parameters' ];
        $_iCacheDuration = $aRequestInfo[ 'cache_duration' ];
        $_oAmazonAPI     = new AmazonAutoLinks_ProductAdvertisingAPI(
            $_sLocale, 
            $_oOption->get( array( 'authentication_keys', 'access_key' ), '' ), 
            $_oOption->get( array( 'authentication_keys', 'access_key_secret' ), '' )
        );
        $_oAmazonAPI->request( $_aParameters, $_iCacheDuration, true );
        
    }
    
}