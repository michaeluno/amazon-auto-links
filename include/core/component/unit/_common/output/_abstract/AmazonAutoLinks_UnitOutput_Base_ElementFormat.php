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

        // Second Iteration - format items and access custom database table.
        foreach( $aProducts as $_iIndex => &$_aProduct ) {

            try {
                $_aProduct = $this->___getProductFormatted(
                    $_aProduct,
                    $_aDBProductRows,
                    $sLocale,
                    $sAssociateID
                );
            } catch ( Exception $_oException ) {
                unset( $aProducts[ $_iIndex ] );
                continue;
            }

            // Item
            $_oItemFormatter = new AmazonAutoLinks_UnitOutput__ItemFormatter(
                $this, $_aProduct
            );
            $_aProduct[ 'formatted_item' ] = $_oItemFormatter->get();
            $_aProduct[ 'formed_item' ]    = $_aProduct[ 'formatted_item' ];   // backward compatibility

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
            $_oProducts = new AmazonAutoLinks_ProductDatabase_Rows( $aASINLocales );
            return $_oProducts->get();
        }
        /**
         * @param       array       $_aProduct
         * @since       3.5.0
         * @throws      Exception
         * @return      array
         */
        private function ___getProductFormatted( $_aProduct, $_aDBProductRows, $sLocale, $sAssociateID ) {

            if ( ! $this->bDBTableAccess ) {
                return $_aProduct;
            }
            if ( ! $this->oOption->isAPIConnected() ) {
                return $_aProduct;
            }

            // Price, Rating, Reviews, and Image Sets - these need to access the plugin cache database. e.g. %price%, %rating%, %review%
            $_aDBProductRow                 = $this->___getDBProductRow(
                $_aDBProductRows,
                $_aProduct[ 'ASIN' ],
                $sLocale,
                $sAssociateID // for scheduling a background task when a row is not found
            );

            $_oPriceFormatter                = new AmazonAutoLinks_UnitOutput___ElementFormatter_Price(
                $_aProduct[ 'ASIN' ], $sLocale, $sAssociateID, $_aDBProductRow, $this->oUnitOption
            );
            $_aProduct[ 'price' ]            = $_oPriceFormatter->get();
            $_oUserReviewFormatter           = new AmazonAutoLinks_UnitOutput___ElementFormatter_CustomerReview(
                $_aProduct[ 'ASIN' ], $sLocale, $sAssociateID, $_aDBProductRow, $this->oUnitOption
            );
            $_aProduct[ 'review' ]           = $_oUserReviewFormatter->get();
            $_oUserRatingFormatter           = new AmazonAutoLinks_UnitOutput___ElementFormatter_UserRating(
                $_aProduct[ 'ASIN' ], $sLocale, $sAssociateID, $_aDBProductRow, $this->oUnitOption
            );
            $_aProduct[ 'rating' ]           = $_oUserRatingFormatter->get();
            $_oImageSetFormatter             = new AmazonAutoLinks_UnitOutput___ElementFormatter_ImageSet(
                $_aProduct[ 'ASIN' ], $sLocale, $sAssociateID, $_aDBProductRow, $this->oUnitOption, $_aProduct
            );
            $_aProduct[ 'image_set' ]        = $_oImageSetFormatter->get();
            $_oSimilarItemsFormatter         = new AmazonAutoLinks_UnitOutput__ElementFormatter_SimilarItems(
                $this, $_aProduct[ 'ASIN' ], $sLocale, $sAssociateID, $_aDBProductRow
            );
            $_aProduct[ 'similar_products' ] = $_oSimilarItemsFormatter->get();

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

            // 3.5.0+ the product can be filtered out.
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
             */
            private function ___getDBProductRow( $aDBRows, $sASIN, $sLocale, $sAssociateID='' ) {

                if ( ! $this->bDBTableAccess ) {
                    return array();
                }
                $_aDBProductRow = $this->getElementAsArray( $aDBRows, $sASIN . '_' . $sLocale );

                // Schedule a background task to retrieve the product information.
                if ( $this->___shouldRenewRow( $_aDBProductRow, $sAssociateID ) ) {
                    AmazonAutoLinks_Event_Scheduler::scheduleProductInformation(
                        $sAssociateID,
                        $sASIN,
                        $sLocale,
                        ( integer ) $this->oUnitOption->get( 'cache_duration' ),
                        ( boolean ) $this->oUnitOption->get( '_force_cache_renewal' )
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
     */
    protected function _getProductTitleFormatted( array $aProduct ) {
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
    }        
    
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
                    $aProduct[ 'product_url' ], 
                    $aProduct[ 'title' ], 
                    $aProduct[ 'thumbnail_url' ], 
                    $this->oUnitOption->get( 'image_size' ), 
                    $aProduct[ 'text_description' ] 
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
        return " <a href='{$sReadMoreURL}' target='_blank' rel='nofollow' style='display:inline;'>" 
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
     * Strips HTML tags and sanitizes the product title.
     * @return  string
     */
    protected function _getTitleSanitized( $sTitle ) {

        // $sTitle = strip_tags( $sTitle );

        // removes the heading numbering. e.g. #3: Product Name -> Product Name
        // Do not use "substr($sTitle, strpos($sTitle, ' '))" since some title contains double-quotes and they mess up html formats
        // $sTitle = trim( preg_replace('/#\d+?:\s+?/i', '', $sTitle ) );
        
        $sTitle = apply_filters(
            'aal_filter_unit_product_raw_title', 
            $sTitle
        );
        
        // Title character length
        if ( 0 == $this->oUnitOption->get( 'title_length' ) ) {
            return '';
        }
        if ( 
            $this->oUnitOption->get( 'title_length' ) > 0 
            && $this->getStringLength( $sTitle ) > $this->oUnitOption->get( 'title_length' ) 
        ) {
            $sTitle = $this->getSubstring( $sTitle, 0, $this->oUnitOption->get( 'title_length' ) ) . '...';
        }
        
//        return $sTitle;
        // @todo Examine whether escaping is needed here. The returned value may be used in an attribute.
         return esc_attr( $sTitle );

    }

    /**
     * Formats a button.
     * 
     * @since       3
     * @return      string
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
                    $iButtonType, 
                    $isButtonID, 
                    $sProductURL
                );
            
        }
    }    
        /**
         * @since       3.1.0
         */
        protected function _getLinkButton( $iButtonType, $isButtonID, $sProductURL ) {
            $sProductURL = esc_url( $sProductURL );
            return "<a href='{$sProductURL}' target='_blank' rel='nofollow'>"
                    . $this->getButton( $isButtonID )
                . "</a>";            
            
        }
        /**
         * Returns an add to cart button.
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
            return "<a href='{$_sGETFormURL}' target='_blank' rel='nofollow'>"
                    . $this->getButton( $isButtonID )
                . "</a>";            
      
        }

    /**
     * Formats the given url such as adding associate ID, ref=nosim, and link style.
     * 
     * @return      string
     * @since       unknown
     * @since       3.5.0       Changed the visibility scope from protected.
     * @remark      The similarity product formatter class accesses it.
     */
    public function getProductLinkURLFormatted( $sURL, $sASIN ) {

        $_sStyledURL = $this->___getFormattedProductLinkByStyle(
            $sURL, 
            $sASIN, 
            $this->oUnitOption->get( 'link_style' ), 
            $this->oUnitOption->get( 'ref_nosim' ), 
            $this->oUnitOption->get( 'associate_id' ), 
            $this->oUnitOption->get( 'country' )
        );
        // 3.6.4+   Allows third parties to modify the link.
        $_sStyledURL = apply_filters(
           'aal_filter_product_link', // filter hook name
            $_sStyledURL,    // filtering value
            $sURL,  // 1st param
            $sASIN, // 2nd param
            $this->oUnitOption->get()    // 3rd param
        );
        return esc_url( $_sStyledURL );
            
    }
        /**
         * A helper function for the above `getProductLinkURLFormatted()` method.
         * 
         * @remark      $iStyle should be 1 to 5 indicating the url style of the link.
         * @return      string
         */
        private function ___getFormattedProductLinkByStyle( $sURL, $sASIN, $iStyle=1, $bRefNosim=false, $sAssociateID='', $sLocale='US' ) {
            
            $iStyle = $iStyle
                ? ( integer ) $iStyle 
                : 1;
            $_sClassName = "AmazonAutoLinks_Output_Format_LinksStyle_{$iStyle}";
            $_oLinksTyle = new $_sClassName(
                $bRefNosim,
                $sAssociateID,
                $sLocale
            );
            $_sURL = $_oLinksTyle->get(
                $sURL,
                $sASIN
            );
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
     * 
     */
    protected function _getContents( $aItem ) {

        $_aEditorialReviews = $this->getElementAsArray( 
            $aItem,
            array( 'EditorialReviews', 'EditorialReview' )
        );
                
        $_oContentFormatter = new AmazonAutoLinks_UnitOutput__Format_content( 
            $_aEditorialReviews,
            $this->oDOM,
            $this->oUnitOption
        );
        $_sContents = $_oContentFormatter->get();                
        
        return "<div class='amazon-product-content'>"
                . $_sContents
            . "</div>";

    }
     
}