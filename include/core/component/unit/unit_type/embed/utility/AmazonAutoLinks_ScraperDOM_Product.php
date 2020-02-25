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

    /**
     * @remark  setting `null` will make the unit continue retrieving the value from the database. To prevent that, set an empty string when the value is not found.
     * @var array
     */
    protected $_aProduct = array(

        // required
        'product_url'       => '',    // (string) gets set in the constructor
        'ASIN'              => '',

        // optional
        'thumbnail_url'     => null,
        'image_set'         => null,    // (string) sub-image set output
        'title'             => '',
        'formatted_rating'  => null,    // (string) partial HTML string
        'rating_point'      => null,    // (integer)
        'review_count'      => null,    // (integer)
        'formatted_price'   => null,    // (string) HTML formatted price with currency e.g. <span class='proper-price'>$35.43</span>
        'feature'           => null,    // (string) a list of product features
        'description'       => null,    // (string) a product description

        // internal
        '_features'     => array(), // (array) the product features

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

        add_filter( 'aal_filter_http_request_arguments', array( $this, 'replyToGetHTTPRequestArguments' ), 10, 2 );
        parent::__construct( $sURL, $sCharset );
        remove_filter( 'aal_filter_http_request_arguments', array( $this, 'replyToGetHTTPRequestArguments' ), 10 );

        $this->_oXPath       = new DOMXPath( $this->oDoc );
        $this->_oUtil        = new AmazonAutoLinks_Unit_Utility;
        $this->_aProduct[ 'product_url' ] = $sURL;

    }
        /**
         * @param array $aArguments
         * @param string $sRequestType
         * @return array
         * @since   4.0.0
         * @since   4.0.1   Added The `$sRequestType` parameter`
         */
        public function replyToGetHTTPRequestArguments( $aArguments, $sRequestType ) {
            $aArguments[ 'timeout' ] = 15; // the default is 5 (seconds) and it often times out
            return $aArguments;
        }

    /**
     * @param string $sAssociateID
     * @param string $sSiteDomain       The store URL of the domain part with scheme without trailing slash. e.g. https://www.amazon.com
     *
     * @return array    Returns a single product array.
     */
    public function get( $sAssociateID, $sSiteDomain ) {

        $_aProduct   = $this->_aProduct;
        $_oXPath     = $this->_oXPath;

        $_noItemNode = $this->oDoc->getElementsByTagName( 'body' )->item( 0 );
        if ( ! $_noItemNode ) {
            return array();
        }
        $_oItemNode  = $_noItemNode;

        if ( $this->___isBlockedByCaptcha( $_oXPath, $_oItemNode ) ) {
            return array();
        }

        // Mandatory Elements
        $_aProduct[ 'ASIN' ]                = $this->_oUtil->getASINFromURL( $_aProduct[ 'product_url' ] );

        // Optional
        $_aProduct[ 'title' ]               = $this->___getProductTitle( $_oXPath, $_oItemNode );
        $_aImages                           = $this->___getProductImages( $_oXPath, $_oItemNode );
        $_aProduct[ 'thumbnail_url' ]       = $this->___getThumbnailURLFormatted( array_shift($_aImages ) );  // extract the first array item
        $_aProduct[ 'image_set' ]           = $this->_oUtil->getSubImageOutput( $_aImages, $_aProduct[ 'title' ], $_aProduct[ 'product_url' ] );
        if ( $_aProduct[ 'thumbnail_url' ] && ! $_aProduct[ 'image_set' ] ) {
            $_aProduct[ 'image_set' ] = "<div class='sub-images'></div>"; // change the value from null to an empty tag so that further data inspection will not continue
        }

        $_aProduct[ '_features' ]           = $this->___getFeatures( $_oXPath, $_oItemNode );   // internal element which will be unset later on
        $_aProduct[ 'feature' ]             = $this->_oUtil->getFeatures( $_aProduct[ '_features' ] );
        $_aProduct[ 'feature' ]             = $_aProduct[ 'feature' ]
            ? $_aProduct[ 'feature' ]
            : null;
        $_aProduct[ 'description' ]         = $this->___getDescription( $_oXPath, $_oItemNode );

        $_oNodeRating                       = $this->_getRatingNode( $_oXPath, $_oItemNode, $sSiteDomain, $sAssociateID );
        if ( $_oNodeRating ) {
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
        }
        $_aProduct[ 'formatted_price' ]               = $this->_getPrice( $_oXPath, $_oItemNode );
        return $_aProduct;

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
         * @param DOMXPath $oXPath
         * @param DOMNode $oItemNode
         *
         * @return bool
         */
        private function ___isBlockedByCaptcha( DOMXPath $oXPath, DOMNode $oItemNode ) {
            $_noNode = $oXPath->query( './/form[@action="/errors/validateCaptcha"]', $oItemNode )->item( 0 );
            return null !== $_noNode;
        }

        /**
         *
         * @param DOMXPath $oXPath
         * @param DOMNode $oItemNode
         *
         * @return  string|null
         */
        protected function _getPrice( DOMXPath $oXPath, DOMNode $oItemNode ) {
            $_noNode = $oXPath->query( './/span[@id="priceblock_ourprice"]/text()', $oItemNode )->item( 0 );
            if ( null === $_noNode ) {
                $_noNode = $oXPath->query( './/div[@id="buybox"]//span[contains(@class, "offer-price")]//text()', $oItemNode )->item( 0 );
            }
            if ( null === $_noNode ) {
                return null;
            }
            return "<span class='amazon-prices'>"
                    . "<span class='offered-price'>"
                        . trim( $_noNode->nodeValue )
                    . "</span>"
                . "</span>";

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
                return ( integer ) preg_replace( '/[^0-9]/', '', $_oNode->nodeValue );
            }
            return null;
        }

        /**
         * @param DOMXPath $oXPath
         * @param $oRatingNode
         *
         * @return int|null
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
                return ( integer ) preg_replace( '/[^0-9]/', '', $_sRatingPoint );
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
         * @param DOMXPath $oXPath
         * @param DOMNode $oItemNode
         * @return  string|null
         */
        private function ___getDescription( DOMXPath $oXPath, DOMNode $oItemNode ) {
            $_onDescriptions  = $oXPath->query( './/div[@id="productDescription"]//p', $oItemNode );
            if ( null === $_onDescriptions ) {
                return null;
            }
            $_sDescription = '';
            foreach( $_onDescriptions as $_oParagraph ) {
                $_sDescription .= '<p>' . trim( $_oParagraph->nodeValue ) . '</p>';
            }
            return $_sDescription
                ? $_sDescription
                : null;
        }

        /**
         * @param DOMXPath $oXPath
         * @param DOMNode $oItemNode
         *
         * @return array
         */
        private function ___getFeatures( DOMXPath $oXPath, DOMNode $oItemNode ) {
            $_aFeatures   = array();
            $_onFeatures  = $oXPath->query( './/div[@id="feature-bullets"]', $oItemNode );
            if ( null === $_onFeatures ) {
                return $_aFeatures;
            }
            $_oLis        = $oXPath->query( './/li', $_onFeatures->item( 0 ) );
            foreach( $_oLis as $_oLi ) {
                $_sLi = trim( $_oLi->nodeValue );
                $_sLi = preg_replace("/[\n\r]/","", $_sLi );
                $_sLi = preg_replace("/\s+/"," ", $_sLi );
                $_aFeatures[] = $_sLi;
            }
            return $_aFeatures;
        }

        /**
         * `CR..._` part in the URL needs to be removed.
         * e.g. https://images-na.ssl-images-amazon.com/images/I/41KBEESJReL._AC_SX150_SY150_CR,0,0,150,150_.jpg
         *  -> https://images-na.ssl-images-amazon.com/images/I/41KBEESJReL._AC_SX150_SY150_.jpg
         * @param   string  $sThumbnailSRC
         * @return  string|null
         */
        private function ___getThumbnailURLFormatted( $sThumbnailSRC ) {
            if ( is_null( $sThumbnailSRC ) ) {
                return null;
            }
            if ( substr( $sThumbnailSRC, 0, 11 ) === 'data:image/' ) {
                return $sThumbnailSRC;
            }
            $_sFileName = basename( $sThumbnailSRC );
            $_sURLWithoutFileName = str_replace( $_sFileName, '', $sThumbnailSRC );
            $_sFileName = preg_replace( '/_\KCR.+_/', '', $_sFileName );
            return $_sURLWithoutFileName . $_sFileName;
        }

        /**
         * @param $oXPath
         * @param $oItemNode
         *
         * @return array    holding image URLs including sub-images
         */
        private function ___getProductImages( DOMXPath $oXPath, DOMNode $oItemNode ) {
            $_aImages           = array();
            $_oSRCs             = $oXPath->query( './/div[@id="altImages"]//img/@src', $oItemNode );
            if ( ! $_oSRCs->length  ) {
                $_oSRCs = $oXPath->query( './/div[@id="imageBlock"]//img/@src', $oItemNode );
            }

            foreach( $_oSRCs as $_oAttribute ) {
                $_aImages[] = trim( $_oAttribute->nodeValue );
            }
            return $_aImages;
        }

        /**
         * Extracts the product title
         * @param DOMXPath $oXPath
         * @param DOMNode $oItemNode
         *
         * @return string|null
         */
        private function ___getProductTitle( DOMXPath $oXPath, DOMNode $oItemNode ) {
            $_oTitleNodes = $oXPath->query( './/h1[@id="title"]', $oItemNode );
            foreach( $_oTitleNodes as $_oTitleNode ) {
                $_sTitle = trim( $_oTitleNode->nodeValue );
                $_sTitle = preg_replace( '/\s+/', ' ', $_sTitle ); // remove doubled white spaces and line breaks
                return $_sTitle;
            }
            return null;
        }

}