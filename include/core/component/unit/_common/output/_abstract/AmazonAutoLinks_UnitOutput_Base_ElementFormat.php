<?php
/**
 * Auto Amazon Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2022 Michael Uno
 */

/**
 * One of the base classes for unit classes.
 * 
 * Provides shared methods and properties relating formatting product elements.
 *
 * @since 3
 */
abstract class AmazonAutoLinks_UnitOutput_Base_ElementFormat extends AmazonAutoLinks_UnitOutput_Base_ProductFilter {

    /**
     * @var   array
     * @since 4.7.8
     */
    static public $aStructure_ProductCommon = array(
        'thumbnail_url'         => null,
        'ASIN'                  => null,
        'product_url'           => null,
        'raw_title'             => null,
        'title'                 => null,
        'description'           => null,    // the formatted feed item description - some elements are removed
        'text_description'      => null,    // the non-html description

        // [3]
        'formatted_price'       => null, // 4.0.0+ (string|null) HTML formatted price. Changed from the name, `price` to be compatible with merged database table column key names.
        'review'                => null,
        'formatted_rating'      => null, // 4.0.0+ Changed from `rating` to distinguish from the database table column key name
        'image_set'             => null,
        'button'                => null,

        // [3.8.11]
        'proper_price'          => null,

        // used for disclaimer
        'updated_date'          => null,    // the date posted - usually it's the updated time of the feed at Amazon so it's useless

        // [3.3.0]
        'content'               => null,
        'meta'                  => null,
        'similar_products'      => null,

        // [3.8.0]
        'category'              => null,
        'feature'               => null,
        'sales_rank'            => null,

        // [3.9.0]
        'is_prime'              => null,

        // [4.1.0]
        'author'                => null,

        // [4.7.8]
        'formatted_discount'    => null,
    );

    /**
     * @return array
     * @since  ?
     * @since  3.5.0  Renamed from `_formatProducts()`.
     * @since  5.0.0  Changed the visibility scope to public from protected as formatter classes access it.
     * @param  array  $aProducts
     * @param  array  $aASINLocaleCurLangs
     * @param  string $sLocale
     * @param  string $sAssociateID
     */
    public function getProductsFormatted( array $aProducts, array $aASINLocaleCurLangs, $sLocale, $sAssociateID ) {

        $_aDBProductRows = $this->___getProductsRowsFromDatabase( $aASINLocaleCurLangs );
        $_sLocale        = strtoupper( $this->oUnitOption->get( array( 'country' ), 'US' ) ); // @todo use the third parameter value
        $_sCurrency      = $this->oUnitOption->get( array( 'preferred_currency' ), AmazonAutoLinks_PAAPI50___Locales::getDefaultCurrencyByLocale( $_sLocale ) );
        $_sLanguage      = $this->oUnitOption->get( array( 'language' ), AmazonAutoLinks_PAAPI50___Locales::getDefaultLanguageByLocale( $_sLocale ) );

        // Second Iteration - format items and access custom database table.
        foreach( $aProducts as $_iIndex => &$_aProduct ) {

            try {
                $_aProduct = $this->___getProductFormatted(
                    $_aProduct,
                    $_aDBProductRows,
                    $sLocale,
                    $sAssociateID,
                    $_sCurrency,
                    $_sLanguage
                );
            } catch ( Exception $_oException ) {
                unset( $aProducts[ $_iIndex ] );
                continue;
            }

            // Item
            $_oItemFormatter = new AmazonAutoLinks_UnitOutput__ItemFormatter(
                $this,
                $_aProduct + self::$aStructure_ProductCommon,
                $this->getElementAsArray( $_aDBProductRows, $this->getElement( $_aProduct, 'ASIN', '' ) . '|' . $sLocale . '|' . $_sCurrency . '|' . $_sLanguage )
            );
            $_aProduct[ 'formatted_item' ] = $_oItemFormatter->get();

        }
        return $aProducts;
        
    }
        /**
         * If the user wants elements which need to access the custom database table,
         * retrieve all the products at once to save the number of database queries.
         *
         * @param   array $aASINLocaleCurLangs
         * @return  array
         * @since   3.5.0
         */
        private function ___getProductsRowsFromDatabase( array $aASINLocaleCurLangs ) {
            
            if ( ! $this->bDBTableAccess ) {
                return array();
            }
            $_oProducts = new AmazonAutoLinks_ProductDatabase_Rows(
                $aASINLocaleCurLangs,
                $this->oUnitOption->get( array( 'preferred_currency' ), '' ),
                $this->oUnitOption->get( array( 'language' ), '' )
            );
            return $_oProducts->get();

        }

        /**
         * @param  array     $_aProduct
         * @param  array     $_aDBProductRows
         * @param  string    $sLocale
         * @param  string    $sAssociateID
         * @param  string    $_sCurrency
         * @param  string    $_sLanguage
         * @return array
         * @throws Exception
         * @since  3.5.0
         * @since  3.9.0       Added the $_sCurrency, $_sLanguage parameters.
         */
        private function ___getProductFormatted( $_aProduct, $_aDBProductRows, $sLocale, $sAssociateID, $_sCurrency, $_sLanguage ) {

            if ( ! $this->bDBTableAccess ) {
                return $_aProduct;
            }

            // @deprecated Even the API is disconnected, it should return the cache
            // if ( ! $this->oOption->isAPIConnected() ) {
            //     return $_aProduct;
            // }

            // Rating and Reviews - these need to access the plugin cache database. e.g. %rating%, %review%
            $_aDBProductRow                 = $this->___getDBProductRow(
                $_aDBProductRows,
                $_aProduct[ 'ASIN' ],
                $sLocale,
                $sAssociateID, // for scheduling a background task when a row is not found
                $_sCurrency,
                $_sLanguage
            );

            $_sNativeTitle        = $_aProduct[ 'title' ];
            $_sNativeThumbnailURL = $_aProduct[ 'thumbnail_url' ];

            // Format elements
            $_aFormatterClasses   = array(
                'formatted_price'    => 'AmazonAutoLinks_UnitOutput___ElementFormatter_Price',               // 4.0.0 The key `price` is deprecated and replaced with `formatted_price`, to be compatible with database table column keys.
                'review'             => 'AmazonAutoLinks_UnitOutput___ElementFormatter_CustomerReview',
                'formatted_rating'   => 'AmazonAutoLinks_UnitOutput___ElementFormatter_UserRating',
                'image_set'          => 'AmazonAutoLinks_UnitOutput___ElementFormatter_ImageSet',
                'feature'            => 'AmazonAutoLinks_UnitOutput___ElementFormatter_Features',            // 3.8.0
                'category'           => 'AmazonAutoLinks_UnitOutput___ElementFormatter_Categories',          // 3.8.0
                'sales_rank'         => 'AmazonAutoLinks_UnitOutput___ElementFormatter_SalesRank',           // 3.8.0
                'title'              => 'AmazonAutoLinks_UnitOutput___ElementFormatter_Title',               // 3.10.0
                'formatted_discount' => 'AmazonAutoLinks_UnitOutput___ElementFormatter_DiscountPercentage',  // 4.7.8
                'updated_date'       => 'AmazonAutoLinks_UnitOutput___ElementFormatter_UpdatedTime',         // 5.1.4  The document last updated and the product table updated time can be different as the latter gets updated in the background. The `updated_date` value is used for elements including prices, disclaimer, prime and customer ratings and it should use the latest value possible.
            );
            foreach( $_aFormatterClasses as $_sKey => $_sClassName ) {
                $_oFormatter         = new $_sClassName( $_aProduct[ 'ASIN' ], $sLocale, $sAssociateID, $_aDBProductRow, $this->oUnitOption, $_aProduct );
                $_aProduct[ $_sKey ] = $_oFormatter->get();
            }

            // 3.9.0 Deprecated
            $_aProduct[ 'similar_products' ] = '';

            // 3.10.0
            if ( $_sNativeTitle !== $_aProduct[ 'title' ] ) {
                $_aProduct[ 'formatted_title' ] = $this->getProductTitleFormatted( $_aProduct, $this->oUnitOption->get( 'title_format' ) );
            }

            // 4.2.8
            $_aProduct[ 'thumbnail_url' ] = isset( $_aProduct[ 'thumbnail_url' ] )
                ? $_aProduct[ 'thumbnail_url' ]
                : $this->getElement( $_aDBProductRow, array( 'images', 'main', 'MediumImage' ), '' );
            if ( $_sNativeThumbnailURL !== $_aProduct[ 'thumbnail_url' ] ) {
                $_aProduct[ 'formatted_thumbnail' ] = $this->getProductThumbnailFormatted( $_aProduct );
            }

            /**
             * Merge the product array with the database product row
             * This is mainly for the feed unit type that helps sites without API access to reuse data from external sites with API access.
             * @since   4.0.0
             * @todo    Some elements such as `category` and `feature` are different from the database table keys and they hold the same values.
             * So the array structure needs to be redesigned and reconstructed.
             */
            $_aProduct = array_filter( $_aProduct, array( $this, 'isNotNull' ) )
                + $_aDBProductRow
                + $_aProduct;   // refill null elements so that it prevents undefined index warning in further processing.

            // Let unit types that need to use the data from database rows access them.
            $_aProduct = apply_filters(
                'aal_filter_unit_each_product_with_database_row',
                $_aProduct,
                $_aDBProductRow,
                array(
                    'locale'        => $sLocale,
                    'asin'          => $_aProduct[ 'ASIN' ],
                    'associate_id'  => $sAssociateID
                )
            );

            // 3.5.0+ the product can be filtered out with the above applied filters.
            if ( empty( $_aProduct ) ) {
                throw new Exception( 'The product array is empty. Most likely it is filtered out.' );
            }

            return $_aProduct;

        }

            /**
             * Retrieves a row array from the given database rows.
             *
             * In order to perform a background task scheduling when a row is not found,
             * pass the associate ID.
             *
             * @remark The keys of the database rows must be formatted to have {asin}_{locale}.
             * @param  array  $aDBRows
             * @param  string $sASIN
             * @param  string $sLocale
             * @param  string $sAssociateID
             * @param  string $_sCurrency
             * @param  string $_sLanguage
             * @return array
             * @since  3
             * @since  3.5.0  Changed the visibility scope from protected.
             * @since  3.5.0  Moved from `AmazonAutoLinks_UnitOutput_Base_CustomDBTable`.
             * @since  3.9.0  Added the $_sCurrency, $_sLanguage parameters.
             */
            private function ___getDBProductRow( array $aDBRows, $sASIN, $sLocale, $sAssociateID, $_sCurrency, $_sLanguage ) {

                if ( ! $this->bDBTableAccess ) {
                    return array();
                }

                $_aDBProductRow = $this->getElementAsArray( $aDBRows, $sASIN . '|' . $sLocale . '|' . $_sCurrency . '|' . $_sLanguage );

                // Schedule a background task to retrieve the product information.
                if ( $this->___shouldRenewRow( $_aDBProductRow, $sAssociateID ) ) {
                    AmazonAutoLinks_Event_Scheduler::scheduleProductInformation(
                        $sAssociateID . '|' . $sLocale . '|' . $_sCurrency . '|' . $_sLanguage,
                        $sASIN,
                        ( integer ) $this->oUnitOption->get( 'cache_duration' ),
                        ( boolean ) $this->oUnitOption->get( '_force_cache_renewal' ),
                        $this->oUnitOption->get( 'item_format' )
                    );
                }
                return $_aDBProductRow;

            }

                /**
                 * @param  array   $aDBProductRow
                 * @param  string  $sAssociateID
                 * @return boolean
                 * @since  3.5.0
                 */
                private function ___shouldRenewRow( $aDBProductRow, $sAssociateID ) {
                    if ( empty( $aDBProductRow ) && $sAssociateID ) {
                        return true;
                    }
                    if ( $this->oUnitOption->get( '_force_cache_renewal' ) ) {
                        return true;
                    }
                    return false;
                }

    /**
     * Returns the formatted product thumbnail HTML block.
     *
     * @param  array  $aProduct
     * @since  2.1.1
     * @since  3.5.0  Renamed from `_formatProductThumbnail()`.
     * @return string
     */
    public function getProductThumbnailFormatted( array $aProduct ) {

        if ( ! isset( $aProduct[ 'thumbnail_url' ] ) ) {
            return '';
        }
        $_iImageSize   = $this->oUnitOption->get( 'image_size' );
        $_sProductUL   = esc_url( $aProduct[ 'product_url' ] );
        $_sLImageURL   = esc_url( $this->getImageURLBySize( $aProduct[ 'thumbnail_url' ], 500 ) );
        $_bPopupImage  = ( boolean ) $this->oUnitOption->get( 'pop_up_images' );
        $_sAttributes  = "class='amazon-product-thumbnail-container'";
        $_sAttributes .= $_bPopupImage
            ? " data-href='{$_sProductUL}' data-large-src={$_sLImageURL}"
            : "";
        return "<div {$_sAttributes}>"
            . str_replace(
                array( 
                    "%href%", 
                    "%title_text%", 
                    "%src%", 
                    "%max_width%",          // @deprecated 4.1.0 Kept for backward-compatibility
                    "%description_text%",
                    "%image_size%",         // 4.1.0
                ),
                array( 
                    $_sProductUL,
                    esc_attr( strip_tags( $aProduct[ 'title' ] ) ),
                    esc_url( $aProduct[ 'thumbnail_url' ] ),
                    $_iImageSize,
                    esc_attr( $aProduct[ 'text_description' ] ),
                    $_iImageSize    // 4.1.0
                ),
                $this->oUnitOption->get( 'image_format' )
            )
            . "</div>";
        
    }

    /**
     * Strips tags and truncates the given string.
     *
     * @param  string              $sDescription
     * @param  null|integer|double $nMaxLength    A numeric value that determines the length.
     * @param  string              $sReadMoreText
     * @return string
     * @since  ?
     * @since  3.3.0               Renamed from `sanitizeDescription()`.
     * @since  5.0.0               Changed the visibility scope to public from protected as formatter classes access this. Renamed from `_getDescriptionSanitized()`.
     */
    public function getDescriptionFormatted( $sDescription, $nMaxLength=null, $sReadMoreText='' ) {

        $sDescription = strip_tags( $sDescription );
        $sDescription = preg_replace( '/[\s\t]+/', ' ', $sDescription );

        // Title character length
        $nMaxLength = $nMaxLength ? $nMaxLength : $this->oUnitOption->get( 'description_length' );
        if ( $nMaxLength == 0 ) { 
            return ''; 
        }
        $sDescription = ( $nMaxLength > 0 && $this->getStringLength( $sDescription ) > $nMaxLength )
            ? esc_attr( $this->getSubstring( $sDescription, 0, $nMaxLength ) ) . '...'
                . $sReadMoreText
            : esc_attr( $sDescription );
        
        return trim( $sDescription );
        
    }

    /**
     * @param  string The url to be linked.
     * @return string
     * @since  3.3.0
     * @since  5.0.0  Changed the visibility scope to public from protected as formatter classes access this.
     */
    public function getReadMoreText( $sReadMoreURL ) {
        
        if ( ! $sReadMoreURL ) {
            return '';
        }
        $sReadMoreURL = esc_url( $sReadMoreURL );
        
        $_sText       = $this->oUnitOption->get( 'description_suffix' );
        if ( ! $_sText ) {
            return '';
        }
        return " <a href='{$sReadMoreURL}' target='_blank' rel='nofollow noopener' style='display:inline;'>"
                . $_sText
            . "</a>";
        
    }
    
    /**
     * Sanitizes the raw title. 
     * 
     * This does not create a final result of the title as this method is called from sorting items as well.
     *
     * @param  string  $sTitle
     * @remark Used for sorting as well.
     * @since  3
     * @return string
     */
    public function replyToModifyRawTitle( $sTitle ) {
        $sTitle = strip_tags( ( string ) $sTitle );
        return $this->oUnitOption->get( 'keep_raw_title' )
            ? $sTitle
            : trim( preg_replace( '/#\d+?:\s+?/i', '', $sTitle ) ); // Remove heading numbering e.g. #2. Product name
    }

    /**
     * Formats a button.
     *
     * @param  integer        $iButtonType
     * @param  integer|string $isButtonID
     * @param  string         $sProductURL
     * @param  string         $sASIN
     * @param  string         $sLocale
     * @param  string         $sAssociateID
     * @param  string         $sAccessKey
     * @param  string         $nsButtonLabelToOverride
     * @return string
     * @since  3
     * @since  5.0.0  Renamed from `_getButton()`. Changed the visibility scope to public from protected as accessed from formatter classes.
     */
    public function getButtonFormatted( $iButtonType, $isButtonID, $sProductURL, $sASIN, $sLocale, $sAssociateID, $sAccessKey, $nsButtonLabelToOverride=null ) {
        $_aButtonArguments = array(
            'type'          => ( integer ) $iButtonType,
            'id'            => ( integer ) $isButtonID,
            'asin'          => $sASIN,
            'country'       => $sLocale,
            'associate_id'  => $sAssociateID,
            'access_key'    => $sAccessKey,
        );
        if ( null !== $nsButtonLabelToOverride ) {
            $_aButtonArguments[ 'label' ] = $nsButtonLabelToOverride;
        }
        return apply_filters( 'aal_filter_linked_button', '', $_aButtonArguments );
    }

    /**
     * Formats the given url such as adding associate ID, ref=nosim, and link style.
     *
     * @remark The similarity product formatter class accesses this method.
     * @param  string $sURL
     * @param  string $sASIN
     * @param  string $sLanguageCode
     * @param  string $sCurrency
     * @return string
     * @since  ?
     * @since  3.5.0  Changed the visibility scope from protected.
     * @since  3.10.0 Added the `$sLanguageCode` and `$sCurrency` parameter.
     * @since  4.3.0  Moved major part to a separate class.
     */
    public function getProductLinkURLFormatted( $sURL, $sASIN, $sLanguageCode='', $sCurrency='' ) {
        /**
         * Allows third parties to modify the link.
         * @since 3.6.4
         */
        return apply_filters(
           'aal_filter_product_link', // filter hook name
            $sURL,    // filtering value
            $sURL,    // raw URL
            $sASIN,   //
            $this->oUnitOption->get(),
            $sLanguageCode,                             // 4.3.0
            $sCurrency                                  // 4.3.0
        );
    }

    /**
     * @param       array $aItem
     * @return      string
     * @since       3.3.0
     * @since       3.5.0   Renamed from `getContents()`.
     * @since       3.9.0   The EditorialReviews element has gone in PA-API 5.0. So use the Features element.
     * @deprecated  3.9.0   Use the method of `AmazonAutoLinks_Unit_Utility`.
     */
    protected function _getContents( $aItem ) {
        $_aFeatures = $this->getElementAsArray( $aItem, array( 'ItemInfo', 'Features', 'DisplayValues' ) );
        $_sContents = implode( ' ', $_aFeatures );
        return "<div class='amazon-product-content'>"
                . $_sContents
            . "</div>";
    }

}