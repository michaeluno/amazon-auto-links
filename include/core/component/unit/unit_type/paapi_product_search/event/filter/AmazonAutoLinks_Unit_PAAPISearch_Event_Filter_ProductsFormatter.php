<?php
/**
 * Amazon Auto Links
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 *
 */

/**
 * Formats products array.
 *
 * @since 5.0.0
 */
class AmazonAutoLinks_Unit_PAAPISearch_Event_Filter_ProductsFormatter extends AmazonAutoLinks_Unit_UnitType_Common_Event_Filter_ProductsFormatter_Base {

    /**
     * @var   string
     * @since 5.0.0
     */
    public $sUnitType = 'search';

    /**
     * @var   AmazonAutoLinks_UnitOutput_search
     * @sicne 5.0.0
     */
    public $oUnitOutput;

    /**
     * Unit type specific product structure.
     * @var array
     * @since 5.0.0   Moved from `AmazonAutoLinks_UnitOutput_search`. Renamed from `$aStructure_Item`.
     */
    public static $aStructure_Product = array(
        'ASIN'              => null,
        'DetailPageURL'     => null,
        'ItemInfo'          => null,
        'BrowseNodeInfo'    => null,
        'Images'            => null,
        'Offers'            => null,
    );

    /**
     * @param  array $aProducts
     * @return array
     * @since  5.0.0
     */
    protected function _getItemsFormatted( $aProducts ) {
        return $this->___getProductsFromResponseItems(
            $aProducts,  // Items
            strtoupper( $this->oUnitOutput->oUnitOption->get( 'country' ) ), // locale
            $this->oUnitOutput->oUnitOption->get( 'associate_id' ),          // associate id
            $this->oUnitOutput->sResponseDate, // response date - no need to adjust for GMT, will be done later
            $this->oUnitOutput->oUnitOption->get( 'count' )
        );
    }
        /**
         * @param  array   $aItems
         * @param  string  $_sLocale
         * @param  string  $_sAssociateID
         * @param  string  $_sResponseDate
         * @param  integer $_iCount
         * @return array
         * @since  3.5.0
         * @since  3.9.0   Changed the scope to protected.
         * @since  5.0.0   Moved from `AmazonAutoLinks_UnitOutput_search`. Changed the scope to private from protected as no other classes use it.
         */
        private function ___getProductsFromResponseItems( array $aItems, $_sLocale, $_sAssociateID, $_sResponseDate, $_iCount ) {

            $_aASINLocaleCurLangs  = array();  // stores added product ASINs for performing a custom database query.
            $_aProducts     = array();

            $_sCurrency     = $this->oUnitOutput->oUnitOption->get( array( 'preferred_currency' ), AmazonAutoLinks_PAAPI50___Locales::getDefaultCurrencyByLocale( $_sLocale ) );
            $_sLanguage     = $this->oUnitOutput->oUnitOption->get( array( 'language' ), AmazonAutoLinks_PAAPI50___Locales::getDefaultLanguageByLocale( $_sLocale ) );

            // First Iteration - Extract displaying ASINs.
            foreach ( $aItems as $_iIndex => $_aItem ) {

                // This parsed item is no longer needed and must be removed once it is parsed
                // as this method is called recursively.
                unset( $aItems[ $_iIndex ] );

                try {
                    $_aItem         = $this->___getItemStructured( $_aItem );
                    $_sTitleRaw     = $this->___getTitleRaw( $_aItem );
                    $_sTitle        = $this->oUnitOutput->getTitleSanitized( $_sTitleRaw, $this->oUnitOutput->oUnitOption->get( 'title_length' ) );
                    $_sThumbnailURL = $this->___getThumbnailURL( $_aItem );
                    $_sProductURL   = $this->oUnitOutput->getProductLinkURLFormatted(
                        rawurldecode( $_aItem[ 'DetailPageURL' ] ),
                        $_aItem[ 'ASIN' ],
                        $this->oUnitOutput->oUnitOption->get( 'language' ),
                        $this->oUnitOutput->oUnitOption->get( 'preferred_currency' )
                    );
                    $_sContent      = $this->oUnitOutput->getContent( $_aItem );
                    $_sDescription  = $this->___getDescription( $_sContent, $_sProductURL  );
                    $this->___checkProductBlocked( $_aItem[ 'ASIN' ], $_sTitleRaw, $_sDescription );

                    // At this point, update the black&white lists as this item is parsed.
                    $this->oUnitOutput->setParsedASIN( $_aItem[ 'ASIN' ] );

                    $_aProduct      = $this->___getProduct(
                        $_aItem,
                        $_sTitle,
                        $_sThumbnailURL,
                        $_sProductURL,
                        $_sContent,
                        $_sDescription,
                        $_sLocale,
                        $_sAssociateID,
                        $_sResponseDate
                    );

                } catch ( Exception $_oException ) {
                    // Blocked by product filters
                    if ( false !== strpos( $_oException->getMessage(), '(product filter)' ) ) {
                        $this->oUnitOutput->aBlockedASINs[ $_aItem[ 'ASIN' ] ] = $_aItem[ 'ASIN' ];
                    }
                    continue;   // skip
                }

                $_aASINLocaleCurLang = "{$_aProduct[ 'ASIN' ]}|{$_sLocale}|{$_sCurrency}|{$_sLanguage}";
                $_aASINLocaleCurLangs[ $_aASINLocaleCurLang ] = array(
                    'asin'     => $_aProduct[ 'ASIN' ],
                    'locale'   => $_sLocale,
                    'currency' => $_sCurrency,
                    'language' => $_sLanguage,
                );

                $_aProducts[]    = $_aProduct;

                // Max Number of Items
                if ( count( $_aProducts ) >= $_iCount ) {
                    break;
                }

            }

            return $this->___getProductsFormattedFromResponseItems(
                $aItems,
                $_aProducts,
                $_aASINLocaleCurLangs,
                $_sLocale,
                $_sAssociateID,
                $_iCount,
                $_sResponseDate
            );

        }

            /**
             *
             * @param  array   $aItems
             * @param  array   $_aProducts
             * @param  array   $aASINLocaleCurLangs    Items for db queries.
             * @param  string  $_sLocale
             * @param  string  $_sAssociateID
             * @param  integer $_iCount
             * @param  string  $_sResponseDate
             * @return array
             * @since  3.5.0
             * @since  5.0.0   Moved from `AmazonAutoLinks_UnitOutput_search`.
             */
            private function ___getProductsFormattedFromResponseItems( $aItems, $_aProducts, $aASINLocaleCurLangs, $_sLocale, $_sAssociateID, $_iCount, $_sResponseDate ) {

                try {

                    $_iResultCount = count( $_aProducts );
                    // Second iteration.
                    $_aProducts = $this->oUnitOutput->getProductsFormatted(
                        $_aProducts,
                        $aASINLocaleCurLangs,
                        $_sLocale,
                        $_sAssociateID
                    );
                    $_iCountAfterFormatting = count( $_aProducts );
                    if ( $_iResultCount > $_iCountAfterFormatting ) {
                        throw new Exception( $_iCount - $_iCountAfterFormatting );
                    }

                } catch ( Exception $_oException ) {

                    // Do a recursive call
                    $_aAdditionalProducts = $this->___getProductsFromResponseItems(
                        $aItems,
                        $_sLocale,
                        $_sAssociateID,
                        $_sResponseDate,
                        ( integer ) $_oException->getMessage() // the number of items to retrieve
                    );
                    $_aProducts = array_merge( $_aProducts, $_aAdditionalProducts );

                }
                return $_aProducts;

            }

            /**
             * @throws Exception
             * @since  3.5.0
             * @since  5.0.0   Moved from `AmazonAutoLinks_UnitOutput_search`.
             * @param  array $aItem
             * @return array
             */
            private function ___getItemStructured( $aItem ) {
                if ( ! is_array( $aItem ) ) {
                    throw new Exception( 'The product element must be an array.' );
                }
                return $aItem + self::$aStructure_Product;
            }

            private function ___getTitleRaw( $aItem ) {
                $_sTitle = $this->oUnitOutput->oUnitOption->get( array( 'product_title' ) );
                return $_sTitle
                    ? $_sTitle
                    : $this->getElement( $aItem, array( 'ItemInfo', 'Title', 'DisplayValue' ), '' );
            }

            /**
             * @param  string $sASIN
             * @param  string $sTitleRaw
             * @param  string $sDescription
             * @throws Exception
             * @since  ?
             * @since  5.0.0   Moved from `AmazonAutoLinks_UnitOutput_search`.
             */
            private function ___checkProductBlocked( $sASIN, $sTitleRaw, $sDescription ) {
                if ( $this->oUnitOutput->isWhiteListed( $sASIN, $sTitleRaw, $sDescription ) ) {
                    return;
                }
                if ( $this->oUnitOutput->isASINBlocked( $sASIN ) ) {
                    throw new Exception( '(product filter) The product ASIN is black-listed: ' . $sASIN );
                }
                if ( $this->oUnitOutput->isTitleBlocked( $sTitleRaw ) ) {
                    throw new Exception( '(product filter) The title is black-listed: ' . $sTitleRaw );
                }
                if ( $this->oUnitOutput->isDescriptionBlocked( $sDescription ) ) {
                    throw new Exception( '(product filter) The description is not allowed: ' . $sDescription );
                }
            }

            /**
             * @param  array $aItem
             * @return string
             * @throws Exception
             * @since  3.5.0
             * @since  5.0.0   Moved from `AmazonAutoLinks_UnitOutput_search`.
             */
            private function ___getThumbnailURL( $aItem ) {
                $_sThumbnailURL = $this->getElement( $aItem, array( 'Images', 'Primary', 'Medium', 'URL' ), '' );

                /**
                 * Occasionally, the `MediumImage` element (main thumbnail image) does not exist but sub-images do.
                 * In that case, use the first sub-image.
                 *
                 * @since  3.5.2
                 * @since  5.0.0   Moved from `AmazonAutoLinks_UnitOutput_search`.
                 */
                if ( empty( $_sThumbnailURL ) ) {
                    $_sThumbnailURL = $this->getElement( $aItem, array( 'Images', 'Variants', '0', 'Medium', 'URL' ), '' );
                }

                $this->___checkImageAllowed( $_sThumbnailURL );
                return $_sThumbnailURL;
            }
                /**
                 * @since  3.5.0
                 * @since  5.0.0     Moved from `AmazonAutoLinks_UnitOutput_search`.
                 * @throws Exception
                 * @param  string    $sThumbnailURL
                 */
                private function ___checkImageAllowed( $sThumbnailURL ) {
                    if ( ! $this->oUnitOutput->isImageAllowed( $sThumbnailURL ) ) {
                        throw new Exception( '(product filter) No image is allowed: ' . $sThumbnailURL );
                    }
                }

            /**
             * @param  string $sContent
             * @param  string $sProductURL
             * @return string
             * @since  3.5.0
             * @since  5.0.0   Moved from `AmazonAutoLinks_UnitOutput_search`.
             */
            private function ___getDescription( $sContent, $sProductURL ) {
                return $this->oUnitOutput->getDescriptionFormatted(
                    $sContent,
                    $this->oUnitOutput->oUnitOption->get( 'description_length' ),
                    $this->oUnitOutput->getReadMoreText( $sProductURL )
                );
            }

            /**
             * @return array
             * @throws Exception
             * @compat PA-API5
             * @since  3.5.0
             * @since  5.0.0   Moved from `AmazonAutoLinks_UnitOutput_search`.
             */
            private function ___getProduct(
                $_aItem,
                $sTitle,
                $_sThumbnailURL,
                $_sProductURL,
                $_sContent,
                $_sDescription,
                $_sLocale,
                $_sAssociateID,
                $_sResponseDate
            ) {

                // Construct a product array. This will be passed to a template.
                // @remark  For values that could not be retrieved, leave it null so that later it will be filled with formatting routine or triggers a background routine to retrieve product data
                $_aProduct = array(
                    'ASIN'               => $_aItem[ 'ASIN' ],
                    'product_url'        => $_sProductURL,
                    'title'              => $sTitle, // the shortcode parameter 'title' can suppress the title in the parsed data but an empty string is not accepted. To remove a title, use the `Title Length` / `Item Format` option.
                    'text_description'   => $this->oUnitOutput->getDescriptionFormatted( $_sContent, 250, '' /* no read more link */ ),  // forced-truncated version of the contents
                    'description'        => $_sDescription, // reflects the user set character length. Additional meta data will be prepended.
                    'meta'               => '', // @todo maybe deprecated?
                    'content'            => $_sContent,
                    'image_size'         => $this->oUnitOutput->oUnitOption->get( 'image_size' ),
                    'thumbnail_url'      => $this->oUnitOutput->getProductImageURLFormatted(
                        $_sThumbnailURL,
                        $this->oUnitOutput->oUnitOption->get( 'image_size' ),
                        strtoupper( $this->oUnitOutput->oUnitOption->get( 'country' ) )  // locale
                    ),
                    'author'             => $this->___getAuthors( $_aItem ),
                    // @todo 3.9.0 implement manufacturer, brand, etc.
                    'updated_date'       => $_sResponseDate, // not GMT aware at this point. Will be formatted later in the ItemFormatter class.
                    'release_date'       => $this->getElement(
                        $_aItem,
                        array( 'ItemInfo', 'ContentInfo', 'PublicationDate', 'DisplayValue' ),
                        ''
                    ),
                    'is_adult'           => ( boolean ) $this->getElement(
                        $_aItem,
                        array( 'ItemInfo', 'ProductInfo', 'IsAdultProduct', 'DisplayValue' ),
                        false
                    ),
                    // Not all items have top level sales rank information available. Hence, the WebsiteSalesRank information is not present for all items.
                    // @see https://webservices.amazon.com/paapi5/documentation/use-cases/organization-of-items-on-amazon/browse-nodes/browse-nodes-and-sales-ranks.html#how-to-get-salesrank-information-for-an-item
                    'sales_rank'          => $this->getElement(
                        $_aItem,
                        array( 'BrowseNodeInfo', 'WebsiteSalesRank', 'SalesRank' ),
                        0
                    ), // 3.8.0
                    'is_prime'            => $this->oUnitOutput->isPrime( $_aItem ),
                    'feature'             => $this->___getFeatures( $_aItem ),
                    'category'            => $this->___getCategories( $_aItem ),

                    // These must be retrieved separately -> There are cases that the review count and rating is returned.
                    'review'              => null,  // customer reviews

                    // 3+ // 4.0.0+ Changed from `rating` to distinguish from the database table column key name
                    'formatted_rating'    => $this->oUnitOutput->getFormattedRatingFromItem( $_aItem, $_sLocale, $_sAssociateID ),

                    // These will be assigned below
                    'image_set'           => null,
                    'button'              => null,  // 3+

                    // @deprecated 3.9.0 PA-API 5 does not support below
                    'editorial_review'    => '',  // 3+ // @todo add a format method for editorial reviews.
                    'similar_products'    => '',

                )
                + $this->oUnitOutput->getPrices( $_aItem )
                + $_aItem;

                // 3.8.11 Retrieve the images directly from the response rather than the custom database table
                $_aProduct[ 'image_set' ] = $this->___getImageSet(
                    $_aItem,
                    $_sProductURL,
                    $sTitle,
                    $this->oUnitOutput->oUnitOption->get( 'subimage_size' ),
                    $this->oUnitOutput->oUnitOption->get( 'subimage_max_count' ),
                    ( boolean ) $this->oUnitOutput->oUnitOption->get( 'pop_up_images' )
                );

                // Add meta data to the description
                $_aProduct[ 'meta' ]        = $this->___getProductMetaFormatted( $_aProduct );
                $_aProduct[ 'description' ] = $this->___getProductDescriptionFormatted( $_aProduct );

                // Thumbnail
                $_aProduct[ 'formatted_thumbnail' ] = $this->oUnitOutput->getProductThumbnailFormatted( $_aProduct );

                // Title
                $_aProduct[ 'formatted_title' ] = $this->oUnitOutput->getProductTitleFormatted( $_aProduct, $this->oUnitOutput->oUnitOption->get( 'title_format' ) );

                // Button - check if the %button% variable exists in the item format definition.
                // It accesses the database, so if not found, the method should not be called.
                if ( $this->oUnitOutput->oUnitOption->hasItemFormatTags( array( '%button%', ) ) ) {
                    $_aProduct[ 'button' ] = $this->oUnitOutput->getButtonFormatted(
                        $this->oUnitOutput->oUnitOption->get( 'button_type' ),
                        $this->oUnitOutput->getButtonID(),
                        $_aProduct[ 'product_url' ],
                        $_aProduct[ 'ASIN' ],
                        $_sLocale,
                        $_sAssociateID,
                        $this->oUnitOutput->oOption->getPAAPIAccessKey( $_sLocale ), // public access key
                        $this->oUnitOutput->oUnitOption->get( 'override_button_label' ) ? $this->oUnitOutput->oUnitOption->get( 'button_label' ) : null
                    );
                }

                /**
                 * Let third-parties filter products.
                 * @since 3.4.13
                 */
                $_aProduct = apply_filters(
                    'aal_filter_unit_each_product',
                    $_aProduct,
                    array(
                        'locale'        => $_sLocale,
                        'asin'          => $_aProduct[ 'ASIN' ],
                        'associate_id'  => $_sAssociateID,
                        'asin_locale'   => $_aProduct[ 'ASIN' ] . '_' . strtoupper( $_sLocale ),
                    ),
                    $this
                );
                if ( empty( $_aProduct ) ) {
                    throw new Exception( 'The product array is empty.' );
                }
                return $_aProduct;

            }
                /**
                 * Extracts authors of an item
                 * @param  array  $aItem
                 * @since  3.9.0
                 * @since  5.0.0  Moved from `AmazonAutoLinks_UnitOutput_search`.
                 * @return string
                 */
                private function ___getAuthors( array $aItem ) {
                    $_aAuthors      = array();
                    $_aContributors = $this->getElementAsArray( $aItem, array( 'ItemInfo', 'ByLineInfo', 'Contributors' ), array() );
                    foreach( $_aContributors as $_aContributor ) {
                        $_aAuthors[] = $this->getElement( $_aContributor, array( 'Name' ) );
                    }
                    return implode( ", ", $_aAuthors );
                }

            /**
             * @return string
             * @since  3.8.11
             * @since  4.7.0   Added the `$bImagePreview` parameter.
             * @since  5.0.0   Moved from `AmazonAutoLinks_UnitOutput_search`.
             */
            private function ___getImageSet( $aItem, $sProductURL, $sTitle, $iMaxImageSize, $iMaxNumberOfImages, $bImagePreview=true ) {
                $_aImages = $this->oUnitOutput->getImageSet( $aItem );
                return $this->oUnitOutput->getSubImages( $_aImages, $sProductURL, $sTitle, $iMaxImageSize, $iMaxNumberOfImages, $bImagePreview );
            }

            /**
             * @param  array  $aItem
             * @return string
             * @since  3.8.0
             * @since  3.8.11 Moved from `AmazonAutoLinks_UnitOutput_Base_ElementFormat`.
             * @since  5.0.0  Moved from `AmazonAutoLinks_UnitOutput_search`.
             */
            private function ___getCategories( array $aItem ) {
                $_aNodes = $this->getElementAsArray( $aItem, array( 'BrowseNodeInfo', 'BrowseNodes', ) );
                return $this->oUnitOutput->getCategories( $_aNodes );
            }

            /**
             * @return string
             * @since  3.8.0
             * @since  3.8.11  Moved from `AmazonAutoLinks_UnitOutput_Base_ElementFormat`.
             * @since  5.0.0   Moved from `AmazonAutoLinks_UnitOutput_search`.
             */
            private function ___getFeatures( array $aItem ) {
                $_aFeatures = $this->getElementAsArray( $aItem, array( 'ItemInfo', 'Features', 'DisplayValues' ) );
                return $this->oUnitOutput->getFeatures( $_aFeatures );
            }

            /**
             * Returns the formatted product meta HTML block.
             *
             * @since   2.1.1
             * @since   5.0.0   Moved from `AmazonAutoLinks_UnitOutput_search`.
             * @return  string
             * @param   array  $aProduct
             * @todo    3.9.0  Add `brand`, `manufacturer` etc
             */
            private function ___getProductMetaFormatted( array $aProduct ) {

                $_aOutput = array();
                if ( $aProduct[ 'author' ] ) {
                    $_aOutput[] = "<span class='amazon-product-author'>"
                            . sprintf( __( 'by %1$s', 'amazon-auto-links' ) , $aProduct[ 'author' ] )
                        . "</span>";
                }
                if ( $aProduct[ 'proper_price' ] ) {
                    $_aOutput[] = "<span class='amazon-product-price'>"
                            . sprintf( __( 'for %1$s', 'amazon-auto-links' ), $aProduct[ 'proper_price' ] )
                        . "</span>";
                }
                if ( $aProduct[ 'discounted_price' ] ) {
                    $_aOutput[] = "<span class='amazon-product-discounted-price'>"
                            . $aProduct[ 'discounted_price' ]
                        . "</span>";
                }
                if ( $aProduct[ 'lowest_new_price' ] ) {
                    $_aOutput[] = "<span class='amazon-product-lowest-new-price'>"
                            . sprintf( __( 'New from %1$s', 'amazon-auto-links' ), $aProduct[ 'lowest_new_price' ] )
                        . "</span>";
                }
                if ( $aProduct[ 'lowest_used_price' ] ) {
                    $_aOutput[] = "<span class='amazon-product-lowest-used-price'>"
                            . sprintf( __( 'Used from %1$s', 'amazon-auto-links' ), $aProduct[ 'lowest_used_price' ] )
                        . "</span>";
                }
                return empty( $_aOutput )
                    ? ''
                    : "<div class='amazon-product-meta'>"
                        . implode( ' ', $_aOutput )
                        . "</div>";

            }
            /**
             * Returns the formatted product description HTML block.
             *
             * @since  2.1.1
             * @since  3.9.0  Removed the `meta` element.
             * @since  5.0.0   Moved from `AmazonAutoLinks_UnitOutput_search`.
             * @param  array  $aProduct
             * @return string
             */
            private function ___getProductDescriptionFormatted( array $aProduct ) {
                return $aProduct[ 'description' ]
                    ? "<div class='amazon-product-description'>"
                            . $aProduct[ 'description' ]
                        . "</div>"
                    : ''; // 3.10.0 In case of no description, do not even add the div element.
            }

}