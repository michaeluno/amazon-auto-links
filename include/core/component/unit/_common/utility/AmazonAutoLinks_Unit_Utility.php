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
 * A class that provides utility methods for the unit component.
 * @since   3.8.11
 */
class AmazonAutoLinks_Unit_Utility extends AmazonAutoLinks_PluginUtility {

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
     * @since       3.8.11
     * @return      string  The price output. If a discount is available, the discounted price is also returned along with the proper price.
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
            array( 'Offers', 'Listings', 'SavingBasis', 'DisplayAmount' ),
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
     * @return      string
     * @since       3
     * @since       3.8.11  Renamed from `___getFormattedDiscountPrice()` and moved from `AmazonAutoLinks_Event___Action_APIRequestSearchProduct`.
     */
    static public function getFormattedDiscountPrice( array $aOffer, $nPrice, $sPriceFormatted, $nDiscountedPrice ) {

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
            '/[\d\.,]+/',
            $nDiscountedPrice / 100,   // decimal,  // numeric price
            $sPriceFormatted // price format
        );

    }

    /**
     * Extracts `Offers` element from the product item array given by the PAAPI.
     * This element is needed to get price information.
     * @return  array
     * @since   3
     * @since   3.8.11      Moved from `AmazonAutoLinks_Event___Action_APIRequestSearchProduct`. Renamed from `___getOfferArray()`.
     * @deprecated  3.9.0       PA-API 5 changed the structure
     */
    static public function getOffers( array $aProduct, $nPrice=null ) {

        $_iTotalOffers = self::getElement( $aProduct, array( 'Offers', 'TotalOffers' ), 0 );  
        if ( 2 > $_iTotalOffers  ) {
            return self::getElementAsArray( $aProduct, array( 'Offers', 'Offer', 'OfferListing' ) );
        }
        $_aOffers = self::getElementAsArray( $aProduct, array( 'Offers', 'Offer' ) );
        
        $_aDiscountedPrices = array();
        foreach( $_aOffers as $_iIndex => $_aOffer ) {
            if ( ! isset( $_aOffer[ 'OfferListing' ] ) ) {
                continue;
            }
            $_aDiscountedPrices[ $_iIndex ] = self::getDiscountedPrice( $_aOffer[ 'OfferListing' ], $nPrice );
        }
        $_iIndex = self::getKeyOfLowestElement( $_aDiscountedPrices );
        return $_aOffers[ $_iIndex ][ 'OfferListing' ];                    
        
    }   
    /**
     * Calculates the discounted price and returns the numeric value as an integer.
     * @param       array       $aOffer     ListedOffer element in the Offer element array in the response product data.
     * @param       mixed       $nPrice     The listed price.
     * @since       3.8.11
     * @since       3
     * @return      integer
     * @deprecated  3.9.0       PA-API5 changed the structure
     */
    static public function getDiscountedPrice( $aOffer, $nPrice ) {
        
        $_nDiscountedPrice = self::getElement( $aOffer, array( 'Price', 'Amount' ), null );
        if ( null !== $_nDiscountedPrice ) {
            return $_nDiscountedPrice;
        }
        
        // If saving amount is set
        $_nSavingAmount = self::getElement( $aOffer, array( 'AmountSaved', 'Amount' ), null );
        if ( null !== $_nSavingAmount ) {
            return $nPrice - $_nSavingAmount;
        }
        
        // If discount percentage is set,
        $_nDiscountPercentage = self::getElement( $aOffer, array( 'PercentageSaved' ), null );
        if ( null !== $_nDiscountPercentage ) {
            return $nPrice * ( ( 100 - $_nDiscountPercentage ) / 100 );
        }                    
        return 0;   // 3.8.5 changed the default value from null to 0 to avoid automatic background task of retrieving product details

    }    
    
    
    /**
     * Extracts and returns the price from the product item array that PA API returns.
     * @return      string
     * @since       3
     * @since       3.8.11  Moved from `AmazonAutoLinks_Event___Action_APIRequestSearchProduct`.
     * @param       array   $aProduct
     * @param       string  $sKey       FormattedPrice|Amount
     * @deprecated  3.9.0   PA-API5 changed the stricture
     */
    static public function getPriceByKey( array $aProduct, $sKey, $mDefault ) {
        
        // There are cases the listed price is not set.
        $_sFormattedPrice = self::getElement(
            $aProduct,
            array( 'ItemAttributes', 'ListPrice', $sKey ),
            $mDefault // avoid null as in the front-end, when null is returned, it triggers a background task
        );
        if ( ! empty( $_sFormattedPrice ) ) {
            return $_sFormattedPrice;
        }

        // Try to use a lowest new one.
        $_sFormattedPrice = self::getElement(
            $aProduct,
            array( 'OfferSummary', 'LowestNewPrice', $sKey ),
            $mDefault  // avoid null as in the front-end, when null is returned, it triggers a background task
        ); 
        return $_sFormattedPrice;

    }

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
                    'src'   => esc_url( $_sImageURL ),
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
     * @param array   $aImages         The extracted image array from a PA-API response.
     * @param string  $sProductURL
     * @param string  $sTitle
     * @param integer $iMaxImageSize    The maximum size of each sub-image.
     * @param integer $iMaxNumberOfImages
     *
     * @return      string
     * @since       3           Originally defined in `AmazonAutoLinks_UnitOutput___ElementFormatter_ImageSet`.
     * @since       3.8.11      Renamed from `___getFormattedOutput()` and moved from `AmazonAutoLinks_UnitOutput___ElementFormatter_ImageSet`.
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
             * @remark      available key names
             * - Small
             * - Medium
             * - Large
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
                $_sURL = self::getImageURLBySize( $_sURL, $iImageSize );
                return $_sURL;
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
     * @return      string
     * @since       3.8.11      Moved from `AmazonAutoLinks_UnitOutput_Utility`
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
             * @param array $aBrowseNode
             * @param string $sBreadcrumb
             * @since 3.8.0
             * @since 3.8.11      Moved from `AmazonAutoLinks_UnitOutput_Utility`
             * @return string
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
            '/\d[\.,]\d/', // needle
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
        $_sRatingStarImageURL = 'https://images-na.ssl-images-amazon.com/images/G/01/x-locale/common/customer-reviews/stars-'
            . $_sRating . '.gif';
        return $_sRatingStarImageURL;
    }

    /**
     * @param   $iRating
     * @param   $iReviewCount
     * @since   3.9.0
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
            ? "&nbsp;("
                . ( $sReviewURL
                    ? "<a " . $_sAAttributes . ">"
                        . $iReviewCount
                    . "</a>"
                    : $iReviewCount
                )
            .  ")"
            : '';
        return "<div class='crIFrameNumCustReviews'>"
                   . "<span class='crAvgStars'>"
                       . "<span class='review-stars' style='display:inline-block;'>"
                            . ( $sReviewURL
                                ? "<a " . $_sAAttributes . ">"
                                    . "<img " . $_sImgAttributes . "/>"
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
        $_sPrimeImageURL = AmazonAutoLinks_Registry::getPluginURL( 'asset/image/unit/prime.gif' );
        $_aAttributes    = array(
            'role'  => 'img',
            'class' => 'prime-icon',
            'style' => 'background-image: url(' . esc_url( $_sPrimeImageURL ) . ');'
        );
        return "<span class='amazon-prime'>"
                . "<i " . self::getAttributes( $_aAttributes ) . "></i>"
            . "</span>";
    }

}
