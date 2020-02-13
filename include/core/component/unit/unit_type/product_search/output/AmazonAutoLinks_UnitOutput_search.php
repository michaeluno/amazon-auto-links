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
     * The array element key name that containes `Items` element.
     * PA-API 5 operations such as `GetItems`, `SearchItems` have different key names such as `ItemsResult` abd `SearchResult`.
     * @var string
     * @since   3.9.0
     */
    protected $_sResponseItemsParentKey = 'SearchResult';

    /**
     * Lists the variables used in the Item Format unit option that require to access the custom database.
     * @since       3.5.0
     * @var array
     */
    protected $_aItemFormatDatabaseVariables = array(
        '%review%', '%rating%', '%similar%',
        '%_discount_rate%', '%_review_rate%', // 3.9.2  - used for advanced filters
        '%price%'   // 3.10.0   - as preferred currency is now supported, the `GetItem` operation is more up-to-date than `SearchItem` then sometimes it gives a different result so use it if available.
    );

    /**
     * Represents the array structure of the item array element of API response data.
     * @since            unknown
     * @todo        3.9.0       The structure has been entirely changed in PA-API5
     */    
    public static $aStructure_Item = array(
        'ASIN'              => null,
        'DetailPageURL'     => null,
        'ItemInfo'          => null,
        'BrowseNodeInfo'    => null,
        'Images'            => null,
        'Offers'            => null,
// @deprecated       'ItemAttributes'    => null,
// @deprecated      'EditorialReviews'  => null,
//        'ItemLinks'         => null,
//        'ImageSets'         => null,
//        'BrowseNodes'       => null,
//        'SimilarProducts'   => null,
//        'MediumImage'       => null,
//        'OfferSummary'      => null,
    );

    /**
     * 
     * @return    array    The response array.
     */
    public function fetch( $aURLs=array() ) {

        // Get API responses
        $_aResponse = $this->_getResponses( $aURLs );
        $_aError    = $this->getElement( $_aResponse, array( 'Error' ), array() );

        // Check errors
        if (
            ! empty( $_aError )
            && ! isset( $_aResponse[ $this->_sResponseItemsParentKey ] )    // There are cases that error is set but items are returned
        ) {
            return $_aResponse;
        }

        // Format items
        return $this->getProducts( $_aResponse );

    }

        /**
         * @since       3.1.4
         * @since       3.8.1           Added the $aURLs parameter.
         * @param       array       $aURLs      The `search` unit type does not use this parameter but `url` and `category` do.
         * @scope       protected       The 'url' unit type will extend this method.
         * @return      array
         */
        protected function _getResponses( array $aURLs=array() ) {

            // Sanitize the search terms
            $this->___setSearchTerms();

            // Normal operation
            if ( ! $this->oUnitOption->get( 'search_per_keyword' ) ) {
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
                    
                    // The above 'search_per_keyword' = true will trigger `___getResponsesByMultipleKeywords()`
                    // so an array can be set for the terms. 
                    $this->oUnitOption->set( 
                        $this->sSearchTermKey,  // ItemId | Keywords
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
                
                $_iCount    = $this->___getCountForSearchPerKeyword();
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
                    array( $this->_sResponseItemsParentKey, 'Items', ),
                    $_aItems
                );
                if ( 0 < count( $this->getElementAsArray( $_aResponse, array( $this->_sResponseItemsParentKey, 'Items' ) ) ) ) {
                    unset(
                        $_aResponse[ 'Error' ]
                        // @deprecated 3.9.0 $_aResponse[ 'Error' ][ 'Code' ]
                        // @deprecated 3.9.0       $_aResponse[ 'Items' ][ 'Request' ][ 'Errors' ]
                    );
                }            

                return $_aResponse; 
             
            }    

                /**
                 * @return      array
                 */
                private function ___getItemsMerged( array $aItems, array $aResponse ) {
                    
                    $aItems        = array_merge(
                        $aItems,
                        $this->getElementAsArray( $aResponse, array( $this->_sResponseItemsParentKey, 'Items' ) )
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
                 * @remark      This sets the minimum count as 10 to cover cases that too few items shown
                 * due to removals with product filters. 10 is also the maximum count for the API `ItemID` parameter.
                 * @since       3.8.7
                 * @return      integer     The item count.
                 */
                private function ___getCountForSearchPerKeyword() {
                    $_iCount = $this->oOption->isAdvancedAllowed()
                        ? ( integer ) $this->oUnitOption->get( 'count' )
                        : 10;
                    return $_iCount <= 10
                        ? 10
                        : $_iCount;
                }

    /**
     * Performs paged API requests.
     *
     * This enables to retrieve more than 10 items. However, for it, it performs multiple requests, thus, it will be slow.
     *
     * @since           2.0.1
     * @return          array
     */
    protected function getRequest( $iCount ) {
        
        $_oAPI = new AmazonAutoLinks_PAAPI50(
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
        
        // Check if it has an item
        if ( ! $this->___hasItem( $_aResponse ) ) {
            return $_aResponse;
        }

        // @deprecated 3.9.0
//        if ( ! isset( $_aResponse[ 'Items' ][ 'Item' ] ) || ! is_array( $_aResponse[ 'Items' ][ 'Item' ] ) ) {
//            return $_aResponse;
//        }

        // @deprecated 3.9.0
        // Calculate the required number of pages.
//        $_iPage = $this->_getTotalPageNumber(
//            $iCount,
//            $_aResponse,
//            $this->oUnitOption->get( 'SearchIndex' )
//        );

        /**
         * As PA-API 5.0 does not return reliable total page count,
         * perform search from the lowest page number and when it hits an error stop.
         */
        $_iMaxPage = ( integer ) ceil( $iCount / 10 );

        $_aResponseTrunk = $_aResponse;

        // @deprecated 3.9.0
        // Prefetch from backwards. First perform fetching data in the background if caches are not available. Parse backwards
/*        $_iScheduled = 0;
        for ( $_i = $_iPage; $_i >= 2 ; $_i-- ) {
            $_iScheduled += ( integer ) $_oAPI->scheduleInBackground(
                $this->getAPIParameterArray( $this->oUnitOption->get( 'Operation' ), $_i ),
                $this->oUnitOption->get( 'cache_duration' )
            );
        }
        if ( $_iScheduled ) {
            // there are items scheduled to fetch in the background, do it right now.
            AmazonAutoLinks_Shadow::gaze();
        }*/
        
        // Start from the second page since the first page has been already done. 
        for ( $_i = 2; $_i <= $_iMaxPage; $_i++ ) {

            $_aResponse = $_oAPI->request( 
                $this->getAPIParameterArray(
                    $this->oUnitOption->get( 'Operation' ), 
                    $_i // page number
                ),
                $this->oUnitOption->get( 'cache_duration' ),
                $this->oUnitOption->get( '_force_cache_renewal' )
            );
            if ( isset( $_aResponse[ 'Error' ] ) ) {
                break;
            }
            if ( $this->___hasItem( $_aResponse ) ) {
                $_aResponseTrunk[ $this->_sResponseItemsParentKey ][ 'Items' ] = $this->___addItems(
                    $_aResponseTrunk[ $this->_sResponseItemsParentKey ][ 'Items' ],
                    $_aResponse[ $this->_sResponseItemsParentKey ][ 'Items' ]
                );
            }

        }    
        
        return $_aResponseTrunk;
        
    }
        /**
         * @param $aResponse
         *
         * @return bool
         */
        private function ___hasItem( array $aResponse ) {
            $_aItems = $this->getElement( $aResponse, array( $this->_sResponseItemsParentKey, 'Items' ) );
            if ( ! is_array( $_aItems ) ) {
                return false;
            }
            return ( boolean ) count( $_aItems );
        }

        /**
         * Returns the total page number
         *
         * @since   2.0.4.1b
         * @see     http://docs.aws.amazon.com/AWSECommerceService/latest/DG/ItemSearch.html
         * @see https://forums.aws.amazon.com/message.jspa?messageID=919160
         * @return  integer
         * @deprecated 3.9.0 `SearchResult` is not reliable for the bug that only returns 146 for every search request.
         */
/*        protected function _getTotalPageNumber( $iCount, $aResponse, $sSearchIndex='All' ) {
            return 0;
            // @remark `SearchResult` is not reliable for the bug that only returns 146 for every search request.
            // @see https://forums.aws.amazon.com/message.jspa?messageID=919160
            // $_iTotalResultCount = $this->getElement( $aResponse, array( $this->_sResponseItemsParentKey, 'TotalResultCount' ), 0 );

            $iMaxAllowedPages = $sSearchIndex == 'All' ? 5 : 10;
            $iPage = ceil( $iCount / 10 );
            $iPage = $iPage > $iMaxAllowedPages ? $iMaxAllowedPages : $iPage;
            $iFoundTotalPages = isset( $aResponse[ 'Items' ][ 'TotalPages' ] ) ? $aResponse[ 'Items' ][ 'TotalPages' ] : 1;
            return $iFoundTotalPages <= $iPage ?
                $iFoundTotalPages
                : $iPage;

        }*/
        /**
         * Adds product item elements in a response array if the same ASIN is not already in there
         *
         * @since       2.0.4.1
         * @since       3.9.0   Changed the scope to private.
         */
        private function ___addItems( $aMain, $aItems ) {

            // Extract all ASINs from the main array.
            $_aASINs = array();
            foreach( $aMain as $_aItem ) {
                if ( ! isset( $_aItem[ 'ASIN' ] ) ) {
                    continue;
                }
                $_aASINs[ $_aItem[ 'ASIN' ] ] = $_aItem[ 'ASIN' ];
            }

            // Add the items if not already there.
            foreach ( $aItems as $_aItem ) {
                if ( ! isset( $_aItem[ 'ASIN' ] ) ) {
                    continue;
                }
                if ( in_array( $_aItem[ 'ASIN' ], $_aASINs ) ) {
                    continue;
                }
                $aMain[] = $_aItem;    // finally add the item
            }

            return $aMain;

        }
    /**
     *
     * @since   2.0.2
     * @see     http://docs.aws.amazon.com/AWSECommerceService/latest/DG/ItemSearch.html
     * @see     http://docs.aws.amazon.com/AWSECommerceService/latest/DG/PowerSearchSyntax.html
     * @since   3.9.0   The parameter format has been changed in PA-API 5
     */
    protected function getAPIParameterArray( $sOperation='SearchItems', $iItemPage=null ) {

        // @deprecated 3.9.0
        $_bIsIndexAllOrBlended  = ( 'All' === $this->oUnitOption->get( 'SearchIndex' ) || 'Blended' === $this->oUnitOption->get( 'SearchIndex' ) );

        $_sTitle                = $this->trimDelimitedElements( $this->oUnitOption->get( 'Title' ), ',', false );
        $_aPayload               = array(
            'Keywords'              => $this->trimDelimitedElements( $this->oUnitOption->get( 'Keywords' ), ',', false ),
            'Title'                 => $_sTitle ? $_sTitle : null,
            'Operation'             => $this->_getOperation( $this->oUnitOption->get( 'Operation' ) ),  // SearchItems
            'SearchIndex'           => $this->oUnitOption->get( 'SearchIndex' ),
            $this->oUnitOption->get( 'search_by' ) => $this->oUnitOption->get( 'additional_attribute' )
                ? $this->oUnitOption->get( 'additional_attribute' )
                : null,
            'SortBy'                => $this->___getParameterSortBy( $this->oUnitOption ),
            'BrowseNodeId'          => $this->oUnitOption->get( 'BrowseNode' )
                ? $this->oUnitOption->get( 'BrowseNode' )
                : null,
            'Availability'          => $this->oUnitOption->get( 'Availability' ) 
                ? 'Available' 
                : 'IncludeOutOfStock',
            'Condition'             => $this->oUnitOption->get( 'Condition' ),
            'MaxPrice'              => $this->oUnitOption->get( 'MaximumPrice' )
                ? ( integer ) $this->oUnitOption->get( 'MaximumPrice' )
                : null,
            'MinPrice'              => $this->oUnitOption->get( 'MinimumPrice' )
                ? ( integer ) $this->oUnitOption->get( 'MinimumPrice' )
                : null,
            'MinReviewsRating'      => $this->oUnitOption->get( 'MinReviewsRating' )
                ? ( integer ) $this->oUnitOption->get( 'MinReviewsRating' )
                : null,
            'MinSavingPercent'      => $this->oUnitOption->get( 'MinPercentageOff' )
                ? ( integer ) $this->oUnitOption->get( 'MinPercentageOff' )
                : null,
            'Merchant'            => 'Amazon' === $this->oUnitOption->get( 'MerchantId' )
                ? 'Amazon'
                : null,

            'DeliveryFlags'         => array_keys( array_filter( ( array ) $this->oUnitOption->get( 'DeliveryFlags' ) ) ),
            'CurrencyOfPreference'  => $this->oUnitOption->get( 'preferred_currency' )
                ? $this->oUnitOption->get( 'preferred_currency' )
                : null,
            'LanguagesOfPreference' => $this->oUnitOption->get( 'language' )
                ? $this->getAsArray( $this->oUnitOption->get( 'language' ) )
                : null,
            'Resources'             => AmazonAutoLinks_PAAPI50___Payload::$aResources,
        );
        $_aPayload = $iItemPage
            ? $_aPayload + array( 'ItemPage' => $iItemPage )
            : $_aPayload;

        // not sure but it occurred an element without an empty key got inserted
        unset( $_aPayload[ '' ] );
        return $_aPayload;

    }

        /**
         * For backward compatibility for PA-API 4
         *
         * @param string $sOperation
         *
         * @return string
         * @since   3.9.0
         */
        protected function _getOperation( $sOperation ) {
            // ItemSearch, ItemLookup, SimilarityLookup
            if ( 'ItemSearch' === $sOperation ) {
                return 'SearchItems';
            }
            if ( 'ItemLookup' === $sOperation ) {
                return 'GetItems';
            }
            return $sOperation;
        }

        /**
         * @param $oUnitOption
         *
         * Accepted parameter values:
         *  AvgCustomerReviews     Sorts results according to average customer reviews
         *  Featured             Sorts results with featured items having higher rank
         *  NewestArrivals         Sorts results with according to newest arrivals
         *  Price:HighToLow     Sorts results according to most expensive to least expensive
         *  Price:LowToHigh     Sorts results according to least expensive to most expensive
         *  Relevance             Sorts results with relevant items having higher rank
         * @see https://webservices.amazon.com/paapi5/documentation/search-items.html#sortby-parameter
         * @return string
         * @since   3.9.0
         */
        private function ___getParameterSortBy( $oUnitOption ) {

            $_sSortOption = $oUnitOption->get( 'Sort' );

            // First, check if it is a valid values. If so, just return it.
            if ( in_array( $_sSortOption, array( 'AvgCustomerReviews', 'Featured', 'NewestArrivals', 'Relevance', 'Price:HighToLow', 'Price:LowToHigh' ) ) ) {
                return $_sSortOption;
            }

            // Check backward compatible values.
            if ( in_array( $_sSortOption, array( 'price', 'pricerank' ) ) ) {
                return 'Price:LowToHigh';
            }
            if ( in_array( $_sSortOption, array( '-price', 'inversepricerank' ) ) ) {
                return 'Price:HighToLow';
            }
            if ( in_array( $_sSortOption, array( 'reviewrank' ) ) ) {
                return 'AvgCustomerReviews';
            }

            // Other values will be treated as Relevance as default.
            return 'Relevance';
            // sales rank option is not available in PA-API 5.0
            // @deprecated 3.10.0
//            if (
//                in_array(
//                    strtolower( $_sSortOption ),
//                    array(
//                        'relevancerank', 'salesrank',
//                        'all', 'raw'    // not sure how these get passed in the search unit type but there is a report of an error with these
//                    )
//                )
//            ) {
//                return 'Relevance';
//            }
//            return $_sSortOption;
        }


    /**
     * Constructs products array to be parsed in the template.
     * 
     * @return      array
     */
    protected function getProducts( $aResponse ) {
        return $this->_getProductsFromResponseItems(
            $this->getElementAsArray( $aResponse, array( $this->_sResponseItemsParentKey, 'Items' ), array() ),  // Items
            strtoupper( $this->oUnitOption->get( 'country' ) ), // locale
            $this->oUnitOption->get( 'associate_id' ),          // associate id
            $this->getElement( $aResponse, array( '_ResponseDate' ) ), // response date - no need to adjust for GMT, will be done later
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
         * @since       3.9.0   Changed the scope to protected.
         */
        protected function _getProductsFromResponseItems( array $aItems, $_sLocale, $_sAssociateID, $_sResponseDate, $_iCount ) {

            $_aASINLocales = array();  // stores added product ASINs for performing a custom database query.
            $_aProducts           = array();

            $_sLocale   = strtoupper( $this->oUnitOption->get( array( 'country' ), 'US' ) );            
            $_sCurrency = $this->oUnitOption->get( array( 'preferred_currency' ), AmazonAutoLinks_PAAPI50___Locales::getDefaultCurrencyByLocale( $_sLocale ) );
            $_sLanguage = $this->oUnitOption->get( array( 'language' ), AmazonAutoLinks_PAAPI50___Locales::getDefaultLanguageByLocale( $_sLocale ) );
            
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
                        $_aItem[ 'ASIN' ],
                        $this->oUnitOption->get( 'language' ),
                        $this->oUnitOption->get( 'preferred_currency' )
                    );
                    $_sContent      = $this->getContent( $_aItem );
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
                    continue;   // skip
                }

                // @deprecated 3.9.0 $_aASINLocales[] = $_aProduct[ 'ASIN' ] . '_' . strtoupper( $_sLocale );
                $_aASINLocaleCurLang    = $_aProduct[ 'ASIN' ] . '|' . strtoupper( $_sLocale ) . '|' . $_sCurrency . '|' . $_sLanguage;
                $_aASINLocales[ $_aASINLocaleCurLang ] = $_aProduct[ 'ASIN' ] . '_' . strtoupper( $_sLocale );
                
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
                    $_aAdditionalProducts = $this->_getProductsFromResponseItems(
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
             * @param array $aItem
             *
             * @return  string
             * @throws Exception
             * @since   3.5.0
             */
            private function ___getTitle( $aItem ) {
                $_sTitle = $this->getElement( $aItem, array( 'ItemInfo', 'Title', 'DisplayValue' ), '' );
                if ( $this->isTitleBlocked( $_sTitle ) ) {
                    throw new Exception( 'The title is black-listed: ' . $_sTitle );
                }
                return $this->getTitleSanitized( $_sTitle, $this->oUnitOption->get( 'title_length' ) );
            }

            /**
             * @param array $aItem
             *
             * @return      string
             * @throws Exception
             * @since       3.5.0
             */
            private function ___getThumbnailURL( $aItem ) {
                $_sThumbnailURL = $this->getElement( $aItem, array( 'Images', 'Primary', 'Medium', 'URL' ), '' );

                /**
                 * Occasionally, the `MediumImage` element (main thumbnail image) does not exist but sub-images do.
                 * In that case, use the first sub-image.
                 *
                 * @since  3.5.2
                 */
                if ( empty( $_sThumbnailURL ) ) {
                    $_sThumbnailURL = $this->getElement( $aItem, array( 'Images', 'Variants', '0', 'Medium', 'URL' ), '' );
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
             * @param string $sContent
             * @param string $sProductURL
             *
             * @return  string
             * @throws Exception
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
             * @compat PA-API5
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

                // Construct a product array. This will be passed to a template.\
                // @remark  For values that could not retrieved, leave it null so that later it will be filled with formatting routine or triggers a background routine to retrieve product data
                $_aProduct = array(
                    'ASIN'               => $_aItem[ 'ASIN' ],
                    'product_url'        => $_sProductURL,
                    'title'              => $this->oUnitOption->get( array( 'product_title' ), $_sTitle ), // the shortcode parameter 'title' can suppress the title in the parsed data
                    'text_description'   => $this->_getDescriptionSanitized( $_sContent, 250, '' /* no read more link */ ),  // forced-truncated version of the contents
                    'description'        => $_sDescription, // reflects the user set character length. Additional meta data will be prepended.
                    'meta'               => '', // @todo maybe deprecated?
                    'content'            => $_sContent,
                    'image_size'         => $this->oUnitOption->get( 'image_size' ),
                    'thumbnail_url'      => $this->getProductImageURLFormatted(
                        $_sThumbnailURL,
                        $this->oUnitOption->get( 'image_size' ),
                        strtoupper( $this->oUnitOption->get( 'country' ) )  // locale
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
                    'is_prime'            => $this->isPrime( $_aItem ),
                    'feature'             => $this->___getFeatures( $_aItem ),
                    'category'            => $this->___getCategories( $_aItem ),

                    // These must be retrieved separately
                    'review'              => null,  // customer reviews
                    'formatted_rating'    => null,  // 3+ // 4.0.0+ Changed from `rating` to distinguish from the database table column key name

                    // These will be assigned below
                    'image_set'           => null,
                    'button'              => null,  // 3+

                    // @deprecated 3.9.0 PA-API 5 does not support below
                    'editorial_review'    => '',  // 3+ // @todo add a format method for editorial reviews.
                    'similar_products'    => '',

                )
                + $this->getPrices( $_aItem )
                + $_aItem;

                // 3.8.11 Retrieve the images directly from the response rather than the custom database table
                $_aProduct[ 'image_set' ] = $this->___getImageSet(
                    $_aItem,
                    $_sProductURL,
                    $_sTitle,
                    $this->oUnitOption->get( 'subimage_size' ),
                    $this->oUnitOption->get( 'subimage_max_count' )
                );

                // Add meta data to the description
                $_aProduct[ 'meta' ]        = $this->___getProductMetaFormatted( $_aProduct );
                $_aProduct[ 'description' ] = $this->___getProductDescriptionFormatted( $_aProduct );
    
                // Thumbnail
                $_aProduct[ 'formatted_thumbnail' ] = $this->_getProductThumbnailFormatted( $_aProduct );

                // Title
                $_aProduct[ 'formatted_title' ] = $this->getProductTitleFormatted( $_aProduct, $this->oUnitOption->get( 'title_format' ) );

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
                 * Extracts authors of an item
                 * @param array $aItem
                 * @since   3.9.0
                 * @return  string
                 */
                private function ___getAuthors( array $aItem ) {
                    $_aAuthors = array();
                    $_aContributors = $this->getElementAsArray( $aItem, array( 'ItemInfo', 'ByLineInfo', 'Contributors' ), array() );
                    foreach( $_aContributors as $_aContributor ) {
                        $_sAuthor = $this->getElement( $_aContributor, array( 'Role' ) );
                        if ( 'Author' === $_sAuthor ) {
                            $_aAuthors[] = $this->getElement( $_aContributor, array( 'Name' ) );
                        }
                    }
                    return implode( ", ", $_aAuthors );
                }

            /**
             * @return  string
             * @since   3.8.11
             */
            private function ___getImageSet( $aItem, $sProductURL, $sTitle, $iMaxImageSize, $iMaxNumberOfImages ) {
//                if ( ! $this->hasCustomVariable( $this->oUnitOption->get( 'item_format' ), array( '%image_set%', ) ) ) {
//                    return '';
//                }
                $_aImages = $this->getImageSet( $aItem );
                return $this->getSubImages( $_aImages, $sProductURL, $sTitle, $iMaxImageSize, $iMaxNumberOfImages );
            }

            /**
             * @param array $aItem
             * @return  string
             * @since   3.8.0
             * @since   3.8.11  Moved from `AmazonAutoLinks_UnitOutput_Base_ElementFormat`.
             */
            private function ___getCategories( array $aItem ) {
//                if ( ! $this->hasCustomVariable( $this->oUnitOption->get( 'item_format' ), array( '%category%', ) ) ) {
//                    return '';
//                }
                $_aNodes = $this->getElementAsArray( $aItem, array( 'BrowseNodeInfo', 'BrowseNodes', ) );
                return $this->getCategories( $_aNodes );
            }

            /**
             * @param array $aItem
             * @return  string
             * @since   3.8.0
             * @since   3.8.11  Moved from `AmazonAutoLinks_UnitOutput_Base_ElementFormat`.
             */
            private function ___getFeatures( array $aItem ) {
                $_aFeatures = $this->getElementAsArray( $aItem, array( 'ItemInfo', 'Features', 'DisplayValues' ) );
                return $this->getFeatures( $_aFeatures );
            }

            /**
             * Returns the formatted product meta HTML block.
             * 
             * @since       2.1.1
             * @return      string
             * @todo        3.9.0   Add `brand`, `manufacturer` etc
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
             * @since       2.1.1
             * @return      string
             * @since       3.9.0       Removed the `meta` element.
             */        
            private function ___getProductDescriptionFormatted( array $aProduct ) {
                return $aProduct[ 'description' ]
                    ? "<div class='amazon-product-description'>"
                            . $aProduct[ 'description' ]
                        . "</div>"
                    : ''; // 3.10.0 In case of no description, do not even add the div element.
            }

}