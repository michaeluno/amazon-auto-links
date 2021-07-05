<?php
/**
 * Amazon Auto Links
 * 
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 * 
 */

/**
 * Provides abstract methods for scraping web contents using DOMDocument
 * 
 * @since        3
 */
abstract class AmazonAutoLinks_ScraperDOM_Base extends AmazonAutoLinks_PluginUtility {

    /**
     * Indicates whether the site uses https or not.
     * @var bool
     */
    public $bIsSSL;

    /**
     * @var AmazonAutoLinks_DOM
     */
    public $oDOM;

    /**
     * Stores a DOM document object.
     * @var DOMDocument
     */
    public $oDoc;    
        
    public function __construct( $sURLOrFilePathOrHTML, $sCharset='' ) {
        
        $this->bIsSSL        = is_ssl();
        $this->oDOM          = new AmazonAutoLinks_DOM;
        $_bsCharSetFrom = $sCharset
            ? $sCharset
            : false;
        
        // If a given value is a url,
        if ( filter_var( $sURLOrFilePathOrHTML, FILTER_VALIDATE_URL ) ){ 
            $this->oDoc = $this->oDOM->loadDOMFromURL( 
                $sURLOrFilePathOrHTML, 
                '',  // mb_lang
                false, // use file_get_contents()
                $_bsCharSetFrom // detect encoding
            );
            return;
        }        
        // Else if it is a file path, (disable warnings here as in PHP 5.3 or above, PHP_MAXPATHLEN is defined and it may cause warnings)
        if ( @file_exists( $sURLOrFilePathOrHTML ) ) {
            $sURLOrFilePathOrHTML = file_get_contents( $sURLOrFilePathOrHTML );
        } 
        // Else, treat it as HTML contents.
        $_sTestHTML = substr( $sURLOrFilePathOrHTML, 0, 20 );
        if ( false !== stripos( $_sTestHTML, '<html ' ) ) { // complete HTML document
            $this->oDoc = $this->oDOM->loadDOMFromHTML(
                $sURLOrFilePathOrHTML,
                '', // mb_lang
                $_bsCharSetFrom // detect encoding
            );
            return;
        }
        $this->oDoc = $this->oDOM->loadDOMFromHTMLElement(
            $sURLOrFilePathOrHTML,
            '', // mb_lang
            $_bsCharSetFrom // detect encoding
        );

    }

    /**
     * Converts the url scheme to https:// from http:// and uses the amazon's secure image server.
     *
     * @param DOMDocument $oDoc
     */
    protected function setSSLImagesByDOM( DOMDocument $oDoc ) {
        foreach ( $oDoc->getElementsByTagName( 'img' ) as $_oNodeImg ) {
            $_oNodeImg->attributes->getNamedItem( "src" )->value = $this->getAmazonSSLImageURL(
                $_oNodeImg->attributes->getNamedItem( "src" )->value
            );    
        }
    }    
    
} 