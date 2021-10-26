<?php
/**
 * Auto Amazon Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
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
        // 'image_set'         => null,    // (string) sub-image set output // @deprecatd 4.7.0
        'title'             => null,
        'formatted_rating'  => null,    // (string) partial HTML string
        'rating_point'      => null,    // (integer)
        'review_count'      => null,    // (integer)
        'formatted_price'   => null,    // (string) HTML formatted price with currency e.g. <span class='proper-price'>$35.43</span>
        'feature'           => null,    // (string) a list of product features
        'description'       => null,    // (string) a product description
        'content'           => null,    // (string) a product content

        // internal
        '_features'         => array(), // (array) the product features
        '_sub_image_urls'   => array(), // (array) holding product image urls

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
     * @param string $sHTML
     * @param string $sURL
     * @param string $sCharset
     * @since 4.0.0
     * @since 4.3.4  Added the `$sHTML` parameter. As HTTP requests to Amazon sites requires cookies, the HTML document is retrieved outside the class.
     */
    public function __construct( $sHTML, $sURL, $sCharset='' ) {

        add_filter( 'aal_filter_http_request_arguments', array( $this, 'replyToGetHTTPRequestArguments' ), 10, 1 );
        parent::__construct( $sHTML, $sCharset );
        remove_filter( 'aal_filter_http_request_arguments', array( $this, 'replyToGetHTTPRequestArguments' ), 10 );

        $this->_oXPath       = new DOMXPath( $this->oDoc );
        $this->_oUtil        = new AmazonAutoLinks_Unit_Utility;
        $this->_aProduct[ 'product_url' ] = $sURL;

    }
        /**
         * Sets a longer timeout value.
         * The default is 5 (seconds) and it often times out
         * @param  array $aArguments
         * @return array
         * @since  4.0.0
         * @since  4.0.1 Added the `$sRequestType` parameter.
         * @since  4.3.1 Removed the `$sRequestType` parameter.
         */
        public function replyToGetHTTPRequestArguments( $aArguments ) {
            $aArguments[ 'timeout' ] = $aArguments[ 'timeout' ] > 15
                ? $aArguments[ 'timeout' ]
                : 15;
            return $aArguments;
        }

    /**
     * @param  string $sAssociateID
     * @param  string $sSiteDomain       The store URL of the domain part with scheme without trailing slash. e.g. https://www.amazon.com
     * @return array  Returns a single product array.
     * @since  4.0.0
     * @since  4.2.2  When failing to retrieve the product information, returns a structure array instead of an empty array.
     */
    public function get( $sAssociateID, $sSiteDomain ) {

        $_aProduct   = $this->_aProduct;
        $_oXPath     = $this->_oXPath;

        // Mandatory Elements
        $_aProduct[ 'ASIN' ]                = $this->_oUtil->getASINFromURL( $_aProduct[ 'product_url' ] );

        $_noItemNode = $this->oDoc->getElementsByTagName( 'body' )->item( 0 );
        if ( ! $_noItemNode ) {
            return $_aProduct;
        }
        $_oItemNode  = $_noItemNode;

        if ( $this->___isBlockedByCaptcha( $_oXPath, $_oItemNode ) ) {
            return $_aProduct;
        }

        $this->___removeUnnecessary( $_oXPath, $_oItemNode );

        // Optional
        $_aProduct[ 'title' ]               = $this->___getProductTitle( $_oXPath, $_oItemNode );
        $_aImages                           = $this->___getProductImages( $_oXPath, $_oItemNode, $_aProduct[ 'ASIN' ] );
        $_aProduct[ 'thumbnail_url' ]       = $this->___getThumbnailURLFormatted( array_shift($_aImages ) );  // extract the first array item
        $_aProduct[ '_sub_image_urls' ]     = $_aImages;
        $_aProduct[ '_features' ]           = $this->___getFeatures( $_oXPath, $_oItemNode );   // internal element which will be unset later on
        $_aProduct[ 'feature' ]             = $this->_oUtil->getFeatures( $_aProduct[ '_features' ] );
        $_aProduct[ 'feature' ]             = $_aProduct[ 'feature' ]
            ? $_aProduct[ 'feature' ]
            : null;
        $_aProduct[ 'description' ]         = $this->___getDescription( $_oXPath, $_oItemNode );
        $_aProduct[ 'content' ]             = $this->___getContent( $_oXPath, $_oItemNode );

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
         * @param DOMXPath $oXPath
         * @param DOMNode  $oItemNode
         * @since 4.4.6
         */
        private function ___removeUnnecessary( DOMXPath $oXPath, DOMNode $oItemNode ) {
            $_boDOMNodeList = $oXPath->query( './/*[@id="prsubswidget"]', $oItemNode );
            if ( false === $_boDOMNodeList ) {
                return;
            }
            foreach( $_boDOMNodeList as $_oDOMNode ) {
                $_oDOMNode->parentNode->removeChild( $_oDOMNode );
            }
        }

        /**
         * @param  string $sURLMaybeRelative
         * @param  string $sSiteDomain
         * @param  string $sAssociateID
         * @return string
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
                $_noNode = $oXPath->query( './/span[@id="kindle-price"]//text()', $oItemNode )->item( 0 );
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

            $_oNodeList = $oXPath->query( '//span[@id="acrCustomerReviewText"]', $oRatingNode );
            if ( ! $_oNodeList->length ) {
                $_oNodeList = $oXPath->query( './/a[not(contains(@title, "5"))]', $oRatingNode );
            }
            foreach( $_oNodeList as $_oNode ) {
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

            $_oNodes = $oXPath->query( './/i[contains(@class, "a-star")]/span', $oRatingNode );
            if ( ! $_oNodes->length ) {
                $_oNodes = $oXPath->query( './/a[contains(@title, "5")]', $oRatingNode );   // old Amazon site design
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
         * @param  DOMXPath $oXPath
         * @param  DOMNode  $oRatingNode
         * @param  integer  $iRatingPoint
         * @param  integer  $iReviewCount
         * @param  string   $sASIN
         * @param  string   $sSiteDomain
         * @param  string   $sAssociateID
         * @return string
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
         * @return mixed    null|DOMNode
         */
        protected function _getRatingNode( DOMXPath $oXPath, $oItemNode, $sSiteDomain, $sAssociateID ) {

            $_oRatingNodes = $oXPath->query( '//*[@id="averageCustomerReviews"]', $oItemNode );
            if ( ! $_oRatingNodes->length ) {
                $_oRatingNodes = $oXPath->query( './/*[./a/i[contains(@class, "a-icon-star")]]', $oItemNode ); // * is used to match `div` or `span`
            }
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
            return null;
        }

        /**
         * @param  DOMXPath $oXPath
         * @param  DOMNode $oItemNode
         * @return string|null
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
         * @param  DOMXPath $oXPath
         * @param  DOMNode $oItemNode
         * @return string|null
         * @since  4.4.6
         */
        private function ___getContent( DOMXPath $oXPath, DOMNode $oItemNode ) {
            $_onContents = $oXPath->query( './/div[@id="aplus"]/div', $oItemNode );
            if ( empty( $_onContents ) ) {
                return null;
            }
            $_oDOMNode = $_onContents->item( 0 );
            if ( null === $_oDOMNode ) {
                return null;
            }

            $this->___removeTags( $oXPath, array( 'style', 'script', 'noscript' ), $_oDOMNode );
            $this->___fixImgAttributes( $oXPath, $_oDOMNode );
            $this->___fixInlineStyles( $_oDOMNode );
            $this->___removeComments( $oXPath, $_oDOMNode );
            $_sContent = $this->oDoc->saveXml( $_oDOMNode, LIBXML_NOEMPTYTAG );
            $_sContent = trim( preg_replace( '/(?!>)(\s+[\r\n]+)(?=\s+<)/', PHP_EOL, $_sContent ) ); // sanitize extra line breaks
            return $_sContent
                ? $_sContent
                : null;
        }
            /**
             * @param DOMNode $oDOMNode
             * @since 4.4.6
             */
            private function ___fixInlineStyles( DOMNode $oDOMNode ) {
                $_oDOMNodeList = $oDOMNode->getElementsByTagName( 'div' );
                foreach( $_oDOMNodeList as $_oNode ) {
                    $_oNode->removeAttribute( 'style' );
                }
            }
            /**
             * @param DOMXPath $oXPath
             * @param DOMNode $oDOMNode
             * @since 4.4.6
             */
            private function ___fixImgAttributes( DOMXPath $oXPath, DOMNode $oDOMNode ) {
                $_oDOMNodeList = $oXPath->query('.//img', $oDOMNode );
                if ( false === $_oDOMNodeList ) {
                    return;
                }
                foreach ( $_oDOMNodeList as $_oImageNode ) {
                    $_sImageURL = $_oImageNode->getAttribute( 'data-src' );
                    if ( $_sImageURL ) {
                        $_oImageNode->setAttribute( 'src', $_sImageURL );
                    }
                }
            }
            /**
             * @param DOMXPath $oXPath
             * @param DOMNode $oDOMNode
             * @since 4.4.6
             */
            private function ___removeComments( DOMXPath $oXPath, DOMNode $oDOMNode ) {
                $_boDOMNodeList = $oXPath->query('.//comment()', $oDOMNode );
                if ( false === $_boDOMNodeList ) {
                    return;
                }
                foreach ( $_boDOMNodeList as $_oCommentNode ) {
                    $_oCommentNode->parentNode->removeChild( $_oCommentNode );
                }
            }
            /**
             * @remark Using XPath somehow did not find elements.
             * @param  DOMXPath $oXPath
             * @param  array    $aTags
             * @param  DOMNode  $oDOMNode
             * @since  4.4.6
             */
            private function ___removeTags( DOMXPath $oXPath, array $aTags, DOMNode $oDOMNode ) {
                foreach( $aTags as $_sTag ) {
                    /** @var DOMNodeList $_oDONNodeList */
                    $_oDONNodeList  = $oXPath->query( '//' . $_sTag , $oDOMNode );
                    foreach( $_oDONNodeList as $_oTagDOMNode ) {
                        /** @var DOMElement $_oTagDOMNode */
                        $_oTagDOMNode->parentNode->removeChild( $_oTagDOMNode );
                    }
                }
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
            if ( null === $_onFeatures || ! $_onFeatures->length ) {
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
         * @param  DOMXPath $oXPath
         * @param  DOMNode  $oItemNode
         * @param  string   $sASIN
         * @return string[] holding image URLs including sub-images
         */
        private function ___getProductImages( DOMXPath $oXPath, DOMNode $oItemNode, $sASIN ) {
            $_aImages           = array();
            $_oSRCs             = $oXPath->query( './/div[@id="altImages"]//li[contains(@class, "imageThumbnail")]//img/@src', $oItemNode );
            if ( ! $_oSRCs->length  ) {
                $_oSRCs = $oXPath->query( './/div[@id="imageBlock"]//img/@src', $oItemNode );
            }
            foreach( $_oSRCs as $_oAttribute ) {
                $_aImages[] = trim( $_oAttribute->nodeValue );
            }
            if ( empty( $_aImages ) ) {
                $_aImages[] = 'https://images-na.ssl-images-amazon.com/images/P/' . $sASIN . '.01.L.jpg';
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
                return $_sTitle ? $_sTitle : null;
            }
            return null;
        }

}