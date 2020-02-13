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
 * A class that extracts product elements from Amazon best seller pages.
 * @since   3.8.12
 */
class AmazonAutoLinks_ScraperDOM_BestsellerProducts extends AmazonAutoLinks_ScraperDOM_BestsellerProducts_Base {

    protected $_aProduct = array(
        // mandatory
        'product_url'       => null,
        'ASIN'              => null,

        // optional
        'thumbnail_url'     => null,
        'title'             => null,
        'formatted_rating'  => null,    // (string) partial HTML string, [4.0.0+] changed the name from `rating` to distinguish the table column key name
        'rating_point'      => null,    // (integer)
        'review_count'      => null,    // (integer)
        'formatted_price'   => null,    // (string) HTML formatted price
        'is_prime'          => null,
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
            $_aProduct[ 'title' ]               = $this->___getProductTitle( $_oXPath, $_oItemNode );
            $_aProduct[ 'thumbnail_url' ]       = $this->___getProductThumbnail( $_oXPath, $_oItemNode );
            $_aProduct[ 'is_prime' ]            = $this->___hasPrimeSupport( $_oXPath, $_oItemNode );
            $_oNodeRating = $this->_getRatingNode( $_oXPath, $_oItemNode, $sSiteDomain, $sAssociateID );

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
         * @return  string
         */
        protected function _getPrice( DOMXPath $oXPath, $oItemNode ) {
            $_oNodes = $oXPath->query( './/span[contains(@class, "p13n-sc-price")]', $oItemNode );
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
         * @param $oItemNode
         */
        protected function _getReviewCount( DOMXPath $oXPath, $oRatingNode ) {
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
                   . AmazonAutoLinks_Unit_Utility::getRatingOutput( $iRatingPoint, $_sReviewLink, $iReviewCount )
                . "</div>";

            // @deprecated 3.10.0
//            $_sRatingRound = ( string ) ( round( ( ( integer ) $iRatingPoint ) * 2, -1 ) / 2 );
//            $_iFirstDigit  = $_sRatingRound[ 0 ];
//            $_iSecondDigit = isset( $_sRatingRound[ 1 ] ) ? $_sRatingRound[ 1 ] : 0;
//            $_sRatingStar  = $_iFirstDigit . "-" . $_iSecondDigit;
//            $_sStarImage   = "https://images-eu.ssl-images-amazon.com/images/G/08/x-locale/common/customer-reviews/ratings/stars-{$_sRatingStar}.gif";
//            return "<div class='amazon-customer-rating-stars'>"
//                    . "<div class='crIFrameNumCustReviews'>"
//                        . "<span class='crAvgStars' style='white-space:no-wrap;'>"
//                            . "<span class='asinReviewsSummary' name='{$sASIN}'>"
//                                . "<a href='" . esc_url( $_sReviewLink ) . "' target='_blank' rel='nofollow noopener'>"
//                                     . "<img src='" . esc_url( $_sStarImage ) . "'/>"
//                                . "</a>&nbsp;"
//                            . "</span>"
//                            . "("
//                               . "<a href='"  . esc_url( $_sReviewLink ) . "' target='_blank' rel='nofollow noopener'>"
//                                    . $iReviewCount
//                                . "</a>"
//                            . ")"
//                        . "</span>"
//                    . "</div>"
//                 . "</div>";
            // This output uses CSS images and only shows text
            // return $this->oDoc->saveXml( $oRatingNode, LIBXML_NOEMPTYTAG );
        }

        /**
         * @param DOMXPath $oXPath
         * @param $oItemNode
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
         * @return bool
         */
        private function ___hasPrimeSupport( DOMXPath $oXPath, $oItemNode ) {
            $_oIcons = $oXPath->query( './/i[contains(@class, "a-icon-prime")]', $oItemNode );
            return ( boolean ) $_oIcons->length;
        }
        /**
         * @param $oXPath
         * @param $oItemNode
         *
         * @return string
         */
        private function ___getProductThumbnail( DOMXPath $oXPath, $oItemNode ) {
            $_oSRCs = $oXPath->query( './/img/@src', $oItemNode );
            foreach( $_oSRCs as $_oAttribute ) {
                return $_oAttribute->nodeValue;
            }
            return '';
        }

        /**
         * Extracts the product title
         * @param $oXPath
         * @param $oItemNode
         *
         * @return string
         */
        private function ___getProductTitle( DOMXPath $oXPath, $oItemNode ) {
            // The class name `p13n-sc-truncated` is given by JavaScript; for plain HTML, `p13n-sc-truncate` is used.
            $_oTitleNodes = $oXPath->query( './/div[contains(@class, "p13n-sc-truncate")]', $oItemNode );
            foreach( $_oTitleNodes as $_oTitleNode ) {
                $_sTitleAttribute = $_oTitleNode->getAttribute( 'title' ); // for cases of being truncated
                return $_sTitleAttribute
                    ? $_sTitleAttribute
                    : trim( $_oTitleNode->nodeValue );
            }
            return '';
        }


}