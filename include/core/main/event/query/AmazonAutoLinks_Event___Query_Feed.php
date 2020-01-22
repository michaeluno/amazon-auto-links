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
 * Deals with feed outputs.
 * 
 * @package     Amazon Auto Links
 * @since       3.1.0
 * @since       3.5.0   Renamed from `AmazonAutoLinks_Event_Feed`.
 * 
 */
class AmazonAutoLinks_Event___Query_Feed extends AmazonAutoLinks_PluginUtility {
    
    /**
     * Sets up properties and hooks.
     * @since       3.0.1       
     */
    public function __construct( $sQueryKey ) {

        if ( 'feed' !== $_GET[ $sQueryKey ] ) {
            return;
        }
        switch( $this->getElement( $_GET, array( 'output' ), '' ) ) {
        
            case 'json':
                new AmazonAutoLinks_Event___Feed_JSON;
                break;
            default:
            case 'rss2':
                new AmazonAutoLinks_Event___Feed_RSS2;
                break;
            
        }
        
    }

}