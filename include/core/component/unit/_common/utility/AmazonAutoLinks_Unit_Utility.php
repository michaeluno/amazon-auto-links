<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2018 Michael Uno
 */

/**
 * A class that provides utility methods for the unit component.
 * @since   3.8.11
 */
class AmazonAutoLinks_Unit_Utility extends AmazonAutoLinks_PluginUtility {

    /**
     * Extract the price information from PA API response and generates the price output.
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
            ? '<span class="proper-price"><s>' . $sPriceFormatted . '</s></span> '
                . '<span class="offered-price">' . $_sLowestFormatted . '</span>'
            : '<span class="offered-price">' . $sPriceFormatted . '</span>';

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
     * Extract the sub-image (image-set) information from PA API response and generates the output.
     *
     * @since       3           Originally defined in `AmazonAutoLinks_UnitOutput___ElementFormatter_ImageSet`.
     * @since       3.8.11      Renamed from `___getFormattedOutput()` and moved from `AmazonAutoLinks_UnitOutput___ElementFormatter_ImageSet`.
     * @return      string
     */
    static public function getSubImages( array $aImages, $sProductURL, $sTitle, $iMaxImageSize, $iMaxNumberOfImages ) {

        if ( empty( $aImages ) ) {
            return '';
        }
        $sTitle    = strip_tags( $sTitle );

        // Extract image urls
        $_aImageURLs = self::___getSubImageURLs( $aImages, $iMaxImageSize, $iMaxNumberOfImages );

        $_aSubImageTags = array();
        foreach( $_aImageURLs as $_sImageURL ) {
            $_sImageTag = self::getHTMLTag(
                'img',
                array(
                    'src'   => esc_url( $_sImageURL ),
                    'class' => 'sub-image',
                    'alt'   => $sTitle,
                )
            );
            $_sATag     = self::getHTMLTag(
                'a',
                array(
                    'href'   => esc_url( $sProductURL ),
                    'target' => '_blank',
                    'title'  => $sTitle,
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
        return "<div class='sub-images'>" . implode( '', $_aSubImageTags ) . "</div>";

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
             * - SwatchImage
             * - SmallImage
             * - ThumbnailImage
             * - TinyImage
             * - MediumImage
             * - LargeImage
             * - HiResImage
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
                $_sURL = self::getImageURLBySize(
                    $_sURL,
                    $iImageSize
                );
                return is_ssl()
                    ? self::getAmazonSSLImageURL( $_sURL )
                    : $_sURL;

            }

    /**
     * Returns the resized image url.
     *
     * @rmark       Adjusts the image size. _SL160_ or _SS160_
     * @return      string
     * @param       $sImgURL        string
     * @param       $iImageSize     integer     0 to 500.
     * @since       3
     * @since       3.5.0       Moved from `AmazonAutoLinks_UnitOutput_Base_ElementFormat`.
     * @since       3.8.11      Moved from `AmazonAutoLinks_UnitOutput_Utility`.
     */
    static public function getImageURLBySize( $sImgURL, $iImageSize ) {
        return preg_replace(
            '/(?<=_S)([LS])(\d{1,3})(?=_)/i',
            '${1}'. $iImageSize,
            $sImgURL
       );
    }

    /**
     * Extracts image set (including the main thumbnail image and sub-images) from the product array.
     * @return      array
     * @since       3.8.11      Moved from `AmazonAutoLinks_Event___Action_APIRequestSearchProduct`.
     */
    static public function getImageSet( array $aProduct ) {

        $_aMainImage = array(
            'SwatchImage' => self::getElement( $aProduct, array( 'SwatchImage' ) ),
            'SmallImage'  => self::getElement( $aProduct, array( 'SmallImage' ) ),
            'MediumImage' => self::getElement( $aProduct, array( 'MediumImage' ) ),
            'LargeImage'  => self::getElement( $aProduct, array( 'LargeImage' ) ),
            'HiResImage'  => self::getElement( $aProduct, array( 'HiResImage' ) ),
        );
        // Will be numerically indexed array holding sub-image each.
        $_aSubImages = self::getElementAsArray( $aProduct, array( 'ImageSets', 'ImageSet' ), array() );

        // Sub-images can be only single. In that case, put it in a numeric element.
        if ( ! isset( $_aSubImages[ 0 ] ) ) {
            $_aSubImages = array( $_aSubImages );
        }

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
         * @since       3.8.11      Moved from `AmazonAutoLinks_UnitOutput_Utility`
         */
        static private function ___getBrowseNodes( array $aBrowseNodes ) {

            $_aList = array();
            $_aBrowseNodes = self::getElementAsArray( $aBrowseNodes, 'BrowseNode' );
            if ( empty( $_aBrowseNodes ) ) {
                return $_aList;
            }

            // For multiple nodes, the it is numerically indexed. Otherwise, the associative array itself.
            $_aBrowseNodes = isset( $_aBrowseNodes[ 0 ] ) ? $_aBrowseNodes : array( $_aBrowseNodes );
            foreach( $_aBrowseNodes as $_aBrowseNode ) {
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
                $_sName       = self::getElement( $aBrowseNode, 'Name' );
                if ( ! $_sName ) {
                    return $sBreadcrumb;
                }

                $sBreadcrumb = $sBreadcrumb
                    ? $sBreadcrumb . $sDelimiter . $_sName
                    : $_sName;

                $_aAncestor = self::getElementAsArray( $aBrowseNode, array( 'Ancestors', 'BrowseNode' ) );
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
        return "<ul class='features'>" . $_sList . "</ul>";
    }

}
