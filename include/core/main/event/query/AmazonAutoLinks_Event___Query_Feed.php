<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2017 Michael Uno
 */

/**
 * Deals with feed outputs.
 * 
 * @package     Amazon Auto Links
 * @since       3.1.0
 * @since       3.5.0   Renamed from `AmazonAutoLinks_Event_Feed`.
 * 
 */
class AmazonAutoLinks_Event___Query_Feed {
    
    /**
     * Sets up properties and hooks.
     * @since       3.0.1       
     */
    public function __construct() {
        
        $_sOutputType = isset( $_GET[ 'output' ] )
            ? $_GET[ 'output' ]
            : '';
        
        switch( $_sOutputType ) {            
        
            case 'json':
                add_filter( 'aal_filter_unit_output', array( $this, 'replyToRemoveCredit' ) );
                add_action( 'init', array( $this, 'replyToLoadJSONFeed' ), 999 );
            break;
            
            default:
            case 'rss2':
                add_action( 'init', array( $this, 'replyToLoadRSS2Feed' ), 999 );
            break;            
            
        }
        
    }
    
    /**
     * 
     * @since       3.1.0
     */
    public function replyToLoadJSONFeed() {
        
        $_aArguments = $_GET;
        $_aArguments[ 'template_path' ] = AmazonAutoLinks_Registry::$sDirPath . '/template/json/template.php';
        $_aArguments[ 'credit_link' ]   = false;
        header( 
            'Content-Type: application/json; charset=' . get_option( 'blog_charset' ),
            true
        );
        AmazonAutoLinks(
            $_aArguments,
            true    // echo or return
        );
        exit;
        
    }    
    /**
     * @since       3.1.0
     */
    public function replyToRemoveCredit( $sUnitOutput ) {
        return str_replace(
            AmazonAutoLinks_PluginUtility::getCommentCredit(),
            '',
            $sUnitOutput
        );
    }
    
    /**
     * 
     * @since       3.1.0
     */
    public function replyToLoadRSS2Feed() {
        
        $_aArguments = $_GET;
        $_aArguments[ 'template_path' ] = AmazonAutoLinks_Registry::$sDirPath . '/template/rss2/template.php';
        $_aArguments[ 'credit_link' ] = false;

        header( 
            'Content-Type: ' . feed_content_type('rss-http') . '; charset=' . get_option('blog_charset'),
            true 
        );

        AmazonAutoLinks(
            $_aArguments,
            true    // echo or return 
        );        
        exit;
    }

}