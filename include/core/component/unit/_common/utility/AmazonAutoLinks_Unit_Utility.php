<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 */

/**
 * A class that provides utility methods for the unit component.
 * @since   3.8.11
 */
class AmazonAutoLinks_Unit_Utility extends AmazonAutoLinks_PluginUtility {

    /**
     * @param array $aItem  A product array of a PA-API 5 response.
     * @return null|integer The star rating with two digits like 45 for 4.5.
     * @since 4.3.2
     */
    static public function getRatingFromItem( array $aItem ) {
        $_dnStarRating = self::getElement( $aItem, array( 'CustomerReviews', 'StarRating', 'Value' ) );
        return null === $_dnStarRating
            ? null
            : ( integer ) ( ( double ) $_dnStarRating * 10 );
    }

    /**
     * @param array $aItem  A product array of a PA-API 5 response.
     * @return null|integer The review count.
     * @since 4.3.2
     */
    static public function getReviewCountFromItem( array $aItem ) {
        $_aCustomerReview = self::getElementAsArray( $aItem, array( 'CustomerReviews' ) );
        $_inReviewCount   = self::getElement( $_aCustomerReview, array( 'Count' ) );
        if ( null === $_inReviewCount ) {
            return null;
        }
        return ( integer ) $_inReviewCount;
    }

    /**
     * Generates a rating star output.
     *
     * There seems to be cases that the response contains rating information.
     *
     * @see https://stackoverflow.com/a/64002035
     * @param array  $aItem  A product array of a PA-API 5 response.
     * @param string $sLocale
     * @param string $sAssociateID
     * @since 4.3.2
     * @since 4.3.4 Added the `$sAssociateID` parameter.
     * @return string|null
     */
    static public function getFormattedRatingFromItem( array $aItem, $sLocale, $sAssociateID ) {
        $_inReviewCount   = self::getReviewCountFromItem( $aItem );
        $_diStarRating    = self::getRatingFromItem( $aItem );
        if ( ! isset( $_inReviewCount, $_diStarRating ) ) {
            return null;
        }
        $_oLocale         = new AmazonAutoLinks_Locale( $sLocale );
        $_sReviewURL      = $_oLocale->getCustomerReviewURL( $aItem[ 'ASIN' ], $sAssociateID );
        return "<div class='amazon-customer-rating-stars'>"
            . self::getRatingOutput( $_diStarRating, $_sReviewURL, $_inReviewCount )
        . "</div>";
    }

    /**
     * Generates a thumbnail URL from a given ASIN.
     *
     *
     * ## Examples
     * ### HTTP
     * http://images-jp.amazon.com/images/P/{ASIN,ISBN}.{COUNTRY_NUMBER}.{SIZE}.jpg
     * http://images.amazon.com/images/P/{ASIN,ISBN}.{COUNTRY_NUMBER}._{DISCOUNT_RATE}_PI_SC{SIZE}_.jpg
     * http://images.amazon.com/images/P/{ASIN,ISBN}.{COUNTRY_NUMBER}._SCL_SX{PIXELSIZE}_.jpg
     *
     * ### HTTPS (SSL)
     * https://images-na.ssl-images-amazon.com/images/P/{ASIN,ISBN}.{COUNTRY_NUMBER}.{SIZE}.jpg
     *
     * #### COUNTRY_NUMBER (optional)
     * US: 01
     * UK:
     * JP: 09
     *
     * #### SIZE - Image Sizes
     * - THUMBZZZ	tiny	75x75	52x75
     * - TZZZZZZZ	small	110x110	77x110
     * - MZZZZZZZ	medium	160x160	112x160
     * - LZZZZZZZ	large	500x500	349x500
     *
     * ### DISCOUNT_RATE - discount mark
     * - PE{*}
     * e.g. `PE30` for 30% off
     *
     * ### Widget Method
     * @see https://stackoverflow.com/questions/58142293/fetching-amazon-product-image-by-asin
     *
     * @param   string  $sASIN
     * @param   string  $sLocale
     * @param   integer $iImageSize
     * @return  string
     * @since   4.2.2
     * @see https://www.oreilly.com/library/view/amazon-hacks/0596005423/ch01s07.html
     * @see https://www.ipentec.com/document/internet-get-amazon-product-image
     * @remark      This method is not reliable as the locale code is unknown.
     * @deprecated 4.2.2    Not used at the moment.
     */
    static public function getThumbnailURLFromASIN( $sASIN, $sLocale, $iImageSize ) {
        $_oLocale       = new AmazonAutoLinks_Locale( $sLocale );
        $_sLocaleNumber = $_oLocale->getLocaleNumber();
        return is_ssl()
            ? "https://images-na.ssl-images-amazon.com/images/P/{$sASIN}.{$_sLocaleNumber}._SCL_SX{$iImageSize}_.jpg"
            : "http://images.amazon.com/images/P/{$sASIN}.{$_sLocaleNumber}._SCL_SX{$iImageSize}_.jpg";
    }


    /**
     * Extracts ASIN from the given url.
     *
     * ASIN is a product ID consisting of 10 characters.
     *
     * example regex patterns:
     *         /http:\/\/(?:www\.|)amazon\.com\/(?:gp\/product|[^\/]+\/dp|dp)\/([^\/]+)/
     *         "http://www.amazon.com/([\\w-]+/)?(dp|gp/product)/(\\w+/)?(\\w{10})"
     *
     * @return      string      The found ASIN, or an empty string when not found.
     * @since       unknown
     * @since       3.5.0       Renamed from `getASIN()`
     * @since       3.5.0       Moved from `AmazonAutoLinks_UnitOutput_Base_ElementFormat`.
     * @since       3.8.12      Moved from `AmazonAutoLinks_UnitOutput_Utility`
     * @param       string      $sURL
     */
    static public function getASINFromURL( $sURL ) {

        $sURL = remove_query_arg(
            array( 'smid', 'pf_rd_p', 'pf_rd_s', 'pf_rd_t', 'pf_rd_i', 'pf_rd_m', 'pf_rd_r' ),
            $sURL
        );

        $sURL = preg_replace(
            array(
                '/[A-Z0-9]{11,}/',  // Remove strings like an ASIN but with more than 10 characters.
            ),
            '',
            $sURL
        );

        preg_match(
            '/(dp|gp|e)\/(.+\/)?([A-Z0-9]{10})\W/', // needle - [A-Z0-9]{10} is the ASIN
            $sURL,  // subject
            $_aMatches // match container
        );
        return isset( $_aMatches[ 3 ] )
            ? $_aMatches[ 3 ]
            : '';

    }


    /**
     * Generates price output.
     * @param string $sPriceFormatted
     * @param integer|null $inDiscounted
     * @param integer|null $inLowestNew
     * @param string $sDiscountedFormatted
     * @param string $sLowestNewFormatted
     * @return      string  The price output. If a discount is available, the discounted price is also returned along with the proper price.
     * @since       3.8.11
     */
    static public function getPrice( $sPriceFormatted, $inDiscounted, $inLowestNew, $sDiscountedFormatted, $sLowestNewFormatted ) {

        $_inOffered            = self::___getLowestPrice( $inLowestNew, $inDiscounted );
        $_sLowestFormatted     = $inDiscounted === $_inOffered
            ? $sDiscountedFormatted
            : $sLowestNewFormatted;
        $_bDiscounted          = $_sLowestFormatted && ( $sPriceFormatted !== $_sLowestFormatted );
        return $_bDiscounted
            ? '<span class="amazon-prices"><span class="proper-price"><s>' . $sPriceFormatted . '</s></span> '
                . '<span class="offered-price">' . $_sLowestFormatted . '</span></span>'
            : ( '' === $sPriceFormatted
                ? ''
                : '<span class="amazon-prices"><span class="offered-price">' . $sPriceFormatted . '</span></span>'
            );

    }
        /**
         * @param   integer $_iLowestNew
         * @param   integer $_iDiscount
         * @return  integer|null
         * @since   3.4.11
         * @since   3.5.0       Moved from `AmazonAutoLinks_UnitOutput_Base_ElementFormatter`.
         */
        static private function ___getLowestPrice( $_iLowestNew, $_iDiscount ) {
            $_aOfferedPrices        = array();
            if ( null !== $_iLowestNew ) {
                $_aOfferedPrices[] = ( integer ) $_iLowestNew;
            }
            if ( null !== $_iDiscount ) {
                $_aOfferedPrices[] = ( integer ) $_iDiscount;
            }
            return ! empty( $_aOfferedPrices )
                ? min( $_aOfferedPrices )
                : null;
        }

    /**
     * Extracts price information from a PA API response item element.
     * @param   array $aItem
     * @since   3.9.0
     * @return  array
     */
    static public function getPrices( array $aItem ) {
        
        // The actual displayed tag price. This can be a discount price or proper price.
        $_aBuyingPrice       = self::getElementAsArray(
            $aItem,
            array( 'Offers', 'Listings', 0, 'Price' )
        );
        $_sBuyingPrice       = self::getElement( $_aBuyingPrice, array( 'DisplayAmount' ), '' );
        $_sCurrency          = self::getElement( $_aBuyingPrice, array( 'Currency' ), '' );
        $_inBuyingPrice      = self::getElement( $_aBuyingPrice, array( 'Amount' ) );
        // Saved price, present if there is a discount
        $_sSavingPrice       = self::getElement(
            $aItem,
            array( 'Offers', 'Listings', 0, 'Price', 'Savings', 'DisplayAmount' ),
            ''
        );

        // There cases that `SavingBasis` is missing when there is no discount item.
        // @see https://webservices.amazon.com/paapi5/documentation/offers.html#savingbasis
        $_sProperPrice   = self::getElement(
            $aItem,
            array( 'Offers', 'Listings', 0, 'SavingBasis', 'DisplayAmount' ),
            $_sBuyingPrice
        );
        $_sDiscountedPrice   = $_sSavingPrice ? $_sBuyingPrice : '';
        $_inDiscountedPrice  = $_sSavingPrice ? $_inBuyingPrice : null;
        $_aSummaries = self::getElementAsArray( $aItem, array( 'Offers', 'Summaries' ) );
        $_aLowests   = self::___getLowestPrices( $_aSummaries );

        $_aPrices = array(
            'proper_price'       => $_sProperPrice  // 3.8.11 changed from `price`
                ? "<span class='amazon-product-price-value'>"  
                       . "<span class='proper-price'>" . $_sProperPrice . "</span>"
                    . "</span>"
                : "",
            'discounted_price'   => $_sDiscountedPrice
                ? "<span class='amazon-product-discounted-price-value'>" 
                        . $_sDiscountedPrice
                    . "</span>"
                : '',
            'lowest_new_price'     => isset( $_aLowests[ 'new_formatted' ] )
                ? "<span class='amazon-product-lowest-new-price-value'>"
                        . $_aLowests[ 'new_formatted' ]
                    . "</span>"
                : '',
            'lowest_used_price'  => isset( $_aLowests[ 'used_formatted' ] )
                ? "<span class='amazon-product-lowest-used-price-value'>"
                        . $_aLowests[ 'used_formatted' ]
                    . "</span>"
                : '',

            // For DB
            'currency'                      => $_sCurrency,
            'price_amount'                  => is_null( $_inBuyingPrice ) ? '' : $_inBuyingPrice * 100, // price
            'price_formatted'               => $_sBuyingPrice,  // price_formatted
            'lowest_new_price_amount'       => is_null( $_aLowests[ 'new_amount' ] ) ? '' : $_aLowests[ 'new_amount' ] * 100, // lowest_new_price
            'lowest_new_price_formatted'    => $_aLowests[ 'new_formatted' ], // lowest_new_price_formatted
            'lowest_used_price_amount'      => is_null( $_aLowests[ 'used_amount' ] ) ? '' : $_aLowests[ 'used_amount' ] * 100, // lowest_used_price
            'lowest_used_price_formatted'   => $_aLowests[ 'used_formatted' ], // lowest_used_price_formatted
            'discounted_price_amount'       => is_null( $_inDiscountedPrice ) ? '' : $_inDiscountedPrice * 100, // discounted_price
            'discounted_price_formatted'    => $_sDiscountedPrice, // discounted_price_formatted
        );

        $_aPrices[ 'formatted_price' ] = self::getPrice(
            $_sProperPrice,                 // string
            $_inDiscountedPrice,            // integer|null
            $_aLowests[ 'new_amount' ],     // integer|null
            $_sDiscountedPrice,             // string
            $_aLowests[ 'new_formatted' ]   // string
        );
        return $_aPrices;

    }
        /**
         * @param array $aOffers
         *
         * @return array
         * @since   3.9.0
         */
        static private function ___getLowestPrices( array $aOffers ) {
            $_aLowests = array(
                'new_amount'     => null, // integer
                'new_formatted'  => null, // string
                'used_amount'    => null, // integer
                'used_formatted' => null, // string
            );
            foreach( $aOffers as $_aOffer ) {
                $_sCondition = self::getElement( $_aOffer, array( 'Condition', 'Value' ) );
                $_dAmount    = self::getElement( $_aOffer, array( 'LowestPrice', 'Amount' ) );
                $_sFormatted = self::getElement( $_aOffer, array( 'LowestPrice', 'DisplayAmount' ) );
                if ( 'New' === $_sCondition ) {
                    if (
                        null === $_aLowests[ 'new_amount' ]
                        || $_aLowests[ 'new_amount' ] > $_dAmount
                    ) {
                        $_aLowests[ 'new_amount' ]    = $_dAmount;
                        $_aLowests[ 'new_formatted' ] = $_sFormatted;
                    }
                    continue;
                }
                if ( 'Used' === $_sCondition ) {
                    if (
                        null === $_aLowests[ 'used_amount' ]
                        || $_aLowests[ 'used_amount' ] > $_dAmount
                    ) {
                        $_aLowests[ 'used_amount' ]    = $_dAmount;
                        $_aLowests[ 'used_formatted' ] = $_sFormatted;
                    }
                    continue;
                }
            }
            return $_aLowests;
        }


    /**
     *
     * @param   array          $aOffer
     * @param   integer|double $nPrice
     * @param   string         $sPriceFormatted
     * @param   integer|double $nDiscountedPrice
     * @return  string
     * @since   3
     * @since   3.8.11         Renamed from `___getFormattedDiscountPrice()` and moved from `AmazonAutoLinks_Event___Action_APIRequestSearchProduct`.
     * @deprecated 4.3.4 Unused.
     */
/*    static public function getFormattedDiscountPrice( array $aOffer, $nPrice, $sPriceFormatted, $nDiscountedPrice ) {

        // If the formatted price is set in the Offer element, use it.
        $_sDiscountedPriceFormatted = self::getElement(
            $aOffer,
            array( 'Price', 'FormattedPrice' ),
            ''  // 3.8.5 Changed the value from `null` to an empty string to avoid automatic background product detail retrieval tasks
        );
        if ( '' !== $_sDiscountedPriceFormatted ) {
            return $_sDiscountedPriceFormatted;
        }

        // Otherwise, replace the price part of the listed price with the discounted one.
        return preg_replace(
            '/[\d.,]+/',
            $nDiscountedPrice / 100,   // decimal,  // numeric price
            $sPriceFormatted // price format
        );

    }*/

    /**
     * @param array     $aImageURLs     A numerically index array holding sub-image URLs.
     * @param string    $sTitle         The product title.
     * @param string    $sProductURL    The product URL.
     *
     * @return      string|null  An HTML portion of a set of sub-images.
     * @since       4.0.0
     */
    static public function getSubImageOutput( array $aImageURLs, $sTitle, $sProductURL ) {
        $_aSubImageTags = array();
        foreach( $aImageURLs as $_iIndex => $_sImageURL ) {
            $_sTitle    = trim( $sTitle ) . ' #' . ( $_iIndex + 1 );
            $_sImageTag = self::getHTMLTag(
                'img',
                array(
                    'src'   => self::hasPrefix( 'data:image/', $_sImageURL )
                        ? $_sImageURL
                        : esc_url( $_sImageURL ),
                    'class' => 'sub-image',
                    'alt'   => $_sTitle,
                )
            );
            $_sATag     = self::getHTMLTag(
                'a',
                array(
                    'href'   => esc_url( $sProductURL ),
                    'target' => '_blank',
                    'title'  => $_sTitle,
                ),
                $_sImageTag
            );
            $_aSubImageTags[] = self::getHTMLTag(
                'div',
                array(
                    'class' => 'sub-image-container',
                ),
                $_sATag
            );
        }
        return empty( $aImageURLs )
            ? null
            : "<div class='sub-images'>" . implode( '', $_aSubImageTags ) . "</div>";
    }

    /**
     * Extract the sub-image (image-set) information from PA API response and generates the output.
     *
     * @param  array   $aImages         The extracted image array from a PA-API response.
     * @param  string  $sProductURL
     * @param  string  $sTitle
     * @param  integer $iMaxImageSize    The maximum size of each sub-image.
     * @param  integer $iMaxNumberOfImages
     * @return string
     * @since  3       Originally defined in `AmazonAutoLinks_UnitOutput___ElementFormatter_ImageSet`.
     * @since  3.8.11  Renamed from `___getFormattedOutput()` and moved from `AmazonAutoLinks_UnitOutput___ElementFormatter_ImageSet`.
     */
    static public function getSubImages( array $aImages, $sProductURL, $sTitle, $iMaxImageSize, $iMaxNumberOfImages ) {

        if ( empty( $aImages ) ) {
            return '';
        }
        return self::getSubImageOutput(
            self::___getSubImageURLs( $aImages, $iMaxImageSize, $iMaxNumberOfImages ),  // extract image urls
            strip_tags( $sTitle ),
            $sProductURL
        );

    }

        /**
         * @return      array       An array holding image urls.
         * @param       array       $aImages
         * @param       integer     $iMaxImageSize
         * @param       integer     $iMaxNumberOfImages
         */
        static private function ___getSubImageURLs( array $aImages, $iMaxImageSize, $iMaxNumberOfImages ) {

            // If the size is set to 0, it means the user wants no image.
            if ( ! $iMaxImageSize ) {
                return array();
            }

            // The 'main' element is embedded by the plugin.
            unset( $aImages[ 'main' ] );

            $_aURLs  = array();
            foreach( $aImages as $_iIndex => $_aImage ) {

                // The user may set 0 to disable it.
                if ( ( integer ) $_iIndex >= $iMaxNumberOfImages ) {
                    break;
                }
                unset( $_aImage[ 'Large' ] );
                $_aURLs[] = self::___getImageURLFromResponseElement(
                    $_aImage,
                    $iMaxImageSize
                );

            }
            // Drop empty items.
            return array_filter( $_aURLs );

        }
            /**
             *
             * @remark Available key names
             * - Small
             * - Medium
             * - Large
             * @param  array $aImage
             * @param  integer $iImageSize
             * @return string
             */
            static private function ___getImageURLFromResponseElement( array $aImage, $iImageSize ) {

                $_sURL = '';
                foreach( $aImage as $_sKey => $_aDetails ) {
                    $_sURL = self::getElement(
                        $_aDetails, // subject array
                        array( 'URL' ), // dimensional key
                        ''  // default
                    );
                    if ( $_sURL ) {
                        break;
                    }
                }
                return self::getImageURLBySize( $_sURL, $iImageSize );

                // @deprecated 3.9.0 All Amazon images now start from https
//                return is_ssl()
//                    ? self::getAmazonSSLImageURL( $_sURL )
//                    : $_sURL;

            }

    /**
     * Returns the resized image url.
     *
     * ## Example
     * to adjust the image size to 350 pixels,
     * https://images-na.ssl-images-amazon.com/images/I/81pFmoL7GVL._AC_UL200_SR200,200_.jpg -> https://images-na.ssl-images-amazon.com/images/I/81pFmoL7GVL._AC_UL350_SR350,350_.jpg
     * https://m.media-amazon.com/images/I/31SSaevqVNL._SL160_.jpg -> https://m.media-amazon.com/images/I/31SSaevqVNL._SL350_.jpg
     *
     * @return      string
     * @param       $sImgURL        string
     * @param       $iImageSize     integer     0 to 500.
     * @since       3
     * @since       3.5.0       Moved from `AmazonAutoLinks_UnitOutput_Base_ElementFormat`.
     * @since       3.8.11      Moved from `AmazonAutoLinks_UnitOutput_Utility`.
     * @since       3.9.4       Supported more complex image URLs.
     */
    static public function getImageURLBySize( $sImgURL, $iImageSize ) {

        preg_match( "/.+\/(.+)$/i", $sImgURL, $_aMatches );
        $_sAfterLastSlash = self::getElement( $_aMatches, array( 1 ), '' );
        $_sDigitsReplaced = preg_replace(
            '/(?<=[\w,])(\d{1,3})(?=[,_])/i',
            '${2}'. $iImageSize,
            $_sAfterLastSlash
        );
        return str_replace( $_sAfterLastSlash, $_sDigitsReplaced, $sImgURL );

    }

    /**
     * Extracts image set (including the main thumbnail image and sub-images) from the product array.
     * @return      array
     * @since       3.8.11      Moved from `AmazonAutoLinks_Event___Action_APIRequestSearchProduct`.
     * @param       array       $aItem
     */
    static public function getImageSet( array $aItem ) {

        $_aMainImage = array(
            'MediumImage' => self::getElement( $aItem, array( 'Images', 'Primary', 'Medium', 'URL' ), '' ),
            'LargeImage'  => self::getElement( $aItem, array( 'Images', 'Primary', 'Large', 'URL' ), '' ),
        );
        // Will be numerically indexed array holding sub-image each.
        $_aSubImages = self::getElementAsArray( $aItem, array( 'Images', 'Variants' ) );

        // @deprecated 3.9.0
        // Sub-images can be only single. In that case, put it in a numeric element.
//        if ( ! isset( $_aSubImages[ 0 ] ) ) {
//            $_aSubImages = array( $_aSubImages );
//        }

        return array( 'main' => $_aMainImage, ) + $_aSubImages;
        
    }


    /**
     * Constructs the category output from an array of nested browse nodes.
     * @since       3.8.0
     * @since       3.8.11      Moved from `AmazonAutoLinks_UnitOutput_Utility`
     * @return      string
     * @param       array       $aBrowseNodes
     */
    static public function getCategories( array $aBrowseNodes ) {
        $_sList = '';
        foreach( self::___getBrowseNodes( $aBrowseNodes ) as $_sBrowseNode ) {
            $_sList .= "<li class='category'>" . $_sBrowseNode . "</li>";
        }
        return "<ul class='categories'>" . $_sList . "</ul>";
    }
        /**
         * @param array $aBrowseNodes
         *
         * @return array
         * @sicne   3.8.0
         * @since   3.8.11      Moved from `AmazonAutoLinks_UnitOutput_Utility`
         */
        static private function ___getBrowseNodes( array $aBrowseNodes ) {

            $_aList = array();
            if ( empty( $aBrowseNodes ) ) {
                return $_aList;
            }
            foreach( $aBrowseNodes as $_aBrowseNode ) {
                if ( is_scalar( $_aBrowseNode ) ) {
                    $_aList[] = $_aBrowseNode;
                    continue;
                }
                $_aList[] = self::___getNodeBreadcrumb( $_aBrowseNode, '' );
            }
            return $_aList;
        }
            /**
             * @param  array  $aBrowseNode
             * @param  string $sBreadcrumb
             * @param  string $sDelimiter
             * @return string
             * @since  3.8.0
             * @since  3.8.11      Moved from `AmazonAutoLinks_UnitOutput_Utility`
             */
            static private function ___getNodeBreadcrumb( array $aBrowseNode, $sBreadcrumb, $sDelimiter=' > ' ) {

                // There are cases that the `Name` does not exist.
                $_sName       = self::getElement( $aBrowseNode, 'DisplayName' );
                if ( ! $_sName ) {
                    return $sBreadcrumb;
                }

                $sBreadcrumb = $sBreadcrumb
                    ? $_sName . $sDelimiter . $sBreadcrumb
                    : $_sName;

                $_aAncestor = self::getElementAsArray( $aBrowseNode, array( 'Ancestor' ) );
                if ( ! empty( $_aAncestor ) ) {
                   $sBreadcrumb = self::___getNodeBreadcrumb( $_aAncestor, $sBreadcrumb );
                }
                return $sBreadcrumb;

            }

    /**
     * Constructs the features list output from an array storing features.
     * @since       3.8.0
     * @since       3.8.11      Moved from `AmazonAutoLinks_UnitOutput_Utility`
     * @param       array $aFeatures
     * @return      string
     */
    static public function getFeatures( array $aFeatures ) {
        $_sList = "";
        foreach( $aFeatures as $_sFeature ) {
            if ( ! trim( $_sFeature ) ) {
                continue;
            }
            $_sList .= "<li class='feature'>$_sFeature</li>";
        }
        return $_sList
            ? "<ul class='features'>" . $_sList . "</ul>"
            : '';
    }

    /**
     * @param array $aItem
     *
     * @return string
     * @since   3.9.0
     */
    static public function getContent( array $aItem ) {
        $_aFeatures = self::getElementAsArray( $aItem, array( 'ItemInfo', 'Features', 'DisplayValues' ) );
        $_sContents = implode( ' ', $_aFeatures );
        return trim( $_sContents )
            ? "<div class='amazon-product-content'>"
                . $_sContents
            . "</div>"
            : '';
    }

    /**
     * @param array $aItem
     *
     * @return bool
     * @since   3.9.0
     */
    static public function isPrime( array $aItem ) {
        return self::isDeliveryEligible( $aItem, array( 'DeliveryInfo', 'IsPrimeEligible' ) );
//        $_bHasPrime     = false;
//        $_aOfferListing = self::getElementAsArray( $aItem, array( 'Offers', 'Listings' ) );
//        foreach( $_aOfferListing as $_aOffer ) {
//            if ( self::getElement( $_aOffer, array( 'DeliveryInfo', 'IsPrimeEligible' ), false ) ) {
//                return true;
//            }
//        }
//        return $_bHasPrime;
    }

    /**
     * @param array $aItem
     * @param array $aKeys
     *
     * @return bool
     * @since   3.10.0
     */
    static public function isDeliveryEligible( array $aItem, array $aKeys=array( 'DeliveryInfo', 'IsPrimeEligible' ) ) {
        $_bHasPrime     = false;
        $_aOfferListing = self::getElementAsArray( $aItem, array( 'Offers', 'Listings' ) );
        foreach( $_aOfferListing as $_aOffer ) {
            if ( self::getElement( $_aOffer, $aKeys, false ) ) {
                return true;
            }
        }
        return $_bHasPrime;
    }
    /**
     * Extracts a rating from a given string.
     * e.g.
     * 4.5 out of 5 stars -> 4.5 -> 45
     * 4,7 su 5 stelle -> 4.7 -> 47
     *
     * @param $sString
     *
     * @return int|null
     * @since   3.9.0
     */
    static public function getRatingExtracted( $sString ) {
        preg_match(
            '/\d[.,]\d/', // needle
            $sString,   // subject
            $_aMatches
        );
        if ( ! isset( $_aMatches[ 0 ] ) ) {
            return null;
        }
        $_sRating = str_replace( ',', '.', $_aMatches[ 0 ] );
        return is_numeric( $_sRating )
            ? ( integer ) ( $_sRating * 10 )
            : null;
    }

    /**
     * e.g. https://images-na.ssl-images-amazon.com/images/G/01/x-locale/common/customer-reviews/stars-5-0.gif
     * @since   3.9.0
     * @param   integer     $iRating    IMPORTANT! Not a decimal number.  e.g. 45 (not 4.5)
     * @return  string
     */
    static public function getRatingStarImageURL( $iRating ) {

        // Only .5 numbers can be displayed.
        // e.g. 42 -> no image -> must be converted to 4.5
        $_iRating = $iRating * 2 ;
        $_iRating = round( $_iRating, -1 );
        $_iRating = $_iRating / 2 / 10;
        $_dRating = number_format( $_iRating, 1, '.', '');
        $_sRating = str_replace( '.', '-', $_dRating );

        // https://images-na.ssl-images-amazon.com/images/G/01/x-locale/common/customer-reviews/stars-5-0.gif
        return 'https://images-na.ssl-images-amazon.com/images/G/01/x-locale/common/customer-reviews/stars-'
            . $_sRating . '.gif';

    }

    /**
     * @param   integer $iRating
     * @param   string $sReviewURL
     * @param   integer $iReviewCount
     * @since   3.9.0
     * @return  string
     */
    static public function getRatingOutput( $iRating, $sReviewURL='', $iReviewCount=null ) {
        $_sStarImageURL = self::getRatingStarImageURL( $iRating );
        $_sAAttributes  = self::getAttributes(
            array(
                'href'     => $sReviewURL,
                'target'   => '_blank',
                'rel'      => 'nofollow noopener',
            )
        );
        $_dRating   = number_format( $iRating / 10, 1, '.', '');
        $_sAlt      = sprintf( __( '%1$s out of 5 stars', 'amazon-auto-links' ), $_dRating );
        $_sImgAttributes = self::getAttributes(
            array(
                'src'    => esc_url( $_sStarImageURL ),
                'alt'    => $_sAlt,
                'title'  => $_sAlt,
            )
        );
        $_sReviewCount = null !== $iReviewCount
            ? ( $sReviewURL
                    ? "(<a " . $_sAAttributes . ">" .$iReviewCount . "</a>)"
                    : "(" . $iReviewCount . ")"
                )
            : '';
        $_sIconRatingStars = self::___getRatingStarsSVG( $_sAlt, $iRating, $_sStarImageURL );
        return "<div class='crIFrameNumCustReviews'>"
                    . "<span class='crAvgStars'>"
                        . "<span class='review-stars'>"
                            . ( $sReviewURL
                                ? "<a " . $_sAAttributes . ">"
                                    . $_sIconRatingStars
                                . "</a>"
                                : $_sIconRatingStars
                            )
                        . "</span>"
                        . "<span class='review-count'>"
                            . $_sReviewCount
                        . "</span>"
                    . "</span>"
               . "</div>";
        return "<div class='crIFrameNumCustReviews'>"
                   . "<span class='crAvgStars'>"
                       . "<span class='review-stars' style='display:inline-block;'>"
                            . ( $sReviewURL
                                ? "<a " . $_sAAttributes . ">"
                                    . "<img " . $_sImgAttributes . " alt='" . __( 'Rating stars', 'amazon-auto-links' ) . "'/>"
                                . "</a>"
                                : "<img " . $_sImgAttributes . "/>"
                            )
                       . "</span>"
                       . "<span class='review-count'>"
                            . $_sReviewCount
                       . "</span>"
                   . "</span>"
               . "</div>";
    }

        /**
         * @param  integer $iRating Two digits representing the rating such as 50 for 5 stars, 35 for 3.5 stars.
         * @return string
         * @since  4.6.0   Uses the SVG image instead of the one from the amazon site.
         */
        static private function ___getRatingStarsSVG( $sTitle, $iRating, $sFallbackIMGSRC ) {

            static $_aDefinitions   = array();
            $_sSVGDefinition        = '';
            $_sDefsGradient          = '';

            if ( ! isset( $_aDefinitions[ 'main' ] ) ) {
                $_sSVGDefinition = "<svg xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' width=0 height=0 viewBox='0 0 160 32' display='none'>"
                    . "<g id='amazon-rating-stars'>"
                        . "<path stroke='#E17B21' stroke-width='2' d='M 16.025391 0.58203125 L 11.546875 10.900391 L 0 12.099609 L 8.8222656 19.849609 L 6.1269531 31.382812 L 16.021484 25.294922 L 25.914062 31.382812 L 23.265625 19.849609 L 32 12.099609 L 20.388672 10.900391 L 16.025391 0.58203125 z M 32 12.099609 L 40.822266 19.849609 L 38.126953 31.382812 L 48.021484 25.294922 L 57.914062 31.382812 L 55.265625 19.849609 L 64 12.099609 L 52.388672 10.900391 L 48.025391 0.58203125 L 43.546875 10.900391 L 32 12.099609 z M 64 12.099609 L 72.822266 19.849609 L 70.126953 31.382812 L 80.021484 25.294922 L 89.914062 31.382812 L 87.265625 19.849609 L 96 12.099609 L 84.388672 10.900391 L 80.025391 0.58203125 L 75.546875 10.900391 L 64 12.099609 z M 96 12.099609 L 104.82227 19.849609 L 102.12695 31.382812 L 112.02148 25.294922 L 121.91406 31.382812 L 119.26562 19.849609 L 128 12.099609 L 116.38867 10.900391 L 112.02539 0.58203125 L 107.54688 10.900391 L 96 12.099609 z M 128 12.099609 L 136.82227 19.849609 L 134.12695 31.382812 L 144.02148 25.294922 L 153.91406 31.382812 L 151.26562 19.849609 L 160 12.099609 L 148.38867 10.900391 L 144.02539 0.58203125 L 139.54688 10.900391 L 128 12.099609 z' />"    // the actual vector images of 5 stars
                    . "</g>"
                . "</svg>";
                $_aDefinitions[ 'main' ] = true;
            }
            $_sIDGradient = 'star-fill-gradient-' . $iRating;
            if ( ! isset( $_aDefinitions[ $_sIDGradient ] ) ) {
                $_sDefsGradient = "<defs>"
                        . "<linearGradient id='{$_sIDGradient}'>"
                            . "<stop offset='" . ( $iRating * 2 ) . "%' stop-color='#FFA41C'/>"
                            . "<stop offset='" . ( $iRating * 2 ) . "%' stop-color='transparent' stop-opacity='1' />"
                        . "</linearGradient>"
                    . "</defs>";
                $_aDefinitions[ $_sIDGradient ] = true;
            }
            $_sSVG = "<svg xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' viewBox='0 0 160 32' enable-background='new 0 0 160 32'>"
                    . "<title>"
                        . esc_html( $sTitle )
                    . "</title>"
                    . $_sDefsGradient
                    . "<use xlink:href='#amazon-rating-stars' fill='url(#{$_sIDGradient})' />"
                    . "<image src='" . esc_url( $sFallbackIMGSRC ) . "' />" // fallback for browsers not supporting SVG
                . "</svg>";
            return $_sSVGDefinition . $_sSVG;
            return "<svg xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' viewBox='0 0 160 32' enable-background='new 0 0 160 32'>"
                    . "<title>"
                        . esc_html( $sTitle )
                    . "</title>"
                    . "<defs>"
                        . "<linearGradient id='star-fill-gradient-{$iRating}'>"
                            . "<stop offset='" . ( $iRating * 2 ) . "%' stop-color='#FFA41C'/>"
                            . "<stop offset='" . ( $iRating * 2 ) . "%' stop-color='transparent' stop-opacity='1' />"
                        . "</linearGradient>"
                    . "</defs>"
                    . "<g fill='url(#star-fill-gradient-{$iRating})'>"
                        . "<path stroke='#E17B21' stroke-width='2' d='M 16.025391 0.58203125 L 11.546875 10.900391 L 0 12.099609 L 8.8222656 19.849609 L 6.1269531 31.382812 L 16.021484 25.294922 L 25.914062 31.382812 L 23.265625 19.849609 L 32 12.099609 L 20.388672 10.900391 L 16.025391 0.58203125 z M 32 12.099609 L 40.822266 19.849609 L 38.126953 31.382812 L 48.021484 25.294922 L 57.914062 31.382812 L 55.265625 19.849609 L 64 12.099609 L 52.388672 10.900391 L 48.025391 0.58203125 L 43.546875 10.900391 L 32 12.099609 z M 64 12.099609 L 72.822266 19.849609 L 70.126953 31.382812 L 80.021484 25.294922 L 89.914062 31.382812 L 87.265625 19.849609 L 96 12.099609 L 84.388672 10.900391 L 80.025391 0.58203125 L 75.546875 10.900391 L 64 12.099609 z M 96 12.099609 L 104.82227 19.849609 L 102.12695 31.382812 L 112.02148 25.294922 L 121.91406 31.382812 L 119.26562 19.849609 L 128 12.099609 L 116.38867 10.900391 L 112.02539 0.58203125 L 107.54688 10.900391 L 96 12.099609 z M 128 12.099609 L 136.82227 19.849609 L 134.12695 31.382812 L 144.02148 25.294922 L 153.91406 31.382812 L 151.26562 19.849609 L 160 12.099609 L 148.38867 10.900391 L 144.02539 0.58203125 L 139.54688 10.900391 L 128 12.099609 z' />"    // the actual vector images of 5 stars
                    . "</g>"
                    . "<image src='" . esc_url( $sFallbackIMGSRC ) . "' />" // fallback for browsers not supporting SVG
                . "</svg>";
        }

    /**
     * @param array $aProduct
     *
     * @return string
     * @since   3.9.0
     * @since   3.10.0  Moved from `AmazonAutoLinks_UnitOutput_search`.
     */
    static public function getPrimeMark( array $aProduct ) {
        if ( ! self::getElement( $aProduct, 'is_prime' ) ) {
            return '';
        }
        $_sPrimeImageURL = AmazonAutoLinks_Registry::getPluginURL( AmazonAutoLinks_UnitLoader::$sDirPath . '/asset/image/unit/prime.gif', true );
        $_aAttributes    = array(
            'role'  => 'img',
            'class' => 'prime-icon',
            'style' => 'background-image: url(' . esc_url( $_sPrimeImageURL ) . ');'
        );

        static $_bSVGShown = false;
        if ( $_bSVGShown ) {
            return "<span class='amazon-prime'>"
                    . "<svg height='20px' viewBox='0 0 64 20' version='1.1' xmlns='http://www.w3.org/2000/svg' xmlns:svg='http://www.w3.org/2000/svg' preserveAspectRatio='xMinYMin meet'>"
                        . "<use xlink:href='#svg-prime-icon' />"
                        // . "<image src='" . esc_url( $_sPrimeImageURL ) . "' />" // fallback for browsers not supporting SVG
                    . "</svg>"
                . "</span>";
        }
        $_bSVGShown = true;
        return "<span class='amazon-prime'>"
            . "<svg height='20px' viewBox='0 0 64 20' version='1.1' xmlns='http://www.w3.org/2000/svg' xmlns:svg='http://www.w3.org/2000/svg' preserveAspectRatio='xMinYMin meet'>"    // xMinYMin meet to align left
                . "<g id='svg-prime-icon' transform='translate(-1.4046423,-0.11313718)'>"
                    . "<g fill='#fd9a18' transform='matrix(1.1343643,0,0,0.86753814,-0.1955262,0.39080443)'>"
                        . "<path d='M 2.4895689,12.174275 Q 2.3072772,11.982868 2.2070168,11.84615 2.115871,11.709431 2.115871,11.581827 q 0,-0.164063 0.1549479,-0.328125 0.1640625,-0.164063 0.4101563,-0.291667 0.2552083,-0.127604 0.5468749,-0.200521 0.3007813,-0.08203 0.5742188,-0.08203 0.2916666,0 0.4739583,0.08203 0.1914062,0.07292 0.3645833,0.255209 0.7018229,0.738281 1.266927,1.51302 0.5742188,0.765625 1.1210938,1.585938 Q 7.5572771,12.70292 8.1223812,11.39042 8.6874854,10.068806 9.3163916,8.8109935 9.9544124,7.5440664 10.674464,6.3227123 q 0.729167,-1.2304687 1.576823,-2.470052 0.309896,-0.4648437 0.911459,-0.6835937 0.610677,-0.21875 1.558593,-0.21875 0.282552,0 0.455729,0.082031 0.173178,0.082031 0.173178,0.21875 0,0.1184896 -0.05469,0.2369792 -0.05469,0.1184896 -0.164062,0.2734375 -2.069011,2.8893228 -3.764323,6.0520831 -1.6953127,3.1627606 -2.9440105,6.6718746 -0.082031,0.255209 -0.4010417,0.382813 -0.3190104,0.127604 -0.938802,0.127604 -0.2916667,0 -0.4921875,-0.01823 Q 6.3906104,16.959431 6.2538917,16.904743 6.1171729,16.85917 6.0260271,16.786254 5.9348813,16.713337 5.8710792,16.603962 5.4791521,15.965941 5.1054542,15.419066 4.7408709,14.863077 4.3398293,14.343545 3.9479022,13.824014 3.492173,13.295368 3.0455585,12.766722 2.4895689,12.174275 Z' />"
                    . "</g>"
                    . "<g fill='#2492c1'>"
                        . "<path d='m 21.362997,12.694364 h -0.03385 v 4.99348 H 18.654669 V 5.034874 h 2.674474 v 1.3033828 h 0.03385 q 0.990233,-1.514971 2.7845,-1.514971 1.684242,0 2.598302,1.159503 0.922525,1.1510394 0.922525,3.1399678 0,2.1666624 -1.074868,3.4785094 -1.066404,1.311846 -2.843745,1.311846 -1.565752,0 -2.386714,-1.218748 z m -0.07617,-3.5546803 v 0.6940091 q 0,0.8971332 0.473957,1.4641902 0.473957,0.567056 1.244138,0.567056 0.914061,0 1.413409,-0.702473 0.507811,-0.710936 0.507811,-2.0058553 0,-2.2851518 -1.77734,-2.2851518 -0.820962,0 -1.3457,0.6263009 -0.516275,0.6178373 -0.516275,1.6419239 z' />"
                        . "<path d='M 35.056981,7.4469786 Q 34.57456,7.1846094 33.931332,7.1846094 q -0.871743,0 -1.362627,0.6432279 Q 32.07782,8.4626017 32.07782,9.56286 v 4.138664 h -2.674474 v -8.66665 h 2.674474 v 1.6080698 h 0.03385 q 0.634765,-1.7604133 2.285152,-1.7604133 0.423177,0 0.660155,0.1015623 z' />"
                        . "<path d='m 37.655283,3.6637829 q -0.677082,0 -1.108722,-0.3977857 -0.431639,-0.4062492 -0.431639,-0.9902325 0,-0.6009102 0.431639,-0.9817689 0.43164,-0.38085862 1.108722,-0.38085862 0.685546,0 1.108722,0.38085862 0.43164,0.3808587 0.43164,0.9817689 0,0.6093739 -0.43164,0.998696 -0.423176,0.3893222 -1.108722,0.3893222 z m 1.32031,10.0377411 h -2.674474 v -8.66665 h 2.674474 z' />"
                        . "<path d='M 54.988583,13.701524 H 52.322572 V 8.7588251 q 0,-1.8873662 -1.388018,-1.8873662 -0.660155,0 -1.074867,0.5670562 -0.414713,0.5670562 -0.414713,1.4134087 V 13.701524 H 46.7705 V 8.7080439 q 0,-1.836585 -1.362628,-1.836585 -0.685545,0 -1.100258,0.5416656 -0.406249,0.5416657 -0.406249,1.4726534 v 4.8157461 h -2.674474 v -8.66665 h 2.674474 v 1.354164 h 0.03385 q 0.414713,-0.6940091 1.159503,-1.1256489 0.753254,-0.4401033 1.641924,-0.4401033 1.836585,0 2.513667,1.6165333 0.990232,-1.6165333 2.911452,-1.6165333 2.826818,0 2.826818,3.4869724 z' />"
                        . "<path d='m 64.890907,10.129916 h -5.653635 q 0.135417,1.887366 2.378251,1.887366 1.430336,0 2.513667,-0.677082 v 1.929684 q -1.201821,0.643228 -3.123041,0.643228 -2.098954,0 -3.258457,-1.159503 -1.159503,-1.167967 -1.159503,-3.2499937 0,-2.158199 1.252602,-3.4192642 1.252601,-1.2610653 3.080723,-1.2610653 1.895829,0 2.92838,1.1256489 1.041013,1.1256488 1.041013,3.0553326 z M 62.411094,8.4879922 q 0,-1.8619755 -1.506507,-1.8619755 -0.643228,0 -1.117186,0.5332021 -0.465493,0.5332021 -0.567056,1.3287734 z' />"
                    . "</g>"
                . "</g>"
               . "<image src='" . esc_url( $_sPrimeImageURL ) . "' />" // fallback for browsers not supporting SVG
            . "</svg>"
        . "</span>";

        // @deprecated 3.6.0 Now use an embedded SVG icon
        return "<span class='amazon-prime'>"
                . "<i " . self::getAttributes( $_aAttributes ) . "></i>"
            . "</span>";
    }

}
