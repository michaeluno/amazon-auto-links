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
 * Generates Amazon product links for feed outputs.
 *
 * @since       4.0.0
 */
class AmazonAutoLinks_UnitOutput_feed extends AmazonAutoLinks_UnitOutput_category3 {

    /**
     * Stores the unit type.
     * @remark      Note that the base constructor will create a unit option object based on this value.
     */
    public $sUnitType = 'feed';

    /**
     * Lists the variables used in the Item Format unit option that require to access the custom database.
     * @remark  For this `feed` unit type, this must be empty not to trigger unnecessary database access.
     * @see AmazonAutoLinks_UnitOutput_Base::___hasCustomDBTableAccess()
     * @var array
     */
    protected $_aItemFormatDatabaseVariables = array();

    /**
     * Represents the structure of each product array.
     * @var array
     */
    public static $aStructure_Product = array();

    /**
     * Fetches product data and returns the associative array containing the output of product links.
     *
     * @param array $aURLs
     * @return            array            An array contains products.
     */
    public function fetch( $aURLs=array() ) {

        $_aProducts    = array();
        $_aFeedURLs    = $this->getAsArray( $this->oUnitOption->get( 'feed_urls' ) );
        $_aFeedURLs    = array_merge( $_aFeedURLs, $aURLs );
        foreach( $_aFeedURLs as $_sFeedURL ) {
            if ( false === filter_var( $_sFeedURL, FILTER_VALIDATE_URL ) ){
                continue;
            }
            $_aProducts = $_aProducts+ $this->___getProducts( $_sFeedURL );
        }

        return $this->_getProducts(
            $_aProducts,
            ( string ) $this->oUnitOption->get( 'country' ),
            ( string ) $this->oUnitOption->get( 'associate_id' ),
            ( integer ) $this->oUnitOption->get( 'count' )
        );

    }

        /**
         * @param string $sFeedURL  The JSON feed URL to parse.
         * @return array
         */
        private function ___getProducts( $sFeedURL ) {
            $_oHTTPClient = new AmazonAutoLinks_HTTPClient(
                $sFeedURL,
                $this->oUnitOption->get( 'cache_duration' ),
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