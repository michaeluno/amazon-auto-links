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
 * Reads, loads and saves HTML documents.
 * 
 * It has a caching system built-in.
 * 
 * @since       3       
 */
class AmazonAutoLinks_RSSClient extends AmazonAutoLinks_PluginUtility {
    
    
    /**
     * Supported sort order slugs.
     * 'date_ascending'
     * 'date_decending' (default) 
     * 'title_ascending'
     * 'title_decending'
     * 'random' 
     */
    public $sSortOrder = 'date';
    
    /**
     * Sets up properties
     */
    public function __construct( $asURLs, $iCacheDuration=86400 ) {
        
        $this->aURLs = $this->getAsArray( $asURLs );
        $this->iCacheDuration = $iCacheDuration;
        
    }

    public function get() {
        
        $_oHTTP     = new AmazonAutoLinks_HTTPClient(
            $this->aURLs,
            $this->iCacheDuration,
            null, // arguments
            'rss'   // response type - this just leaves a mark in the database table.
        );  

        $_aItems = array();
        foreach( $_oHTTP->get() as $_sHTTPBody ) {
            $_aItems = array_merge(
                $_aItems,
                $this->_getRSSItems( $_sHTTPBody )
            );
        }
        
        $this->_sort( $_aItems );
        return $_aItems;
                            
    }
        protected function _sort( &$aItems ) {
            if ( 'random' === $this->sSortOrder ) {
                shuffle( $aItems );
                return;
            }
            $_sSortOrder = $this->sSortOrder;
            usort( 
                $aItems, 
                array( $this, 'replyToSortBy_'. $_sSortOrder ) 
            );
        }
            /**
             * 
             * @callback    function        usort
             */
            public function replyToSortBy_date_ascending( $aA, $aB ) {
                return strtotime( $aA[ 'pubDate' ] ) - strtotime( $aB[ 'pubDate' ] );
            }
            public function replyToSortBy_date( $aA, $aB ) {
                return strtotime( $aB[ 'pubDate' ] ) - strtotime( $aA[ 'pubDate' ] );
            } 
            public function replyToSortBy_date_descending( $aA, $aB ) {
                return strtotime( $aB[ 'pubDate' ] ) - strtotime( $aA[ 'pubDate' ] );
            }            
            public function replyToSortBy_title_ascending( $aA, $aB ) {
                
                $_sTitle_A = apply_filters(
                    'aal_filter_unit_product_raw_title', 
                    $aA[ 'title' ]
                );
                $_sTitle_B = apply_filters(
                    'aal_filter_unit_product_raw_title', 
                    $aB[ 'title' ]
                );
                return strnatcasecmp( $_sTitle_A, $_sTitle_B );
                
            }               
            public function replyToSortBy_title( $aA, $aB ) {
                $_sTitle_A = apply_filters(
                    'aal_filter_unit_product_raw_title', 
                    $aA[ 'title' ]
                );
                $_sTitle_B = apply_filters(
                    'aal_filter_unit_product_raw_title', 
                    $aB[ 'title' ]
                );
                return strnatcasecmp( $_sTitle_B, $_sTitle_A );                    
            }
            public function replyToSortBy_title_descending( $aA, $aB ) {

                $_sTitle_A = apply_filters(
                    'aal_filter_unit_product_raw_title', 
                    $aA[ 'title' ]
                );
                $_sTitle_B = apply_filters(
                    'aal_filter_unit_product_raw_title', 
                    $aB[ 'title' ]
                );
                return strnatcasecmp( $_sTitle_B, $_sTitle_A );    
            }               
   
        /**
         * 
         * @return      array
         */
        private function _getRSSItems( $_sHTTPBody ) {
   
            $_boXML = $this->getXMLObject( 
                $_sHTTPBody, 
                false // do not strip HTML/XML tags
            );            
            if ( false === $_boXML ) {
                return array();
            }
            $_oXML   = $_boXML;
            $_aXML   = $this->convertXMLtoArray( $_oXML );

            $_aItems = $this->getElement(
                $_aXML,
                array( 'channel', 'item' )
            );
            return $this->isAssociative( $_aItems )
                ? array( 0 => $_aItems )
                : $_aItems;
   
        }

}