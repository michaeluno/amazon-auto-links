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
 * A class that extracts product minimum elements from Amazon best seller pages.
 * @since   3.8.12
 */
class AmazonAutoLinks_ScraperDOM_BestsellerProducts_Minimal extends AmazonAutoLinks_ScraperDOM_BestsellerProducts {


    protected $_aProduct = array(
        // mandatory
        'product_url'       => null,
        'ASIN'              => null,

        // optional
        'formatted_rating'  => null,
        'formatted_price'   => null,

    );


    /**
     * @return array
     */
    public function get( $sAssociateID, $sSiteDomain ) {

        $_aProducts = array();
        $_oXPath    = $this->_oXPath;

        foreach( $this->_oItemNodes as $_oItemNode ) {

            $_aProduct = array();

            // Mandatory Elements
            $_sURLMaybeRelative                 = $this->_getProductLink( $_oXPath, $_oItemNode );
            $_aProduct[ 'ASIN' ]                = AmazonAutoLinks_Unit_Utility::getASINFromURL( $_sURLMaybeRelative );
            $_aProduct[ 'product_url' ]         = $this->_getURLResolved( $_sURLMaybeRelative, $sSiteDomain, $sAssociateID );

            // Optional
            $_oNodeRating                       = $this->_getRatingNode( $_oXPath, $_oItemNode, $sSiteDomain, $sAssociateID );
            $_aProduct[ 'rating_point' ]        = $this->_getRatingPoint( $_oXPath, $_oNodeRating );
            $_aProduct[ 'review_count' ]        = $this->_getReviewCount( $_oXPath, $_oNodeRating );
            $_aProduct[ 'formatted_rating' ]    = $this->_getRatingHTML(
                $_oXPath,
                $_oNodeRating,
                $_aProduct[ 'rating_point' ],
                $_aProduct[ 'review_count' ],
                $_aProduct[ 'ASIN' ],
                $sSiteDomain,
                $sAssociateID
            );
            $_aProduct[ 'formatted_price' ]               = $this->_getPrice( $_oXPath, $_oItemNode );

            // Set an array
            $_aProducts[ $_aProduct[ 'ASIN' ] ] = $_aProduct + $this->_aProduct;

        }

        return $_aProducts;

    }


}