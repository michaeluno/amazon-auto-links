<?php
/**
 * Amazon Auto Links
 * 
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2015 Michael Uno
 * 
 */

/**
 * Plugin event handler.
 * 
 * @package      Amazon Auto Links
 * @since        2.0.0
 * @action       aal_action_simplepie_renew_cache
 * @action       aal_action_unit_prefetch
 * @action       aal_action_api_transient_renewal    
 * @action       aal_action_event_convert_template_options    
 * @filter       aal_filter_store_redirect_url - [2.0.5+] receives the redirecting url of the Amazon store
 */
class AmazonAutoLinks_Event {

    /**
     * Triggers event actions.
     */
    public function __construct() {

        new AmazonAutoLinks_Event_HTTPCacheRenewal;
        
        new AmazonAutoLinks_Event_Action_SimplePie_CacheRenewal(
            'aal_action_simplepie_renew_cache'  // action name
        );
        
        new AmazonAutoLinks_Event_Action_UnitPrefetch(
            'aal_action_unit_prefetch'
        );
        
        new AmazonAutoLinks_Event_Action_ProductAdvertisingAPICacheRenewal(
            'aal_action_api_transient_renewal'
        );
        
        new AmazonAutoLinks_Event_Action_API_SearchProduct(
            'aal_action_api_get_product_info'
        );
        
        new AmazonAutoLinks_Event_Action_CustomerReview(
            'aal_action_api_get_customer_review'
        );
        
        new AmazonAutoLinks_Event_Action_TemplateOptionConverter(
            'aal_action_event_convert_template_options',
            2   // number of arguments
        );
        
        // This must be called after the above action hooks are added.
        $_oOption               = AmazonAutoLinks_Option::getInstance();
        $_bIsIntenceCachingMode = 'intense' === $_oOption->get( 'cache', 'chaching_mode' );
        
        // Force executing actions.
        new AmazonAutoLinks_Shadow(    
            $_bIsIntenceCachingMode
                ? array(
                    'aal_action_unit_prefetch',
                    'aal_action_simplepie_renew_cache',
                    'aal_action_api_transient_renewal',
                    'aal_action_api_get_product_info',
                    'aal_action_api_get_customer_review',
                    'aal_action_http_cache_renewal',
                )
                : array(
                    'aal_action_unit_prefetch',
                    'aal_action_api_get_product_info',
                    'aal_action_api_get_customer_review',
                    'aal_action_http_cache_renewal',
                )
        );    
                
        if ( ! $_bIsIntenceCachingMode ) {
            if ( AmazonAutoLinks_Shadow::isBackground() ) {
                exit;
            }
        }
              
        new AmazonAutoLinks_Event_Redirect;

    }
    
}