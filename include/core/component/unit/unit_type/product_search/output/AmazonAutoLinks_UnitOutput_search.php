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
 * Creates Amazon product links by ItemSearch.
 * 
 * @package         Amazon Auto Links
 */
class AmazonAutoLinks_UnitOutput_search extends AmazonAutoLinks_UnitOutput_Base_ElementFormat {
    
    /**
     * Stores the unit type.
     * @remark      Note that the base constructor will create a unit option object based on this value.
     */    
    public $sUnitType = 'search';
    
    /**
     * Stores the unit option key that is used for the search.
     * This is needed for the `search_per_keyword` option.
     * @since       3.2.0
     */
    public $sSearchTermKey = 'Keywords';    

    /**
     * Represents the array structure of the item array element of API response data.
     * @since            unknown
     */    
    public static $aStructure_Item = array(
        'ASIN'              => null,
        'ItemAttributes'    => null,
        'DetailPageURL'     => null,
        'EditorialReviews'  => null,
        'ItemLinks'         => null,
        'ImageSets'         => null,
        'BrowseNodes'       => null,
        'SimilarProducts'   => null,
        'MediumImage'       => null,
        'OfferSummary'      => null,
    );
    
    /**
     * 
     * @return    array    The response array.
     */
    public function fetch( $aURLs=array() ) {
        
        // The search unit type does not use directly passed urls.
        // Maybe later at some point, custom request URIs can get implemented and they can be directly passed to this method.
        unset( $aURLs );
                
        // Get API responses
        $_aResponse = $this->_getResponses();

        $_aError    = $this->_getResponseError( $_aResponse );
        if ( ! empty( $_aError ) ) {
            return $this->oUnitOption->get( 'show_errors' )
                ? $_aError
                : array();
        }
            
        $_aProducts = $this->getProducts( $_aResponse );

        // echo "<pre>" . htmlspecialchars( print_r( $_aResponse[ 'Items' ][ 'Item' ], true ) ) . "</pre>"
        return $_aProducts;
        
    }
        /**
         * @since       3.2.1
         * @return      array
         */
        private function _getResponseError( $aResponse ) {
            
            // HTTP or API Resonse error
            if ( isset( $aResponse[ 'Error' ][ 'Code' ] ) ) {
                return $aResponse;
            }
            
            // Error in the API Requests.
            $_aErrors = $this->getElement( 
                $aResponse,
                array( 'Items', 'Request', 'Errors' )
            );
            if ( ! empty( $_aErrors ) ) {
                
                // Retrun the first error item as the template currently only checks the Error element.
                if ( isset( $_aErrors[ 'Error' ][ 0 ] ) ) {
                    $_aErrors[ 'Error' ] = $_aErrors[ 'Error' ][ 0 ];
                }
                return $_aErrors;
                    
            }       
            return array();
            
        }
        /**
         * @since       3.1.4
         * @scope       protected       The 'url' unit type will extend this method.
         * @return      array
         */
        protected function _getResponses() {

            // Sanitize the search terms
            $this->___setSearchTerms();
     
            if ( ! $this->oUnitOption->get( 'search_per_keyword' ) ) {
                // Normal operation
                return $this->getRequest( $this->oUnitOption->get( 'count' ) );
                
            } 
                
            // For contextual search, perform search by each keyword
            return $this->___getResponsesByMultipleKeywords();
            
        }
            /**
             * Sanitizes the search terms.
             * @since       3.2.0
             * @return      void
             */
            private function ___setSearchTerms() {
                
                $_sTerms    = trim( $this->oUnitOption->get( $this->sSearchTermKey ) );
                if ( ! $_sTerms ) {
                    $this->oUnitOption->set( 'search_per_keyword', false );
                    return;
                }
                $_sTerms    = str_replace(
                    PHP_EOL,
                    ',',
                    $_sTerms
                );
                $_aTerms    =  $this->getStringIntoArray( $_sTerms, ',' );

                /**
                 * When the sort order is `random`, the query items should be shuffled first
                 * because shuffling the retrieved truncated results will just display the same products with different order.
                 * @since   3.5.1
                 */
                if ( 'random' === $this->oUnitOption->get( '_sort' ) ) {
                    shuffle( $_aTerms );
                }

                $_bSearchPerTerm = $this->oUnitOption->get( 'search_per_keyword' );
                if ( count( $_aTerms ) > 10 && ! $_bSearchPerTerm ) {
                     
                    // Auto-truncate search terms to 10 as the Amazon API does not allow more than 10 terms to be set per request.
                    $this->oUnitOption->set( 'search_per_keyword', true );
                    
                    // The above 'search_per_keyword' = false will trigger `___getResponsesByMultipleKeywords()` 
                    // so an array can be set for the terms. 
                    $this->oUnitOption->set( 
                        $this->sSearchTermKey,  // ItemId
                        array_chunk( $_aTerms, 10 )
                    );                    
                    
                } else {
                                    
                    $this->oUnitOption->set( 
                        $this->sSearchTermKey,  
                        implode( ',', $_aTerms ) 
                    );
                    
                }
                
            }        
            /**
             * @since       3.2.0
             * @return      array
             */
            private function ___getResponsesByMultipleKeywords() {
             
                $_aItems    = array();
                $_aResponse = array();           
                $_asTerms   = $this->oUnitOption->get( $this->sSearchTermKey );                
                $_aTerms    = is_array( $_asTerms )
                    ? $_asTerms
                    : $this->getStringIntoArray( $_asTerms, ',' );
                
                $_iCount    = $this->_getMaximumCountForSearchPerKeyword( $_aTerms );
                foreach( $_aTerms as $_asSearchTerms ) {
                    
                    $_sSearchTerms = is_scalar( $_asSearchTerms )    
                        ? $_asSearchTerms
                        : implode( ',', $_asSearchTerms );

                    $this->oUnitOption->set( 
                        $this->sSearchTermKey, 
                        // 3.2.1+ Nested array is supported to auto-truncate terms more than 10 as API does not allow it.
                        $_sSearchTerms
                    );
                    $_aResponse = $this->getRequest( $_iCount );

                    $_aItems    = $this->___getItemsMerged( $_aItems, $_aResponse );                    
                    if ( count( $_aItems ) >= $_iCount ) {
                        break;
                    }
                }

                // Up to the set item count.
                array_splice( $_aItems, $_iCount );   
                $this->setMultiDimensionalArray( 
                    $_aResponse,
                    array( 'Items', 'Item' ),
                    $_aItems
                );
                if ( 0 < count( $this->getElementAsArray( $_aResponse, array( 'Items', 'Item' ) ) ) ) {
                    unset(
                        $_aResponse[ 'Error' ][ 'Code' ],
                        $_aResponse[ 'Items' ][ 'Request' ][ 'Errors' ]
                    );
                }            

                return $_aResponse; 
             
            }    

                /**
                 * @return      array
                 */
                private function ___getItemsMerged( $aItems, $aResponse ) {
                    
                    $aItems        = array_merge(
                        $aItems,
                        $this->___getItemsNumericallyIndexed( $aResponse )
                    );
                                             
                    // Drop duplicates.
                    $_aParsedASINs = array();
                    foreach( $aItems as $_iIndex => $_aItem ) {
                        
                        $_sASIN = $this->getElement( $_aItem, 'ASIN' );

                        // In some cases, an empty array can be contained.
                        if ( ! $_sASIN ) {
                            unset( $aItems[ $_iIndex ] );       
                            continue;
                        }
                        
                        // remove the entry as it is a duplicate
                        if ( isset( $_aParsedASINs[ $_sASIN ] ) )  {
                            unset( $aItems[ $_iIndex ] );     
                            continue;
                        }
                               
                        // Set a parsed ASIN
                        $_aParsedASINs[ $_sASIN ] = $_sASIN;
                        
                    }
                    $aItems = array_values( $aItems );
                    return $aItems;
                    
                }            
                /**
                 * If a single item is returned, it is an associative array; otherwize, numeric.
                 * @return      array       Numerically indexed item element.
                 */
                private function ___getItemsNumericallyIndexed( $aResponse ) {
                    
                    $_aItems = $this->getElementAsArray( $aResponse, array( 'Items', 'Item' ) );
                    return $this->isAssociative( $_aItems )
                        ? array( $_aItems )
                        : $_aItems;
                    
                }
                /**
                 * @scope       protected       Item look-up and Similarity look-up will override this method.
                 * @since       3.2.0
                 * @return      integer
                 */
                protected function _getMaximumCountForSearchPerKeyword( $aTerms ) {
                    return $this->oOption->isAdvancedAllowed()
                        ? $this->oUnitOption->get( 'count' )
                        : 10;
                }

    
        /**
         * Checks whether response has an error.
         * @return      boolean
         * @since       3
         */
        protected function _isError( $aProducts ) {
            if ( isset( $aProducts[ 'Error' ][ 'Code' ] ) ) {
                return true;
            }
            if ( isset( $aProducts[ 'Items' ][ 'Request' ][ 'Errors' ] ) ) {
                return true;
            }
            return parent::_isError( $aProducts );
            
        }    
    
    /**
     * Performs paged API requests.
     * 
     * This enables to retrieve more than 10 items. However, for it, it performs multiple requests, thus, it will be slow.
     * 
     * @since           2.0.1
     * @return          arrat
     */
    protected function getRequest( $iCount ) {
        
        $_oAPI = new AmazonAutoLinks_ProductAdvertisingAPI( 
            $this->oUnitOption->get( 'country' ), 
            $this->oOption->get( 'authentication_keys', 'access_key' ),
            $this->oOption->get( 'authentication_keys', 'access_key_secret' ),
            $this->oUnitOption->get( 'associate_id' )
        );

        // First, perform the search for the first page regardless the specified count (number of items).
        // Keys with an empty value will be filtered out when performing the request.            
        $_aResponse = $_oAPI->request(
            $this->getAPIParameterArray( $this->oUnitOption->get( 'Operation' ) ),
            $this->oUnitOption->get( 'cache_duration' ),
            $this->oUnitOption->get( '_force_cache_renewal' )
        );

        if ( $iCount <= 10 ) {
            return $_aResponse;
        }
        
        // Check if the necessary key is set
        if ( ! isset( $_aResponse[ 'Items' ][ 'Item' ] ) || ! is_array( $_aResponse[ 'Items' ][ 'Item' ] ) ) {
            return $_aResponse;
        }
        
        // Calculate the required number of pages.
        $_iPage = $this->_getTotalPageNumber( 
            $iCount, 
            $_aResponse, 
            $this->oUnitOption->get( 'SearchIndex' ) 
        );
        
        $_aResponseTrunk = $_aResponse;
                
        // First perform fetching data in the background if caches are not available. Parse backwards 
        $_iScheduled = 0;
        for ( $_i = $_iPage; $_i >= 2 ; $_i-- ) {
            $_iScheduled += ( integer ) $_oAPI->scheduleInBackground(
                $this->getAPIParameterArray( $this->oUnitOption->get( 'Operation' ), $_i ),
                $this->oUnitOption->get( 'cache_duration' )
            );
        }
        if ( $_iScheduled ) {
            // there are items scheduled to fetch in the background, do it right now.
            AmazonAutoLinks_Shadow::gaze();
        }
        
        // Start from the second page since the first page has been already done. 
        for ( $_i = 2; $_i <= $_iPage; $_i++ ) {
            
            $_aResponse = $_oAPI->request( 
                $this->getAPIParameterArray(
                    $this->oUnitOption->get( 'Operation' ), 
                    $_i 
                ),
                $this->oUnitOption->get( 'cache_duration' ),
                $this->oUnitOption->get( '_force_cache_renewal' )
            );
            if ( isset( $_aResponse[ 'Items' ][ 'Item' ] ) && is_array( $_aResponse[ 'Items' ][ 'Item' ] ) ) {
                $_aResponseTrunk[ 'Items' ][ 'Item' ] = $this->_addItems( $_aResponseTrunk[ 'Items' ][ 'Item' ], $_aResponse[ 'Items' ][ 'Item' ] );    
            }
                            
        }    
        
        return $_aResponseTrunk;
        
    }
        /**
         * Returns the total page number
         * 
         * @since   2.0.4.1b
         * @see     http://docs.aws.amazon.com/AWSECommerceService/latest/DG/ItemSearch.html
         */
        protected function _getTotalPageNumber( $iCount, $aResponse, $sSearchIndex='All' ) {
            
            $iMaxAllowedPages = $sSearchIndex == 'All' ? 5 : 10;        
            $iPage = ceil( $iCount / 10 );
            $iPage = $iPage > $iMaxAllowedPages ? $iMaxAllowedPages : $iPage;
            $iFoundTotalPages = isset( $aResponse[ 'Items' ][ 'TotalPages' ] ) ? $aResponse[ 'Items' ][ 'TotalPages' ] : 1;
            return $iFoundTotalPages <= $iPage ? 
                $iFoundTotalPages 
                : $iPage;
            
        }    
        /**
         * Adds product item elements in a response array if the same ASIN is not already in there
         * 
         * @since            2.0.4.1
         */
        protected function _addItems( $aMain, $aItems ) {
            
            // Extract all ASINs from the main array.
            $_aASINs = array();
            foreach( $aMain as $_aItem ) {
                if ( ! isset( $_aItem[ 'ASIN' ] ) ) { continue; }
                $_aASINs[ $_aItem[ 'ASIN' ] ] = $_aItem[ 'ASIN' ];
            }
            
            // Add the items if not already there.
            foreach ( $aItems as $_aItem ) {
                if ( ! isset( $_aItem[ 'ASIN' ] ) ) { continue; }
                if ( in_array( $_aItem[ 'ASIN' ], $_aASINs ) ) { continue; }
                $aMain[] = $_aItem;    // finally add the item
            }
            
            return $aMain;
            
        }
    /**
     * 
     * 'Operation' => 'ItemSearch',    // ItemSearch, ItemLookup, SimilarityLookup
     * @since   2.0.2
     * @see     http://docs.aws.amazon.com/AWSECommerceService/latest/DG/ItemSearch.html
     * @see     http://docs.aws.amazon.com/AWSECommerceService/latest/DG/PowerSearchSyntax.html
     */
    protected function getAPIParameterArray( $sOperation='ItemSearch', $iItemPage=null ) {

        $_bIsIndexAllOrBlended  = ( 'All' === $this->oUnitOption->get( 'SearchIndex' ) || 'Blended' === $this->oUnitOption->get( 'SearchIndex' ) );
        $_sTitle                = $this->trimDelimitedElements( 
            $this->oUnitOption->get( 'Title' ), 
            ',', 
            false 
        ); 
        $_aParams               = array(
            'Keywords'              => $this->trimDelimitedElements( 
                $this->oUnitOption->get( 'Keywords' ), 
                ',', 
                false 
            ),
            // 3+ Power parameter, which can only be used when the search index equals 'Books'.
            'Power'                 => $this->oUnitOption->get( 'Power' ),
            'Title'                 => $_bIsIndexAllOrBlended 
                ? null 
                : ( $_sTitle ? $_sTitle : null ),
            'Operation'             => $this->oUnitOption->get( 'Operation' ),
            'SearchIndex'           => $this->oUnitOption->get( 'SearchIndex' ),
            $this->oUnitOption->get( 'search_by' ) => $this->oUnitOption->get( 'additional_attribute' )
                ? $this->oUnitOption->get( 'additional_attribute' )
                : null,
            'Sort'                  => $this->___getParameterOfSort( $this->oUnitOption, ! $_bIsIndexAllOrBlended ),
            'ResponseGroup'         => "Large",
            'BrowseNode'            => ! $_bIsIndexAllOrBlended && $this->oUnitOption->get( 'BrowseNode' ) 
                ? $this->oUnitOption->get( 'BrowseNode' )
                : null,
            'Availability'          => $this->oUnitOption->get( 'Availability' ) 
                ? 'Available' 
                : null,
            'Condition'             => $_bIsIndexAllOrBlended 
                ? null 
                : $this->oUnitOption->get( 'Condition' ),
            'IncludeReviewsSummary' => "True",
            'MaximumPrice'          => ! $_bIsIndexAllOrBlended && $this->oUnitOption->get( 'MaximumPrice' )
                ? $this->oUnitOption->get( 'MaximumPrice' )
                : null,
            'MinimumPrice'          => ! $_bIsIndexAllOrBlended && $this->oUnitOption->get( 'MinimumPrice' )
                ? $this->oUnitOption->get( 'MinimumPrice' )
                : null,
            'MinPercentageOff'      => $this->oUnitOption->get( 'MinPercentageOff' )
                ? $this->oUnitOption->get( 'MinPercentageOff' )
                : null,
            
            // 2.0.7+
            'MerchantId'            => 'Amazon' === $this->oUnitOption->get( 'MerchantId' )
                ? 'Amazon'
                : null, 
                
            // 2.1.0+
            'MarketplaceDomain'     => 'Marketplace' === $this->oUnitOption->get( 'SearchIndex' )
                ? AmazonAutoLinks_Property::getMarketplaceDomainByLocale( $this->oUnitOption->get( 'country' ) )
                : null,                
                
        );        
        $_aParams = $iItemPage
            ? $_aParams + array( 'ItemPage' => $iItemPage )
            : $_aParams;


        // 3+ When the Power argument is set, the SearchIndex must not be set. 
        // and when the SearchIndex is not set, Sort cannot be set.
        if ( $_aParams[ 'Power' ] ) {
            unset( 
                $_aParams[ 'Sort' ]
            );
        }
        
        unset(
            $_aParams[ '' ]         // not sure but it occured an element without an empty key got inserted
        );
        // if ( $_aParams[ 'Title' ] ) {
            // unset(
                // $_aParams[ 'SearchIndex' ]
            // );
            // $_aParams[ 'SearchIndex' ] = 'Books';
        // }
        
        return $_aParams;
    }
        /**
         * @param   boolean         $bParamterAllowed       Whether the sort parameter is allowed or not. When the search index is set to `All` or `Blended`, this is not allowed.
         * @return  null|string
         * @since   3.5.5
         * @remark  when the search index is All, sort cannot be specified
         */
        private function ___getParameterOfSort( $oUnitOption, $bParamterAllowed ) {

            if ( ! $bParamterAllowed ) {
                return null;
            }

            // Backward compatibility for v3.5.4 or below.
            // In recent Amazon Advertising API, `inversepricerank` is not supported in many locales.
            $_sSortOption = $oUnitOption->get( 'Sort' );
            if ( 'pricerank' == $_sSortOption ) {
                return 'price';
            }
            if ( 'inversepricerank' == $_sSortOption ) {
                return '-price';
            }
            return $_sSortOption;

        }

    /**
     * Constructs products array to be parsed in the template.
     * 
     * @return      array
     */
    protected function getProducts( $aResponse ) {
        return $this->___getProductsFromResponseItems(
            $this->___getItemsExtracted( $aResponse ),          // items
            strtoupper( $this->oUnitOption->get( 'country' ) ), // locale
            $this->oUnitOption->get( 'associate_id' ),          // associate id
            $this->___getAPIResponseDate( $aResponse ),         // response date
            $this->oUnitOption->get( 'count' )
        );
    }
        /**
         * @param       array   $aItems
         * @param       string  $_sLocale
         * @param       string  $_sAssociateID
         * @param       string  $_sResponseDate
         * @since       3.5.0
         * @return      array
         */
        private function ___getProductsFromResponseItems( array $aItems, $_sLocale, $_sAssociateID, $_sResponseDate, $_iCount ) {

            $_aASINLocales  = array();  // stores added product ASINs for performing a custom database query.
            $_aProducts     = array();

            // First Iteration - Extract displaying ASINs.
            foreach ( $aItems as $_iIndex => $_aItem ) {

                // This parsed item is no longer needed and must be removed once it is parsed
                // as this method is called recursively.
                unset( $aItems[ $_iIndex ] );

                try {
                    $_aItem         = $this->___getItemStructured( $_aItem );
                    $_sTitle        = $this->___getTitle( $_aItem );
                    $_sThumbnailURL = $this->___getThumbnailURL( $_aItem );
                    $_sProductURL   = $this->getProductLinkURLFormatted(
                        rawurldecode( $_aItem[ 'DetailPageURL' ] ),
                        $_aItem[ 'ASIN' ]
                    );
                    $_sContent      = $this->_getContents( $_aItem );
                    $_sDescription  = $this->___getDescription( $_sContent, $_sProductURL );
    
                    // At this point, update the black&white lists as this item is parsed.
                    $this->setParsedASIN( $_aItem[ 'ASIN' ] );
    
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
                    // AmazonAutoLinks_Debug::log( $_oException->getMessage() );
                    continue;   // skip
                }
    
                $_aASINLocales[] = $_aProduct[ 'ASIN' ] . '_' . strtoupper( $_sLocale );
                $_aProducts[]    = $_aProduct;
                
                // Max Number of Items 
                if ( count( $_aProducts ) >= $_iCount ) {
                    break;            
                }
                
            }

            return $this->___getProductsFormattedFromResponseItems(
                $aItems,
                $_aProducts,
                $_aASINLocales,
                $_sLocale,
                $_sAssociateID,
                $_iCount,
                $_sResponseDate
            );
            
        }    
                
            /**
             *
             * @return     array
             * @since      3.5.0
             */
            private function ___getProductsFormattedFromResponseItems( $aItems, $_aProducts, $_aASINLocales, $_sLocale, $_sAssociateID, $_iCount, $_sResponseDate ) {
        
                try {

                    $_iResultCount = count( $_aProducts );
                    // Second iteration.
                    $_aProducts = $this->_getProductsFormatted(
                        $_aProducts,
                        $_aASINLocales,
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
             * @throws  Exception
             * @since   3.5.0
             */
            private function ___getItemStructured( $aItem ) {
                if ( ! is_array( $aItem ) ) {
                    throw new Exception( 'The product element must be an array.' );
                }
                $_aItem = $aItem + self::$aStructure_Item;
                $this->___checkASINBlocked( $_aItem[ 'ASIN' ] );
                return $_aItem;
            }
                /**
                 * @throws  Exception
                 * @since   3.5.0
                 */
                private function ___checkASINBlocked( $_sASIN ) {
                    if ( $this->isASINBlocked( $_sASIN ) ) {
                        throw new Exception( 'The product ASIN is black-listed: ' . $_sASIN );
                    }
                }
    
            /**
             * @param   array   $aItem
             * @since   3.5.0
             * @return  string
             */
            private function ___getTitle( $aItem ) {
                $_sTitle = $this->_getTitleSanitized( $aItem[ 'ItemAttributes' ][ 'Title' ] );
                $this->___checkTitleBlocked( $_sTitle );
                return $_sTitle;
            }
                /**
                 * @since   3.5.0
                 * @throws  Exception
                 */
                private function ___checkTitleBlocked( $sTitle ) {
                    if ( $this->isTitleBlocked( $sTitle ) ) {
                        throw new Exception( 'The title is black-listed: ' . $sTitle );
                    }
                }
    
            /**
             * @param       array       $aItem
             * @return      string
             * @since       3.5.0
             */
            private function ___getThumbnailURL( $aItem ) {
                $_sThumbnailURL = $this->getElement( $aItem, array( 'MediumImage', 'URL' ), '' );

                /**
                 * Occasionally, the `MediumImage` element (main thumbnail image) does not exist but sub-images do.
                 * In that case, use the first sub-image.
                 *
                 * @since  3.5.2
                 */
                if ( empty( $_sThumbnailURL ) ) {
                    $_sThumbnailURL = $this->getElement( $aItem, array( 'ImageSets', 'ImageSet', '0', 'MediumImage', 'URL' ), '' );
                }

                $this->___checkImageAllowed( $_sThumbnailURL );
                return $_sThumbnailURL;
            }
                /**
                 * @since   3.5.0
                 * @throws  Exception
                 */
                private function ___checkImageAllowed( $sThumbnailURL ) {
                    if ( ! $this->isImageAllowed( $sThumbnailURL ) ) {
                        throw new Exception( 'No image is allowed: ' . $sThumbnailURL );
                    }
                }
            /**
             * @param $sContent
             * @param $sProductURL
             * @return  string
             * @since   3.5.0
             */
            private function ___getDescription( $sContent, $sProductURL ) {
                $_sDescription  = $this->_getDescriptionSanitized(
                    $sContent,
                    $this->oUnitOption->get( 'description_length' ),
                    $this->_getReadMoreText( $sProductURL )
                );
                $this->___checkDescriptionBlocked( $_sDescription );
                return $_sDescription;
            }
                /**
                 * @since   3.5.0
                 * @throws  Exception
                 */
                private function ___checkDescriptionBlocked( $sDescription ) {
                    if ( $this->isDescriptionBlocked( $sDescription ) ) {
                        throw new Exception( 'The description is not allowed: ' . $sDescription );
                    }
                }
    
            /**
             * @since   3.5.0
             * @return array
             * @throws Exception
             */
            private function ___getProduct(
                $_aItem,
                $_sTitle,
                $_sThumbnailURL,
                $_sProductURL,
                $_sContent,
                $_sDescription,
                $_sLocale,
                $_sAssociateID,
                $_sResponseDate
            ) {
    
                // Construct a product array. This will be passed to a template.
                $_aProduct = array(
                    'ASIN'               => $_aItem[ 'ASIN' ],
                    'product_url'        => $_sProductURL,
                    'title'              => $_sTitle,
                    'text_description'   => $this->_getDescriptionSanitized( $_sContent, 250, '' /* no read more link */ ),  // forced-truncated version of the contents
                    'description'        => $_sDescription, // reflects the user set character length
                    'meta'               => '',
                    'content'            => $_sContent,
                    'image_size'         => $this->oUnitOption->get( 'image_size' ),
                    'thumbnail_url'      => $this->getProductImageURLFormatted(
                        $_sThumbnailURL,
                        $this->oUnitOption->get( 'image_size' ),
                        strtoupper( $this->oUnitOption->get( 'country' ) )  // locale
                    ),
                    'author'             => isset( $_aItem[ 'ItemAttributes' ][ 'Author' ] )
                        ? implode( ', ', ( array ) $_aItem[ 'ItemAttributes' ][ 'Author' ] )
                        : '',
                    // 'manufacturer' => $_aItem[ 'ItemAttributes' ][ 'Manufacturer' ],
                    'category'           => $this->getElement(
                        $_aItem,
                        array( 'ItemAttributes', 'ProductGroup' ),
                        ''
                    ),
                    // Either the released date or the published date. @see     http://docs.aws.amazon.com/AWSECommerceService/latest/DG/CHAP_response_elements.html#PublicationDate
                    'date'               => $this->getElement(
                        $_aItem,
                        array( 'ItemAttributes', 'ReleaseDate' ),
                        $this->getElement(
                            $_aItem,
                            array( 'ItemAttributes', 'PublicationDate' )
                        )
                    ),
                    'updated_date'       => $_sResponseDate,
                    'is_adult_product'   => $this->getElement(
                        $_aItem,
                        array( 'ItemAttributes', 'IsAdultProduct' ),
                        false
                    ),
    
                    'review'              => '',  // customer reviews
                    'rating'              => '',  // 3+
                    'button'              => '',  // 3+
                    'image_set'           => '',  // 3+
                    'editorial_review'    => '',  // 3+ // @todo add a format method for editorial reviews.
                    'similar_products'    => '', // $this->getElement( $_aItem, 'SimilarProducts' ),
                )
                + $this->___getPrices( $_aItem )
                + $_aItem;
    
                // Add meta data to the description
                $_aProduct[ 'meta' ]        = $this->___getProductMetaFormatted( $_aProduct );
                $_aProduct[ 'description' ] = $this->___getProductDescriptionFormatted( $_aProduct );
    
                // Thumbnail
                $_aProduct[ 'formatted_thumbnail' ] = $this->_getProductThumbnailFormatted( $_aProduct );
                $_aProduct[ 'formed_thumbnail' ]    = $_aProduct[ 'formatted_thumbnail' ]; // backward compatibility
    
                // Title
                $_aProduct[ 'formatted_title' ] = $this->_getProductTitleFormatted( $_aProduct );
                $_aProduct[ 'formed_title' ]    = $_aProduct[ 'formatted_title' ]; // backward compatibility
    
    
                // Button - check if the %button% variable exists in the item format definition.
                // It accesses the database, so if not found, the method should not be called.
                if (
                    $this->hasCustomVariable(
                        $this->oUnitOption->get( 'item_format' ),
                        array( '%button%', )
                    )
                ) {
    
                    $_aProduct[ 'button' ] = $this->_getButton(
                        $this->oUnitOption->get( 'button_type' ),
                        $this->_getButtonID(),
                        $_aProduct[ 'product_url' ],
                        $_aProduct[ 'ASIN' ],
                        $_sLocale,
                        $_sAssociateID,
                        $this->_getButtonID(),
                        $this->oOption->get( 'authentication_keys', 'access_key' ) // public access key
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
             * Returns the API response date which must be inserted in the advertisement output for the API agreements.
             * @see         https://affiliate-program.amazon.com/gp/advertising/api/detail/agreement.html/ref=amb_link_83957651_1?ie=UTF8&rw_useCurrentProtocol=1&pf_rd_m=ATVPDKIKX0DER&pf_rd_s=assoc-center-1&pf_rd_r=&pf_rd_t=501&pf_rd_p=&pf_rd_i=assoc-api-detail-5-v2
             * @see         (0) in the above link.
             * @since       3.2.0
             * @return      string 
             */
            private function ___getAPIResponseDate( $aResponse ) {
                
                $_aArguments = $this->getElementAsArray( 
                    $aResponse,
                    array( 'OperationRequest', 'Arguments', 'Argument' )
                );
                foreach( $_aArguments as $_aArgument ) {
                    $_sTimeStampKey = $this->getElement(
                        $_aArgument,
                        array( '@attributes', 'Name' )
                    );
                    if ( 'Timestamp' === $_sTimeStampKey ) {
                        return $this->getElement(
                            $_aArgument,
                            array( '@attributes', 'Value' )
                        );
                    }
                }
                return '';
                
            }      
          
            /**
             * Extracts items array from the API response array.
             * @since       3
             * @return      array
             */
            private function ___getItemsExtracted( $aResponse ) {
    
                $_aItems = $this->getElement(
                    $aResponse, // subject array
                    array( 'Items', 'Item' ), // dimensional keys
                    $aResponse  // default
                );
                
                // When only one item is found, the item elements are not contained in an array. So contain it.
                if ( isset( $_aItems[ 'ASIN' ] ) ) {
                    $_aItems = array( $_aItems ); 
                }
                return $_aItems;
                
            }
            
            /**
             * Returns prices of the product as an array.
             * @since       2.1.2
             * @return      array
             */
            private function ___getPrices( array $aItem ) {
                
                $_sProperPirce      = $this->getElement(
                    $aItem,
                    array( 'ItemAttributes', 'ListPrice', 'FormattedPrice' ),
                    ''
                );
                $_sDiscountedPrice  = $this->getElement(
                    $aItem,
                    array( 'Offers', 'Offer', 'OfferListing', 'Price', 'FormattedPrice' ),
                    ''
                );
                $_sDiscountedPrice  = $_sProperPirce && $_sDiscountedPrice === $_sProperPirce
                    ? ''
                    : $_sDiscountedPrice;
                $_sProperPirce      = $_sDiscountedPrice
                    ? "<s>" . $_sProperPirce . "</s>"
                    : $_sProperPirce;
                    
                $_aPrices = array(
                    'price'              => $_sProperPirce
                        ? "<span class='amazon-product-price-value'>"  
                               . $_sProperPirce
                            . "</span>"
                        : "",
                    'discounted_price'   => $_sDiscountedPrice
                        ? "<span class='amazon-product-discounted-price-value'>" 
                                . $aItem[ 'Offers' ][ 'Offer' ][ 'OfferListing' ][ 'Price' ][ 'FormattedPrice' ]
                            . "</span>"
                        : '',
                    'lowest_new_price'   => isset( $aItem[ 'OfferSummary' ][ 'LowestNewPrice' ][ 'FormattedPrice' ] )
                        ? "<span class='amazon-product-lowest-new-price-value'>"
                                . $aItem[ 'OfferSummary' ][ 'LowestNewPrice' ][ 'FormattedPrice' ]
                            . "</span>"
                        : '',
                    'lowest_used_price'  => isset( $aItem[ 'OfferSummary' ][ 'LowestUsedPrice' ][ 'FormattedPrice' ] )
                        ? "<span class='amazon-product-lowest-used-price-value'>"
                                . $aItem[ 'OfferSummary' ][ 'LowestUsedPrice' ][ 'FormattedPrice' ]
                            . "</span>"
                        : '',
                );
    
                return $_aPrices;
            }
    
            /**
             * Returns the formatted product meta HTML block.
             * 
             * @since       2.1.1
             * @return      string
             */
            private function ___getProductMetaFormatted( array $aProduct ) {
                
                $_aOutput = array();
                if ( $aProduct[ 'author' ] ) {
                    $_aOutput[] = "<span class='amazon-product-author'>" 
                            . sprintf( __( 'by %1$s', 'amazon-auto-links' ) , $aProduct[ 'author' ] ) 
                        . "</span>";
                }
                if ( $aProduct[ 'price' ] ) {
                    $_aOutput[] = "<span class='amazon-product-price'>" 
                            . sprintf( __( 'for %1$s', 'amazon-auto-links' ), $aProduct[ 'price' ] )
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
             * @since       2.1.1
             * @return      string
             */        
            private function ___getProductDescriptionFormatted( array $aProduct ) {
                return $aProduct[ 'meta' ] 
                    . "<div class='amazon-product-description'>" 
                        . $aProduct[ 'description' ] 
                    . "</div>";
            }
                     

    
}