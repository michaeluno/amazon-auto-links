<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2018 Michael Uno
 */

/**
 * Creates Amazon product links by urls.
 * 
 * @package         Amazon Auto Links
 */
class AmazonAutoLinks_UnitOutput_url extends AmazonAutoLinks_UnitOutput_item_lookup {
    
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
     * @since       3.2.0
     * @scope       protected       The 'url' unit type will extend this method.
     * @return      array
     */
    protected function _getResponses() {

        /**
         * Retrieve the HTML body from data base. It will fetch if the data does not exist.
         * Also updates the `_found_items` unit option.
         */
        $_aHTMLs = $this->_getHTMLBodies( $this->oUnitOption->get( 'urls' ) );       
        
        // Retrieve ASINs from the given documents. Supports plain text.
        $_aFoundASINs = $this->_getFoundItems( $_aHTMLs );
                
        // Update unit options.
        $this->_setUnitTypeSpecificUnitOptions( $_aFoundASINs );
                
        // If the id is set, save the found items so that the user can view what's found in the unit editing page.
        $_iPostID = $this->oUnitOption->get( 'id' );
        if ( $_iPostID ) {
            update_post_meta( 
                $_iPostID, 
                '_found_items', 
                empty( $_aFoundASINs )
                    ? __( 'Product not found.', 'amazon-auto-links' )
                    : implode( PHP_EOL, $_aFoundASINs )
            );
        }
        
        // Now do the API request and get responses.
        return parent::_getResponses();
        
    }  

        /**
         * Updated unit options.
         * @since       3.2.1
         */
        private function _setUnitTypeSpecificUnitOptions( $aFoundASINs ) {
                            
            // Set the found items to the `ItemId` argument.
            $this->oUnitOption->set( 
                $this->sSearchTermKey,  // ItemId
                implode( ',', $aFoundASINs )
            );
            
            // In v3.2.0, the `Operation` meta was missing and `ItemSearch` may be stored instead. So override it here.
            $this->oUnitOption->set( 'Operation', 'ItemLookup' );
            
            // Set allowed ASINs. This way items other than the queried ASINs will not be returned.
            $this->oUnitOption->set( '_allowed_ASINs', $aFoundASINs );
            
        }
        
        /**
         * 
         */
        private function _getHTMLBodies( $asURLs ) {
            $_aURLs = $this->getAsArray( $asURLs );
            $_oHTTP = new AmazonAutoLinks_HTTPClient( 
                $_aURLs,
                $this->oUnitOption->get( 'cache_duration' ),
                array(  // http arguments
                    'timeout'     => 20,
                    'redirection' => 20,
                ),
                'url_unit_type' // request type
            );
            
            $_aHTMLBodies = $_oHTTP->get();
            
            // Set a debug output
            $this->_setDebugInfoForHTMLBodies( $_aHTMLBodies );            
            
            return $_aHTMLBodies;
        }
            
            /**
             * Stores retrieved HTML bodies for debug outputs.
             * @since       3.2.2
             */
            private $_aHTMLs = array();
            /**
             * @since      3.2.2
             * @return     void
             */
            private function _setDebugInfoForHTMLBodies( $aHTMLs ) {
                if ( ! $this->oOption->isDebug() ) {
                    return;
                }
                $this->_aHTMLs = $aHTMLs;
                add_filter( 
                    'aal_filter_unit_output',
                    array( $this, '_replyToAddHTMLBodies' ),
                    20,  // priority
                    3    // 3 parameters
                );                
            }
                /**
                 * @since       3.2.2
                 * @return      string
                 * @callback    filter      aal_filter_unit_output
                 */
                public function _replyToAddHTMLBodies( $sProductHTML, $sASIN, $sLocale ) {
                    $_aHTMLs = $this->_aHTMLs;
                    $this->_aHTMLs = array();   // reset for next outputs.
                    return $sProductHTML
                        . '<pre class="debug" style="max-height: 300px; overflow-y: scroll; overflow-x: auto; padding: 0 1em; word-wrap: break-word; word-break: break-all; margin: 1em 0;">'
                            . '<h3>' 
                                . __( 'Debug Info', 'amazon-auto-links' ) 
                                . ' - ' . __( 'HTTP Bodies', 'amazon-auto-links' ) 
                            . '</h3>'
                            . AmazonAutoLinks_Debug::get( $_aHTMLs )      
                        . "</pre>";
                    
                }
        
        /**
         * Parses the given HTML content and returns found ASINs.
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
                    $_sASIN = $this->getASINFromURL( $_sURL );
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
            return empty( $_aURLs )
                ? $_aURLs
                : array_combine( $_aURLs, $_aURLs );
            
        }
  
   
    
}