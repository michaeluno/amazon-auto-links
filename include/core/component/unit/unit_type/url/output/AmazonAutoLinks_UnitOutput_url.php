<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
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
     * @scope       protected   The 'url' unit type will extend this method.
     * @param       array       $aURLs
     * @return      array
     */
    protected function _getResponses( array $aURLs=array() ) {

        /**
         * Retrieve the HTML body from data base. It will fetch if the data does not exist.
         * Also updates the `_found_items` unit option.
         */
        $_aURLs  = array_merge( $aURLs, $this->getAsArray( $this->oUnitOption->get( 'urls' ) ) );
        $_aHTMLs = $this->_getHTMLBodies( $_aURLs );
        
        // Retrieve ASINs from the given documents. Supports plain text.
        $_aFoundASINs = $this->getASINsFromHTMLs( $_aHTMLs );
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
                        'Code'    => AmazonAutoLinks_Registry::NAME,
                    ),
            );
        }

        // Now do the API request and get responses.
        $this->oUnitOption->set( 'search_per_keyword', false ); // [4.6.22+] This option for the url unit type is deprecated and it is always off.
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
         *
         * @param       array   $aURLs
         * @since       unknown
         * @since       3.8.1   Changed the visibility scope to protected from private as category unit accesses this method.
         * @return      array
         */
        protected function _getHTMLBodies( array $aURLs ) {

            $_aHTMLBodies    = array();
            $_iCacheDuration = ( integer ) $this->oUnitOption->get( 'cache_duration' );
            foreach( $aURLs as $_sURL ) {
                $_oHTTP    = new AmazonAutoLinks_HTTPClient(
                    $_sURL,
                    $_iCacheDuration,
                    array(  // http arguments
                        'timeout'     => 20,
                        'redirection' => 20,
                    ),
                    $this->sUnitType . '_unit_type' // request type
                );
                $_aHTMLBodies[ $_sURL ] = $_oHTTP->getBody();
            }
            return $_aHTMLBodies;

        }

}