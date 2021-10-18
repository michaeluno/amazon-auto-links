<?php
/**
 * Amazon Auto Links
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 *
 */

/**
 * Fetches product data from outside source.
 *
 * @since 5.0.0
 */
class AmazonAutoLinks_Unit_URL_Event_Filter_ProductsFetcher extends AmazonAutoLinks_Unit_PAAPIItemLookUp_Event_Filter_ProductsFetcher {

    public $sUnitType = 'url';

    /**
     * @var AmazonAutoLinks_UnitOutput_url
     */
    public $oUnitOutput;

    /**
     * Performs API requests and get responses.
     *
     * First, sets up the unit options for the item look up API query.
     *
     * @since  3.2.0
     * @since  3.8.1       Added the `$aURLs` parameter to accept direct URLs to be passed.
     * @since  5.0.0       Removed the first parameter `$aURLs`. Moved from `AmazonAutoLinks_UnitOutput_url`.
     * @return array
     */
    protected function _getResponses() {

        /**
         * Retrieve the HTML body from the database table. It will fetch if the data does not exist.
         * Also updates the `_found_items` unit option.
         */
        $_aURLs  = $this->getAsArray( $this->oUnitOutput->oUnitOption->get( 'urls' ) );
        $_aHTMLs = $this->___getHTMLBodies( $_aURLs );

        // Retrieve ASINs from the given documents. Supports plain text.
        $_aFoundASINs = $this->oUnitOutput->getASINsFromHTMLs( $_aHTMLs );
        $_bNoProducts = empty( $_aFoundASINs );

        // Update unit options.
        $this->___setUnitTypeSpecificUnitOptions( $_aFoundASINs );

        // If the id is set, save the found items so that the user can view what's found in the unit editing page.
        $_iPostID = $this->oUnitOutput->oUnitOption->get( 'id' );
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
        $this->oUnitOutput->oUnitOption->set( 'search_per_keyword', false ); // [4.6.22+] This option for the url unit type is deprecated and it is always off.
        return parent::_getResponses();

    }

        /**
         * Updated unit options.
         * @since 3.2.1
         * @since 5.0.0 Moved from `AmazonAutoLinks_UnitOutput_url`
         */
        private function ___setUnitTypeSpecificUnitOptions( $aFoundASINs ) {

            // Set the found items to the `ItemId` argument.
            $this->oUnitOutput->oUnitOption->set(
                $this->sSearchTermKey,  // ItemId
                implode( ',', $aFoundASINs )
            );

            // In v3.2.0, the `Operation` meta was missing and `ItemSearch` may be stored instead. So override it here.
            $this->oUnitOutput->oUnitOption->set( 'Operation', 'ItemLookup' );

            // Set allowed ASINs. This way items other than the queried ASINs will not be returned.
            $this->oUnitOutput->oUnitOption->set( '_allowed_ASINs', $aFoundASINs );

        }

        /**
         *
         * @param  array $aURLs
         * @since  ?
         * @since  3.8.1 Changed the visibility scope to protected from private as category unit accesses this method.
         * @since  5.0.0 Moved from `AmazonAutoLinks_UnitOutput_url`. Changed the visibility scope to private as no other classes use it.
         * @return array
         */
        private function ___getHTMLBodies( array $aURLs ) {

            $_aHTMLBodies    = array();
            $_iCacheDuration = ( integer ) $this->oUnitOutput->oUnitOption->get( 'cache_duration' );
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