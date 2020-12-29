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
 * One of the base classes for unit classes.
 * 
 * Provides shared methods and properties relating formatting product elements.
 *
 * @since       3
 *
 */
abstract class AmazonAutoLinks_UnitOutput_Base_ElementFormat extends AmazonAutoLinks_UnitOutput_Base_ProductFilter {
        
    /**
     * @return      array
     * @since       unknown
     * @since       3.5.0   Renamed from `_formatProducts()`.
     * @param       array   $aProducts
     * @param       array   $aASINLocaleCurLangs
     * @param       string  $sLocale
     * @param       string  $sAssociateID
     */
    protected function _getProductsFormatted( array $aProducts, array $aASINLocaleCurLangs, $sLocale, $sAssociateID ) {

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
                $_aProduct,
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

            // Even the API is disconnected, it should return the cache
//            if ( ! $this->oOption->isAPIConnected() ) {
//                return $_aProduct;
//            }

            // Rating and Reviews - these need to access the plugin cache database. e.g. %rating%, %review%
            $_aDBProductRow                 = $this->___getDBProductRow(
                $_aDBProductRows,
                $_aProduct[ 'ASIN' ],
                $sLocale,
                $sAssociateID, // for scheduling a background task when a row is not found
                $_sCurrency,
                $_sLanguage
            );

            $_oPriceFormatter                = new AmazonAutoLinks_UnitOutput___ElementFormatter_Price(
                $_aProduct[ 'ASIN' ], $sLocale, $sAssociateID, $_aDBProductRow, $this->oUnitOption, $_aProduct
            );
            // @since 4.0.0 the key `price` is deprecated and replaced with `formatted_price`, to be compatible with database table column keys.
            $_aProduct[ 'formatted_price' ]  = $_oPriceFormatter->get(); // 4.0.0

            $_oUserReviewFormatter           = new AmazonAutoLinks_UnitOutput___ElementFormatter_CustomerReview(
                $_aProduct[ 'ASIN' ], $sLocale, $sAssociateID, $_aDBProductRow, $this->oUnitOption
            );
            $_aProduct[ 'review' ]           = $_oUserReviewFormatter->get();

            $_oUserRatingFormatter           = new AmazonAutoLinks_UnitOutput___ElementFormatter_UserRating(
                $_aProduct[ 'ASIN' ], $sLocale, $sAssociateID, $_aDBProductRow, $this->oUnitOption, $_aProduct
            );
            $_aProduct[ 'formatted_rating' ] = $_oUserRatingFormatter->get();

            $_oImageSetFormatter             = new AmazonAutoLinks_UnitOutput___ElementFormatter_ImageSet(
                $_aProduct[ 'ASIN' ], $sLocale, $sAssociateID, $_aDBProductRow, $this->oUnitOption, $_aProduct
            );
            $_aProduct[ 'image_set' ]        = $_oImageSetFormatter->get();

            // 3.9.0 Deprecated
            $_aProduct[ 'similar_products' ] = '';

            // 3.8.0 adding `feature`, `category`, `rank`
            // for search-type units, the value is already assigned
            $_oFeatureFormatter         = new AmazonAutoLinks_UnitOutput___ElementFormatter_Features(
                $_aProduct[ 'ASIN' ], $sLocale, $sAssociateID, $_aDBProductRow, $this->oUnitOption, $_aProduct
            );
            $_aProduct[ 'feature' ]     = $_oFeatureFormatter->get();

            $_oCategoryFormatter         = new AmazonAutoLinks_UnitOutput___ElementFormatter_Categories(
                $_aProduct[ 'ASIN' ], $sLocale, $sAssociateID, $_aDBProductRow, $this->oUnitOption, $_aProduct
            );
            $_aProduct[ 'category' ]     = $_oCategoryFormatter->get();
            $_oSalesRankFormatter        = new AmazonAutoLinks_UnitOutput___ElementFormatter_SalesRank(
                $_aProduct[ 'ASIN' ], $sLocale, $sAssociateID, $_aDBProductRow, $this->oUnitOption, $_aProduct
            );
            $_aProduct[ 'sales_rank' ]   = $_oSalesRankFormatter->get();

            // 3.10.0
            $_sNativeTitle = $_aProduct[ 'title' ];
            $_oTitleFormatter            = new AmazonAutoLinks_UnitOutput___ElementFormatter_Title(
                $_aProduct[ 'ASIN' ], $sLocale, $sAssociateID, $_aDBProductRow, $this->oUnitOption, $_aProduct
            );
            $_aProduct[ 'title' ]        = $_oTitleFormatter->get();
            if ( $_sNativeTitle !== $_aProduct[ 'title' ] ) {
                $_aProduct[ 'formatted_title' ] = $this->getProductTitleFormatted( $_aProduct, $this->oUnitOption->get( 'title_format' ) );
            }

            // 4.2.8
            $_sNativeThumbnailURL         = $_aProduct[ 'thumbnail_url' ];
            $_aProduct[ 'thumbnail_url' ] = isset( $_aProduct[ 'thumbnail_url' ] )
                ? $_aProduct[ 'thumbnail_url' ]
                : $this->getElement( $_aDBProductRow, array( 'images', 'main', 'MediumImage' ), '' );
            if ( $_sNativeThumbnailURL !== $_aProduct[ 'thumbnail_url' ] ) {
                $_aProduct[ 'formatted_thumbnail' ] = $this->_getProductThumbnailFormatted( $_aProduct );
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
     * @param       array       $aProduct
     * @since       2.1.1
     * @since       3.5.0       Renamed from `_formatProductThumbnail()`.
     * @return      string
     */
    protected function _getProductThumbnailFormatted( array $aProduct ) {

        $_iImageSize = $this->oUnitOption->get( 'image_size' );
        return isset( $aProduct[ 'thumbnail_url' ] )
            ? str_replace( 
                array( 
                    "%href%", 
                    "%title_text%", 
                    "%src%", 
                    "%max_width%",          // @deprecated 4.1.0 Kept for backward-compatibility
                    "%description_text%",
                    "%image_size%",         // 4.1.0
                ),
                array( 
                    esc_url( $aProduct[ 'product_url' ] ),
                    esc_attr( strip_tags( $aProduct[ 'title' ] ) ),
                    $aProduct[ 'thumbnail_url' ], 
                    $_iImageSize,
                    esc_attr( $aProduct[ 'text_description' ] ),
                    $_iImageSize    // 4.1.0
                ),
                $this->oUnitOption->get( 'image_format' )
            ) 
            : '';            
        
    }

    /**
     * Strips tags and truncates the given string.
     *
     * @param  string              $sDescription
     * @param  null|integer|double $nMaxLength    A numeric value that determines the length.
     * @param  string              $sReadMoreText
     * @return string
     * @since  unknown
     * @since  3.3.0               Renamed from `sanitizeDescription()`.
     */
    protected function _getDescriptionSanitized( $sDescription, $nMaxLength=null, $sReadMoreText='' ) {

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
     * @param       string  The url to be linked.
     * @return      string
     * @since       3.3.0
     */
    protected function _getReadMoreText( $sReadMoreURL ) {
        
        if ( ! $sReadMoreURL ) {
            return '';
        }
        $sReadMoreURL = esc_url( $sReadMoreURL );
        
        $_sText = $this->oUnitOption->get( 'description_suffix' );
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
     * @param       string  $sTitle
     * @remark      Used for sorting as well.
     * @since       3
     * @return      string
     */
    public function replyToModifyRawTitle( $sTitle ) {
        
        $sTitle = strip_tags( ( string ) $sTitle );
        
        // Remove heading numbering e.g. #2. Product name
        return $this->oUnitOption->get( 'keep_raw_title' )
            ? $sTitle
            : trim( preg_replace( '/#\d+?:\s+?/i', '', $sTitle ) );
            
            
    }

    /**
     * Formats a button.
     *
     * @param integer        $iButtonType
     * @param integer|string $isButtonID
     * @param string         $sProductURL
     * @param string         $sASIN
     * @param string         $sLocale
     * @param string         $sAssociateID
     * @param string         $sAccessKey
     * @param string         $nsButtonLabelToOverride
     *
     * @return      string
     * @since       3
     */
    protected function _getButton( $iButtonType, $isButtonID, $sProductURL, $sASIN, $sLocale, $sAssociateID, $sAccessKey, $nsButtonLabelToOverride=null ) {
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
     * @remark      The similarity product formatter class accesses this method.
     * @param string $sURL
     * @param string $sASIN
     * @param string $sLanguageCode
     * @param string $sCurrency
     * @return      string
     * @since       unknown
     * @since       3.5.0       Changed the visibility scope from protected.
     * @since       3.10.0      Added the `$sLanguageCode` and `$sCurrency` parameter.
     * @since       4.3.0       Moved major part to a separate class.
     */
    public function getProductLinkURLFormatted( $sURL, $sASIN, $sLanguageCode='', $sCurrency='' ) {

        // 3.6.4+   Allows third parties to modify the link.
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

        // @deprecated 3.9.0    EditorialReviews no longer exist in the response of PA-API 5.0.
/*        $_aEditorialReviews = $this->getElementAsArray(
            $aItem,
            array( 'EditorialReviews', 'EditorialReview' )
        );
        $_oContentFormatter = new AmazonAutoLinks_UnitOutput__Format_content( 
            $_aEditorialReviews,
            $this->oDOM,
            $this->oUnitOption
        );
        $_sContents = $_oContentFormatter->get();*/

        $_aFeatures = $this->getElementAsArray( $aItem, array( 'ItemInfo', 'Features', 'DisplayValues' ) );
        $_sContents = implode( ' ', $_aFeatures );
        return "<div class='amazon-product-content'>"
                . $_sContents
            . "</div>";

    }

}