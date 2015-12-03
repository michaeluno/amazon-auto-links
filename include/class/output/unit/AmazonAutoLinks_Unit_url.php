<?php
/**
 * Amazon Auto Links
 * 
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2015 Michael Uno
 * 
 */

/**
 * Creates Amazon product links by urls.
 * 
 * @package         Amazon Auto Links
 */
class AmazonAutoLinks_Unit_url extends AmazonAutoLinks_Unit_item_lookup {
    
    /**
     * Stores the unit type.
     * @remark      Note that the base constructor will create a unit option object based on this value.
     */    
    public $sUnitType = 'url';
    
    /**
     * Performs API requests and get responses.
     * 
     * First, sets up the unit options for the item look up API query.
     * 
     * @since       3.1.4
     * @scope       protected       The 'url' unit type will extend this method.
     * @return      array
     */
    protected function _getResponses() {

        /**
         * Retrieve the HTML body from data base. It will fetch if the data does not exist.
         * Also updates the `_found_items` unit option.
         */
        $_aHTMLs = $this->_getHTMLBodies( $this->oUnitOption->get( 'urls' ) );       
        
        // Retrive ASINs from the given documents. Supports plain text.
        $_aFoundASINs = $this->_getFoundItems( $_aHTMLs );

        // Set the found items to the `ItemId` argument.
        $this->oUnitOption->set( 
            $this->sSearchTermKey,  // ItemId
            implode( ',', $_aFoundASINs )
        );
        
        // In v3.2.0, the Operation meta was missing and ItemSearch may be storead instead. So override it here.
        $this->oUnitOption->set( 
            'Operation',  // ItemId
            'ItemLookup'
        );
        
        // Set allowed ASINs. This way items other than the queried ASINs will not be returned.
        $this->oUnitOption->set( 
            '_allowed_ASINs', 
            $_aFoundASINs
        );        
        
        // If the id is set, save the found items so that the user can view what's found in the unit editing page.
        $_iPostID = $this->oUnitOption->get( 'id' );
        if ( $_iPostID ) {
            update_post_meta( $_iPostID, '_found_items', implode( PHP_EOL, $_aFoundASINs ) );
        }
        
        // Now do the API request and get responses.
        return parent::_getResponses();
        
    }  
        /**
         * 
         */
        private function _getHTMLBodies( $asURLs ) {
            $_aURLs = $this->getAsArray( $asURLs );
            $_oHTTP = new AmazonAutoLinks_HTTPClient( 
                $_aURLs,
                $this->oUnitOption->get( 'cache_duration' )
            );
            return $_oHTTP->get();                 
        }
        
        /**
         * Parses the given HTML content and returns the found ASINs.
         * @since       3.2.0
         * @return      array
         */
        private function _getFoundItems( $asHTMLs ) {

            $_aURLs  = array();
            $_aTexts = array();
            $_oDOM   = new AmazonAutoLinks_DOM;
            foreach( $asHTMLs as $_sURL => $_sHTML ) {   
            
                $_oDoc      = $_oDOM->loadDOMFromHTML( $_sHTML );
                $_oDOM->removeTags( $_oDoc, array( 'script', 'style', 'noscript' ) );
                
                // HTML documents, extract a tag href attribute value.
                $_aURLs     = $_aURLs + $this->_getLinksFromHTML( $_oDoc );
            
                // For plain text pages, sanitize HTML entities.
                $_sText     = $_oDOM->getTagOuterHTML( $_oDoc, 'body' );
                $_sText     = str_replace( 
                    array( '&#13;', '&#10;' ), // search
                    PHP_EOL, // replacement
                    $_sText // subject
                );
                $_aTexts[ $_sURL ] = $_sText;
                
            }
            
            $_aURLs = $_aURLs + $this->_getURLs( implode( PHP_EOL, $_aTexts ) );
            
            $_aASINs = $this->_getASINsExtracted( $_aURLs );
            return $_aASINs;

        }
            /**
             * @return      array
             */
            private function _getASINsExtracted( array $aURLs ) {
                
                $_aASINs = array();
                foreach( $aURLs as $_sURL ) {
                    $_sASIN = $this->getASIN( $_sURL );
                    if ( ! $_sASIN ) {
                        continue;
                    }
                    $_aASINs[ $_sASIN ] = $_sASIN;
                }
                return $_aASINs;
            }
            /**
             * 
             * @return      array
             */
            private function _getLinksFromHTML( $oDOM ) {
                
                $_aLinks = array();
                foreach( $oDOM->getElementsByTagName( 'a' ) as $nodeA ) {
                    $sHref = $nodeA->getAttribute( 'href' );                
                    $_aLinks[ $sHref ] = $sHref;
                }
                return $_aLinks;
                
            }        
            
        /**
         * Finds and returns urls from a given string.
         * @return      array    List of urls
         */
        private function _getURLs( $sText ) {
            
            $_aURLs = array();
            preg_match_all( 
                '#\bhttps?://[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/))#s',
                $sText,
                $_aURLs
            );
            $_aURLs = array_merge( $_aURLs[ 0 ], $_aURLs[ 1 ] );
            
            // Make it associative so that duplicate items will be lost.
            return array_combine( $_aURLs, $_aURLs );
            
        }
  
   
    
}