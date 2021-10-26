<?php
/**
 * Auto Amazon Links
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
class AmazonAutoLinks_Unit_Feed_Event_Filter_ProductsFetcher extends AmazonAutoLinks_Unit_UnitType_Common_Event_Filter_ProductsFetcher_Base {

    public $sUnitType = 'feed';

    /**
     * @param  array $aProducts
     * @return array
     * @since  5.0.0
     */
    protected function _getItemsFromSource( $aProducts ) {
        $_aProducts    = array();
        foreach( $this->getAsArray( $this->oUnitOutput->oUnitOption->get( 'feed_urls' ) ) as $_sFeedURL ) {
            if ( false === filter_var( $_sFeedURL, FILTER_VALIDATE_URL ) ){
                continue;
            }
            $_aProducts = $_aProducts + $this->___getProducts( $_sFeedURL );
        }
        return $_aProducts;
    }

        /**
         * @param  string $sFeedURL  The JSON feed URL to parse.
         * @return array
         * @since  4.0.0
         * @since  5.0.0  Moved from `AmazonAutoLinks_UnitOutput_feed`.
         */
        private function ___getProducts( $sFeedURL ) {
            $_oHTTPClient = new AmazonAutoLinks_HTTPClient(
                $sFeedURL,
                $this->oUnitOutput->oUnitOption->get( 'cache_duration' ),
                array(  // http arguments
                    'timeout'     => 20,
                    'redirection' => 20,
                ),
                $this->sUnitType . '_unit_type' // request type
            );
            $_sJSON = $_oHTTPClient->get();
            if ( ! $this->isJSON( $_sJSON ) ) {
                return array();
            }
            return json_decode( $_sJSON, true );
        }
}