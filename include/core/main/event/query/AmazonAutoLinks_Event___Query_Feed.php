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
 * Deals with feed outputs.
 * 
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

        if ( 'feed' !== $_GET[ $sQueryKey ] ) {     // sanitization unnecessary as just checking
            return;
        }
        $_sOutputType = $this->getHTTPQueryGET( 'output', '' );
        switch( strtolower( $_sOutputType ) ) {
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