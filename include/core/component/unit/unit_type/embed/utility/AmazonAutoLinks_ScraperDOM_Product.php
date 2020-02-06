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
 * A class that extracts product data from Amazon a product page.
 *
 * @since   4.0.0
 */
class AmazonAutoLinks_ScraperDOM_Product extends AmazonAutoLinks_ScraperDOM_Base {

    protected $_aProduct = array(

        // required
        'product_url'   => null,    // (string) gets set in the constructor
        'ASIN'          => null,

        // optional
        'thumbnail_url' => null,
        'title'         => null,
        'rating'        => null,    // (string) partial HTML string
        'rating_point'  => null,    // (integer)
        'review_count'  => null,    // (integer)
        'price'         => null,

    );

    /**
     * @var DOMXPath
     */
    protected $_oXPath;

    /**
     * @var AmazonAutoLinks_Unit_Utility
     */
    protected $_oUtil;

    /**
     * Sets up properties.
     *
     * @param string $sURL
     * @param string $sCharset
     */
    public function __construct( $sURL, $sCharset='' ) {

        parent::__construct( $sURL, $sCharset );

        $this->_oXPath       = new DOMXPath( $this->oDoc );
        $this->_oUtil        = new AmazonAutoLinks_Unit_Utility;
        $this->_aProduct[ 'product_url' ] = $sURL;

    }

    /**
     * @param string $sAssociateID
     * @param string $sSiteDomain       The store URL of the domain part with scheme without trailing slash. e.g. https://www.amazon.com
     *
     * @return array    Returns a single product array.
     */
    public function get( $sAssociateID, $sSiteDomain ) {

        $_aProduct  = array();

        $_oXPath     = $this->_oXPath;
        $_noItemNode = $this->___getItemNode( $_oXPath );
        if ( $_noItemNode ) {
            return $_aProduct;
        }
        $_oItemNode  = $_noItemNode;

        // Mandatory Elements
        $_aProduct[ 'ASIN' ]                = $this->_oUtil->getASINFromURL( $_aProduct[ 'product_url' ] );

        // Optional
        $_aProduct[ 'title' ]               = $this->___getProductTitle( $_oXPath, $_oItemNode );
        $_aImages                           = $this->___getProductImages( $_oXPath, $_oItemNode );
        $_aProduct[ 'thumbnail_url' ]       = $this->_oUtil->getElement( $_aImages, array( 0 ) );


        $_oNodeRating                       = $this->_getRatingNode( $_oXPath, $_oItemNode, $sSiteDomain, $sAssociateID );
        $_aProduct[ 'rating_point' ]        = $this->_getRatingPoint( $_oXPath, $_oNodeRating );
        $_aProduct[ 'review_count' ]        = $this->_getReviewCount( $_oXPath, $_oNodeRating );
        $_aProduct[ 'rating' ]              = $this->_getRatingHTML(
            $_oXPath,
            $_oNodeRating,
            $_aProduct[ 'rating_point' ],
            $_aProduct[ 'review_count' ],
            $_aProduct[ 'ASIN' ],
            $sSiteDomain,
            $sAssociateID
        );
        $_aProduct[ 'price' ]               = $this->_getPrice( $_oXPath, $_oItemNode );

        // Set an array
        $_aProducts[ $_aProduct[ 'ASIN' ] ] = $_aProduct + $this->_aProduct;

        return $_aProduct;

    }
        /**
         * @param DOMXPath $oXPath
         *
         * @return DOMNode|null
         */
        private function ___getItemNode( DOMXPath $oXPath ) {
            $_oContainerNodes = $oXPath->query( '//body' );
            if ( ! $_oContainerNodes->length ) {
                return null;
            }
            return $_oContainerNodes->item( 0 );
        }

        /**
         * @param $_sRelativeURL
         * @param $sSiteDomain
         * @param $sAssociateID
         */
        protected function _getURLResolved( $sURLMaybeRelative, $sSiteDomain, $sAssociateID ) {
            $_sURL = $this->hasPrefix( 'http', $sURLMaybeRelative )
                ? $sURLMaybeRelative
                : $sSiteDomain . $sURLMaybeRelative;
            return add_query_arg(
                array( 'tag' => $sAssociateID ),
                $_sURL
            );
        }

    /**
     *
     * @param DOMXPath $oXPath
     * @param DOMNode $oItemNode
     *
     * @return  string
     */
        protected function _getPrice( DOMXPath $oXPath, DOMNode $oItemNode ) {
            $_oNodes = $oXPath->query( './/span[@id="priceblock_ourprice"]', $oItemNode );
            foreach( $_oNodes as $_oNode ) {
                return "<span class='amazon-prices'>"
                        . "<span class='offered-price'>"
                            . trim( $_oNode->nodeValue )
                        . "</span>"
                    . "</span>";
            }
            return '';
        }

    /**
     * @param DOMXPath $oXPath
     * @param DOMNode $oRatingNode
     *
     * @return int|null
     */
        protected function _getReviewCount( DOMXPath $oXPath, DOMNode $oRatingNode ) {
            $_oNodes = $oXPath->query( './/a[not(contains(@title, "5"))]', $oRatingNode );
            foreach( $_oNodes as $_oNode ) {
                $_iCount = ( integer ) preg_replace( '/[^0-9]/', '', $_oNode->nodeValue );
                return $_iCount;
            }
            return null;
        }
        /**
         * @param DOMXPath $oXPath
         * @param $oItemNode
         */
        protected function _getRatingPoint( DOMXPath $oXPath, $oRatingNode ) {
            $_oNodes = $oXPath->query( './/a[contains(@title, "5")]', $oRatingNode );
            if ( ! $_oNodes->length ) {
                $_oNodes = $oXPath->query( './/i[contains(@class, "a-star")]/span', $oRatingNode );
            }
            foreach( $_oNodes as $_oNode ) {
                $_sRatingPoint = trim( $_oNode->nodeValue );
                preg_match( "/(\d+(.|,))+(\d)+/", $_sRatingPoint, $_aMatches );
                $_sRatingPoint = $this->getElement( $_aMatches, 0 );
                $_iRatingPoint = ( integer ) preg_replace( '/[^0-9]/', '', $_sRatingPoint );
                return $_iRatingPoint;
            }
            return null;
        }
        /**
         *
         * @return  string
         */
        protected function _getRatingHTML( DOMXPath $oXPath, $oRatingNode, $iRatingPoint, $iReviewCount, $sASIN, $sSiteDomain, $sAssociateID ) {
            if ( ! $iReviewCount || ! $iRatingPoint ) {
                return '';
            }
            $_sReviewLink  = $this->_getURLResolved( "/product-reviews/{$sASIN}", $sSiteDomain, $sAssociateID );
            return "<div class='amazon-customer-rating-stars'>"
                   . $this->_oUtil->getRatingOutput( $iRatingPoint, $_sReviewLink, $iReviewCount )
                . "</div>";

        }

    /**
     * @param DOMXPath $oXPath
     * @param $oItemNode
     *
     * @param $sSiteDomain
     * @param $sAssociateID
     *
     * @return mixed    void|DOMNode
     */
        protected function _getRatingNode( DOMXPath $oXPath, $oItemNode, $sSiteDomain, $sAssociateID ) {
            // * is used to match `div` or `span`
            $_oRatingNodes = $oXPath->query( './/*[./a/i[contains(@class, "a-icon-star")]]', $oItemNode );
            foreach( $_oRatingNodes as $_oRatingNode ) {
                // Convert the link
                $_oANodes = $oXPath->query( './/a', $_oRatingNode );
                foreach( $_oANodes as $_oANode ) {
                    $_sURLMaybeRelative = $_oANode->getAttribute( 'href' );
                    $_sURL              = $this->_getURLResolved( $_sURLMaybeRelative, $sSiteDomain, $sAssociateID );
                    $_oANode->setAttribute( 'href', $_sURL );
                    $_oANode->setAttribute( 'rel', 'nofollow noopener' );
                    $_oANode->setAttribute( 'target', '_blank' );
                }
                return $_oRatingNode;
            }
        }

        /**
         * @param $oXPath
         * @param $oItemNode
         *
         * @return array    holding image URLs including sub-images
         */
        private function ___getProductImages( DOMXPath $oXPath, DOMNode $oItemNode ) {

            $_oImageContainers  = $oXPath->query( './/div[@id="altImages"]', $oItemNode );
            $_noImageContainer  = $_oImageContainers->item( 0 );
            if ( null === $_noImageContainer ) {
                return '';
            }
            $_oSRCs             = $oXPath->query( './/img/@src', $_noImageContainer );
            $_aImages           = array();
            foreach( $_oSRCs as $_oAttribute ) {
                $_aImages[] = $_oAttribute->nodeValue;
            }
            return $_aImages;
        }

        /**
         * Extracts the product title
         * @param DOMXPath $oXPath
         * @param DOMNode $oItemNode
         *
         * @return string
         */
        private function ___getProductTitle( DOMXPath $oXPath, DOMNode $oItemNode ) {
            $_oTitleNodes = $oXPath->query( './/h1[@id="title"]', $oItemNode );
            foreach( $_oTitleNodes as $_oTitleNode ) {
                return trim( $_oTitleNode->nodeValue );
            }
            return '';
        }


}