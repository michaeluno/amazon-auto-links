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
     * @since       3.8.1       Added the `$aURLs` parameter to accept direct URLs to be passsed.
     * @scope       protected       The 'url' unit type will extend this method.
     * @return      array
     */
    protected function _getResponses( array $aURLs=array() ) {

        /**
         * Retrieve the HTML body from data base. It will fetch if the data does not exist.
         * Also updates the `_found_items` unit option.
         */
        $_aURLs  = array_merge( $aURLs, $this->getAsArray( $this->oUnitOption->get( 'urls' ) ) );
        $_aURLs  = $this->_getURLs( $_aURLs );
        $_aHTMLs = $this->_getHTMLBodies( $_aURLs );
        
        // Retrieve ASINs from the given documents. Supports plain text.
        $_aFoundASINs = $this->_getFoundItems( $_aHTMLs );
        $_bNoProducts = empty( $_aFoundASINs );

        // Update unit options.
        $this->___setUnitTypeSpecificUnitOptions( $_aFoundASINs );
                
        // If the id is set, save the found items so that the user can view what's found in the unit editing page.
        $_iPostID = $this->oUnitOption->get( 'id' );
        if ( $_iPostID ) {
            update_post_meta( 
                $_iPostID, 
                '_found_items', 
                $_bNoProducts
                    ? __( 'Product not found.', 'amazon-auto-links' )
                    : implode( PHP_EOL, $_aFoundASINs )
            );
        }

        // 3.8.13
        if ( $_bNoProducts ) {
            return array(
                'Error' =>
                    array(
                        'Message' => __( 'No products found.', 'amazon-auto-links' ),
                        'Code'    => 'Amazon Auto Links'
                    ),
            );
        }

        // Now do the API request and get responses.
        return parent::_getResponses( $_aURLs );
        
    }  

        /**
         * Updated unit options.
         * @since       3.2.1
         */
        private function ___setUnitTypeSpecificUnitOptions( $aFoundASINs ) {
                            
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
         * Returns the subject urls for this unit.
         * @scope   protected   the category2 unit output class extends this method to set own URLs.
         * @param   $asURLs
         * @since   3.8.1
         * @return  array
         */
        protected function _getURLs( $asURLs ) {
            return $this->getAsArray( $asURLs );
        }

        /**
         *
         * @since       unknown
         * @since       3.8.1   Changed the visibility scope to protected from private as category unit accesses this method.
         */
        protected function _getHTMLBodies( array $aURLs ) {
            $_oHTTP = new AmazonAutoLinks_HTTPClient( 
                $aURLs,
                $this->oUnitOption->get( 'cache_duration' ),
                array(  // http arguments
                    'timeout'     => 20,
                    'redirection' => 20,
                ),
                $this->sUnitType . '_unit_type' // request type
            );
            
            $_aHTMLBodies = $_oHTTP->get();
            
            // Set a debug output
            $this->___setDebugInfoForHTMLBodies( $_aHTMLBodies );
            
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
            private function ___setDebugInfoForHTMLBodies( $aHTMLs ) {
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
                 * @todo       Probably `remove_filter( 'aal_filter_unit_output )` has to be done in the callback.
                 */
                public function _replyToAddHTMLBodies( $sProductHTML, $sASIN, $sLocale ) {
                    remove_filter(
                        'aal_filter_unit_output',
                        array( $this, '_replyToAddHTMLBodies' ),
                        20  // priority
                    );
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
         * @since       3.8.1   Changed the visibility scope to protected from private as category unit accesses this method.
         * @return      array
         */
        protected function _getFoundItems( $aHTMLs ) {

            $_aURLs  = array();
            $_aTexts = array();
            $_oDOM   = new AmazonAutoLinks_DOM;
            foreach( $aHTMLs as $_sURL => $_sHTML ) {
            
                $_oDoc      = $_oDOM->loadDOMFromHTML( $_sHTML );
                $_oDOM->removeTags( $_oDoc, array( 'script', 'style', 'noscript' ) );
                
                // HTML documents, extract a tag href attribute value.
                $_aURLs     = $_aURLs + $this->___getLinksFromHTML( $_oDoc );
            
                // For plain text pages, sanitize HTML entities.
                $_sText     = $_oDOM->getTagOuterHTML( $_oDoc, 'body' );
                $_sText     = str_replace( 
                    array( '&#13;', '&#10;' ), // search
                    PHP_EOL, // replacement
                    $_sText // subject
                );
                $_aTexts[ $_sURL ] = $_sText;
                
            }
            
            $_aURLs = $_aURLs + $this->___getURLsFromText( implode( PHP_EOL, $_aTexts ) );
            $_aASINs = $this->___getASINsExtracted( $_aURLs );
            return $_aASINs;

        }
            /**
             * @return      array
             */
            private function ___getASINsExtracted( array $aURLs ) {
                
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
            private function ___getLinksFromHTML( $oDOM ) {
                
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
            private function ___getURLsFromText( $sText ) {

                $_aURLs = array();
                preg_match_all(
                    '#\bhttps?://[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/))#s',
                    $sText,
                    $_aURLs
                );
                $_aURLs = array_merge( $_aURLs[ 0 ], $_aURLs[ 1 ] );

                // Make it associative so that duplicate items will be removed.
                return empty( $_aURLs )
                    ? $_aURLs
                    : array_combine( $_aURLs, $_aURLs );

            }

}