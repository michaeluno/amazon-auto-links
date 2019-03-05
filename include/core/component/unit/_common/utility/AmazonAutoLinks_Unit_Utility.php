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
     * @return      string
     * @todo        complete this method.
     */
    static public function getPrice() {
        return '';
    }

    /**
     * Extract the sub-image (image-set) information from PA API response and generates the output.
     *
     * @since       3.5.0       Originally defined in `AmazonAutoLinks_UnitOutput___ElementFormatter_ImageSet`.
     * @since       3.8.11      Renamed from `___getFormattedOutput()` and moved from `AmazonAutoLinks_UnitOutput___ElementFormatter_ImageSet`.
     * @return      string
     */
    static public function getSubImages( array $aImages, $sProductURL, $sTitle, $iMaxImageSize, $iMaxNumberOfImages ) {

        if ( empty( $aImages ) ) {
            return '';
        }
        $sTitle    = strip_tags( $sTitle );

        // Extract image urls
        $_aImageURLs = self::___getSubImageURLs(
            $aImages,
            $iMaxImageSize,
            $iMaxNumberOfImages
        );

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
        return "<div class='sub-images'>"
                . implode( '', $_aSubImageTags )
            . "</div>";

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
