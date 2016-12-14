<?php
/**
 * Amazon Auto Links
 * 
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2016 Michael Uno
 * 
 */

/**
 * One of the base classes for unit classes.
 * 
 * Provides shared methods and properties relating formatting product elements.
 *
 * @since       3
 * 
 * @filter      apply       aal_filter_unit_product_formatted_html
 * @filter      apply       aal_filter_unit_each_product_with_database_row
 */
abstract class AmazonAutoLinks_UnitOutput_Base_ElementFormat extends AmazonAutoLinks_UnitOutput_Base_ProductFilter {
        
    /**
     * @filter      apply       aal_filter_unit_each_product_with_database_row
     * @return      array
     */
    protected function _formatProducts( array $aProducts, array $aASINLocales, $sLocale, $sAssociateID ) {
        
        // If the user wants elements which need to access the custom database table,
        // Retrieve all the data at once to save the number of database queries.
        $_aDBProductRows = $this->bDBTableAccess
            ? $this->_getProductsFromCustomDBTable( $aASINLocales )
            : array();

        // Second Iteration - format items and access custom database table.
        foreach( $aProducts as &$_aProduct ) {

            // Price, Rating, Reviews, and Image Sets - these need to access the plugin cache database. e.g. %price%, %rating%, %review%                
            $_aDBProductRow = $this->_getDBProductRow(
                $_aDBProductRows,
                $_aProduct[ 'ASIN' ],
                $sLocale,
                $sAssociateID // for scheduling a background task when a row is not found
            );

            if ( $this->bDBTableAccess ) {
                
                $_aProduct[ 'price' ]     = $this->formatPrices( 
                    $_aProduct[ 'ASIN' ], 
                    $sLocale, 
                    $sAssociateID,
                    $_aDBProductRow
                );
                $_aProduct[ 'review' ]    = $this->formatUserReview( 
                    $_aProduct[ 'ASIN' ], 
                    $sLocale, 
                    $sAssociateID,
                    $_aDBProductRow
                );
                $_aProduct[ 'rating' ]    = $this->formatUserRating( 
                    $_aProduct[ 'ASIN' ], 
                    $sLocale, 
                    $sAssociateID,
                    $_aDBProductRow
                );
                $_aProduct[ 'image_set' ] = $this->formatImageSet( 
                    $_aProduct[ 'ASIN' ], 
                    $sLocale, 
                    $sAssociateID, 
                    $_aProduct[ 'product_url' ], 
                    $_aProduct[ 'formatted_title' ],
                    $this->oUnitOption->get( 'subimage_size' ),
                    $this->oUnitOption->get( 'subimage_max_count' ),
                    $_aDBProductRow
                );
                
                $_aProduct[ 'similar_products' ] = $this->getSimilarProductsFormatted(
                    $_aProduct[ 'ASIN' ], 
                    $sLocale, 
                    $sAssociateID,
                    $_aDBProductRow                
                );
                
                // This lets unit types that need to use the data from database rows access them.
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
                
            }              
        
            // Item        
            $_aProduct[ 'formatted_item' ] = $this->_formatProductOutput( 
                $_aProduct 
            );

            $_aProduct[ 'formed_item' ]    = $_aProduct[ 'formatted_item' ];   // backward compatibility

        }
        return $aProducts;
        
    }    
    
    /**
     * 
     * @since       3.3.0
     * @return      string
     */    
    public function getSimilarProductsFormatted( $sASIN, $sLocale, $sAssociateID, array $aRow ) {

        $_bEnabledSimilarProducts = $this->_hasCustomVariable( 
            $this->oUnitOption->get( 'item_format' ),
            apply_filters(
                'aal_filter_item_format_database_query_variables',
                array( '%similar%' )
            )
        );    
        if ( ! $_bEnabledSimilarProducts ) {
            return '<!-- Similar products are not enabled -->';
        }
    
        $_aSimilarProducts = $this->_getValueFromRow( 
            'similar_products', // column name
            $aRow, // row
            null,  // default
            array( // schedule task
                'asin'          => $sASIN, 
                'locale'        => $sLocale, 
                'associate_id'  => $sAssociateID,
            )               
        );
        if ( null === $_aSimilarProducts && $this->oUnitOption->get( '_search_similar_products' ) ) {
            AmazonAutoLinks_Event_Scheduler::getProductInfo(
                $sAssociateID,
                $sASIN,
                $sLocale, 
                ( int ) $this->oUnitOption->get( 'cache_duration' )
            );
            return $this->oUnitOption->get( 'show_now_retrieving_message' )
                ? '<p>' 
                    . __( 'Now retrieving similar products.', 'amazon-auto-links' ) 
                . '</p>'
                : '';            
            
        }
        
        $_aOutputs = $this->_getSimilarProductOutputs( $_aSimilarProducts );
    
        if ( empty( $_aOutputs ) ) {
            return '<!-- No similar products for '  . $sASIN . ' -->';
        }
        
        return "<div class='amazon-similar-products'>"
                . implode( '', $_aOutputs )
            . "</div>";
            
    }    
        /**
         * @return      array
         * @since       3.3.0
         */
        private function _getSimilarProductOutputs( $_aSimilarProducts ) {

            $_iImageSIze        = $this->oUnitOption->get( 'similar_product_image_size' );
            $_iMaxCount         = $this->oUnitOption->get( 'similar_product_max_count' );
        
            // By setting 0 or below to the image size, the user can disable the similar products.
            if ( 0 >= $_iImageSIze ) {
                return array();
            }
        
            $_aOutputs = array();
            foreach( $this->getAsArray( $_aSimilarProducts ) as $_iIndex => $_aProduct ) {
                
                if ( $_iIndex >= $_iMaxCount ) {
                    break;
                }
                
                $_sOutput = $this->_getSimilarProductEach( $_aProduct, $_iImageSIze, $_iMaxCount );
                if ( ! $_sOutput ) {
                    continue;
                }
                $_aOutputs[] = $_sOutput;
                
            }               
            return $_aOutputs;
            
        }
            /**
             * @since       3.3.0
             * @return      string
             */
            private function _getSimilarProductEach( $_aProduct, $_iImageSize ) {
                
                $_sThumbnailURL     = $this->_getProductImageURLFormatted( 
                    $this->getElement(
                        $_aProduct,
                        array( 'MediumImage', 'URL' )
                    ), 
                    $_iImageSize 
                );
                if ( ! $_sThumbnailURL ) {
                    return '';
                }
                
                if ( ! $this->_isNoImageAllowed( $_sThumbnailURL ) ) {
                    return '';
                }
                
                $_sTitle        = $this->getElement(
                    $_aProduct,
                    array( 'ItemAttributes', 'Title' )
                );
                if ( $this->isTitleBlocked( $_sTitle ) ) {
                    return '';
                }
                
                $_sASIN         = $this->getElement(
                    $_aProduct,
                    array( 'ASIN' )
                );
                if ( $this->isASINBlocked( $_sASIN ) ) {
                    return '';
                }
                
                $_sProductURL   = $this->getProductLinkURLFormatted( 
                    rawurldecode( $this->getElement( $_aProduct, 'DetailPageURL' ) ),
                    $_sASIN
                );

                $_sProductURL   = esc_url( $_sProductURL );
                $_sThumbnailURL = esc_url( $_sThumbnailURL );
                $_sTitle        = esc_attr( $_sTitle );
                return "<div class='amazon-similar-product' style='max-height: {$_iImageSize}px; max-width: {$_iImageSize}px;'>"
                        . "<a href='{$_sProductURL}' target='blank'>"
                            . "<img class='amazon-similar-product-thumbnail' src='{$_sThumbnailURL}' title='{$_sTitle}' alt='{$_sTitle}' style='max-height: {$_iImageSize}px;' />"
                        . "</a>"
                    . "</div>";

            }
    
    /**
     * Returns the formatted product HTML block.
     * @since       2.1.1
     * @filter      apply       aal_filter_unit_product_formatted_html
     * @filter      apply       aal_filter_unit_item_format
     * @return      string
     */
    protected function _formatProductOutput( array $aProduct ) {

        $_sOutput = str_replace( 
            array( 
                "%href%", 
                "%title_text%", 
                "%description_text%", 
                "%title%", 
                "%image%", 
                "%description%",
                "%rating%",
                "%review%",
                "%price%",
                "%button%",
                "%image_set%",
                "%disclaimer%", // 3.2.0+
                "%content%", // 3.3.0+
                "%meta%",   // 3.3.0+
                "%similar%",   // 3.3.0+
            ),
            array( 
                $aProduct[ 'product_url' ], 
                $aProduct[ 'title' ], 
                $aProduct[ 'text_description' ],
                $aProduct[ 'formatted_title' ], 
                $aProduct[ 'formatted_thumbnail' ],
                $aProduct[ 'description' ],
                $aProduct[ 'rating' ],
                $aProduct[ 'review' ],
                $aProduct[ 'price' ],
                $aProduct[ 'button' ],
                $aProduct[ 'image_set' ],
                $this->_getPricingDisclaimer( $aProduct[ 'updated_date' ] ), // 3.2.0+
                $aProduct[ 'content' ], // 3.3.0+
                $aProduct[ 'meta' ], // 3.3.0+
                $aProduct[ 'similar_products' ], // 3.3.0+
            ),
            apply_filters(
                'aal_filter_unit_item_format',
                $this->oUnitOption->get( 'item_format' ),
                $aProduct,
                $this->oUnitOption->get()
            )
        );

        return apply_filters(
            'aal_filter_unit_product_formatted_html',    // filter hook name
            $_sOutput, // filtering value
            $aProduct[ 'ASIN' ], // parameter 1
            $this->oUnitOption->get( 'country' ) // additional parameter 2
        );
    }        
    
        /**
         * @since       3.2.0
         * @return      string
         */
        protected function _getPricingDisclaimer( $sResponseDate ) {
            
            return "<span class='pricing-disclaimer'>"
                . "(" 
                    . sprintf(
                        __( 'as of %1$s', 'amazon-auto-links' ),
                        $this->getSiteReadableDate( strtotime( $sResponseDate ) )
                    )
                    . ' - '
                    . $this->_getDisclaimerTooltip()
                . ")"
                . "</span>";
        }            
            /**
             * @since       3.2.0
             * @return      string
             */
            private function _getDisclaimerTooltip() {
                return "<a href='#' class='amazon-disclaimer-tooltip'>"
                        . __( 'More info', 'amazon-auto-links' )
                        . "<span class='amazon-disclaimer-tooltip-content'>"
                            . "<span class='amazon-disclaimer-tooltip-content-text'>"   // needed for widget CSS 
                                . __( "Product prices and availability are accurate as of the date/time indicated and are subject to change. Any price and availability information displayed on [relevant Amazon Site(s), as applicable] at the time of purchase will apply to the purchase of this product.", 'amazon-auto-links' )
                            . "</span'>"
                        . "</span>"
                    . "</a>";                
            }
        

    /**
     * Returns the formatted product title HTML Block.
     * @since       2.1.1
     */
    protected function _formatProductTitle( array $aProduct ) {
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
     */
    protected function _formatProductThumbnail( array $aProduct ) {
        
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
     * 
     * @since       unknown
     * @since       2.1.1       Changed the name from `formatImage()`. Changed the scope from protected to private.
     */
    protected function _getProductImageURLFormatted( $sImageURL, $isImageSize ) {
        
        // If no product image is found
        if ( ! $sImageURL ) {
            $_sLocale  = strtoupper( $this->oUnitOption->get( 'country' ) );
            $sImageURL = isset( AmazonAutoLinks_Property::$aNoImageAvailable[ $_sLocale ] )
                ? AmazonAutoLinks_Property::$aNoImageAvailable[ $_sLocale ]
                : AmazonAutoLinks_Property::$aNoImageAvailable[ 'US' ];
        }
        
        if ( $this->bIsSSL ) {
            $sImageURL = $this->getAmazonSSLImageURL( $sImageURL );
        }
        
        return $this->setImageSize( $sImageURL, $isImageSize );
        
    }    
    
    
    /**
     * Strips tags and truncates the given string.
     * 
     * @since       unknown
     * @since       3.3.0       Renamed from `sanitizeDecription()`.
     */
    protected function getDescriptionSanitized( $sDescription, $nMaxLength=null, $sReadMoreText='' ) {

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
     * @since       3.3.0
     * @return      string
     * @deprecated  Not used at the moment.
     */
/*     protected function getHTMLStringTruncated( $sHTMLDescription, $iMaxLength=-1, $sReadMore='' ) {
       
        $iMaxLength = ( integer ) $iMaxLength;
        $_oTruncator = new AmazonAutoLInks_HTMLStringTruncator;
        if ( -1 !== $iMaxLength ) {
            $sHTMLDescription = $_oTruncator->getTrimmed( $sHTMLDescription, $iMaxLength, $sReadMore );
        }
        return $sHTMLDescription;
        
    } */
    
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
     * 
     */
    protected function getTitleSanitized( $sTitle ) {

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
        
        // return $sTitle;
        return esc_attr( $sTitle );

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
     */
    protected function getASIN( $sURL ) {
        
        $sURL = remove_query_arg( 
            array( 'smid', 'pf_rd_p', 'pf_rd_s', 'pf_rd_t', 'pf_rd_i', 'pf_rd_m', 'pf_rd_r' ),
            $sURL
        );
        
        $sURL = preg_replace(
            array(
                '/[A-Z0-9]{11,}/',  // Remove strings like an ASIN but with more than 10 characters.
                '/[0-9]{10}/',      // Remove ones consisting of only numbers.
            ), 
            '', 
            $sURL
        );
        
        preg_match( 
            '/(dp|gp|e)\/(.+\/)?([A-Z0-9]{10})(\/|$|\?)/', // needle - [A-Z0-9]{10} is the ASIN
            $sURL,  // subject
            $_aMatches // match container
        );
        return isset( $_aMatches[ 3 ] ) 
            ? $_aMatches[ 3 ] 
            : '';
    }
    
    /**
     * Returns the resized image url.
     * 
     * @rmark       Adjusts the image size. _SL160_ or _SS160_
     * @return      string
     * @param       $sImgURL        string
     * @param       $iImageSize     integer     0 to 500.
     */
    protected function getImageURLBySize( $sImgURL, $iImageSize ) {
        return preg_replace( 
            '/(?<=_S)([LS])(\d{1,3})(?=_)/i', 
            '${1}'. $iImageSize, 
            $sImgURL 
       );          
    }    
        /**
         * @deprecated
         */
        protected function setImageSize( $sImgURL, $iImageSize ) {
            return $this->getImageURLBySize( $sImgURL, $iImageSize );
        }
    
        /**
         * @deprecated
         */
        protected function respectSSLImage( $sImgURL ) {
            return $this->getAmazonSSLImageURL( $sImgURL );
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
            return "<a href='{$sProductURL}' target='_blank'>"
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
            return "<a href='{$_sGETFormURL}' target='_blank'>"
                    . $this->getButton( $isButtonID )
                . "</a>";            
      
        }
    
    /**
     * Formats the given url such as adding associate ID, ref=nosim, and link style.
     * 
     * @return      string
     */
    protected function getProductLinkURLFormatted( $sURL, $sASIN ) {

        $_sStyledURL = $this->getFormattedProductLinkByStyle( 
            $sURL, 
            $sASIN, 
            $this->oUnitOption->get( 'link_style' ), 
            $this->oUnitOption->get( 'ref_nosim' ), 
            $this->oUnitOption->get( 'associate_id' ), 
            $this->oUnitOption->get( 'country' )
        );
        return esc_url( $_sStyledURL );
            
    }
        /**
         * A helper function for the above getProductLinkURLFormatted() method.
         * 
         * @remark      $iStyle should be 1 to 5 indicating the url style of the link.
         * @return      string
         */
        protected function getFormattedProductLinkByStyle( $sURL, $sASIN, $iStyle=1, $bRefNosim=false, $sAssociateID='', $sLocale='US' ) {
            
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
     * 
     * @since       3
     * @return      string
     */
    public function formatPrices( $sASIN, $sLocale, $sAssociateID, array $aRow ) {

        $_sPriceFormatted = $this->_getValueFromRow( 
            'price_formatted', 
            $aRow, 
            null,   // default
            array( 'asin' => $sASIN, 'locale' => $sLocale, 'associate_id'  => $sAssociateID, )
        );
        
        // If a price is not found, return a message or an empty string.
        if ( null === $_sPriceFormatted ) {
            return $this->oUnitOption->get( 'show_now_retrieving_message' )
            ? '<p>' 
                . __( 'Now retrieving the price.', 'amazon-auto-links' )
                . '</p>'
            : '';
        }
        
        // At this point, a price for the product is found.
        return $this->___getPriceOutput( $_sPriceFormatted, $sASIN, $sLocale, $sAssociateID, $aRow );

    }
        /**
         * @since       3.4.11
         * @return      string
         */
        private function ___getPriceOutput( $_sPriceFormatted, $sASIN, $sLocale, $sAssociateID, $aRow ) {

            // Check if there is a discounted price.
            $_inPrice                = $this->_getValueFromRow(
                'price',
                $aRow,
                null,   // default
                array( 'asin' => $sASIN, 'locale' => $sLocale, 'associate_id'  => $sAssociateID, )
            );
            $_inLowestNew            = $this->_getValueFromRow(
                'lowest_new_price',
                $aRow,
                null,   // default
                array( 'asin' => $sASIN, 'locale' => $sLocale, 'associate_id'  => $sAssociateID, )
            );
            $_inDiscount            = $this->_getValueFromRow(
                'discounted_price',
                $aRow,
                null,   // default
                array( 'asin' => $sASIN, 'locale' => $sLocale, 'associate_id'  => $sAssociateID, )
            );

            $_inOffered             = $this->___getLowestPrice( $_inLowestNew, $_inDiscount );
            $_sOfferedFormatted     = $this->_getValueFromRow(
                $_inDiscount === $_inOffered ? 'discounted_price_formatted' : 'lowest_new_price_formatted',
                $aRow,
                null,   // default
                array( 'asin' => $sASIN, 'locale' => $sLocale, 'associate_id'  => $sAssociateID, )
            );
            return $this->___isPriceDiscounted( $_inPrice, $_inOffered )
                ? '<s>' . $_sPriceFormatted . '</s> ' . $_sOfferedFormatted
                : $_sPriceFormatted;

        }
            /**
             * @param integer $_iLowestNew
             * @param integer $_iDiscount
             *
             * @return integer|null
             * @since  3.4.11
             */
            private function ___getLowestPrice( $_iLowestNew, $_iDiscount ) {
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
             * @since       3.4.3
             * @since       3.4.11      Renamed the name from `_isPriceDiscounted()`.
             * @return      boolean
             */
            private function ___isPriceDiscounted( $iPrice, $iOfferedPrice ) {
                if ( null === $iOfferedPrice ) {
                    return false;
                }
                return ( $iPrice > $iOfferedPrice );
            }
    
    /**
     * 
     * @since       3
     * @return      string
     */
    public function formatImageSet( $sASIN, $sLocale, $sAssociateID, $sProductURL, $sTitle, $iMaxImageSize=100, $iMaxNumberOfImages=5, array $aRow=array() ) {

        $_asImages = $this->_getValueFromRow(
            'images', // column name
            $aRow, // row
            null, // default
            array(
                'asin'          => $sASIN,
                'locale'        => $sLocale,
                'associate_id'  => $sAssociateID,
            )
        );       
        
        // When the DB row is not set, the value will be null.
        if ( null === $_asImages ) {            
            return $this->oUnitOption->get( 'show_now_retrieving_message' )
                ? '<p>'
                        . __( 'Now retrieving an image set.', 'amazon-auto-links' )
                    . '</p>'
                : '';
        }
  
        $sTitle    = strip_tags( $sTitle );
  
        // Extract image urls
        $_aImageURLs = $this->_getSubImageURLs( 
            $_asImages, 
            $iMaxImageSize, 
            $iMaxNumberOfImages
        );

        $_aSubImageTags = array();
        foreach( $_aImageURLs as $_sImageURL ) {
            $_sImageTag = $this->generateHTMLTag(
                'img',
                array(
                    'src'   => esc_url( $_sImageURL ),
                    'class' => 'sub-image',
                    'alt'   => $sTitle,
                )
            );
            $_sATag     = $this->generateHTMLTag(
                'a',
                array(
                    'href'   => esc_url( $sProductURL ),
                    'target' => '_blank',
                    'title'  => $sTitle,
                ),
                $_sImageTag
            );
            $_aSubImageTags[] = $this->generateHTMLTag(
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
        private function _getSubImageURLs( array $aImages, $iMaxImageSize, $iMaxNumberOfImages ) {
            
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
                $_aURLs[] = $this->_getImageURLFromResposeElement( 
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
            private function _getImageURLFromResposeElement( array $aImage, $iImageSize ) {
                 
                $_sURL = '';
                foreach( $aImage as $_sKey => $_aDetails ) {
                    $_sURL = $this->getElement(
                        $_aDetails, // subject array
                        array( 'URL' ), // dimensional key
                        ''  // default
                    );                    
                    if ( $_sURL ) {
                        break;
                    }
                }
                $_sURL = $this->getImageURLBySize(
                    $_sURL,
                    $iImageSize
                );
                return $this->bIsSSL
                    ? $this->getAmazonSSLImageURL( $_sURL )
                    : $_sURL;
            }
    
    /**
     * 
     * @since       3
     * @return      string
     */    
    public function formatUserReview( $sASIN, $sLocale, $sAssociateID, array $aRow ) {
        $_sEncodedHTML = $this->_getValueFromRow( 
            'customer_reviews', // column name
            $aRow, // row
            null,  // default
            array( // schedule task
                'asin'          => $sASIN, 
                'locale'        => $sLocale, 
                'associate_id'  => $sAssociateID,
            )               
        );
        if ( null === $_sEncodedHTML ) {
            AmazonAutoLinks_Event_Scheduler::getProductInfo(
                $sAssociateID,
                $sASIN,
                $sLocale, 
                ( int ) $this->oUnitOption->get( 'cache_duration' )
            );
            
            return $this->oUnitOption->get( 'show_now_retrieving_message' )
                ? '<p>' 
                    . __( 'Now retrieving customer reviews.', 'amazon-auto-links' ) 
                . '</p>'
                : '';            
            
        }
        if ( ! $_sEncodedHTML ) {
            return '';
        }
        
        // Now modify the raw output.
        // 1. Remove unnecessary elements.
        $_oScraper  = new AmazonAutoLinks_ScraperDOM_CustomerReview_Each(
            $_sEncodedHTML,
            true,
            $this->oUnitOption->get( 'customer_review_max_count' ),
            $this->oUnitOption->get( 'customer_review_include_extra' )
        );
        return "<div class='amazon-customer-reviews'>" 
                . $_oScraper->get()
            . "</div>";        
        
    }
    /**
     * 
     * @since       3
     * @return      string
     */    
    public function formatUserRating( $sASIN, $sLocale, $sAssociateID, array $aRow=array() ) {
        
        $_sEncodedHTML = $this->_getValueFromRow(
            'rating_html', // column name
            $aRow, // row
            null, // default - will
            array( // shcedule background task
                'asin'          => $sASIN,
                'locale'        => $sLocale,
                'associate_id'  => $sAssociateID,
            )
        );
        // If the value is null, it means the value does not exist in the database table.
        if ( null === $_sEncodedHTML ) {
            AmazonAutoLinks_Event_Scheduler::getProductInfo(
                $sAssociateID,
                $sASIN,
                $sLocale, 
                ( int ) $this->oUnitOption->get( 'cache_duration' )
            );
            
            return $this->oUnitOption->get( 'show_now_retrieving_message' )
                ? '<p>' 
                        . __( 'Now retrieving the rating.', 'amazon-auto-links' ) 
                    . '</p>'
                : '';
            
        }         
        if ( '' === $_sEncodedHTML ) {
            return '';
        }        
        
        // Now modify the raw output.   
        $_oScraper = new AmazonAutoLinks_ScraperDOM_UserRating(
            $_sEncodedHTML,
            true // character set - auto detect
        );
        return "<div class='amazon-customer-rating-stars'>"
                . $_oScraper->get()
            . "</div>";
            
    }
    
    /**
     * @return      string
     * @since       3.3.0
     * 
     */
    protected function getContents( $aItem ) {

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