<?php
/**
 * Auto Amazon Links
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2022 Michael Uno
 *
 */

/**
 * Fetches product data from outside source.
 *
 * @since 5.0.0
 */
class AmazonAutoLinks_Unit_PAAPISearch_Event_Filter_ProductsFetcher extends AmazonAutoLinks_Unit_UnitType_Common_Event_Filter_ProductsFetcher_Base {

    public $sUnitType = 'search';

    /**
     * @var AmazonAutoLinks_UnitOutput_search
     */
    public $oUnitOutput;

    /**
     * Stores the unit option key that is used for the search.
     * This is needed for the `search_per_keyword` option.
     * @var   string
     * @since 3.2.0
     * @since 5.0.0  Moved from `AmazonAutoLinks_UnitOutput_search`.
     */
    public $sSearchTermKey = 'Keywords';

    /**
     * The array element key name that contains `Items` element.
     * PA-API 5 operations such as `GetItems`, `SearchItems` have different key names such as `ItemsResult` abd `SearchResult`.
     * @var   string
     * @since 3.9.0
     * @since 5.0.0  Moved from `AmazonAutoLinks_UnitOutput_search`.
     */
    protected $_sResponseItemsParentKey = 'SearchResult';    

    /**
     * @param  array $aProducts
     * @return array
     * @since  5.0.0
     */
    protected function _getItemsFromSource( $aProducts ) {
        
        // Get API responses
        $_aResponse = $this->_getResponses();
        $_aError    = $this->getElement( $_aResponse, array( 'Error' ), array() );

        $this->oUnitOutput->sResponseDate = $this->getElement( $_aResponse, array( '_ResponseDate' ) );

        // Find the `Items` container key.
        $_sItemsKey = $this->___getResponseItemsParentKey( $_aResponse );

        // Errors
        if (
            ! empty( $_aError )
            && ! isset( $_aResponse[ $_sItemsKey ] )    // There are cases that error is set but items are returned
        ) {
            return $_aResponse;
        }

        // Extract items
        return $this->getElementAsArray( $_aResponse, array( $_sItemsKey, 'Items' ), array() );

    }
        /**
         * @param  array $aResponse
         * @return string
         * @since  5.0.0  Moved from `AmazonAutoLinks_UnitOutput_scratchpad_payload`. Renamed from `___getItemsKey()`.
         */
        private function ___getResponseItemsParentKey( $aResponse ) {
            if ( $this->_sResponseItemsParentKey ) {
                return $this->_sResponseItemsParentKey;
            }
            // Search from the response array. This is important for the Custom Payload unit type, which extends this class.
            foreach( $aResponse as $_sKey => $_aItem ) {
                if ( isset( $_aItem[ 'Items' ] ) ) {
                    return $_sKey;
                }
            }
            return '';
        }
        /**
         * @since  3.1.4
         * @since  3.8.1      Added the `$aURLs` parameter.
         * @since  5.0.0      Removed the first parameter `$aURLs`. Moved from `AmazonAutoLinks_UnitOutput_search`.
         * @return array
         */
        protected function _getResponses() {

            // Sanitize the search terms
            $this->___setSearchTerms();

            // Normal operation
            if ( ! $this->oUnitOutput->oUnitOption->get( 'search_per_keyword' ) ) {
                $_iCount = ( integer ) $this->oUnitOutput->oUnitOption->get( 'count' );
                // [5.0.6] Adding 10 more items when the shuffle option is enabled to avoid displaying always the same items.
                // @todo Maybe, at a later point, add an advanced option for the internal fetching count for the user to decide how many items to fetch
                // so that this won't be necessary.
                $_iCount = $this->oUnitOutput->oUnitOption->get( 'shuffle' )
                    ? $_iCount + 10
                    : $_iCount;
                return $this->oUnitOutput->getAPIResponse( $_iCount );
            }

            // For contextual search, perform search by each keyword
            return $this->___getResponsesByMultipleKeywords();
            
        }

            /**
             * Sanitizes the search terms.
             * @since 3.2.0
             * @since 5.0.0 Moved from `AmazonAutoLinks_UnitOutput_search`.
             */
            private function ___setSearchTerms() {

                $_sTerms    = trim( $this->oUnitOutput->oUnitOption->get( $this->sSearchTermKey ) );
                if ( ! $_sTerms ) {
                    $this->oUnitOutput->oUnitOption->set( 'search_per_keyword', false );
                    return;
                }
                $_sTerms    = str_replace( PHP_EOL, ',', $_sTerms );
                $_aTerms    =  $this->getStringIntoArray( $_sTerms, ',' );

                /**
                 * When the sort order is `random`, the query items should be shuffled first
                 * because shuffling the retrieved truncated results will just display the same products with different order.
                 * @since   3.5.1
                 */
                if (
                    'random' === $this->oUnitOutput->oUnitOption->get( '_sort' )
                    || $this->oUnitOutput->oUnitOption->get( 'shuffle' )
                ) {
                    shuffle( $_aTerms );
                }

                $_bSearchPerTerm = $this->oUnitOutput->oUnitOption->get( 'search_per_keyword' );
                if ( count( $_aTerms ) > 10 && ! $_bSearchPerTerm ) {
                     
                    // Auto-truncate search terms to 10 as the Amazon API does not allow more than 10 terms to be set per request.
                    $this->oUnitOutput->oUnitOption->set( 'search_per_keyword', true );
                    
                    // The above 'search_per_keyword' = true will trigger `___getResponsesByMultipleKeywords()`
                    // so an array can be set for the terms. 
                    $this->oUnitOutput->oUnitOption->set( 
                        $this->sSearchTermKey,  // ItemId | Keywords
                        array_chunk( $_aTerms, 10 )
                    );                    
                    
                } else {
                                    
                    $this->oUnitOutput->oUnitOption->set( 
                        $this->sSearchTermKey,  
                        implode( ',', $_aTerms ) 
                    );
                    
                }

            }        
            /**
             * @since  3.2.0
             * @since  5.0.0 Moved from `AmazonAutoLinks_UnitOutput_search`.
             * @return array
             */
            private function ___getResponsesByMultipleKeywords() {
             
                $_aItems    = array();
                $_aResponse = array();           
                $_asTerms   = $this->oUnitOutput->oUnitOption->get( $this->sSearchTermKey );                
                $_aTerms    = is_array( $_asTerms )
                    ? $_asTerms
                    : $this->getStringIntoArray( $_asTerms, ',' );
                
                $_iCount    = $this->___getCountForSearchPerKeyword();
                foreach( $_aTerms as $_asSearchTerms ) {
                    
                    $_sSearchTerms = is_scalar( $_asSearchTerms )    
                        ? $_asSearchTerms
                        : implode( ',', $_asSearchTerms );

                    $this->oUnitOutput->oUnitOption->set( 
                        $this->sSearchTermKey, 
                        // 3.2.1+ Nested array is supported to auto-truncate terms more than 10 as API does not allow it.
                        $_sSearchTerms
                    );
                    $_aResponse = $this->oUnitOutput->getAPIResponse( $_iCount );
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
                 * @return array
                 * @param  array $aItems
                 * @param  array $aResponse
                 * @since  ?
                 * @since  5.0.0 Moved from `AmazonAutoLinks_UnitOutput_search`.
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
                    return array_values( $aItems );

                }            

                /**
                 * @remark This sets the minimum count as 10 to cover cases that too few items shown
                 * due to removals with product filters. 10 is also the maximum count for the API `ItemID` parameter.
                 * @return integer     The item count.
                 * @since  3.8.7
                 * @since  5.0.0 Moved from `AmazonAutoLinks_UnitOutput_search`.
                 */
                private function ___getCountForSearchPerKeyword() {
                    $_iCount = $this->oUnitOutput->oOption->isAdvancedAllowed()
                        ? ( integer ) $this->oUnitOutput->oUnitOption->get( 'count' )
                        : 10;
                    return $_iCount <= 10
                        ? 10
                        : $_iCount;
                }    

}