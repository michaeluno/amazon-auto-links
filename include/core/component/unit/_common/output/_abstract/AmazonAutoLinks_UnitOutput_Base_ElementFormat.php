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
     * @since       3.5.0       Renamed from `_formatProducts()`.
     */
    protected function _getProductsFormatted( array $aProducts, array $aASINLocales, $sLocale, $sAssociateID ) {
        
        $_aDBProductRows = $this->___getProductsRowsFromDatabase( $aASINLocales );
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
            $_aDBProductRow  = $this->getElementAsArray( $_aDBProductRows, $this->getElement( $_aProduct, 'ASIN', '' ) . '_' . $sLocale );
            $_oItemFormatter = new AmazonAutoLinks_UnitOutput__ItemFormatter(
                $this,
                $_aProduct,
                $_aDBProductRow
            );
            $_aProduct[ 'formatted_item' ] = $_oItemFormatter->get();

        }
        return $aProducts;
        
    }
        /**
         * If the user wants elements which need to access the custom database table,
         * retrieve all the products at once to save the number of database queries.
         *
         * @param   array $aASINLocales
         * @return  array
         * @since   3.5.0
         */
        private function ___getProductsRowsFromDatabase( array $aASINLocales ) {
            if ( ! $this->bDBTableAccess ) {
                return array();
            }
//            $_oLocale   = new AmazonAutoLinks_PAAPI50___Locales;
//            $_sLocale   = strtoupper( $this->oUnitOption->get( array( 'country' ), 'US' ) );
            // Not setting a default value here for backward-compatibility with v3.8.x or below
            // which do not have `preferred_currency` and `language` columns
            // as the class queries with these columns while it doesn't when there isn't a value passed.
            $_sCurrency = $this->oUnitOption->get( array( 'preferred_currency' ), '' );
            $_sLanguage = $this->oUnitOption->get( array( 'language' ), '' );
            $_oProducts = new AmazonAutoLinks_ProductDatabase_Rows( $aASINLocales, $_sLanguage, $_sCurrency );
            return $_oProducts->get();
        }
        /**
         * @param       array       $_aProduct
         * @since       3.5.0
         * @since       3.9.0       Added the $_sCurrency, $_sLanguage parameters.
         * @throws      Exception
         * @return      array
         */
        private function ___getProductFormatted( $_aProduct, $_aDBProductRows, $sLocale, $sAssociateID, $_sCurrency, $_sLanguage ) {

            if ( ! $this->bDBTableAccess ) {
                return $_aProduct;
            }

            // @todo even the API is disconnected, it should return the cache
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
            // @deprecated 4.0.0 the key `price` is replaced with `formatted_price`, to be compatible with database table column keys.
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
//            $_oSimilarItemsFormatter         = new AmazonAutoLinks_UnitOutput__ElementFormatter_SimilarItems(
//                $this, $_aProduct[ 'ASIN' ], $sLocale, $sAssociateID, $_aDBProductRow
//            );
//            $_aProduct[ 'similar_products' ] = $_oSimilarItemsFormatter->get();
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
             * @remark      The keys of the database rows must be formatted to have {asin}_{locale}.
             * @return      array
             * @since       3
             * @since       3.5.0       Changed the visibility scope from protected.
             * @since       3.5.0       Moved from `AmazonAutoLinks_UnitOutput_Base_CustomDBTable`.
             * @since       3.9.0       Added the $_sCurrency, $_sLanguage parameters.
             */
            private function ___getDBProductRow( $aDBRows, $sASIN, $sLocale, $sAssociateID='', $_sCurrency, $_sLanguage ) {

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
                 * @since       3.5.0
                 * @return      boolean
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
     * Returns the formatted product title HTML Block.
     * @since       2.1.1
     * @since       3.5.0       Renamed from `_formatProductTitle()`.
     * @return      string
     * @deprecated  3.10.0
     */
/*    protected function _getProductTitleFormatted( array $aProduct ) {
        return str_replace( 
            array( 
                "%href%", 
                "%title_text%", 
                "%description_text%" 
            ),
            array( 
                $aProduct[ 'product_url' ], 
                $aProduct[ 'title' ], 
                $aProduct[ 'text_description' ]
            ),
            $this->oUnitOption->get( 'title_format' ) 
        );        
    }*/
    
    /**
     * Returns the formatted product thumbnail HTML block.
     * 
     * @since       2.1.1
     * @since       3.5.0       Renamed from `_formatProductThumbnail()`.
     * @return      string
     */
    protected function _getProductThumbnailFormatted( array $aProduct ) {
        
        return isset( $aProduct[ 'thumbnail_url' ] )
            ? str_replace( 
                array( 
                    "%href%", 
                    "%title_text%", 
                    "%src%", 
                    "%max_width%", 
                    "%description_text%" 
                ),
                array( 
                    esc_url( $aProduct[ 'product_url' ] ),
                    esc_attr( strip_tags( $aProduct[ 'title' ] ) ),
                    $aProduct[ 'thumbnail_url' ], 
                    $this->oUnitOption->get( 'image_size' ), 
                    esc_attr( $aProduct[ 'text_description' ] )
                ),
                $this->oUnitOption->get( 'image_format' )
            ) 
            : '';            
        
    }

    /**
     * Strips tags and truncates the given string.
     * 
     * @since       unknown
     * @since       3.3.0       Renamed from `sanitizeDescription()`.
     */
    protected function _getDescriptionSanitized( $sDescription, $nMaxLength=null, $sReadMoreText='' ) {

        $sDescription = strip_tags( $sDescription );
        
        // Title character length
        $nMaxLength = $nMaxLength 
            ? $nMaxLength 
            : $this->oUnitOption->get( 'description_length' );
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
     *
     * @return      string
     * @since       3
     */
    protected function _getButton( $iButtonType, $isButtonID, $sProductURL, $sASIN, $sLocale, $sAssociateID, $sAccessKey ) {
        switch( ( integer ) $iButtonType ) {
            case 1:
                return $this->_getAddToCartButton( 
                    $sASIN, 
                    $sLocale, 
                    $sAssociateID, 
                    $isButtonID, 
                    $sAccessKey
                );
            
            default:
            case 0:
                return $this->_getLinkButton(
                    $isButtonID, $sProductURL
                );
            
        }
    }

        /**
         * @param string|integer $isButtonID
         * @param string         $sProductURL
         *
         * @return string
         * @since       3.1.0
         */
        protected function _getLinkButton( $isButtonID, $sProductURL ) {
            $sProductURL = esc_url( $sProductURL );
            return "<a href='{$sProductURL}' target='_blank' rel='nofollow noopener'>"
                    . $this->getButton( $isButtonID )
                . "</a>";            
            
        }

        /**
         * Returns an add to cart button.
         *
         * @param string $sASIN
         * @param string $sLocale
         * @param string $sAssociateID
         * @param string|integer $isButtonID
         * @param string $sAccessKey
         *
         * @return string
         * @since       3.1.0
         */
        protected function _getAddToCartButton( $sASIN, $sLocale, $sAssociateID, $isButtonID, $sAccessKey='' ) {            
        
            $_sScheme       = is_ssl() ? 'https' : 'http';
            $_sURL          = isset( AmazonAutoLinks_Property::$aAddToCartURLs[ $sLocale ] )
                ? AmazonAutoLinks_Property::$aAddToCartURLs[ $sLocale ]
                : AmazonAutoLinks_Property::$aAddToCartURLs[ 'US' ];        
            $_sGETFormURL = esc_url(
                add_query_arg(                  
                    array(
                        'AssociateTag'      => $sAssociateID,
                        'SubscriptionId'    => $sAccessKey,
                        'AWSAccessKeyId'    => $sAccessKey,
                        'ASIN.1'            => $sASIN,
                        'Quantity.1'        => 1,
                    ),
                    $_sScheme . '://' . $_sURL
                )
            );
            return "<a href='{$_sGETFormURL}' target='_blank' rel='nofollow noopener'>"
                    . $this->getButton( $isButtonID )
                . "</a>";            
      
        }

    /**
     * Formats the given url such as adding associate ID, ref=nosim, and link style.
     *
     * @param string $sURL
     * @param string $sASIN
     * @param string $sLanguageCode
     * @param string $sCurrency
     *
     * @return      string
     * @since       unknown
     * @since       3.5.0       Changed the visibility scope from protected.
     * @remark      The similarity product formatter class accesses it.
     * @since       3.10.0      Added the `$sLanguageCode` and `$sCurrency` parameter.
     */
    public function getProductLinkURLFormatted( $sURL, $sASIN, $sLanguageCode='', $sCurrency='' ) {

        $_sStyledURL = $this->___getFormattedProductLinkByStyle(
            $sURL, 
            $sASIN, 
            $this->oUnitOption->get( 'link_style' ), 
            $this->oUnitOption->get( 'ref_nosim' ), 
            $this->oUnitOption->get( 'associate_id' ), 
            $this->oUnitOption->get( 'country' ),
            $sLanguageCode,
            $sCurrency
        );
        // 3.6.4+   Allows third parties to modify the link.
        $_sStyledURL = apply_filters(
           'aal_filter_product_link', // filter hook name
            $_sStyledURL,    // filtering value
            $sURL,  // 1st param
            $sASIN, // 2nd param
            $this->oUnitOption->get()    // 3rd param
        );

        // @remark 3.10.0 not escaping to avoid multiple escaping.
        return $_sStyledURL;
            
    }
        /**
         * A helper function for the above `getProductLinkURLFormatted()` method.
         * 
         * @remark      $iStyle should be 1 to 5 indicating the url style of the link.
         * @return      string
         */
        private function ___getFormattedProductLinkByStyle( $sURL, $sASIN, $iStyle=1, $bRefNosim=false, $sAssociateID='', $sLocale='US', $sLanguageCode='', $sCurrency='' ) {
            
            $iStyle      = $iStyle ? ( integer ) $iStyle : 1;
            $_sClassName = "AmazonAutoLinks_Output_Format_LinksStyle_{$iStyle}";
            $_oLinkStyle = new $_sClassName(
                $bRefNosim,
                $sAssociateID,
                $sLocale
            );
            $_sURL = $_oLinkStyle->get( $sURL, $sASIN, $sLanguageCode, $sCurrency );
            return str_replace(
                'amazon-auto-links-20',  // dummy url used for a request
                $sAssociateID,
                $_sURL
            );

        }

    /**
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