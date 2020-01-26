<?php
/**
 * Amazon Auto Links
 * 
 * http://en.michaeluno.jp/amazn-auto-links/
 * Copyright (c) 2013-2020 Michael Uno
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
        
    public function __construct( $sURLOrFIlePathOrHTML, $sCharset='' ) {
        
        $this->bIsSSL        = is_ssl();
        $this->oDOM          = new AmazonAutoLinks_DOM;
        $_bsCharSetFrom = $sCharset
            ? $sCharset
            : false;
        
        // If a given value is a url,
        if ( filter_var( $sURLOrFIlePathOrHTML, FILTER_VALIDATE_URL ) ){ 
            $this->oDoc = $this->oDOM->loadDOMFromURL( 
                $sURLOrFIlePathOrHTML, 
                '',  // mb_lang
                false, // use file_get_contents()
                $_bsCharSetFrom // detect encoding
            );                    
        }        
        // Else if it is a file path, 
        // disable warnings here as in PHP 5.3 or above, PHP_MAXPATHLEN is defined and it may cause warnings
        else if ( @file_exists( $sURLOrFIlePathOrHTML ) ) {
            $this->oDoc = $this->oDOM->loadDOMFromHTMLElement(
                file_get_contents( $sURLOrFIlePathOrHTML ),
                '', // mb_lang
                $_bsCharSetFrom
            );            
        } 
        // Else, treat it as HTML contents.
        else {
            $this->oDoc = $this->oDOM->loadDOMFromHTMLElement(
                $sURLOrFIlePathOrHTML,
                '', // mb_lang
                $_bsCharSetFrom // detect encoding
            );
        }        

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